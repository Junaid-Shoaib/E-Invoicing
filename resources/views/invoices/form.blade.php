<div class="mb-3">
    <label>Customer *</label>
    <select name="customer_id" class="form-control" required>
        <option value="">-- Select Customer --</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" {{ old('customer_id', $invoice->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
                {{ $customer->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Date of Supply *</label>
    <input type="date" name="date_of_supply" class="form-control" 
        value="{{ old('date_of_supply', $invoice->date_of_supply ?? now()->format('Y-m-d')) }}" required>
</div>

<div class="mb-3">
    <label>Time of Supply *</label>
    <input type="time" name="time_of_supply" class="form-control" 
        value="{{ old('time_of_supply', $invoice->time_of_supply ?? now()->format('H:i')) }}" required>
</div>
<h5 class="mt-4 mb-2">Invoice Items</h5>
<div id="items-table-wrapper">
    <table class="table table-bordered" id="items-table">
        <thead class="table-light">
            <tr>
                <th style="min-width: 160px;">Item</th>
                <th style="width: 130px;">Unit Price</th>
                <th style="width: 100px;">Qty</th>
                <th style="width: 130px;">Value</th>
                <th style="width: 100px;">ST %</th>
                <th style="width: 120px;">ST Amount</th>
                <th style="width: 120px;">ST withheld as WH</th>
                <th style="width: 120px;">Extra Tax</th>
                <th style="width: 130px;">Further Tax</th>
                <th style="width: 120px;">Total</th>
                <th style="width: 50px;">
                    <button type="button" class="btn btn-sm btn-success" id="add-row">
                        <i class="fas fa-plus"></i>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody>
            @if(old('items') || isset($invoice))
                @php
                    $itemRows = old('items', isset($invoice) ? $invoice->items->toArray() : []);
                @endphp
                @foreach($itemRows as $i => $row)
                <tr>
                    <td>
                        <select name="items[{{ $i }}][item_id]" class="form-control item-select" required>
                            <option value="">Select Item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ ($row['item_id'] ?? $row['item']['id'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control unit-price" value="{{ $row['unit_price'] ?? '' }}" required></td>
                    <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control quantity" value="{{ $row['quantity'] ?? 1 }}" min="1" required></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][value_of_goods]" class="form-control value" value="{{ $row['value_of_goods'] ?? '' }}" readonly></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][sale_tax_rate]" class="form-control st-rate" value="{{ $row['sale_tax_rate'] ?? 18 }}"></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][amount_of_saleTax]" class="form-control st-amount" value="{{ $row['amount_of_saleTax'] ?? '' }}" readonly></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][extra_tax]" class="form-control et" value="{{ $row['extra_tax'] ?? 0 }}"></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][sale_tax_withheld]" class="form-control stw" value="{{ $row['sale_tax_withheld'] ?? 0 }}"></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][further_tax]" class="form-control ft" value="{{ $row['further_tax'] ?? 0 }}"></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][total]" class="form-control total" value="{{ $row['total'] ?? '' }}" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                </tr>
                @endforeach
            @else
            <tr>
                <td>
                    <select name="items[0][item_id]" class="form-control item-select" required>
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" name="items[0][unit_price]" class="form-control unit-price" required></td>
                <td><input type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="1" required></td>
                <td><input type="number" step="0.01" name="items[0][value_of_goods]" class="form-control value" readonly></td>
                <td><input type="number" step="0.01" name="items[0][sale_tax_rate]" class="form-control st-rate" value="18"></td>
                <td><input type="number" step="0.01" name="items[0][amount_of_saleTax]" class="form-control st-amount" readonly></td>
                <td><input type="number" step="0.01" name="items[0][extra_tax]" class="form-control et" value="0"></td>
                <td><input type="number" step="0.01" name="items[0][sale_tax_withheld]" class="form-control stw" value="0"></td>
                <td><input type="number" step="0.01" name="items[0][further_tax]" class="form-control ft" value="0"></td>
                <td><input type="number" step="0.01" name="items[0][total]" class="form-control total" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

@push('scripts')
<script>
let rowIndex = {{ isset($invoice) ? ($invoice->items->count() ?? 0) : 1 }};
const allItems = @json($items);

function calculateRow(row) {
    const price = parseFloat(row.find('.unit-price').val()) || 0;
    const qty = parseInt(row.find('.quantity').val()) || 1;
    const rate = parseFloat(row.find('.st-rate').val()) || 18;
    const et = parseFloat(row.find('.et').val()) || 0;
    const stw = parseFloat(row.find('.stw').val()) || 0;
    const ft = parseFloat(row.find('.ft').val()) || 0;

    const value = price * qty;
    const tax = value * rate / 100;
    const total = value + tax + et + stw + ft;

    row.find('.value').val(value.toFixed(2));
    row.find('.st-amount').val(tax.toFixed(2));
    row.find('.total').val(total.toFixed(2));
}

$(document).on('input', '.unit-price, .quantity, .st-rate, .et, .stw, .ft', function () {
    const row = $(this).closest('tr');
    calculateRow(row);
});

$('#add-row').on('click', function () {
    const itemOptions = allItems.map(item =>
        `<option value="${item.id}">${item.name}</option>`
    ).join('');

    const newRow = `
        <tr>
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-control item-select" required>
                    <option value="">Select Item</option>
                    ${itemOptions}
                </select>
            </td>
            <td><input type="number" step="0.01" name="items[${rowIndex}][unit_price]" class="form-control unit-price" required></td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control quantity" value="1" min="1" required></td>
            <td><input type="number" step="0.01" name="items[${rowIndex}][value_of_goods]" class="form-control value" readonly></td>
            <td><input type="number" step="0.01" name="items[${rowIndex}][sale_tax_rate]" class="form-control st-rate" value="18"></td>
            <td><input type="number" step="0.01" name="items[${rowIndex}][amount_of_saleTax]" class="form-control st-amount" readonly></td>
            <td><input type="number" step="0.01" name="items[${rowIndex}][extra_tax]" class="form-control et" value="0"></td>
            <td><input type="number" step="0.01" name="items[${rowIndex}][sale_tax_withheld]" class="form-control stw" value="0"></td>
            <td><input type="number" step="0.01" name="items[${rowIndex}][further_tax]" class="form-control ft" value="0"></td>
            <td><input type="number" step="0.01" name="items[${rowIndex}][total]" class="form-control total" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
        </tr>`;

    $('#items-table tbody').append(newRow);
    rowIndex++;
});

$(document).on('click', '.remove-row', function () {
    $(this).closest('tr').remove();
});
</script>
@endpush