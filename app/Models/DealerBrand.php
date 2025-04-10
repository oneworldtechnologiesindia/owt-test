<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealerBrand extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    use SoftDeletes;

    protected $table = 'dealer_brand';

    public $timestamps = false;
}
