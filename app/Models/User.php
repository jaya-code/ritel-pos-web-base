<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'store_id',
        'print_method',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function subscriptionTransactions()
    {
        return $this->hasMany(SubscriptionTransaction::class);
    }

    public function hasActiveSubscription()
    {
        // Admin and Kasir usually bypass this check (Kasir checks Owner's subscription later)
        if ($this->role === 'admin') {
            return true;
        }

        if ($this->store) {
            return $this->store->hasActiveSubscription();
        }

        return false;
    }
    
    // Manual Scope for Store Isolation
    public function scopeForStore(Builder $query)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                if (session()->has('admin_active_store_id')) {
                    $query->where('store_id', session('admin_active_store_id'));
                }
            } elseif ($user->store_id) {
                $query->where('store_id', $user->store_id);
            }
        }
    }
}
