<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';
    protected $primaryKey = 'Inventory_id';
    public $timestamps = true; // match your migration

    protected $fillable = [
        'Product_id',
        'Ingredient_id',
        'QuantityUsed',
        'RemainingStock',
        'Action',
        'DateUsed'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class, 'Product_id', 'Product_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'Ingredient_id', 'Ingredient_id');
    }
}
