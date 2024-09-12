<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Store;
use App\Models\Purchase;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'store_id',
        'quantity',
        'cost_price',
        'last_quantity',

    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stores()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'product_id');
    }
}
