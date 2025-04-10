<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\DealerBrand;
use Illuminate\Http\Request;
use App\Models\AppointmentDealer;
use App\Models\Product;
use App\Models\ProductConnection;
use App\Models\ProductExecution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DealerBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $user = Auth::user();
        $brandData = Brand::query()
            ->leftJoin('dealer_brand', function ($join) use ($user) {
                $join->on('brand.id', '=', 'dealer_brand.brand_id')
                    ->whereNull('dealer_brand.deleted_at')
                    ->where('dealer_brand.dealer_id', $user->id);
            })
            ->select('brand.id', 'brand.brand_name')
            ->where('dealer_brand.id', '=', NULL)
            ->groupBy("brand.id")
            ->get()
            ->pluck('brand_name', 'id')
            ->toArray();
        return view('dealer_brand.index', compact('brandData'));
    }
    public function getBrandList()
    {
        $user = Auth::user();
        $brandData = Brand::query()
            ->leftJoin('dealer_brand', function ($join) use ($user) {
                $join->on('brand.id', '=', 'dealer_brand.brand_id')
                    ->whereNull('dealer_brand.deleted_at')
                    ->where('dealer_brand.dealer_id', $user->id);
            })
            ->select('brand.id', 'brand.brand_name')
            ->where('dealer_brand.id', '=', NULL)
            ->groupBy("brand.id")
            ->get()
            ->pluck('brand_name', 'id')
            ->toArray();
        if (!empty($brandData)) {
            $result = ['status' => true, 'data' => $brandData];
        } else {
            $result = ['status' => false, 'data' => []];
        }
        return response()->json($result);
    }

    public function get(Request $request)
    {
        $user = Auth::user();
        $data = DealerBrand::query()
            ->join('brand', 'brand.id', '=', 'dealer_brand.brand_id')
            ->select('dealer_brand.id', 'brand.brand_name', 'dealer_brand.created_at','brand.id as brand_id')
            ->where('dealer_id', $user->id);

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('brand_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function getProduct(Request $request)
    {
        $user = Auth::user();
        $all_connections = ProductConnection::query()
            ->select('id', 'connection_name')
            ->get()
            ->toArray();
        $connection_collector = collect($all_connections);
        $all_executions = ProductExecution::query()
            ->select('id', 'execution_name')
            ->get()
            ->toArray();
        $executions_collector = collect($all_executions);
        $data = Product::query()
            ->where('product.brand_id', $request->id)
            ->join('product_category', 'product_category.id', '=', 'product.category_id')
            ->join('product_type', 'product_type.id', '=', 'product.type_id')
            ->join('brand', 'brand.id', '=', 'product.brand_id')
            ->leftJoin('product_attributes', function ($join) use($user) {
                $join->on('product_attributes.product_id', '=', 'product.id')->where('dealer_id', $user->id);
            })
            ->join('dealer_brand', function ($join) use ($user) {
                $join->on('brand.id', '=', 'dealer_brand.brand_id')
                    ->whereNull('dealer_brand.deleted_at')
                    ->where('dealer_brand.dealer_id', $user->id);
            })
            ->groupBy('product.id')
            ->select(
                'product.*',
                'brand.brand_name',
                'product_category.category_name',
                'product_type.type_name',
                DB::raw('group_concat(IFNULL(product_attributes.connection_id, "null")) as attribute_connection_ids'),
                DB::raw('group_concat(IFNULL(product_attributes.execution_id, "null")) as attribute_execution_ids'),
                DB::raw('group_concat(product_attributes.in_stock) as attribute_in_stocks'),
                DB::raw('group_concat(product_attributes.is_used) as attribute_is_useds'),
                DB::raw('group_concat(product_attributes.ready_for_demo) as attribute_ready_for_demos')
            )->orderByRaw('
            (CASE
                WHEN FIND_IN_SET(1, group_concat(product_attributes.in_stock)) > 0 THEN 1
                WHEN FIND_IN_SET(1, group_concat(product_attributes.is_used)) > 0 THEN 1
                WHEN FIND_IN_SET(1, group_concat(product_attributes.ready_for_demo)) > 0 THEN 1
                ELSE 2
            END), product.id');

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('url', function ($row) {
                return ($row->url) ? $row->url : "";
            })
            ->editColumn('retail', function ($row) {
                return ($row->retail) ? number_format($row->retail, 2, '.', '') : '0.00';
            })
            ->addColumn('product_attributes', function ($row) use ($connection_collector, $executions_collector) {
                $product_attributes = [];
                if ((isset($row->attribute_in_stocks) && !empty($row->attribute_in_stocks)) || (isset($row->attribute_is_useds) && !empty($row->attribute_is_useds)) || (isset($row->attribute_ready_for_demos) && !empty($row->attribute_ready_for_demos))) {
                    if (str_contains($row->attribute_connection_ids, ',')) {
                        $connections = explode(",", $row->attribute_connection_ids);
                    } else {
                        $connections = [];
                        $connections[] = $row->attribute_connection_ids;
                    }
                    if (str_contains($row->attribute_execution_ids, ',')) {
                        $executions = explode(",", $row->attribute_execution_ids);
                    } else {
                        $executions = [];
                        $executions[] = $row->attribute_execution_ids;
                    }
                    if (str_contains($row->attribute_in_stocks, ',')) {
                        $in_stocks = explode(",", $row->attribute_in_stocks);
                    } else {
                        $in_stocks = [];
                        $in_stocks[] = $row->attribute_in_stocks;
                    }
                    if (str_contains($row->attribute_is_useds, ',')) {
                        $is_useds = explode(",", $row->attribute_is_useds);
                    } else {
                        $is_useds = [];
                        $is_useds[] = $row->attribute_is_useds;
                    }
                    if (str_contains($row->attribute_ready_for_demos, ',')) {
                        $ready_for_demos = explode(",", $row->attribute_ready_for_demos);
                    } else {
                        $ready_for_demos = [];
                        $ready_for_demos[] = $row->attribute_ready_for_demos;
                    }
                    for ($i = 0; $i <= (count($ready_for_demos) - 1); $i++) {
                        if (isset($connections[$i]) && !empty($connections[$i]) && $connections[$i] != 'null') {
                            $product_attributes[$i]['connection'] = $connection_collector->where('id', $connections[$i]);
                        } else {
                            $product_attributes[$i]['connection'] = null;
                        }
                        if (isset($executions[$i]) && !empty($executions[$i]) && $executions[$i] != 'null') {
                            $product_attributes[$i]['execution'] = $executions_collector->where('id', $executions[$i]);
                        } else {
                            $product_attributes[$i]['execution'] = null;
                        }
                        $product_attributes[$i]['in_stocks'] = $in_stocks[$i];
                        $product_attributes[$i]['is_useds'] = $is_useds[$i];
                        $product_attributes[$i]['ready_for_demos'] = $ready_for_demos[$i];
                    }
                }
                return (isset($product_attributes) && !empty($product_attributes)) ? $product_attributes : '';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search')['value'];
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('brand_name', 'LIKE', "%$search%");
                        $w->orWhere('type_name', 'LIKE', "%$search%");
                        $w->orWhere('category_name', 'LIKE', "%$search%");
                        $w->orWhere('product_name', 'LIKE', "%$search%");
                        $w->orWhere('retail', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make();
        die();
    }
    public function addupdate(Request $request)
    {
        if ($request->ajax()) {
            $rules = array(
                'brand_id' => 'required',
            );

            $message = array();
            $validator = Validator::make($request->all(), $rules, $message);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $user = Auth::user();
                if ($request->brand_id) {
                    foreach ($request->brand_id as $key => $brand_id) {
                        $check = DealerBrand::query()
                            ->where('dealer_id', $user->id)
                            ->where('brand_id', $brand_id)
                            ->first();
                        if (empty($check)) {
                            $dealer_brand = new DealerBrand;
                            $dealer_brand->dealer_id = $user->id;
                            $dealer_brand->brand_id = $brand_id;
                            $dealer_brand->created_at = Carbon::now();
                            if ($dealer_brand->save()) {
                                $succssmsg = trans('translation.Brand added to your portfolio successfully');
                                $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
                            } else {
                                $result = ['status' => false, 'message' => trans('translation.Error in saving data'), 'data' => []];
                                return response()->json($result);
                                exit();
                            }
                        } else {
                            $result = ['status' => false, 'message' => trans('translation.This brand already there in your portfolio'), 'data' => []];
                        }
                    }
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Please select brand'), 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $brand = DealerBrand::where('id', $request->id);
        if ($brand->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }
    public function getDealerOfBrand(Request $request)
    {
        if (isset($request->product_ids) && !empty($request->product_ids)) {
            $login_user=Auth::user();
            $productss = $request->product_ids;
            sort($productss);
            // $customer_country=getCustomerCountry($login_user);
            $customer_country = $request->country;
            $dealerQuery = User::query()
                ->leftjoin('packages', 'users.package_id', '=', 'packages.id')
                ->join('dealer_brand', 'users.id', '=', 'dealer_brand.dealer_id')
                ->join('product', 'product.brand_id', '=', 'dealer_brand.brand_id')
                ->join('product_attributes', function ($q) {
                    $q->on('product_attributes.product_id', '=', 'product.id');
                    $q->on('product_attributes.dealer_id', '=', 'users.id');
                    $q->where('product_attributes.ready_for_demo', '1');
                })
                ->select(
                    'users.id',
                    'users.zipcode',
                    'users.shop_start_time',
                    'users.shop_end_time',
                    DB::raw('CONCAT(users.company_name, " - ", users.zipcode, " ", users.city) AS name'),
                    DB::raw('group_concat(IFNULL(product.id, "null")) as product_ids'),
                    DB::raw('group_concat(IFNULL(product.brand_id, "null")) as product_brand_ids')
                )
                ->whereIn('product.id', $productss)
                ->where('role_type', '=', 2)
                ->where('users.country', $customer_country)
                ->whereNull('dealer_brand.deleted_at');

            if($request->appo_type==1){
                $dealerQuery = $dealerQuery->where('packages.is_zoom_meeting', '=', 1);
            }

            $allDealer = $dealerQuery->groupBy('users.zipcode')->get()->toArray();
            $login_user = Auth::user();
            $post_user = substr($login_user->zipcode, 0, 1);

            $up_dealer = $down_dealer = [];

            if (isset($allDealer) && !empty($allDealer)) {
                foreach ($allDealer as $delaerk => $dealerv) {
                    if ((isset($request->appo_date) && !empty($request->appo_date)) && (isset($request->appo_time) && !empty($request->appo_time))) {
                        $appo_date = date('Y-m-d');
                        if($request->appo_date){
                            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->appo_date)->format('Y-m-d');
                            $appo_date = date('Y-m-d', strtotime($formattedDate));
                        }
                        // $appo_date = ($request->appo_date) ? date('Y-m-d', strtotime($request->appo_date)) : date('Y-m-d');
                        $appo_time = ($request->appo_time) ? date('H:i', strtotime($request->appo_time)) : date('H:i');
                        $shop_starttime = ($dealerv['shop_start_time']) ? date('H:i', strtotime($dealerv['shop_start_time'])) : date('H:i');
                        $shop_endtime = ($dealerv['shop_end_time']) ? date('H:i', strtotime($dealerv['shop_end_time'])) : date('H:i');

                        $appo_time_main = strtotime($appo_time);
                        $shop_starttime_main = strtotime($shop_starttime);
                        $shop_endtime_main = strtotime($shop_endtime);
                        $startTime = date("H:i", strtotime('-59 minutes', $appo_time_main));
                        $endTime = date("H:i", strtotime('+59 minutes', $appo_time_main));

                        $condition = [];
                        $condition[] = ['dealer_id', '=', $dealerv['id']];
                        $condition[] = [DB::raw('DATE(appo_date)'), '=', $appo_date];

                        $findAppointment = AppointmentDealer::where($condition)
                            ->where(function ($query) use ($startTime, $endTime, $appo_date) {
                                $query->orwhere(function ($q) use ($startTime, $endTime, $appo_date) {
                                    $q->whereTime('appo_time', '>=', \Carbon\Carbon::parse($startTime))
                                        ->whereTime('appo_time', '<=', \Carbon\Carbon::parse($endTime))
                                        ->where('appo_date', '=', $appo_date)
                                        ->whereIn('status', [1, 2]);
                                });
                                $query->orwhere(function ($q) use ($startTime, $endTime, $appo_date) {
                                    $q->whereTime('reschedule_appo_time', '>=', \Carbon\Carbon::parse($startTime))
                                        ->whereTime('reschedule_appo_time', '<=', \Carbon\Carbon::parse($endTime))
                                        ->where('reschedule_appo_date', '=', $appo_date)
                                        ->whereIn('status', [6, 7]);
                                });
                            })
                            ->first();
                    }
                    $productq = explode(',', $dealerv['product_ids']);
                    sort($productq);

                    if (implode(',', $productq) != implode(',', $productss) || (isset($findAppointment) && !empty($findAppointment)) || ((isset($request->appo_date) && !empty($request->appo_date) && isset($request->appo_time) && !empty($request->appo_time)) && ($appo_time_main < $shop_starttime_main || $appo_time_main > $shop_endtime_main))) {
                        unset($allDealer[$delaerk]);
                    } else {
                        if ((int) $post_user > (int) substr($dealerv['zipcode'], 0, 1)) {
                            $down_dealer[] = $dealerv;
                        } else {
                            $up_dealer[] = $dealerv;
                        }
                    }
                }
                $allDealer = array_merge($up_dealer, $down_dealer);
                if (isset($allDealer) && !empty($allDealer)) {
                    $finalDealerArray = [];
                    $f = 0;
                    foreach ($allDealer as $dvalue) {
                        $feedBackData = getDealerFeedbackData($dvalue['id']);
                        $finalDealerArray[$f] = $dvalue;
                        $finalDealerArray[$f]['average_rating'] = $feedBackData['average_rating'];
                        $f++;
                    }

                    $result = ['status' => true, 'dealer' => $finalDealerArray, "message" => ""];
                } else {
                    if (isset($findAppointment) && !empty($findAppointment) || ((isset($request->appo_date) && !empty($request->appo_date) && isset($request->appo_time) && !empty($request->appo_time)) && ($appo_time_main < $shop_starttime_main || $appo_time_main > $shop_endtime_main))) {
                        $result = ['status' => false, 'dealer' => [], "message" => trans('translation.There is no dealer available at the time and date selected'), 'appo_data' => ['findAppointment' => $findAppointment, 'appo_date' => $request->appo_date, 'appo_time_main' => $appo_time_main, 'shop_starttime_main' => $shop_starttime_main, 'shop_endtime_main' => $shop_endtime_main]];
                    } else {
                        $result = ['status' => false, 'dealer' => [], "message" => trans('translation.This brand dealer not found')];
                    }
                }
            } else {
                $result = ['status' => false, 'dealer' => [], "message" => trans('translation.dealer not found')];
            }
        } else {
            $result = ['status' => false, 'dealer' => [], "message" => trans('translation.Please select the product')];
        }
        return response()->json($result);
    }
}
