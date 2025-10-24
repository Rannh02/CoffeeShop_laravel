<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'orderitem';
    protected $primaryKey = 'OrderItem_id';
    public $timestamps = false;

    protected $fillable = [
        'Order_id',
        'Product_id',
        'Quantity',
        'Price_sale'
    ];
}