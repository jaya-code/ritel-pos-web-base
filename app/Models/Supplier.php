<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStore;

class Supplier extends Model
{
    use HasFactory, HasStore;

    protected $fillable = ['name', 'contact_info', 'address', 'store_id'];
}
