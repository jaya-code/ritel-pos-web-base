<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'logo',
        'owner_id',
        'payment_config',
        'qris_fee',
        'subscription_until',
    ];

    protected $casts = [
        'payment_config' => 'array',
        'qris_fee' => 'decimal:2',
        'subscription_until' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function hasActiveSubscription()
    {
        return $this->subscription_until && $this->subscription_until->isFuture();
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function getQrisBalanceAttribute()
    {
        $qrisSalesTotal = \App\Models\Penjualan::where('store_id', $this->id)
            ->where('metode_pembayaran', 'Qris Dinamis')
            ->where('status', 'paid')
            ->sum('total');

        $qrisFeePercentage = $this->qris_fee ?? 0;
        $netQrisSales = $qrisSalesTotal - ($qrisSalesTotal * ($qrisFeePercentage / 100));

        $withdrawalsTotal = $this->withdrawals()
            ->whereIn('status', ['pending', 'approved'])
            ->sum('amount');

        return max(0, $netQrisSales - $withdrawalsTotal);
    }
}
