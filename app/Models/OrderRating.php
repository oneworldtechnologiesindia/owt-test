<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderRating extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'order_rating';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
