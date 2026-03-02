<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStore;

class Product extends Model
{
    use HasFactory, HasStore;

    protected $guarded = ['id'];

    protected $casts = [
        'cost_price' => 'integer',
        'selling_price' => 'integer',
        'member_price' => 'integer',
        'stock' => 'integer',
        'stock_min' => 'integer',
        'isi' => 'integer',
    ];

    public function getPrice($isMember = false)
    {
        if ($isMember && $this->member_price > 0) {
            return $this->member_price;
        }
        return $this->selling_price;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }
}
