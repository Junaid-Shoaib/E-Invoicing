<?php


namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Exception;

class FbrInvoiceController extends Controller
{

    public function posting(Invoice $invoice, Request $request){
        
        $apiUrl = "https://gw.fbr.gov.pk/di_data/v1/di/postinvoicedata_sb"; 
        $apiKey = env('apiKey'); // Store API key in .env file

        $payload = [
            'invoiceType' => $invoice->invoice_type,
            'invoiceDate' => Carbon::parse($invoice->date_of_supply)->format('Y-m-d'),
            'sellerNTNCNIC' => env('sellerNTNCNIC'),
            'sellerBusinessName' => env('sellerBusinessName'),
            'sellerProvince' =>  env('sellerProvince'),
            'sellerAddress' =>  env('sellerAddress'),

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
                        'uoM' => "Numbers, pieces, units",
                        'quantity' => (int)$item->quantity,
                        'totalValues' => (int)$item->total, 
                        'valueSalesExcludingST' => (int)$item->value_of_goods,
                        'fixedNotifiedValueOrRetailPrice' => 0,
                        'salesTaxApplicable' => (int)$item->amount_of_saleTax,
                        'salesTaxWithheldAtSource' => (int)$item->sale_tax_withheld ?? 0,
                        'extraTax' => (int)$item->extra_tax ?? '',
                        'furtherTax' => (int)$item->further_tax ?? '',
                        'sroScheduleNo' => '',
                        'fedPayable' => 0,
                        'discount' => 0,
                        'saleType' => $item->sale_type ?? 'Goods at standard rate (default)',
                        'sroItemSerialNo' => ''
                    ];
                })->toArray()
        ];
        // dd($payload);
        // $payload = [ 
        //     "invoiceType" => "Sale Invoice", 
        //     "invoiceDate" => "2025-04-21", 
        //     "sellerNTNCNIC" => "1000645", 
        //     "sellerBusinessName" => "PetroChemical & Lubricants Co (Pvt) Ltd", 
        //     "sellerProvince" => "Sindh", 
        //     "sellerAddress" => "Karachi", 
        //     "buyerNTNCNIC" => "1000000000000", 
        //     "buyerBusinessName" => "FERTILIZER MANUFAC IRS NEW", 
        //     "buyerProvince" => "Sindh", 
        //     "buyerAddress" => "Karachi", 
        //     "buyerRegistrationType" => "Unregistered", 
        //     "invoiceRefNo" => "",  
        //     "scenarioId" => "SN002",
        //     "items" => [ 
        //         [ 
        //             "hsCode" => "0101.2100", 
        //             "productDescription" => "product Description", 
        //             "rate" => "18%", 
        //             "uoM" => "Numbers, pieces, units", 
        //             "quantity" => 1, 
        //             "totalValues" => 0, 
        //             "valueSalesExcludingST" => 1000, 
        //             "fixedNotifiedValueOrRetailPrice" => 0, 
        //             "salesTaxApplicable" => 180, 
        //             "salesTaxWithheldAtSource" => 0, 
        //             "extraTax" => null, 
        //             "furtherTax" => 120, 
        //             "sroScheduleNo" => "", 
        //             "fedPayable" => 0, 
        //             "discount" => 0, 
        //             "saleType" => "Goods at standard rate (default)", 
        //             "sroItemSerialNo" => "" 
        //         ] 
        //     ] 
        // ];

        $client = HttpClient::create();
        try {
        $response = $client->request('POST', $apiUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$apiKey}",
            ],
            'json' => $payload, 
        ]);
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getContent(false); 
        $responseJson = json_decode($responseBody, true);
        if($statusCode == 200 && $responseJson != null) {
            if(isset($responseJson['validationResponse'])){
                if($responseJson['validationResponse']['statusCode'] == "00"){
                    $fbrInvNo = $responseJson['validationResponse']['invoiceStatuses'][0]['invoiceNo']; 
                    $invoice->fbr_invoice_no = $fbrInvNo;
                    $invoice->response = serialize($responseJson);
                    $invoice->posting = 1;
                    $invoice->save();
                    return back()->with('success', "Invoice Posted Successfully");
                }elseif(!isset($responseJson['validationResponse']['invoiceStatuses']) && $responseJson['validationResponse']['statusCode'] == "01"){
                    return back()->with('error',$responseJson['validationResponse']['error']);
                }else{
			foreach($responseJson['validationResponse']['invoiceStatuses'] as $key => $validateResp){
				if($validateResp['statusCode'] == "001"){
                			return back()->with('error',$responseJson['validationResponse']['invoiceStatuses'][$key]['error']);	
				}
			}
                }
            }else{
                return back()->with('error','Validation Response Failed!');
            }
        }elseif($statusCode == 401){
            return back()->with('error',$responseJson['validationResponse']['error']);
        }else{
            return back()->with('error','Something Went Wrong');
        }
            return $responseJson;
        } catch (HttpException $e) {
            return back()->with('error',$e);
        }

    }
}

