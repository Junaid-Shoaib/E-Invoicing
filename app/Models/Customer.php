<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'ntn_cnic',
        'province'
    ];

    // Relationship: Customer has many Invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

