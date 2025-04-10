<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanType extends Model
{
    use SoftDeletes;

    protected $table = 'plan_types';

    protected $fillable = [
        'plan_type',
        'silver_level',
        'gold_level',
        'platinum_level',
        'diamond_level',
    ];
}
