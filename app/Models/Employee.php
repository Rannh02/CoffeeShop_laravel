<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';  
    protected $primaryKey = 'Employee_id';
    public $timestamps = true;

    protected $fillable = [
        'First_name',
        'Last_name',
        'Cashier_Account',
        'Password',
        'Gender',
        'Contact_number',
        'Position',
        'Date_of_Hire',
        'Status'
    ];

    protected $hidden = [
        'Password',
    ];
}