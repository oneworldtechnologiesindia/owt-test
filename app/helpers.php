<?php

use App\User;
use Illuminate\Support\Carbon;
use App\Models\PurchaseEnquiry;
use App\Models\AppointmentDealer;
use App\Models\EmailLog;
use App\Models\Order;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\Auth;

function pr($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit();
}
function cacheclear()
{
    return time();
}
function getDateFormateView($date)
{
    return Carbon::parse($date)->format('d.m.Y');
}

function getDateTimeFormateView($date)
{
    return Carbon::parse($date)->format('d.m.Y H:i:s');
}

function getLogoUrl()
{
    return asset('frontend/img/logo.png');
}
function addPageJsLink($link)
{
    return asset('assets/js/pages') . "/" . $link . '?' . time();
}
function readAsArray($filePath, $readWidth = 9)
{
    $reader = new Xlsx();
    $spreadsheet = $reader->load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();

    // echo $spreadsheet->setActiveSheetIndex(0)->getHighestDataRow();
    // echo $spreadsheet->setActiveSheetIndex(0)->getHighestRow();
    // echo $spreadsheet->setActiveSheetIndex(0)->getHighestDataColumn();

    // exit;
    $rows = [];

    $maxCell = "A";
    for ($i = 1; $i < $readWidth; $i++) {
        $maxCell++;
    }

    foreach ($worksheet->getRowIterator() as $rowKey => $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $items = [];
        foreach ($cellIterator as $cellKey => $cell) {
            if ($cellKey > $maxCell) {
                break;
            }
            $items[] = $cell->getValue();
        }
        $rowData = array_filter($items);
        if (isset($rowData) && !empty($rowData)) {
            $rows[] = $items;
        }
    }

    return $rows;
}

function updateDelaerStatus($dealer, $totaloffer, $action = 'add')
{
    $turnover = 0.00;
    if ($action == 'cancel') {
        $amount = round(((float) $dealer->turnover - (float) $totaloffer), 2);
        $turnover = ($amount > 0) ? $amount : '0.00';
    } else {
        $turnover = round(((float) $totaloffer + (float) $dealer->turnover), 2);
    }
    $status = '0';
    switch ($turnover) {
        case ($turnover > 150000):
            $status = '3';
            break;

        case ($turnover > 100000):
            $status = '2';
            break;

        case ($turnover > 75000):
            $status = '1';
            break;

        default:
            $status = '0';
    }

    return ['turnover' => $turnover, 'status' => $status];
}

function checkEnquiryBlock($loginUser = null)
{
    if (isset($loginUser) && !empty($loginUser)) {
        $expired = Carbon::now()->subHours(24);
        $today = Carbon::now();

        $enquiries = PurchaseEnquiry::query()
            ->select(
                'purchase_enquiry.id',
                DB::raw('count(offer_purchase_enquiry.id) as offers')
            )
            ->leftjoin('offer_purchase_enquiry', function ($q) {
                $q->on('offer_purchase_enquiry.customer_enquiry_id', '=', 'purchase_enquiry.id');
                $q->where('offer_purchase_enquiry.status', '1');
            })
            ->where('purchase_enquiry.customer_id', $loginUser->id)
            ->where('purchase_enquiry.status', '1')
            ->where('purchase_enquiry.created_at', '<', $expired)
            ->groupBy('purchase_enquiry.id')
            ->withTrashed()
            ->get()
            ->pluck('offers', 'id')
            ->toArray();

        $enquiryblock = 'false';
        $count = $offers_continue = $blockupto = 0;

        if (isset($enquiries) && !empty($enquiries)) {
            foreach ($enquiries as $enquiry_id => $enquiry_offers) {
                if ($enquiry_id != $offers_continue) {
                    $count = 0;
                    $offers_continue = $enquiry_id;
                }
                if ($enquiry_offers > 0) {
                    $count++;
                }
                if ($count == env("PURCHASE_ENQUIRY_LIMIT", 10)) {
                    $blockupto = Carbon::parse(PurchaseEnquiry::find($enquiry_id)->created_at)->addDays((int) env("PURCHASE_ENQUIRY_BLOCK", 20));

                    if ($today < $blockupto) {
                        break;
                    } else {
                        $count = 0;
                    }
                }
                $offers_continue++;
            }
        }

        if ($count == (int) env("PURCHASE_ENQUIRY_LIMIT", 10)) {
            $enquiryblock = 'true';
        }
        return ['enquiryblock' => $enquiryblock, 'blockupto' => $blockupto];
    } else {
        return ['enquiryblock' => 'false', 'blockupto' => ''];
    }
}

function createDisplayId($role_type = 3)
{
    $userlast = User::query()
        ->where('role_type', $role_type)
        ->whereNull('deleted_at')
        ->orderBy('id', 'desc')
        ->select('display_id')
        ->first();

    $lastUserId = 1000;
    $displayIDPrefix = 'HQD';
    if ($role_type == 2) {
        $displayIDPrefix = 'HQD';
    }
    if ($role_type == 3) {
        $displayIDPrefix = 'HQC';
    }
    if (isset($userlast) && !empty($userlast)) {
        $lastUserId = (int) substr($userlast->display_id, 3);
    }

    return $displayIDPrefix . ($lastUserId + 1);
}

