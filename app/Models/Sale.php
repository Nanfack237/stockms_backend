<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'store_id',
        'stock_id',
        'quantity',
        'date',
        'unit_price',
        'total_price',
        'customer_name',
        'customer_contact',
        'status'
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stores()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function stocks()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }
}
