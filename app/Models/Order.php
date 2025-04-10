<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    public static $status = [
        '0' => 'waiting for payment',
        '1' => 'payment completed',
        '2' => 'shipped',
        '3' => 'canceled'
    ];

    public static function getCancelDocumentUrl($document_file = "")
    {
        $oldfileexists = storage_path('app/public/cancel_proof/') . $document_file;
        if ($document_file != "" && file_exists($oldfileexists)) {
            return asset('/storage/cancel_proof/' . $document_file);
        } else {
            return "";
        }
    }

    public function offerPurchaseEnquiry()
    {
        return $this->belongsTo(OfferPurchaseEnquiry::class, 'offer_id', 'id');
    }

    public function orderRating()
    {
        return $this->hasMany(OrderRating::class, 'order_id');
    }
}
