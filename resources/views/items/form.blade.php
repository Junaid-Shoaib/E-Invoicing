<div class="mb-3">
    <label>H.S. CODE *</label>
    <input type="text" name="hs_code" class="form-control" value="{{ old('hs_code', $item->hs_code ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Product Name *</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label>UoM *</label>
    <input type="text" name="unit" class="form-control" value="{{ old('unit', $item->unit ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Description</label>
    <textarea name="description" class="form-control">{{ old('description', $item->description ?? '') }}</textarea>
</div>
{{-- <div class="mb-3">
    <label>Unit Price *</label>
    <input type="number" name="unit_price" step="0.01" class="form-control" value="{{ old('unit_price', $item->unit_price ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Quantity *</label>
    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Sales Tax Rate (%) *</label>
    <input type="number" name="st_rate" step="0.01" class="form-control" value="{{ old('st_rate', $item->st_rate ?? 18) }}" required>
</div> --}}
