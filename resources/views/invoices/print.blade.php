<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;   /*  <-- was missing */
            word-wrap: break-word;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            font-size: 10px;
            white-space: normal;  /* allow wrapping */
        }

        .no-border td {
            border: none;
            padding: 2px;
        }

        h2,
        h4 {
            margin: 5px 0;
        }

        .section-title {
            font-weight: bold;
        }

        .total-summary {
            text-align: left;
            margin-top: 10px;
            font-weight: bold;
        }

        .footer-section {
            margin-top: 50px;
        }

        .footer-section td {
            border: none !important;
        }

        .signature-box {
            height: 80px;
            border-top: 1px solid #000;
            text-align: center;
            vertical-align: bottom;
            padding-top: 20px;
        }

        .signature-box span{
            border-top: 1px solid #000;
            border-bottom: none;
            border-left: none;
            border-right: none;
            text-align: center;
            vertical-align: bottom;
        }

        .fbr-logo {
            width: 150px;
            display: block;
            margin-right: 0;
        }

        .fbr-logo {
            height: 100px;
            display: block;
        }


        @page{
            size: A4;
            margin: 20px;
        }
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .desc-cell { word-break: break-word; }
    </style>
</head>

<body>

    <table style="width: 100%; border: none; margin-bottom: 10px;">
    <tr>
        <!-- Left: Company Logo -->
        <td style="width: 0%; border: none; text-align: left;">
            <img src="{{ asset('images/logo.png') }}" alt="Company Logo" style="height: 80px;">
        </td>

        <!-- Center: Title -->
        <td style="width: 80%; border: none; text-align: left; vertical-align: middle;">
            <h2 style="margin: 0; font-size: 20px;">Sales Tax Invoice</h2>
        </td>

        <!-- Right: (Optional empty / QR / anything) -->
        <td style="width: 20%; border: none;"></td>
    </tr>
