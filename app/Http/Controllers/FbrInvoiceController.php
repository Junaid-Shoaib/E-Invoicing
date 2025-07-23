<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FbrInvoiceController extends Controller
{

    public function posting(Invoice $invoice){
        

        $invoiceData = [
            'invoiceType' => $invoice->invoice_type,
            'invoiceDate' => Carbon::parse($invoice->date_of_supply)->format('Y-m-d'),
            'sellerNTNCNIC' => '1000645-1',
            'sellerBusinessName' => 'Petrochemical & Lubricants Co(Pvt) Ltd',
            'sellerProvince' => 'Sindh',
            'sellerAddress' => '2nd Floor, Statelife Building No 3, Dr Zia Uddin Ahmed Road, Karachi',

            'buyerNTNCNIC' => $invoice->customer->ntn_cnic,
            'buyerBusinessName' => $invoice->customer->name,
            'buyerProvince' => $invoice->customer->province,
            'buyerAddress' => $invoice->customer->address,
            'buyerRegistrationType' => 'Registered',
            'invoiceRefNo' => $invoice->invoice_no,
            'scenarioId' => 'SN001',
            'items' => $invoice->items->map(function ($item) {
                    return [
                        'hsCode' => $item->item->hs_code,
                        'productDescription' => $item->item->name ?? $item->item->description ,
                        'rate' => $item->sale_tax_rate . '%',
                        'uoM' => $item->item->unit ?? 'Unit',
                        'quantity' => $item->quantity,
                        'totalValues' => $item->total, 
                        'valueSalesExcludingST' => $item->value_of_goods,
                        'fixedNotifiedValueOrRetailPrice' => 0,
                        'salesTaxApplicable' => $item->amount_of_saleTax,
                        'salesTaxWithheldAtSource' => $item->sale_tax_withheld ?? 0,
                        'extraTax' => $item->extra_tax ?? '',
                        'furtherTax' => $item->further_tax ?? '',
                        'sroScheduleNo' => '',
                        'fedPayable' => 0,
                        'discount' => 0,
                        'saleType' => $item->sale_type ?? 'Goods at standard rate (default)',
                        'sroItemSerialNo' => ''
                    ];
                })->toArray()
        ];

        $response = Http::withToken('769de299-8a51-31a3-a325-6ddfa2b6b763')->post('https://gw.fbr.gov.pk/di_data/v1/di/postinvoicedata_sb', $invoiceData);
        dd($response);
        if ($response->successful()) {    
        // Handle successful response
            return response()->json([
                'message' => 'Response from API',
                'data' => $response->json()
            ]);
        } else {
            // Handle error response
            return response()->json([
                'error' => [
                    'status' => $response->status(),
                    'message' => $response->body()
                ]
            ], $response->status());
        }
    }
}
