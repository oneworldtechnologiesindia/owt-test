<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionLog extends Model
{
    use SoftDeletes;
    protected $table = 'subscription_log';
}
