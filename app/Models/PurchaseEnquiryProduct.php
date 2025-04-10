<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseEnquiryProduct extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_enquiry_products';
}
