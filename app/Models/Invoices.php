<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model {
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'store_id',
        'customer_name_snapshot',
        'customer_mobile_snapshot',
        'invoice_date',
        'subtotal',
        'overall_discount_amount',
        'tax_amount',
        'grand_total',
        'payment_method',
        'items', 
        'invoice_status',
        'notes',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function store() {
        return $this->belongsTo(Stores::class);
    }
}