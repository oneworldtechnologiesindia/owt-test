<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseEnquiry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    use SoftDeletes;

    protected $table = 'purchase_enquiry';

    public static $status = [
        0 => 'Pending',
        1 => 'Offer Sent',
        2 => 'Accepted',
        3 => 'Rejected',
        4 => 'Expired',
        5 => 'Offer Expired',
    ];

    public static $statusc = [
        1 => 'Pending',
        2 => 'Offer Received',
        3 => 'Accepted',
        4 => 'Expired',
        5 => 'Offer Expired',
    ];

    // public static $enquiryType = [
    //     1 => 'Vorführbereit',
    //     3 => 'Auf Lager',
    //     2 => 'Gebraucht',
    // ];

    public static $enquiryType = [
        1 => 'In Stock',
        2 => 'Used',
        3 => 'Ready For Demo',
    ];

    public function enquiryTypeView($value)
    {
        $names = "";
        if ($value) {
            $enquiryType = PurchaseEnquiry::$enquiryType;
            $enTypeArr = explode(",", $value);
            foreach ($enquiryType as $exekey => $exelist) {
                if (in_array($exekey, $enTypeArr)) {
                    $names .= ($names) ? ", " : "";
                    $names .= $exelist;
                }
            }
        }
        return $names;
    }

    public function offerPurchaseEnquiry()
    {
        return $this->hasMany(OfferPurchaseEnquiry::class, 'customer_enquiry_id', 'id');
    }
}
