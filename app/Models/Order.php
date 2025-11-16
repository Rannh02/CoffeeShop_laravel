<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\OrderItem;
use App\Models\Payment;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'Order_id';
    public $timestamps = true;

    protected $fillable = [
        'Customer_id',
        'Employee_id',
        'Customer_name',
        'Order_date',
        'TotalAmount',
        'Order_Type'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'Order_date' => 'datetime',
        'TotalAmount' => 'decimal:2',
        'Customer_id' => 'integer',
        'Employee_id' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Customer_id', 'Customer_id');
    }

    /**
     * Get the employee that processed the order.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'Employee_id', 'Employee_id');
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'Order_id', 'Order_id');
    }

    /**
     * Get the payment for the order.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'Order_id', 'Order_id');
    }

    /**
     * Scope a query to only include orders of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('Order_Type', $type);
    }

    /**
     * Scope a query to only include recent orders.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('Order_date', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include orders within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('Order_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include orders for a specific customer.
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('Customer_id', $customerId);
    }

    /**
     * Get the formatted total amount.
     */
    public function getFormattedTotalAttribute()
    {
        return '₱' . number_format($this->TotalAmount, 2);
    }

    /**
     * Get the formatted order date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->Order_date->format('M d, Y h:i A');
    }

    /**
     * Check if order is a dine-in order.
     */
    public function isDineIn()
    {
        return $this->Order_Type === 'Dine In';
    }

    /**
     * Check if order is a takeout order.
     */
    public function isTakeout()
    {
        return $this->Order_Type === 'Takeout';
    }

    /**
     * Get the order total with items.
     */
    public function calculateTotal()
    {
        return $this->orderItems()->sum(function ($item) {
            return $item->Quantity * $item->Price_sale;
        });
    }

    /**
     * Check if order has been paid.
     */
    public function isPaid()
    {
        return $this->payment()->exists();
    }

    /**
     * Boot method for model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set Order_date if not provided
        static::creating(function ($order) {
            if (empty($order->Order_date)) {
                $order->Order_date = now();
            }
        });
    }
}