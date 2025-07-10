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
            float: center;
        }
    </style>
</head>

<body>

    <h2>Sales Tax Invoice</h2>
    <table class="no-border">
        <tr>
            <td><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</td>
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
                <th>Description of Goods</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Value of Sales Excl. Sales Tax</th>
                <th>Rate</th>
                <th>Sales Tax/FED in ST</th>
                <th>Extra Tax</th>
                <th>Further Tax</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->item->hs_code }}</td>
                    <td>{{ $item->item->name }}</td>
                    <td>{{ $item->item->unit }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->value_of_goods, 2) }}</td>
                    <td>{{ number_format($item->sale_tax_rate, 2) }}%</td>
                    <td>{{ number_format($item->amount_of_saleTax, 2) }}</td>
                    <td>{{ number_format($item->extra_tax, 2) }}</td>
                    <td>{{ number_format($item->further_tax, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach

            {{-- Empty rows --}}
            @for($i = $invoice->items->count(); $i < 5; $i++)
                <tr>
                    @for($j = 0; $j < 10; $j++)
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
                <td class="signature-box"><span>Signtaure & Stamp</span></td>
                <td class="signature-box">
                    <img src="{{ public_path('images\fbr_resized.png') }}" class="fbr-logo" alt="FBR e-invoicing Logo">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
