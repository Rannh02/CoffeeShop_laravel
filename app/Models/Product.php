<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'Product_id';
    protected $fillable = [
        'Product_name',
        'Category_id',
        'Price',
        'Image_url'
    ];

    // ✅ Product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class, 'Category_id', 'Category_id');
    }

    // ✅ Product uses many ingredients (through pivot table product_ingredients)
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients', 'Product_id', 'Ingredient_id')
                    ->withPivot('quantity_used')
                    ->withTimestamps();
    }
    
    // ✅ Optional: link to inventory logs
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'Product_id', 'Product_id');
    }
}