function notificationMarkAsRead($type = '', $id = '', $status = 'created')
{
    if (isset($type) && !empty($type) && isset($id) && !empty($id)) {
        $notifications = collect(auth()->user()->unreadNotifications);
        foreach ($notifications as $notification) {
            if ($type == 'appointment') {
                if ($notification->data['type'] == $type && $notification->data['status'] >= $status && $notification->data['id'] == $id) {
                    $notification->markAsRead();
                }
            } else {
                if ($notification->data['type'] == $type && $notification->data['status'] == $status && $notification->data['id'] == $id) {
                    $notification->markAsRead();
                }
            }
        }
        return true;
    } else {
        return false;
    }
}

function getDealerFeedbackData($dealerId)
{
    $total_feedback = $average_rating = 0;
    $appointmentData = AppointmentDealer::query()
        ->select(DB::raw('count(*) as feedback_count'), DB::raw('sum(rating) as total_rating'))
        ->where('dealer_id', $dealerId)
        ->where('status', 3)
        ->first()
        ->toArray();

    if (!empty($appointmentData)) {
        $total_feedback += $appointmentData['feedback_count'];
        $average_rating += $appointmentData['total_rating'];
    }

    $orderData = Order::query()
        ->select(DB::raw('count(*) as feedback_count'), DB::raw('sum(average) as total_rating'))
        ->join('order_rating', 'order_rating.order_id', '=', 'orders.id')
        ->where('dealer_id', $dealerId)
        ->where('status', 1)
        ->first()
        ->toArray();

    if (!empty($orderData)) {
        $total_feedback += $orderData['feedback_count'];
        $average_rating += $orderData['total_rating'];
    }

    $data = array();
    $data['total_feedback'] = $total_feedback;
    if ($total_feedback > 0)
        $data['average_rating'] = round($average_rating / $total_feedback, 2);
    else
        $data['average_rating'] = 0;

    return $data;
}

function addEmailLog($emailLogArray)
{
    // store the log into database

    $emailLogModel = new EmailLog;
    $emailLogModel->user_id = $emailLogArray['user_id'];
    $emailLogModel->subject = $emailLogArray['subject'];
    $emailLogModel->email_body = $emailLogArray['email_body'];
    $emailLogModel->created_at = date('Y-m-d H:i:s');
    $emailLogModel->updated_at = date('Y-m-d H:i:s');
    $emailLogModel->save();
}

function generateRandomString($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function get_rand_capital_letter($length = 1)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function get_rand_lower_letter($length = 1)
{
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function get_rand_number($length = 1)
{
    $characters = '0123456789';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function getInvNumber($user){
    $assign_inv_number = 'INV' . get_rand_capital_letter(2) .'0001';
    $last_invoice = \App\Models\SubscriptionLog::where(['user_id' => $user->id])->orderByDesc('id')->first();
    if (!empty($last_invoice->inv_number)) {
        $inv_number = preg_replace('/[^0-9]/', '', $last_invoice->inv_number);
        $inv_number++;
        $assign_inv_number = str_pad($inv_number, 4, '0', STR_PAD_LEFT);
        $assign_inv_number = 'INV' . get_rand_capital_letter(2) . $assign_inv_number;
    }
    return $assign_inv_number;
}

function getCurrencyFormat($amount,$currency='euro'){
    if(strtolower($currency)=="euro"){
        // Format amount in Euro
        setlocale(LC_MONETARY, 'de_DE');
        return money_format('%.2n', $amount) . " EUR\n";
    }
    else{
        // usd here.
        setlocale(LC_MONETARY, 'en_US');
        return money_format('%.2n', $amount) . " USD\n";
    }
    return $amount;
}
function getCustomerCountry($customer=null){
    if(!empty($customer)){
        return $customer->country;
    }
    else
    {
        $customer=Auth::user();
        return $customer->country;
    }
}
function getDealerCurrencyType($dealer=null){
    $dealer_country='';
    if (!empty($dealer)) {
        $dealer_country=$dealer->country;
    } else {
        $dealer = Auth::user();
        $dealer_country=$dealer->country;
    }
    $german_country = config('common.german_country');
    if (in_array($dealer_country, $german_country)) {
        return 'eur';
    }
    else{
        return "usd";
    }
}
function formatCurrencyOutput($amount,$currency_type,$with_symbol=false,$symbol_loc='after')
{
    $currency='$';
    if ($currency_type == 1 || $currency_type == 'eur') {
        $currency= '€';
    }
    $return_amount=$amount;
    if (is_numeric($amount)) {
        if ($currency_type==1 || $currency_type=='eur') {
            $return_amount = number_format($amount, 2, ',', '.');
        }
        else{
            $return_amount = number_format($amount, 2, '.', ',');
        }
    }
    if($with_symbol){
        if($symbol_loc=="before"){
            $return_amount = $currency . ' ' . $return_amount;
        }
        else{
            $return_amount = $return_amount . ' ' . $currency;
        }
    }
    // It's already been parsed.
    return $return_amount;
}

// get the country specific translation for the invoice
if (!function_exists('getPDFTranslation')) {
    function getPDFTranslation($key, $country=null)
    {
        $germanCountries = ['Germany', 'Austria', 'Switzerland'];

        if (in_array($country, $germanCountries)) {
            return __('translation.' . $key, [], 'de');
        }

        return __('translation.' . $key, [], 'en');
    }
}
