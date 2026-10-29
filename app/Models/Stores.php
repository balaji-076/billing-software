<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stores extends Model
{
    protected $table = 'stores';

    protected $fillable = [
        'store_code',
        'store_name',
        'gstin',
        'phone_numbers',
        'email',
        'address',
        'logo_url',
    ];

    protected $casts = [
        'phone_numbers' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'store_id');
    }
}