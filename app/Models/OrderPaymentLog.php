<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPaymentLog extends Model
{
    use SoftDeletes;

    protected $table = 'order_payment_log';
}
