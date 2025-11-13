<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $primaryKey = 'Ingredient_id';
    protected $fillable = [
        'Ingredient_name',
        'Unit',
        'StockQuantity',
        'ReorderLevel'
    ];

    // ✅ Many products use this ingredient
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients', 'Ingredient_id', 'Product_id')
                    ->withPivot('quantity_used')
                    ->withTimestamps();
    }

    // ✅ Logs of inventory usage
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'Ingredient_id', 'Ingredient_id');
    }
}
