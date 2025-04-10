<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductAttributes extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'product_attributes';

}
