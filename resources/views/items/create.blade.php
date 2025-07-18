@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-box-open me-1"></i> Create Item</h5>
                    <a href="{{ route('items.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('items.store') }}">
                        @csrf
                        @include('items.form')
                        <button type="submit" class="btn btn-primary">Save Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
