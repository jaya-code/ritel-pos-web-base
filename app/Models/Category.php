<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasStore;

class Category extends Model
{
    use HasFactory, HasStore;

    protected $fillable = ['name', 'description', 'store_id'];
}
