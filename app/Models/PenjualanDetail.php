<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $table = 'penjualan_details';
    // No primary key defined in schema, so standard id might be missing if I followed strictly.
    // If I added $table->id() in migration (which I did not yet, let me check what I wrote), 
    // I wrote $table->id() in the migration comment but the code usually adds it if not specified? No `Schema::create` doesn't auto add id.
    // I added $table->id(); in the migration code above.
    
    protected $guarded = [];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id', 'penjualan_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
