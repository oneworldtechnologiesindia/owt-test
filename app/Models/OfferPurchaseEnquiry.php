<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferPurchaseEnquiry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    use SoftDeletes;

    protected $table = 'offer_purchase_enquiry';

    public static $status = [
        1 => 'Offer Received',
        2 => 'Accepted',
        3 => 'Rejected',
        4 => 'Expired',
    ];

    public static $payment_method = [
        1 => 'Bank wire (without stripe)',
        2 => 'Stripe'
        // 3 => 'Pick up at store'
    ];
}
