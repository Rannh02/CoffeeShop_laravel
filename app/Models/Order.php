<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\OrderItem;
use App\Models\Payment;

class Order extends Model
{
    protected $table = 'orders';  // ✅ Specify the correct table name
    protected $primaryKey = 'Order_id';
    public $timestamps = true;  // Changed to true since your table has timestamps

    protected $fillable = [
        'Customer_id',
        'Employee_id',
        'Customer_name',  // ✅ Changed from 'Customers Name'
        'Order_date',     // ✅ Changed from 'OrderDate'
        'TotalAmount',
        'Order_Type'      // ✅ Changed from 'OrderType'
    ];

    // Optional: Define relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Customer_id', 'Customer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'Employee_id', 'Employee_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'Order_id', 'Order_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'Order_id', 'Order_id');
    }
}