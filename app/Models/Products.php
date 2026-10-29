<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model {
    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_name',
        'sku',
        'category',
        'description',
        'original_price',
        'discount_price',
        'unit',
        'image_url',
        'cloudinary_public_id',
        'informational_qty',
        'status',
    ];

    public function store() {
        return $this->belongsTo(Stores::class);
    }
}