<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStore;

class Pembelian extends Model
{
    use HasFactory, HasStore;

    protected $table = 'pembelian';
    protected $primaryKey = 'pembelian_id';
    protected $guarded = [];

    protected $casts = [
        'tgl_beli' => 'datetime',
        'tgl_jatuh_tempo' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class, 'pembelian_id', 'pembelian_id');
    }
}
