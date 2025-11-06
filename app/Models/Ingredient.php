<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';
    protected $primaryKey = 'Ingredient_id'; // Add this line
    
    protected $fillable = [
        'Ingredient_name',
        'StockQuantity',
        'Unit',
        'ReorderLevel',
    ];

    // Relationship: One ingredient can belong to many products
    public function products()
{
    return $this->belongsToMany(Product::class, 'ingredient_product')
                ->withPivot('quantity_used')
                ->withTimestamps();
}


}