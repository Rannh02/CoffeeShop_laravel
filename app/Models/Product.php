<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'Product_name',
        'Category_id',
        'Price',
        'Image_url'
    ];

    protected $table = 'products';
    protected $primaryKey = 'Product_id';
}
