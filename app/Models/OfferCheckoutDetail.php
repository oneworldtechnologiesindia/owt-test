<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferCheckoutDetail extends Model
{
    use SoftDeletes;
    protected $table = 'offer_checkout_detail';
}
