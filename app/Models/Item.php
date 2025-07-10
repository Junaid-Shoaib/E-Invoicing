<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'hs_code',
        'name',
        'unit',
        'description',
        'unit_price',
        'quantity',
        'st_rate',
    ];

    // Relationship: Item can be in many invoice items
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
