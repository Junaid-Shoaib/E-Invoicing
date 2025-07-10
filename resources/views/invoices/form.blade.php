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
                <th style="width: 120px;">Extra Tax</th>
                <th style="width: 130px;">Further Tax</th>
                <th style="width: 120px;">Total</th>
                <th style="width: 50px;">
                    <button type="button" class="btn btn-sm btn-success" id="add-row"><i class="fas fa-plus"></i></button>
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $invoiceItems = old('items', isset($invoice) ? $invoice->items : []);
            @endphp

            @foreach($invoiceItems as $i => $itemRow)
            <tr>
                <td>
                    <select name="items[{{ $i }}][item_id]" class="form-control item-select" required>
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            @php
                                $selectedId = is_object($itemRow) ? $itemRow->item_id : ($itemRow['item_id'] ?? null);
                                $isSelected = $selectedId == $item->id ? 'selected' : '';
                                $itemName = $item->name . ' (Stock: ' . $item->quantity . ')';
                            @endphp
                            <option value="{{ $item->id }}"
                                    data-price="{{ $item->unit_price }}"
                                    data-st="{{ $item->st_rate }}"
                                    data-stock="{{ $item->quantity }}"
                                    {{ $isSelected }}>
                                {{ $itemName }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control unit-price" value="{{ is_object($itemRow) ? $itemRow->unit_price : $itemRow['unit_price'] }}" required></td>
                <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control quantity" value="{{ is_object($itemRow) ? $itemRow->quantity : ($itemRow['quantity'] ?? 1) }}" min="1" required></td>
                <td><input type="number" step="0.01" name="items[{{ $i }}][value_of_goods]" class="form-control value" value="{{ is_object($itemRow) ? $itemRow->value_of_goods : $itemRow['value_of_goods'] }}" readonly></td>
                <td><input type="number" step="0.01" name="items[{{ $i }}][sale_tax_rate]" class="form-control st-rate" value="{{ is_object($itemRow) ? $itemRow->sale_tax_rate : $itemRow['sale_tax_rate'] }}" readonly></td>
                <td><input type="number" step="0.01" name="items[{{ $i }}][amount_of_saleTax]" class="form-control st-amount" value="{{ is_object($itemRow) ? $itemRow->amount_of_saleTax : $itemRow['amount_of_saleTax'] }}" readonly></td>
                <td><input type="number" step="0.01" name="items[{{ $i }}][extra_tax]" class="form-control et" value="{{ is_object($itemRow) ? $itemRow->extra_tax : $itemRow['extra_tax'] }}" required></td>
                <td><input type="number" step="0.01" name="items[{{ $i }}][further_tax]" class="form-control ft" value="{{ is_object($itemRow) ? $itemRow->further_tax : $itemRow['further_tax'] }}" required></td>
                <td><input type="number" step="0.01" name="items[{{ $i }}][total]" class="form-control total" value="{{ is_object($itemRow) ? $itemRow->total : $itemRow['total'] }}" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
let rowIndex = {{ count($invoiceItems) }};
const allItems = @json($items);

function calculateRow(row) {
    const price = parseFloat(row.find('.unit-price').val()) || 0;
    const qty = parseInt(row.find('.quantity').val()) || 1;
    const rate = parseFloat(row.find('.st-rate').val()) || 0;
    const et = parseFloat(row.find('.et').val()) || 0;
    const ft = parseFloat(row.find('.ft').val()) || 0;

    const value = price * qty;
    const tax = value * rate / 100;
    const total = value + tax + et + ft;

    row.find('.value').val(value.toFixed(2));
    row.find('.st-amount').val(tax.toFixed(2));
    row.find('.total').val(total.toFixed(2));
}

function calculateUsedStock(itemId) {
    let used = 0;
    $('.item-select').each(function () {
        if ($(this).val() == itemId) {
            const qty = parseInt($(this).closest('tr').find('.quantity').val()) || 0;
            used += qty;
        }
    });
    return used;
}

function validateQuantity(row) {
    const qtyInput = row.find('.quantity');
    const itemId = row.find('.item-select').val();
    const originalStock = parseInt(row.find('.item-select option:selected').data('stock')) || 0;
    const currentQty = parseInt(qtyInput.val()) || 0;
    const usedStock = calculateUsedStock(itemId);

    if (currentQty < 1) {
        alert('At least 1 quantity is required.');
        qtyInput.val(1);
    } else if (usedStock > originalStock) {
        const remaining = originalStock - (usedStock - currentQty);
        alert(`You cannot exceed available stock. Remaining for this item: ${remaining < 0 ? 0 : remaining}`);
        qtyInput.val(remaining < 1 ? 1 : remaining);
    }

    calculateRow(row);
}

// Add row
$('#add-row').on('click', function () {
    let options = `<option value="">Select</option>`;
    allItems.forEach(item => {
        options += `<option value="${item.id}" data-price="${item.unit_price}" data-st="${item.st_rate}" data-stock="${item.quantity}">
            ${item.name} (Stock: ${item.quantity})
        </option>`;
    });

    let row = `
    <tr>
        <td><select name="items[${rowIndex}][item_id]" class="form-control item-select" required>${options}</select></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][unit_price]" class="form-control unit-price" required></td>
        <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control quantity" value="1" min="1" required></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][value_of_goods]" class="form-control value" readonly></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][sale_tax_rate]" class="form-control st-rate" readonly></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][amount_of_saleTax]" class="form-control st-amount" readonly></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][extra_tax]" class="form-control et" value="0"></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][further_tax]" class="form-control ft" value="0"></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][total]" class="form-control total" readonly></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
    </tr>`;
    
    $('#items-table tbody').append(row);
    rowIndex++;
});

// Item selection
$(document).on('change', '.item-select', function () {
    const selected = $(this).find('option:selected');
    const row = $(this).closest('tr');

    row.find('.unit-price').val(selected.data('price') || 0);
    row.find('.st-rate').val(selected.data('st') || 0);

    validateQuantity(row);
});

// Quantity blur validation
$(document).on('blur', '.quantity', function () {
    const row = $(this).closest('tr');
    validateQuantity(row);
});

// Price or tax change
$(document).on('input', '.unit-price, .ft', function () {
    const row = $(this).closest('tr');
    calculateRow(row);
});

$(document).on('input', '.unit-price, .et', function () {
    const row = $(this).closest('tr');
    calculateRow(row);
});

// Remove row
$(document).on('click', '.remove-row', function () {
    $(this).closest('tr').remove();
});
</script>
@endpush
