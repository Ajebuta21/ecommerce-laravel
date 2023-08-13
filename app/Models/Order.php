<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['cart', 'subtotal', 'total', 'delivery', 'user_id','user_firstname', 'user_phone', 'user_address', 'region', 'order_number', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
