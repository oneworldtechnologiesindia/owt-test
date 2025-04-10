<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferDetails extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'offer_details';

    public $timestamps = false;

    public static $status = [
        1 => 'Sending',
        2 => 'Accepted',
        3 => 'Rejected offer',
        4 => 'Expired',
    ];
}
