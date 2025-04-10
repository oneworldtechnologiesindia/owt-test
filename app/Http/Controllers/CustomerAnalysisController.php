<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerAnalysisController extends Controller
{
    public function index(Request $request)
    {
        return view('analysis.customer');
    }

    public function getAgeChartData(Request $request)
    {
        $age_wise = array();
        $age_arary = ['15-25', '26-35', '36-45', '46-55', '56-65', '66-75', '76-85'];
        $chartFilter = (isset($request->filter_time) && !empty($request->filter_time)) ? $request->filter_time : 'year';
        $customer_filter = (isset($request->filter_customer) && !empty($request->filter_customer)) ? $request->filter_customer : 'dealer_customer';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'users.birth_date')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.status', '!=', 3)
            ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
            ->join('users', 'users.id', '=', 'purchase_enquiry.customer_id')
            ->whereNotNull('users.birth_date');
        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }
        if ($customer_filter == "dealer_customer") {
            $main_query = $main_query->where('orders.dealer_id', Auth::user()->id);
        }
        $get_highest_saling_items = $main_query->groupBy('users.birth_date')->get()->toArray();
        $final_records=[];
        foreach($get_highest_saling_items as $key_data => $age_val_data){
            $currentDate = Carbon::now();
            $birthdate = Carbon::createFromFormat('Y-m-d', $age_val_data['birth_date']);
            $diffInYears = $birthdate->diffInYears($currentDate);
            if($diffInYears >= 15 && ($diffInYears <= 25)){
                $final_records[0][]=$age_val_data['total_qty'];
            }else if($diffInYears >= 26 && ($diffInYears <= 35)){
                $final_records[1][]=$age_val_data['total_qty'];
            }else if($diffInYears >= 36 && ($diffInYears <= 45)){
                $final_records[2][]=$age_val_data['total_qty'];
            }else if($diffInYears >= 46 && ($diffInYears <= 55)){
                $final_records[3][]=$age_val_data['total_qty'];
            }else if($diffInYears >= 56 && ($diffInYears <= 65)){
                $final_records[4][]=$age_val_data['total_qty'];
            }else if($diffInYears >= 76 && ($diffInYears <= 85)){
                $final_records[5][]=$age_val_data['total_qty'];
            }
        }
        $final_arr=[];
        foreach ($age_arary as $key => $value) {
            $final_arr[$key]['data']=0;
            $final_arr[$key]['value']=$value;
           if(array_key_exists($key,$final_records)){
                $final_arr[$key]['data']=array_sum($final_records[$key]);
                $final_arr[$key]['value']=$value;
           }
        }
        foreach ($final_arr as $key => $value) {
            $age_wise['sales'][$key] = $value['data'];
            $age_wise['age_keys'][$key] = $value['value'];
        }
        return response()->json(
            [
                'status' => true,
                'age_wise' => $age_wise,
            ]
        );
    }

    public function getGenderWise(Request $request)
    {
        $gender_wise = array();
        $gender_array = [1 => 'Male', 2=>'Female', 3=>'Others'];
        $chartFilter = (isset($request->filter_time) && !empty($request->filter_time)) ? $request->filter_time : 'year';
        $customer_filter = (isset($request->filter_customer) && !empty($request->filter_customer)) ? $request->filter_customer : 'dealer_customer';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'users.gender')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.status', '!=', 3)
            ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
            ->join('users', 'users.id', '=', 'purchase_enquiry.customer_id')
            ->whereNotNull('users.gender');
        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }
        if ($customer_filter == "dealer_customer") {
            $main_query = $main_query->where('orders.dealer_id', Auth::user()->id);
        }
        $get_highest_saling_items = $main_query->groupBy('users.gender')->get()->toArray();
        $final_records = array();
        foreach($get_highest_saling_items as $key => $get_gender_value){
            if($get_gender_value['gender'] == 1 ){
                $final_records[1][]=$get_gender_value['total_qty'];
            }else if($get_gender_value['gender'] == 2 ){
                $final_records[2][]=$get_gender_value['total_qty'];
            }else if($get_gender_value['gender'] == 3){
                $final_records[3][]=$get_gender_value['total_qty'];
            }
        }
        $final_arr=[];
        foreach ($gender_array as $key => $value) {
            $final_arr[$key]['data']=0;
            $final_arr[$key]['value']=$value;
           if(array_key_exists($key,$final_records)){
                $final_arr[$key]['data']=array_sum($final_records[$key]);
                $final_arr[$key]['value']=$value;
           }
        }
        foreach ($final_arr as $key => $value) {
            $gender_wise['sales'][$key-1] = $value['data'];
            $gender_wise['gender_keys'][$key-1] = $value['value'];
        }
        return response()->json(
            [
                'status' => true,
                'gender_wise' => $gender_wise,
            ]
        );
    }

    public function getCityWise(Request $request)
    {
        $city_wise = array();
        $chartFilter = (isset($request->filter_time) && !empty($request->filter_time)) ? $request->filter_time : 'year';
        $customer_filter = (isset($request->filter_customer) && !empty($request->filter_customer)) ? $request->filter_customer : 'dealer_customer';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'users.city')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.status', '!=', 3)
            ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
            ->join('users', 'users.id', '=', 'purchase_enquiry.customer_id');
        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }
        if ($customer_filter == "dealer_customer") {
            $main_query = $main_query->where('orders.dealer_id', Auth::user()->id);
        }

        $get_highest_saling_items = $main_query->groupBy('users.city')->get()->take(10)->toArray();
        foreach ($get_highest_saling_items as $key => $get_city) {
            $city_wise['city'][$key] = $get_city['city'];
            $city_wise['sales'][$key] = $get_city['total_qty'];
        }
        return response()->json(
            [
                'status' => true,
                'city_wise' => $city_wise,
            ]
        );
    }

    public function getZipcodeWise(Request $request)
    {
        $zip_code_wise = array();
        $chartFilter = (isset($request->filter_time) && !empty($request->filter_time)) ? $request->filter_time : 'year';
        $customer_filter = (isset($request->filter_customer) && !empty($request->filter_customer)) ? $request->filter_customer : 'dealer_customer';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'users.zipcode')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.status', '!=', 3)
            ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
            ->join('users', 'users.id', '=', 'purchase_enquiry.customer_id');
        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }
        if ($customer_filter == "dealer_customer") {
            $main_query = $main_query->where('orders.dealer_id', Auth::user()->id);
        }
        $get_highest_saling_items = $main_query->groupBy('users.zipcode')->get()->take(10)->toArray();
        foreach ($get_highest_saling_items as $key => $get_array_data) {
            $zip_code_wise['zipcode'][$key] = $get_array_data['zipcode'];
            $zip_code_wise['sales'][$key] = $get_array_data['total_qty'];
        }
        return response()->json(
            [
                'status' => true,
                'zip_code_wise' => $zip_code_wise,
            ]
        );
    }

    public function getBrandWise(Request $request)
    {
        $brand_wise = array();
        $chartFilter = (isset($request->filter_time) && !empty($request->filter_time)) ? $request->filter_time : 'year';
        $customer_filter = (isset($request->filter_customer) && !empty($request->filter_customer)) ? $request->filter_customer : 'dealer_customer';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'brand.brand_name')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.status', '!=', 3)
            ->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id')
            ->join('brand', 'brand.id', '=', 'product.brand_id');
        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }
        if ($customer_filter == "dealer_customer") {
            $main_query = $main_query->where('orders.dealer_id', Auth::user()->id);
        }
        $get_highest_saling_items = $main_query->groupBy('brand.brand_name')->get()->take(10)->toArray();

        foreach ($get_highest_saling_items as $key => $get_brand_array) {
            $brand_wise['brand_name'][$key] = $get_brand_array['brand_name'];
            $brand_wise['sales'][$key] = $get_brand_array['total_qty'];
        }
        return response()->json(
            [
                'status' => true,
                'brand_wise' => $brand_wise,
            ]
        );
    }

    public function getCategoryWise(Request $request)
    {
        $category_wise = array();
        $chartFilter = (isset($request->filter_time) && !empty($request->filter_time)) ? $request->filter_time : 'year';
        $customer_filter = (isset($request->filter_customer) && !empty($request->filter_customer)) ? $request->filter_customer : 'dealer_customer';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'product_category.category_name')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.status', '!=', 3)
            ->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id')
            ->join('product_category', 'product_category.id', '=', 'product.category_id');
        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }
        if ($customer_filter == "dealer_customer") {
            $main_query = $main_query->where('orders.dealer_id', Auth::user()->id);
        }
        $get_highest_saling_items = $main_query->groupBy('product_category.category_name')->get()->take(10)->toArray();

        foreach ($get_highest_saling_items as $key => $get_category_array) {
            $category_wise['category_name'][$key] = $get_category_array['category_name'];
            $category_wise['sales'][$key] = $get_category_array['total_qty'];
        }
        return response()->json(
            [
                'status' => true,
                'category_wise' => $category_wise,
            ]
        );
    }

    public function getProducTypeWise(Request $request)
    {
        $product_type_wise = array();
        $chartFilter = (isset($request->filter_time) && !empty($request->filter_time)) ? $request->filter_time : 'year';
        $customer_filter = (isset($request->filter_customer) && !empty($request->filter_customer)) ? $request->filter_customer : 'dealer_customer';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'product_type.type_name')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.status', '!=', 3)
            ->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id')
            ->join('product_type', 'product_type.id', '=', 'product.type_id');
        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }
        if ($customer_filter == "dealer_customer") {
            $main_query = $main_query->where('orders.dealer_id', Auth::user()->id);
        }
        $get_highest_saling_items = $main_query->groupBy('product_type.type_name')->get()->take(10)->toArray();

        foreach ($get_highest_saling_items as $key => $get_type_array) {
            $product_type_wise['type_name'][$key] = $get_type_array['type_name'];
            $product_type_wise['sales'][$key] = $get_type_array['total_qty'];
        }
        return response()->json(
            [
                'status' => true,
                'product_type_wise' => $product_type_wise,
            ]
        );
    }

    public function getProducWise(Request $request)
    {
        $product_wise = array();
        $chartFilter = (isset($request->filter_time) && !empty($request->filter_time)) ? $request->filter_time : 'year';
        $customer_filter = (isset($request->filter_customer) && !empty($request->filter_customer)) ? $request->filter_customer : 'dealer_customer';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'product.product_name')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.status', '!=', 3)
            ->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id');
        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }
        if ($customer_filter == "dealer_customer") {
            $main_query = $main_query->where('orders.dealer_id', Auth::user()->id);
        }
        $get_highest_saling_items = $main_query->groupBy('product.product_name')->get()->take(10)->toArray();

        foreach ($get_highest_saling_items as $key => $get_product_array) {
            $product_wise['product_name'][$key] = $get_product_array['product_name'];
            $product_wise['sales'][$key] = $get_product_array['total_qty'];
        }
        return response()->json(
            [
                'status' => true,
                'product_wise' => $product_wise,
            ]
        );
    }
}
