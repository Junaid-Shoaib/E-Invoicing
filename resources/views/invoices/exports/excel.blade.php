<table>
    <thead>
        <tr>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_no }}</td>
            <td>{{ $invoice->customer->name }}</td>
            <td>{{ $invoice->date_of_supply }}</td>
            <td>{{ $invoice->time_of_supply }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
