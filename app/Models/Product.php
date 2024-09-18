<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'image',
        'store_id',
        'user_id',
        'status'
    ];

    public function stores()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'product_id');
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'product_id');
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id');
    }
}
