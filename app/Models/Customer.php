<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'Customer_id';
    public $timestamps = true;

    protected $fillable = [
        'Customer_name',  
        'Date/Time'      
    ];
}