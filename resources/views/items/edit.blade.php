@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit me-1"></i> Edit Item</h5>
                    <a href="{{ route('items.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('items.update', $item->id) }}">
                        @csrf
                        @method('PUT')
                        @include('items.form', ['item' => $item])
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
