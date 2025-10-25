<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $primaryKey = 'Supplier_id'; // Add this line

    protected $fillable = [
        'Supplier_name',
        'Contact_number',
        'Address',
        'Status',
    ];

    // Relationship: One supplier can supply many ingredients
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'Supplier_id', 'Supplier_id'); // Specify both foreign and local key
    }
}