@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-1"></i> Invoices</h5>
                    <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Create
                    </a>
                </div>
                
                <div class="card-body">
                    <form id="filter-form" class="row mb-3">
                    <div class="col-md-4">
                        <select name="customer_id" class="form-control">
                            <option value="">-- All Customers --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="start_date" class="form-control" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="end_date" class="form-control" placeholder="To Date">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                </form>
                <div class="mb-3">
                    <a href="#" class="btn btn-danger" id="pdf-export"><i class="fas fa-file-pdf"></i> Generate PDF</a>
                    <a href="#" class="btn btn-success" id="excel-export"><i class="fas fa-file-excel"></i> Generate Excel</a>
                </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="invoices-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice No</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>FBR Invoice No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
    let table = $('#invoices-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('invoices.index') }}',
            data: function (d) {
                d.customer_id = $('select[name=customer_id]').val();
                d.start_date = $('input[name=start_date]').val();
                d.end_date = $('input[name=end_date]').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'customer', name: 'customer.name' },
            { data: 'date_of_supply', name: 'date_of_supply' },
            { data: 'time_of_supply', name: 'time_of_supply' },
            { data: 'fbr_invoice_no', name: 'fbr_invoice_no' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

    $('#filter-form').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });
    $('#excel-export').on('click', function(e) {
        e.preventDefault();
        const query = $('#filter-form').serialize();
        window.location.href = "{{ route('invoices.export.excel') }}?" + query;
    });

    $('#pdf-export').on('click', function(e) {
        e.preventDefault();
        const query = $('#filter-form').serialize();
        window.location.href = "{{ route('invoices.exportPdf') }}?" + query;
    });

});

</script>
@endpush
@endsection
