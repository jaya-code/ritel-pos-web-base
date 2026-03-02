<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'unit_name',
        'quantity',
        'barcode',
        'price',
        'member_price',
        'is_default',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'integer',
        'member_price' => 'integer',
        'is_default' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
