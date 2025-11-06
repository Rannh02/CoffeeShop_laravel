<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'Payment_id';
    public $timestamps = false;

    protected $fillable = [
        'Order_id',
        'Payment_method',
        'Amount_Paid',
        'PaymentDate',
        'TransactionReference'
    ];
}