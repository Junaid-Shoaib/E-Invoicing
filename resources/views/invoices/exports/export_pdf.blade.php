<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: center; }
        .filters td { border: none; text-align: left; padding: 2px; }
        .totals { font-weight: bold; }
    </style>
</head>
<body>
    <h3>Invoice Report</h3>

    <table class="filters">
        <tr>
            <td><strong>Customer:</strong></td>
            <td>{{ $customer ?? 'All' }}</td>
            <td><strong>From Date:</strong></td>
            <td>{{ $request->from_date ?? '-' }}</td>
            <td><strong>To Date:</strong></td>
            <td>{{ $request->to_date ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Date of Supply</th>
                <th>Time</th>
                <th>Total Before Tax</th>
                <th>Sale Tax</th>
                <th>Extra Tax</th>
                <th>Further Tax</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_no }}</td>
                    <td>{{ $invoice->customer->name ?? '' }}</td>
                    <td>{{ $invoice->date_of_supply }}</td>
                    <td>{{ $invoice->time_of_supply }}</td>
                    <td>{{ number_format($invoice->items->sum('value_of_goods'), 2) }}</td>
                    <td>{{ number_format($invoice->items->sum('amount_of_saleTax'), 2) }}</td>
                    <td>{{ number_format($invoice->items->sum('extra_tax'), 2) }}</td>
                    <td>{{ number_format($invoice->items->sum('further_tax'), 2) }}</td>
                    <td>{{ number_format($invoice->items->sum('total'), 2) }}</td>
                </tr>
            @endforeach
            <tr class="totals">
                <td colspan="4">TOTAL</td>
                <td>{{ number_format($totals['totalBeforeTax'], 2) }}</td>
                <td>{{ number_format($totals['saleTax'], 2) }}</td>
                <td>{{ number_format($totals['extraTax'], 2) }}</td>
                <td>{{ number_format($totals['furtherTax'], 2) }}</td>
                <td>{{ number_format($totals['grandTotal'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
