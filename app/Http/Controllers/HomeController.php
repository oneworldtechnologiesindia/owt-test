<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\User;
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\PurchaseEnquiry;
use App\Models\AppointmentDealer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $loginUser = Auth::user();
        $roles = User::$role;
        if ($loginUser->role_type == 1) {
            return view('home', compact('loginUser', 'roles'));
        } elseif ($loginUser->role_type == 2) {
            $stauslevels = User::$statulevels;
            $feedBackData = getDealerFeedbackData($loginUser->id);
            return view('dealer.dashboard', compact('loginUser', 'stauslevels', 'roles', 'feedBackData'));
        } else {
            $number_of_dealers = User::where(['role_type' => '2', 'deleted_at' => null])->count();
            $number_of_brands = Brand::whereNull('deleted_at')->count();
            $number_of_products = Product::whereNull('deleted_at')->count();
            return view('customer.dashboard', compact('loginUser', 'number_of_dealers', 'number_of_brands', 'number_of_products', 'roles'));
        }
    }

    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function profile()
    {
        $loginUser = Auth::user();
        $countries = User::$countries;
        if ($loginUser->role_type == 1) {
            return view('admin.profile', compact('loginUser'));
        } elseif ($loginUser->role_type == 2) {
            return view('dealer.profile', compact('loginUser', 'countries'));
        } else {
            return view('customer.profile', compact('loginUser', 'countries'));
        }
    }

    public function subscription()
    {
        return view('dealer.subscription');
    }

    public function postsub()
    {
        echo '<pre>';
        print_r($_POST);
        exit;
    }

    public function profileDetail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $loginUser = Auth::user();
            $loginUser->document_file = $loginUser->getDocumentFileUrl($loginUser->document_file);
            $loginUser->company_logo = $loginUser->getCompanyLogoUrl($loginUser->company_logo);
            $loginUser->shop_start_time = ($loginUser->shop_start_time) ? date('H:i', strtotime($loginUser->shop_start_time)) : "";
            $loginUser->shop_end_time = ($loginUser->shop_end_time) ? date('H:i', strtotime($loginUser->shop_end_time)) : "";

            $loginUser->contract_startdate = ($loginUser->contract_startdate) ? date('F d, Y', strtotime($loginUser->contract_startdate)) : "";
            $loginUser->contract_enddate = ($loginUser->contract_enddate) ? date('F d, Y', strtotime($loginUser->contract_enddate)) : "";
            $loginUser->contract_canceldate = ($loginUser->contract_canceldate) ? date('F d, Y', strtotime($loginUser->contract_canceldate)) : "";

            $contract_status = 'running';
            if ($loginUser->contract_enddate > date("Y-m-d", strtotime("+13 month", strtotime($loginUser->contract_startdate))) && $loginUser->contract_canceldate != null) {
                $contract_status = 'runningcancel';
            } elseif ($loginUser->contract_enddate < date("Y-m-d") || $loginUser->contract_canceldate != null) {
                $contract_status = 'cancel';
            } else {
                $contract_status = 'running';
            }

            $loginUser->birth_date = date('d.m.Y', strtotime($loginUser->birth_date));

            $result = ['status' => true, 'message' => '', 'data' => $loginUser, 'contractStatus' => $contract_status];
        }
        return response()->json($result);
        exit();
    }
    public function updateProfile(Request $request)
    {
        if ($request->ajax()) {
            $result = ['status' => true, 'message' => ""];
            $loginUser = Auth::user();
            if (!isset($loginUser->id)) {
                $result = ['status' => false, 'message' => trans('translation.Data update fail'), 'data' => []];
            }
            $role_type = (isset($loginUser->role_type)) ? $loginUser->role_type : 3;
            $messages = array();
            if ($role_type == 2) {
                $rules = array(
                    'company_name' => 'required|string|max:100',
                    'street' => 'required',
                    'house_number' => 'required|max:10',
                    'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                    'city' => 'required|string',
                    // 'country' => 'required|string',
                    'vat_number' => 'required|string|max:50',
                    'shop_start_time' => 'required',
                    'gender' => 'required',
                    'shop_end_time' => 'required|after:shop_start_time',
                    'email' => 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $loginUser->id . ',id,deleted_at,NULL',
                    'phone' => "required|string|min:10",
                    'bank_name' => 'required',
                    'iban' => 'required',
                    'bic' => 'required',
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'vat' => 'required|numeric:max:5|gt:0'
                );
                $messages['company_name.required'] = trans('validation.custom.customer_company_name.required_if');
                $messages['company_name.max'] = trans('validation.custom.customer_company_name.max');
                $messages['vat.numeric'] = trans('validation.custom.vat_number.numeric');
                $messages['vat.gt'] = trans('validation.custom.vat_number.gt');
            } else if ($role_type == 3) {
                $rules = array(
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'street' => 'required',
                    'gender' => 'required',
                    'house_number' => 'required|max:10',
                    'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                    'city' => 'required|string',
                    // 'country' => 'required|string',
                    'email' => 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $loginUser->id . ',id,deleted_at,NULL',
                    'phone' => "required|string|min:10",
                    'birth_date' => 'required|before:18 years ago',
                    'salutation'  => 'required',
                    'customer_company_name' => 'required_if:salutation,==,firma|nullable|string|max:100',
                    'customer_vat_number' => 'required_if:salutation,==,firma|nullable|string|max:50'
                );
                $messages['customer_company_name.required_if'] = trans('validation.custom.customer_company_name.required_if');
                $messages['customer_company_name.max'] = trans('validation.custom.customer_company_name.max');
            } else {
                $rules = array(
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'email' => 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $loginUser->id . ',id,deleted_at,NULL',
                );
            }


            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                if ($role_type == 2 || $role_type == 3) {
                    $phone = str_replace(array("(", ")", "-", " "), array("", "", "", "", ""), $request->phone);
                    $loginUser->phone = $phone;
                }

                if ($role_type == 2) {
                    if ($request->hasFile('document_file')) {
                        if ($loginUser->document_file != '') {
                            $oldfileExists = storage_path('app/public/document_file/') . $loginUser->document_file;
                            if (file_exists($oldfileExists)) {
                                unlink($oldfileExists);
                            }
                        }
                        $filename = $request->document_file->hashName();
                        $request->document_file->storeAs('document_file', $filename, 'public');
                        $loginUser->document_file = $filename;
                    }
                    if ($request->hasFile('company_logo')) {
                        if ($loginUser->company_logo != '') {
                            $oldfileExists = storage_path('app/public/company_logo/') . $loginUser->company_logo;
                            if (file_exists($oldfileExists)) {
                                unlink($oldfileExists);
                            }
                        }
                        $filename = $request->company_logo->hashName();
                        $request->company_logo->storeAs('company_logo', $filename, 'public');
                        $loginUser->company_logo = $filename;
                    }
                    $loginUser->company_name = ($request->company_name) ? $request->company_name : NULL;
                    $loginUser->vat_number = ($request->vat_number) ? $request->vat_number : NULL;
                    $loginUser->bank_name = ($request->bank_name) ? $request->bank_name : NULL;
                    $loginUser->iban = ($request->iban) ? $request->iban : NULL;
                    $loginUser->bic = ($request->bic) ? $request->bic : NULL;
                    $loginUser->shop_start_time = ($request->shop_start_time) ? date('H:i', strtotime($request->shop_start_time)) : NULL;
                    $loginUser->shop_end_time = ($request->shop_end_time) ? date('H:i', strtotime($request->shop_end_time)) : NULL;
                    $loginUser->vat = ($request->vat) ? $request->vat : NULL;
                }
                if ($role_type == 3) {
                    $loginUser->company_name = ($request->customer_company_name) ? $request->customer_company_name : NULL;
                    $loginUser->vat_number = ($request->customer_vat_number) ? $request->customer_vat_number : NULL;
                }
                $loginUser->street = ($request->street) ? $request->street : NULL;
                $loginUser->salutation = ($request->salutation) ? $request->salutation : NULL;
                $loginUser->house_number = ($request->house_number) ? $request->house_number : NULL;
                $loginUser->zipcode = ($request->zipcode) ? $request->zipcode : NULL;
                $loginUser->city = ($request->city) ? $request->city : NULL;
                // if(!empty($request->country) && empty($loginUser->country)){
                //     $loginUser->country = ($request->country) ? $request->country : NULL;
                // }
                $loginUser->first_name = $request->first_name;
                $loginUser->last_name = $request->last_name;
                $loginUser->email = $request->email;
                $loginUser->birth_date = ($request->birth_date) ? date('Y-m-d', strtotime($request->birth_date)) : NULL;
                $loginUser->gender = $request->gender;
                $loginUser->updated_at = Carbon::now();
                if ($loginUser->save()) {
                    $result = ['status' => true, 'message' => trans('translation.Profile update successfully')];
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Profile update fail')];
                }
            }
            return response()->json($result);
            exit();
        } else {
            return Redirect::to("home");
        }
    }

    public function dashboardDataInfo(User $user)
    {
        $loginUser = Auth::user();
        $data = [];
        $today = Carbon::now();
        if (!isset($loginUser->id))
            return response()->json(['status' => true, 'data' => $data], 200);

        if ($loginUser->role_type == 1) {
            $number_of_dealers = User::where(['role_type' => '2', 'deleted_at' => null])->count();
            $number_of_brands = Brand::whereNull('deleted_at')->count();
            $number_of_products = Product::whereNull('deleted_at')->count();

            $all_order_query =  Order::query()
                ->whereNull('deleted_at')
                ->where('status', '!=', 3)
                ->where(function ($condition) use ($today) {
                    $condition->orWhereYear('created_at', '>=', $today->copy()->year);
                    $condition->orWhereYear('created_at', '<=',  $today->copy()->subYear()->year);
                })
                ->get();

            $all_order = collect($all_order_query);

            // Yearly sales calucalte
            $sales_yearly_filter = $all_order->filter(function ($item) use ($today) {
                return (data_get($item, 'created_at') >= $today->copy()->startOfYear()) && (data_get($item, 'created_at') <= $today->copy()->endOfYear());
            });

            $sales_previous_yearly_filter = $all_order->filter(function ($item) use ($today) {
                return (data_get($item, 'created_at') >= $today->copy()->subYear()->startOfYear()) && (data_get($item, 'created_at') <= $today->copy()->subYear()->endOfYear());
            });

            $yearly_earning_amount = $sales_yearly_filter->sum('amount');
            $yearly_previous_earning_amount = $sales_previous_yearly_filter->sum('amount');

            if ($yearly_previous_earning_amount > 0 && $yearly_earning_amount > 0) {
                $yearly_earning_percentage = ($yearly_earning_amount - $yearly_previous_earning_amount) / $yearly_previous_earning_amount * 100;
            } else {
                if ($yearly_earning_amount > 0) {
                    $yearly_earning_percentage = 100;
                } elseif ($yearly_earning_amount == $yearly_previous_earning_amount) {
                    $yearly_earning_percentage = 0;
                } else {
                    $yearly_earning_percentage = -100;
                }
            }

            $data = [
                'number-of-dealers' => $number_of_dealers,
                'number-of-brands' => $number_of_brands,
                'number-of-products' => $number_of_products,
                'yearly-earning-amount' => number_format($yearly_earning_amount, 2),
                'yearly-earning-percentage' => round($yearly_earning_percentage, 2)
            ];
        } elseif ($loginUser->role_type == 2) {
            $currency = getDealerCurrencyType(Auth::user());
            $all_purchase_enquiry_query =  PurchaseEnquiry::query()
                ->select(
                    DB::raw('IFNULL(count(purchase_enquiry.id), 0) as total'),
                    DB::raw('MONTH(purchase_enquiry.created_at) as month')
                )
                ->join('dealer_purchase_enquiry', 'dealer_purchase_enquiry.customer_enquiry_id', '=', 'purchase_enquiry.id')
                ->where(['purchase_enquiry.status' => '1', 'purchase_enquiry.deleted_at' => null])
                ->whereYear('purchase_enquiry.created_at', $today->copy()->year)
                ->where('dealer_purchase_enquiry.dealer_id', $loginUser->id)
                ->whereNull('dealer_purchase_enquiry.deleted_at')
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            $all_purchase_enquiry = collect($all_purchase_enquiry_query);
            $purchase_enquiry_monthly_collect = $all_purchase_enquiry->where('month', $today->copy()->month)->first();
            $purchase_enquiry_monthly = (isset($purchase_enquiry_monthly_collect) && !empty($purchase_enquiry_monthly_collect)) ? $purchase_enquiry_monthly_collect->total : 0;

            $all_order_query =  Order::query()
                ->whereNull('deleted_at')
                ->where('dealer_id', $loginUser->id)
                ->where('status', '!=', 3)
                ->where(function ($condition) use ($today) {
                    $condition->orWhereYear('created_at', '>=', $today->copy()->year);
                    $condition->orWhereYear('created_at', '<=',  $today->copy()->subYear()->year);
                })
                ->get();

            $all_order = collect($all_order_query);

            // Monthly sales calucalte
            $sales_monthly_filter = $all_order->filter(function ($item) use ($today) {
                return (data_get($item, 'created_at') >= $today->copy()->firstOfMonth()) && (data_get($item, 'created_at') <= $today->copy()->lastOfMonth());
            });

            $sales_monthly = $sales_monthly_filter->count();

            $sales_previous_monthly_filter = $all_order->filter(function ($item) use ($today) {
                return (data_get($item, 'created_at') >= $today->copy()->subMonth()->firstOfMonth()) && (data_get($item, 'created_at') <= $today->copy()->subMonth()->lastOfMonth());
            });

            $monthly_earning_amount = $sales_monthly_filter->sum('amount');
            $monthly_previous_earning_amount = $sales_previous_monthly_filter->sum('amount');

            if ($monthly_previous_earning_amount > 0 && $monthly_earning_amount > 0) {
                $monthly_earning_percentage = ($monthly_earning_amount - $monthly_previous_earning_amount) / $monthly_previous_earning_amount * 100;
            } else {
                if ($monthly_earning_amount > 0) {
                    $monthly_earning_percentage = 100;
                } elseif ($monthly_earning_amount == $monthly_previous_earning_amount) {
                    $monthly_earning_percentage = 0;
                } else {
                    $monthly_earning_percentage = -100;
                }
            }

            // Yearly sales calucalte
            $sales_yearly_filter = $all_order->filter(function ($item) use ($today) {
                return (data_get($item, 'created_at') >= $today->copy()->startOfYear()) && (data_get($item, 'created_at') <= $today->copy()->endOfYear());
            });

            $sales_yearly = $sales_yearly_filter->count();

            $sales_previous_yearly_filter = $all_order->filter(function ($item) use ($today) {
                return (data_get($item, 'created_at') >= $today->copy()->subYear()->startOfYear()) && (data_get($item, 'created_at') <= $today->copy()->subYear()->endOfYear());
            });

            $yearly_earning_amount = $sales_yearly_filter->sum('amount');
            $yearly_previous_earning_amount = $sales_previous_yearly_filter->sum('amount');

            if ($yearly_previous_earning_amount > 0 && $yearly_earning_amount > 0) {
                $yearly_earning_percentage = ($yearly_earning_amount - $yearly_previous_earning_amount) / $yearly_previous_earning_amount * 100;
            } else {
                if ($yearly_earning_amount > 0) {
                    $yearly_earning_percentage = 100;
                } elseif ($yearly_earning_amount == $yearly_previous_earning_amount) {
                    $yearly_earning_percentage = 0;
                } else {
                    $yearly_earning_percentage = -100;
                }
            }

            // calculate status level
            $required_level_amount = 0.00;
            $left_level_percentage = $completed_level_percentage = 0;
            switch ($yearly_earning_amount) {
                case ($yearly_earning_amount < 75000):
                    $required_level_amount = 75000 - $yearly_earning_amount;
                    $completed_level_percentage = ($yearly_earning_amount * 100) / 75000;
                    $left_level_percentage = 100 - $completed_level_percentage;
                    break;

                case ($yearly_earning_amount < 100000):
                    $required_level_amount = 100000 - $yearly_earning_amount;
                    $completed_level_percentage = ($yearly_earning_amount * 100) / 100000;
                    $left_level_percentage = 100 - $completed_level_percentage;
                    break;

                case ($yearly_earning_amount < 150000):
                    $required_level_amount = 150000 - $yearly_earning_amount;
                    $completed_level_percentage = ($yearly_earning_amount * 100) / 150000;
                    $left_level_percentage = 100 - $completed_level_percentage;
                    break;

                default:
                    $required_level_amount = 0.00;
                    $completed_level_percentage = 100;
                    $left_level_percentage = 0;
                    break;
            }

            $all_appointment_query =  AppointmentDealer::query()
                ->select(
                    DB::raw('count(id) as total'),
                    DB::raw("(CASE WHEN status = '6' or status = '7' THEN MONTH(`reschedule_appo_date`) ELSE MONTH(`appo_date`) END) as month"),
                    DB::raw("(CASE WHEN status = '6' or status = '7' THEN `reschedule_appo_date` ELSE `appo_date` END) as date")
                )
                ->whereNull('deleted_at')
                ->where('dealer_id', $loginUser->id)
                ->where(function ($query) use ($today) {
                    $query->orwhere(function ($q) use ($today) {
                        $q->whereIn('status', [1, 2])
                            ->whereYear('appo_date', $today->copy()->year);
                    });
                    $query->orwhere(function ($q) use ($today) {
                        $q->whereIn('status', [6, 7])
                            ->whereYear('reschedule_appo_date', $today->copy()->year);
                    });
                })
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            $all_appointment = collect($all_appointment_query);
            $appointments_monthly_collect = $all_appointment->where('month', $today->copy()->month)->first();
            $appointments_monthly = (isset($appointments_monthly_collect) && !empty($appointments_monthly_collect)) ? $appointments_monthly_collect->total : 0;

            $data = [
                'allPurchaseEnquiry' => $all_purchase_enquiry,
                'allOrder' => $all_order,
                'allAppointment' => $all_appointment,
                'purchase-enquiry-monthly' => $purchase_enquiry_monthly,
                'sales-monthly' => $sales_monthly,
                'appointments-monthly' => $appointments_monthly,
                'monthly-earning-amount' => formatCurrencyOutput($monthly_earning_amount, $currency, true, 'before'),
                'monthly-earning-percentage' => round($monthly_earning_percentage, 2),
                'yearly-earning-amount' => formatCurrencyOutput($yearly_earning_amount, $currency, true, 'before'),
                'yearly-earning-percentage' => round($yearly_earning_percentage, 2),
                'required-level-amount' => formatCurrencyOutput($required_level_amount, $currency, true, 'before'),
                'completed-level-percentage' => round($completed_level_percentage, 2),
                'left-level-percentage' => round($left_level_percentage, 2)
            ];
        } else {
            $number_of_dealers = User::where(['role_type' => '2', 'deleted_at' => null])->count();
            $number_of_brands = Brand::whereNull('deleted_at')->count();
            $number_of_products = Product::whereNull('deleted_at')->count();

            $ads = [
                'size_1' => Ad::where('status', 1)->where('size', 1)->inRandomOrder()->limit(3)->get(),
                'size_2' => Ad::where('status', 1)->where('size', 2)->inRandomOrder()->first(),
                'size_3' => Ad::where('status', 1)->where('size', 3)->inRandomOrder()->first(),
                'size_4' => Ad::where('status', 1)->where('size', 4)->inRandomOrder()->first(),
            ];

            $data = [
                'number-of-dealers' => $number_of_dealers,
                'number-of-brands' => $number_of_brands,
                'number-of-products' => $number_of_products,
            ];
        }

        return response()->json(['status' => true, 'data' => ['data' => $data, 'ads' => $ads]], 200);
    }

    public function summaryChartFilter(Request $request)
    {
        $loginUser = Auth::user();
        $data = [];

        if (!isset($loginUser->id))
            return response()->json(['status' => true, 'data' => $data], 200);

        $chartFilter = (isset($request->type) && !empty($request->type)) ? $request->type : 'year';
        $today = Carbon::now();

        $years = [];
        $months = explode(',', trans('translation.Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec'));
        $weeks = [];
        $categories = [];

        $purchase_enquiries_query =  PurchaseEnquiry::query()
            ->join('dealer_purchase_enquiry', 'dealer_purchase_enquiry.customer_enquiry_id', '=', 'purchase_enquiry.id')
            ->where(['purchase_enquiry.status' => '1', 'purchase_enquiry.deleted_at' => null])
            ->whereNull('dealer_purchase_enquiry.deleted_at');

        $sales_query = Order::query()
            ->whereNull('deleted_at')
            ->where('status', '!=', 3);

        $appointments_query =  AppointmentDealer::query()
            ->whereNull('deleted_at');

        if (isset($loginUser->id) && $loginUser->role_type == 2) {
            $purchase_enquiries_query->where('dealer_purchase_enquiry.dealer_id', $loginUser->id);
            $sales_query->where('dealer_id', $loginUser->id);
            $appointments_query->where('dealer_id', $loginUser->id);
        }

        if ($chartFilter == 'year') {
            $purchase_enquiries = $purchase_enquiries_query->select(
                DB::raw('IFNULL(count(purchase_enquiry.id), 0) as total'),
                DB::raw('YEAR(purchase_enquiry.created_at) as year')
            )
                ->groupBy('year')
                ->get()
                ->pluck('total', 'year')
                ->toArray();

            $sales = $sales_query->select(
                DB::raw('count(id) as total'),
                DB::raw('YEAR(created_at) as year')
            )
                ->groupBy('year')
                ->get()
                ->pluck('total', 'year')
                ->toArray();

            $appointments = $appointments_query->select(
                DB::raw('count(id) as total'),
                DB::raw("(CASE WHEN status = '6' or status = '7' THEN YEAR(`reschedule_appo_date`) ELSE YEAR(`appo_date`) END) as year")
            )
                ->whereIn('status', [1, 2, 6, 7])
                ->groupBy('year')
                ->get()
                ->pluck('total', 'year')
                ->toArray();

            $years_str = '';
            if (count($purchase_enquiries) > 0) {
                $years_str .= implode(',', array_keys($purchase_enquiries));
            }
            if (count($sales) > 0) {
                $years_str .= ',' . implode(',', array_keys($sales));
            }
            if (count($appointments) > 0) {
                $years_str .= ',' . implode(',', array_keys($appointments));
            }

            $years = explode(',', $years_str);
            $years = array_filter($years);
            $years = array_unique($years);

            if (isset($years) && empty($years)) {
                $years[] = $today->copy()->year;
            } else {
                $years = implode(',', $years);
                $years = explode(',', $years);
            }
        }

        if ($chartFilter == 'month') {
            $purchase_enquiries = $purchase_enquiries_query->select(
                DB::raw('IFNULL(count(purchase_enquiry.id), 0) as total'),
                DB::raw('MONTH(purchase_enquiry.created_at) as month')
            )
                ->whereYear('purchase_enquiry.created_at', $today->copy()->year)
                ->groupBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray();

            $sales = $sales_query->select(
                DB::raw('count(id) as total'),
                DB::raw('MONTH(created_at) as month')
            )
                ->whereYear('created_at', $today->copy()->year)
                ->groupBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray();

            $appointments = $appointments_query->select(
                DB::raw('count(id) as total'),
                DB::raw("(CASE WHEN status = '6' or status = '7' THEN MONTH(`reschedule_appo_date`) ELSE MONTH(`appo_date`) END) as month")
            )
                ->where(function ($query) use ($today) {
                    $query->orwhere(function ($q) use ($today) {
                        $q->whereIn('status', [1, 2])
                            ->whereYear('appo_date', $today->copy()->year);
                    });
                    $query->orwhere(function ($q) use ($today) {
                        $q->whereIn('status', [6, 7])
                            ->whereYear('reschedule_appo_date', $today->copy()->year);
                    });
                })
                ->groupBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray();

            for ($i = 1; $i <= 12; $i++) {
                if (!isset($purchase_enquiries[$i])) {
                    $purchase_enquiries[$i] = 0;
                }
                if (!isset($sales[$i])) {
                    $sales[$i] = 0;
                }
                if (!isset($appointments[$i])) {
                    $appointments[$i] = 0;
                }
            }
        }

        if ($chartFilter == 'week') {
            $purchase_enquiries = $purchase_enquiries_query->select(
                DB::raw('IFNULL(count(purchase_enquiry.id), 0) as total'),
                DB::raw('WEEK(purchase_enquiry.created_at) as week')
            )
                ->whereDate('purchase_enquiry.created_at', '>=', $today->copy()->firstOfMonth())
                ->whereDate('purchase_enquiry.created_at', '<=', $today->copy()->lastOfMonth())
                ->groupBy('week')
                ->get()
                ->pluck('total', 'week')
                ->toArray();

            $sales = $sales_query->select(
                DB::raw('count(id) as total'),
                DB::raw('WEEK(created_at) as week')
            )
                ->whereDate('created_at', '>=', $today->copy()->firstOfMonth())
                ->whereDate('created_at', '<=', $today->copy()->lastOfMonth())
                ->groupBy('week')
                ->get()
                ->pluck('total', 'week')
                ->toArray();

            $appointments = $appointments_query->select(
                DB::raw('count(id) as total'),
                DB::raw("(CASE WHEN status = '6' or status = '7' THEN WEEK(`reschedule_appo_date`) ELSE WEEK(`appo_date`) END) as week")
            )
                ->where(function ($query) use ($today) {
                    $query->orwhere(function ($q) use ($today) {
                        $q->whereIn('status', [1, 2])
                            ->whereDate('appo_date', '>=', $today->copy()->firstOfMonth())
                            ->whereDate('appo_date', '<=', $today->copy()->lastOfMonth());
                    });
                    $query->orwhere(function ($q) use ($today) {
                        $q->whereIn('status', [6, 7])
                            ->whereDate('reschedule_appo_date', '>=', $today->copy()->firstOfMonth())
                            ->whereDate('reschedule_appo_date', '<=', $today->copy()->lastOfMonth());
                    });
                })
                ->groupBy('week')
                ->get()
                ->pluck('total', 'week')
                ->toArray();

            $count = 0;
            $weekstart = ($today->copy()->firstOfMonth()->weekOfYear > $today->copy()->lastOfMonth()->weekOfYear) ? 0 : $today->copy()->firstOfMonth()->weekOfYear;
            for ($i = $weekstart; $i <= $today->copy()->lastOfMonth()->weekOfYear; $i++) {
                $count++;
                $weeks[] = $today->copy()->setISODate($today->copy()->year, $i)->startOfWeek()->format('d.m.y') . '-' . $today->copy()->setISODate($today->copy()->year, $i)->endOfWeek()->format('d.m.y');
                if (!isset($purchase_enquiries[$i])) {
                    $purchase_enquiries[$i] = 0;
                }
                if (!isset($sales[$i])) {
                    $sales[$i] = 0;
                }
                if (!isset($appointments[$i])) {
                    $appointments[$i] = 0;
                }
            }
        }

        ksort($purchase_enquiries);
        ksort($sales);
        ksort($appointments);

        if ($chartFilter == 'year') {
            $categories = $years;
        }
        if ($chartFilter == 'month') {
            $categories = $months;
        }
        if ($chartFilter == 'week') {
            $categories = $weeks;
        }

        $data = [
            'purchaseenquiries' => implode(',', $purchase_enquiries),
            'sales' => implode(',', $sales),
            'appointments' => implode(',', $appointments),
            'categories' => $categories
        ];

        $names = [
            'Purchase_Enquiries' => trans('translation.Purchase_Enquiries'),
            'Sales' => trans('translation.Sales'),
            'Appointments' => trans('translation.Appointments')
        ];

        return response()->json(['status' => true, 'data' => $data, 'names' => $names], 200);
    }

    public function topSalesChartFilter(Request $request)
    {
        $product_array = array();
        $brand_array = array();
        $product_type_array = array();
        $final_highest_data = array();
        $chartFilter = (isset($request->type) && !empty($request->type)) ? $request->type : 'year';
        $currentYear = date('Y');
        $currentMonth = date('m');
        $main_query =  Order::query()
            ->select(DB::raw('SUM(purchase_enquiry_products.qty) as total_qty'), 'purchase_enquiry_products.id', 'product.product_name', 'brand.brand_name', 'product_type.type_name')
            ->leftJoin('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
            ->leftJoin('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id')->whereColumn('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
            })
            ->where('orders.dealer_id', Auth::user()->id)
            ->where('orders.status', '!=', 3)
            ->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id')
            ->join('brand', 'brand.id', '=', 'product.brand_id')
            ->join('product_type', 'product_type.id', '=', 'product.type_id');

        if ($chartFilter == 'month') {
            $main_query = $main_query->whereMonth('orders.created_at', $currentMonth);
        } else {
            $main_query = $main_query->whereYear('orders.created_at', $currentYear);
        }

        $get_highest_saling_items = $main_query->orderBy('purchase_enquiry_products.qty', 'DESC')
            ->groupBy('purchase_enquiry_products.product_id')
            ->get()->take(5)->toArray();
        if (count($get_highest_saling_items)) {
            foreach ($get_highest_saling_items as $key => $value) {
                if (isset($product_array[$value['product_name']])) {
                    $product_array[$value['product_name']]['total_qty'] = $product_array[$value['product_name']]['total_qty'] + $value['total_qty'];
                } else {
                    $product_array[$value['product_name']] = $value;
                }

                if (isset($brand_array[$value['brand_name']])) {
                    $brand_array[$value['brand_name']]['total_qty'] = $brand_array[$value['brand_name']]['total_qty'] + $value['total_qty'];
                } else {
                    $brand_array[$value['brand_name']] = $value;
                }

                if (isset($product_type_array[$value['type_name']])) {
                    $product_type_array[$value['type_name']]['total_qty'] = $product_type_array[$value['type_name']]['total_qty'] + $value['total_qty'];
                } else {
                    $product_type_array[$value['type_name']] = $value;
                }
            }
            usort($product_array, function ($a, $b) {
                return $b['total_qty'] - $a['total_qty'];
            });
            usort($brand_array, function ($a, $b) {
                return $b['total_qty'] - $a['total_qty'];
            });
            usort($product_type_array, function ($a, $b) {
                return $b['total_qty'] - $a['total_qty'];
            });
        }
        $final_highest_data =  [$product_array, $brand_array, $product_type_array];
        return response()->json(['status' => true, 'data' => $final_highest_data], 200);
    }

    public function countryDealerFilter(Request $request)
    {
        $dealers = '';
        if (isset($request->country) && !empty($request->country)) {
            $dealers = User::query()
                ->select(
                    'id',
                    'users.company_name as text'
                )
                ->where('country', $request->country)
                ->where('role_type', 2)
                ->get()
                ->toArray();
        } else {
            $dealers = User::query()
                ->select(
                    'id',
                    'users.company_name as text'
                )
                ->where('role_type', 2)
                ->get()
                ->toArray();
        }

        $result = ['status' => true, 'message' => '', 'data' => $dealers];
        return response()->json($result);
    }
}
