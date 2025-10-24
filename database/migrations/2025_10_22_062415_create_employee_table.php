<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';  // ✅ Add this line
    protected $primaryKey = 'Employee_id';
    public $timestamps = true;

    protected $fillable = [
        'First_name',
        'Last_name',
        'Cashier_Account',
        'Password',
        'Gender',
        'Contact_number',
        'Date/Time',
        'Status'
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class, 'Employee_id', 'Employee_id');
    }
}