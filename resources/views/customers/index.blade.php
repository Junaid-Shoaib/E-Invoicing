@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-1"></i> Customers
                    </h5>
                    <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Create
                    </a>
                </div>

                <div class="card-body">
                    {{-- ✅ FIX: Table wrapped in responsive div --}}
                    <div class="table-responsive">
                        <table class="table table-bordered" id="customers-table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>NTN/CNIC</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div> {{-- end card-body --}}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#customers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('customers.index') }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'address', name: 'address' },
            { data: 'phone', name: 'phone' },
            { data: 'ntn_cnic', name: 'ntn_cnic' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        // ✅ Optional Bootstrap compatibility
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search customers...",
        }
    });
});
</script>
@endpush
@endsection
