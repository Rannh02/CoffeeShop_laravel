<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'OrderItem_id';
    public $timestamps = true;

    protected $fillable = [
        'Order_id',
        'Product_id',
        'Quantity',
        'UnitPrice'
    ];
}