<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // Table and primary key match the migration
    protected $table = 'payment';
    protected $primaryKey = 'Payment_id';
    public $timestamps = true;

    // Match columns created by migration
    protected $fillable = [
        'Order_id',
        'PaymentMethod',
        'AmountPaid',
        'PaymentDate',
        'TransactionReference'
    ];
}