<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories'; // or 'categories' depending on your table name
    protected $primaryKey = 'Category_id';

    protected $fillable = [
        'Category_name'
    ];
}