</table>
    <table class="no-border">
        <tr>
            <td><strong>Invoice No:</strong> {{ $invoice->invoice_no }}
            <br>
            <strong>Tax Period:</strong> {{ \Carbon\Carbon::parse($invoice->date_of_supply)->format('Ym') }}</td>
            <td style="text-align: right;">
                <strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice->date_of_supply)->format('d/m/Y') }}
                {{ \Carbon\Carbon::parse($invoice->time_of_supply)->format('h:i A') }}
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Supplier's Name & Address:</strong><br>
                Petrochemical & Lubricants Co (Pvt) Ltd<br>
                2nd Floor, Statelife Building No 3,<br>
                Dr Zia Uddin Ahmed Road, Karachi
            </td>
            <td style="width: 50%; vertical-align: top;">
                <strong>Buyer’s Name & Address:</strong><br>
                {{ $invoice->customer->name ?? '-' }}<br>
                {{ $invoice->customer->address ?? '-' }}
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width: 50%;"><strong>Sale Origination Province:</strong> Sindh </td>
            <td><strong>Destination Province:</strong> {{ $invoice->customer->province ?? '-' }}</td>
            
        </tr>
        <tr>
            <td style="width: 50%;"><strong>Telephone No:</strong> 021-35660293</td>
            <td><strong>Telephone No:</strong> {{ $invoice->customer->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>N.T.N No:</strong> 1000645-1</td>
            <td><strong>N.T.N./CNIC No:</strong> {{ $invoice->customer->ntn_cnic ?? '-' }}</td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th>H.S. Code</th>
                <th class="desc-cell">Description of Goods</th>
                <th class="desc-cell">Invoice Type</th>
                <th class="desc-cell">Sale Type</th>
                <th>UOM</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Value (Excl. ST)</th>
                <th>Rate</th>
                <th class="desc-cell">Sales Tax/FED</th>
                <th>ST WH</th>
                <th>Extra Tax</th>
                <th>Further Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_quantity = 0;
                $total_value_of_goods = 0;
                $total_amount_of_saleTax = 0;
                $total_sale_tax_withheld = 0;
                $total_extra_tax = 0;
                $total_further_tax = 0;
                $grand_total = 0;
            @endphp
            @foreach($invoice->items as $inv_item)
                @php
                    $total_quantity += $inv_item->quantity;
                    $total_value_of_goods += $inv_item->value_of_goods;
                    $total_amount_of_saleTax += $inv_item->amount_of_saleTax;
                    $total_sale_tax_withheld += $inv_item->sale_tax_withheld;
                    $total_extra_tax += $inv_item->extra_tax;
                    $total_further_tax += $inv_item->further_tax;
                    $grand_total += $inv_item->total;
                @endphp
                <tr>
                    <td>{{ $inv_item->item->hs_code }}</td>
                    <td class="desc-cell" >{!! $inv_item->item->name . '<br>' . $inv_item->item->description !!}</td>
                    <td class="desc-cell">{{ $invoice->invoice_type }}</td>
                    <td class="desc-cell">{{  $inv_item->sale_Type }}</td>
                    <td>{{ $inv_item->item->unit }}</td>
                    <td>{{ $inv_item->unit_price }}</td>
                    <td>{{ $inv_item->quantity }}</td>
                    <td>{{ number_format($inv_item->value_of_goods, 2) }}</td>
                    <td>{{ number_format($inv_item->sale_tax_rate, 0) }}%</td>
                    <td class="desc-cell">{{ number_format($inv_item->amount_of_saleTax, 2) }}</td>
                    <td>{{ number_format($inv_item->sale_tax_withheld, 2) }}</td>
                    <td>{{ number_format($inv_item->extra_tax, 2) }}</td>
                    <td>{{ number_format($inv_item->further_tax, 2) }}</td>
                    <td>{{ number_format($inv_item->total, 2) }}</td>
                </tr>
            @endforeach

            {{-- Empty rows --}}
            @for($i = $invoice->items->count(); $i < 5; $i++)
                <tr>
                    @for($j = 0; $j < 14; $j++)
                        <td>&nbsp;</td>
                    @endfor
                </tr>
            @endfor
            <tr style="font-weight: bold;">
                <td colspan="6" style="text-align: right;">Total:</td>
                <td>{{ $total_quantity }}</td>
                <td>{{ number_format($total_value_of_goods, 2) }}</td>
                <td></td>
                <td>{{ number_format($total_amount_of_saleTax, 2) }}</td>
                <td>{{ number_format($total_sale_tax_withheld, 2) }}</td>
                <td>{{ number_format($total_extra_tax, 2) }}</td>
                <td>{{ number_format($total_further_tax, 2) }}</td>
                <td>{{ number_format($grand_total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-summary" style="text-align:right">
        Amount in Words:
        <em>{{ ucwords(\NumberFormatter::create('en', NumberFormatter::SPELLOUT)->format($invoice->items->sum('total'))) }}
            only</em>
    </div>
    <div class="footer-section">
        <table style="width: 100%; margin-top: 40px;">
            <tr>
                <td style="text-align: right;">
                    @if($invoice->fbr_invoice_no != null)
                        @if($isPdf)
                            @php
                                $qr = base64_encode(QrCode::format('png')->size(100)->generate($invoice->fbr_invoice_no));
                            @endphp
                            @if($qr)
                                <div style="display: inline-block; text-align: center;">
                                    <img src="data:image/png;base64,{{ $qr }}" width="100" height="100">
                                    <div style="font-size: 12px; margin-top: 5px;">
                                        {{ $invoice->fbr_invoice_no }}
                                    </div>
                                </div>
                            @endif
                        @else
                            <div style="display: inline-block; text-align: center;">
                                {!! QrCode::size(100)->generate($invoice->fbr_invoice_no) !!}
                                <div style="font-size: 12px; margin-top: 5px;">
                                    {{ $invoice->fbr_invoice_no }}
                                </div>
                            </div>
                        @endif
                    @endif
                </td>
                <td style="text-align: left; vertical-align: middle;">
                    <img src="{{ asset('/images/fbr_resized.png') }}" class="fbr-logo" alt="FBR e-invoicing Logo" style="height: 100px;">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
