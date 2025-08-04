<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'invoice_no',
        'date_of_supply',
        'time_of_supply',
	'fbr_invoice_no',
	'response',
    ];

    // Relationship: Invoice belongs to Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship: Invoice has many Items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($invoice) {
            // 1. Restore stock for each invoice item
            foreach ($invoice->items as $itemRow) {
                $item = $itemRow->item;
                $item->quantity += $itemRow->quantity;
                $item->save();
            }

            // 2. Delete related invoice items
            $invoice->items()->delete();
        });
    }
}
