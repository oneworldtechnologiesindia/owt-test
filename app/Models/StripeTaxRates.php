<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeTaxRates extends Model
{
    use SoftDeletes;
    protected $table = 'stripe_tax_rates';
}
