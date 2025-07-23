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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('customers.store') }}" method="POST" onsubmit="disableSubmitButton(this)">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Name *</label>
                            <input type="text" name="name"   class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="address">Address</label>
                            <input type="text" name="address" value="{{ old('address') }}" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="ntn_cnic">NTN / CNIC</label>
                            <input type="number" name="ntn_cnic" value="{{ old('ntn_cnic') }}" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="ntn_cnic">Destination Province</label>
                            <input type="text" name="province" value="{{ old('province') }}" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary" id="submit-btn">Save Customer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
