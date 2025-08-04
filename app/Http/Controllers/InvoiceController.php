<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\InvoiceItem;
use App\Models\Item;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoicesExport;
use PDF;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {

        $query = Invoice::with('customer');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date_of_supply', [$request->start_date, $request->end_date]);
        }



        if ($request->ajax()) {
            // $data = Invoice::with('customer')->latest()->get();
            return DataTables::of($query)
                ->addColumn('customer', fn($row) => $row->customer->name)
                ->addColumn('fbr_invoice_no', fn($row) => $row->fbr_invoice_no != null ? $row->fbr_invoice_no : '-')
                ->addColumn('action', function ($row) {
                    $btn = null;
                    if ($row->posting == 0) {
                        $btn .= '<a href="' . route('invoices.edit', $row->id) . '" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <form action="' . route('invoices.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                            <a href="' . route('invoices.posting', $row->id) . '" class="btn btn-sm btn-outline-primary ml-2">
                                <i class="fas fa-upload"></i> Invoice Post
                            </a>';
                    };
                    $btn .= '
            			<a href="' . route('invoices.print', $row->id) . '" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-print"></i> Print
                            </a>
                            <a href="' . route('invoices.pdf', $row->id) . '" class="btn btn-sm btn-outline-danger" target="_blank">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>';


                    return $btn;
                })
                ->rawColumns(['fbr_invoice_no','action'])
                ->make(true);
        }
        $customers = Customer::orderBy('name')->get();

        return view('invoices.index', compact('customers'));
    }

    public function exportExcel(Request $request)
    {
        $query = Invoice::with(['customer', 'items']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date_of_supply', [$request->start_date, $request->end_date]);
        }

        $invoices = $query->get();
        return Excel::download(new InvoicesExport($invoices), 'filtered_invoices.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Invoice::with(['customer', 'items']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date_of_supply', [$request->start_date, $request->end_date]);
        }

        $invoices = $query->get();

        $customer = null;
        if ($request->filled('customer_id')) {
            $customer = \App\Models\Customer::find($request->customer_id) ? \App\Models\Customer::find($request->customer_id)->name : 'All';
        }

        // Totals
        $totals = [
            'totalBeforeTax' => $invoices->sum(fn($i) => $i->items->sum('value_of_goods')),
            'saleTax'        => $invoices->sum(fn($i) => $i->items->sum('amount_of_saleTax')),
            'extraTax'       => $invoices->sum(fn($i) => $i->items->sum('extra_tax')),
            'furtherTax'     => $invoices->sum(fn($i) => $i->items->sum('further_tax')),
            'grandTotal'     => $invoices->sum(fn($i) => $i->items->sum('total')),
        ];

        $pdf = PDF::loadView('invoices.exports.export_pdf', compact('invoices', 'customer', 'totals', 'request'));

        return $pdf->download('invoices.pdf');
    }


    public function create()
    {
        $customers = Customer::all();
        $items = Item::all();
        return view('invoices.create', compact('customers', 'items'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'date_of_supply' => 'required|date',
            'time_of_supply' => 'required',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.value_of_goods' => 'required|numeric',
            'items.*.sale_tax_rate' => 'required|numeric',
            'items.*.amount_of_saleTax' => 'required|numeric',
            'items.*.extra_tax' => 'required|numeric',
            'items.*.further_tax' => 'required|numeric',
            'items.*.total' => 'required|numeric',
        ]);

        $prefix = now()->format('Ym');

        $count = Invoice::where('invoice_no', 'LIKE', "{$prefix}-%")->count();

        $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $invoiceNo = "{$prefix}-{$nextNumber}";


        $invoice = Invoice::create([
            'customer_id' => $request->customer_id,
            'invoice_no' => $invoiceNo,
            'date_of_supply' => $request->date_of_supply,
            'time_of_supply' => $request->time_of_supply,
        ]);

        foreach ($request->items as $itemData) {
            $invoice->items()->create($itemData);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }


    public function edit(Invoice $invoice)
    {
        $customers = Customer::all();
        $items = Item::all();
        $invoice->load('items'); // eager load
        return view('invoices.edit', compact('invoice', 'customers', 'items'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_id' => 'required',
            'date_of_supply' => 'required|date',
            'time_of_supply' => 'required',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.value_of_goods' => 'required|numeric',
            'items.*.sale_tax_rate' => 'required|numeric',
            'items.*.amount_of_saleTax' => 'required|numeric',
            'items.*.extra_tax' => 'required|numeric',
            'items.*.further_tax' => 'required|numeric',
            'items.*.total' => 'required|numeric',
        ]);


        $invoice->update([
            'customer_id' => $request->customer_id,
            'date_of_supply' => $request->date_of_supply,
            'time_of_supply' => $request->time_of_supply,
        ]);

        $invoice->items()->delete(); // remove all old items

        // Check and apply new stock
        foreach ($request->items as $i => $itemData) {
            $invoice->items()->create($itemData);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }



    public function destroy(Invoice $invoice)
    {
        $invoice->items()->delete();
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('customer', 'items.item');
        $isPdf = false;
        return view('invoices.print', compact('isPdf','invoice' ));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('customer', 'items.item');
        $isPdf = true;
        $pdf = PDF::loadView('invoices.print', compact('invoice','isPdf'));
        return $pdf->download('invoice_' . $invoice->invoice_no . '.pdf');
    }
}
