<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'Inventory_id';
    public $timestamps = false;

    protected $fillable = [
        'Product_id',
        'QuantityInStock'
    ];
}