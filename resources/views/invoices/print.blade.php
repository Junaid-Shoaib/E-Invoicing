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
        }

       
        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 11px;
            word-wrap: break-word;
       
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
    </style>
</head>

<body>

    <h2>Sales Tax Invoice</h2>
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
            <td style="width: 50%;">
                <strong>Supplier's Name & Address:</strong><br>
                Petrochemical & Lubricants Co(Pvt) Ltd<br>
                2nd Floor, Statelife Building No 3,<br>
                Dr Zia Uddin Ahmed Road, Karachi
            </td>
            <td style="width: 50%;">
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
                <th style="width: 6%;">H.S. Code</th>
                <th style="width: 15%;">Description of Goods</th>
                <th style="width: 9%;">Invoice Type</th>
                <th style="width: 9%;">Sale Type</th>
                <th style="width: 6%;">Rate</th>
                <th style="width: 5%;">UOM</th>
                <th style="width: 6%;">Qty</th>
                <th style="width: 10%;">Value (Excl. ST)</th>
                <th style="width: 10%;">Sales Tax/FED</th>
                <th style="width: 8%;">ST WH</th>
                <th style="width: 6%;">Extra Tax</th>
                <th style="width: 6%;">Further Tax</th>
                <th style="width: 10%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $inv_item)
                <tr>
                    <td>{{ $inv_item->item->hs_code }}</td>
                    <td>{{ $inv_item->item->name }}</td>
                    <td>{{ $invoice->invoice_type }}</td>
                    <td>{{  $inv_item->sale_Type }}</td>
                    <td>{{ number_format($inv_item->sale_tax_rate, 2) }}%</td>
                    <td>{{ $inv_item->item->unit }}</td>
                    <td>{{ $inv_item->quantity }}</td>
                    <td>{{ number_format($inv_item->value_of_goods, 2) }}</td>
                    <td>{{ number_format($inv_item->amount_of_saleTax, 2) }}</td>
                    <td>{{ number_format($inv_item->sale_tax_withheld, 2) }}</td>
                    <td>{{ number_format($inv_item->extra_tax, 2) }}</td>
                    <td>{{ number_format($inv_item->further_tax, 2) }}</td>
                    <td>{{ number_format($inv_item->total, 2) }}</td>
                </tr>
            @endforeach

            {{-- Empty rows --}}
            @for($i = $invoice->items->count(); $i < 5; $i++)
                <tr>
                    @for($j = 0; $j < 13; $j++)
                        <td>&nbsp;</td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="total-summary">
        Total Invoice Amount: {{ number_format($invoice->items->sum('total'), 2) }}
    </div>

    <div class="total-summary">
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
