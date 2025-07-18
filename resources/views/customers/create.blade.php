@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Create Customer</span>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address">Address</label>
                            <input type="text" name="address" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="ntn_cnic">NTN / CNIC</label>
                            <input type="text" name="ntn_cnic" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Save Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
