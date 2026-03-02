<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStore;

class Penjualan extends Model
{
    use HasStore;

    protected $table = 'penjualan';
    protected $primaryKey = 'penjualan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'tgl_penjualan' => 'datetime',
        'payment_config' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(PenjualanDetail::class, 'penjualan_id', 'penjualan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
