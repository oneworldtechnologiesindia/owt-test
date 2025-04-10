<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    use SoftDeletes;

    protected $table = 'brand';

}
