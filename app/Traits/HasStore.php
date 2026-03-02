<?php

namespace App\Traits;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasStore
{
    protected static function bootHasStore()
    {
        // Global Scope: Filter by current user's store
        static::addGlobalScope('store', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'admin') {
                    if (session()->has('admin_active_store_id')) {
                        $builder->where('store_id', session('admin_active_store_id'));
                    }
                } elseif ($user->store_id) {
                    $builder->where('store_id', $user->store_id);
                }
            }
        });

        // Auto-save store_id on creation
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'admin' && session()->has('admin_active_store_id')) {
                    $model->store_id = session('admin_active_store_id');
                } elseif ($user->role !== 'admin' && $user->store_id) {
                    $model->store_id = $user->store_id;
                }
            }
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
