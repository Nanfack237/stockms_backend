<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\supplier;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'store_id',
        'stock_id',
        'supplier_id',
        'quantity',
        'date',
        'unit_price',
        'total_price',
        'status'
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function stocks()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function stores()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
