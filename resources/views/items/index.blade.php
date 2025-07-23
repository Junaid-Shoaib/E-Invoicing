@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-boxes me-1"></i> Items</h5>
                    <a href="{{ route('items.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Create
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="items-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>H.S. Code</th>
                                    <th>Product Name</th>
                                    <th>UoM</th>
                                    {{-- <th>Price</th>
                                    <th>Quantity</th>
                                    <th>ST Rate</th> --}}
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
        $('#items-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('items.index') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'hs_code', name: 'hs_code' },
                { data: 'name', name: 'name' },
                { data: 'unit', name: 'unit' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });
</script>
@endpush
@endsection
