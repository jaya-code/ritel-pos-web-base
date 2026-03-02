<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'amount',
        'status',
        'bank_name',
        'account_name',
        'account_number',
        'admin_note',
        'receipt_path',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
