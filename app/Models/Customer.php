<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact',
        'store_id',
        'status'
    ];

    public function stores()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
