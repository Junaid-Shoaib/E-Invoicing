@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit me-1"></i> Edit Invoice</h5>
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('invoices.update', $invoice->id) }}">
                        @csrf
                        @method('PUT')
                        @include('invoices.form', ['invoice' => $invoice])
                        <button type="submit" class="btn btn-primary">Update Invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
