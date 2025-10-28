<?php
// app/Models/Employee.php (updated to match your existing model, with HasFactory trait added)

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

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

    protected $dates = ['Date_of_Hire'];
}