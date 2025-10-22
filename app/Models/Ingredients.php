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
        'Quantity',
        'Unit',
        'Supplier_id',
    ];

    // Relationship with Supplier - Add this method
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'Supplier_id', 'Supplier_id');
    }

    // Relationship: One ingredient can belong to many products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients', 'Ingredient_id', 'Product_id');
    }
}