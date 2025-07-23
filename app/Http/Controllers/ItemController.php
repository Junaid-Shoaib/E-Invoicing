<?php
namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Item::latest()->get();
            return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn = '<div class="d-flex gap-1">'; // Bootstrap 5 flex utility for spacing

                // Edit Button
                $btn .= '<a href="' . route('items.edit', $row->id) . '" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>';

                // Check invoice usage
            $invoiceCount = InvoiceItem::where('item_id', $row->id)->count();

            if ($invoiceCount === 0) {
                $btn .= '<form action="' . route('items.destroy', $row->id) . '" method="POST" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field("DELETE") . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>';
            } else {
                $btn .= '<span class="text-muted" title="Item in use">
                            <i class="fas fa-lock"></i> In Use
                        </span>';
            }

            $btn .= '</div>'; // Close flex container

            return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);

        }

        return view('items.index');
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'hs_code' => 'required',
            'name' => 'required',
            'unit' => 'required',
            'description' => 'nullable',
            // 'unit_price' => 'required|numeric',
            // 'quantity' => 'required|integer',
            // 'st_rate' => 'required|numeric',
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'hs_code' => 'required',
            'name' => 'required',
            'unit' => 'required',
            'description' => 'nullable',
            // 'unit_price' => 'required|numeric',
            // 'quantity' => 'required|integer',
            // 'st_rate' => 'required|numeric',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
