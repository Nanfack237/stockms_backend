<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'contact',
        'store_id',
        'status'
    ];

    public function stores()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'product_id');
    }

}
