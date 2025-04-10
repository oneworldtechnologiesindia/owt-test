<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\MailerFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\AppointmentDealer;
use App\Models\Order;

class DealerController extends Controller
{
    protected $mailer;
    public function __construct(MailerFactory $mailer)
    {
        $this->middleware(['auth']);
        $this->mailer = $mailer;
    }

    public function index()
    {
        $status = User::$status;
        return view('dealer.index', compact('status'));
    }
    public function create()
    {
        $model = new User();
        $countries = User::$countries;
        return view('dealer.form', compact('model', 'countries'));
    }
    public function edit($id)
    {
        $model = User::find($id);
        $countries = User::$countries;
        if (isset($model->id)) {
            if (isset($model->document_file) && !empty($model->document_file))
                $model->document_file = $model->getDocumentFileUrl($model->document_file);

            if (isset($model->company_logo) && !empty($model->company_logo))
                $model->company_logo = $model->getCompanyLogoUrl($model->company_logo);
            return view('dealer.form', compact('model', 'countries'));
        } else {
            return abort(404);
        }
    }
    public function get(Request $request)
    {
        $role = User::$role;
        $status = User::$status;

        $data = User::query()->where('role_type', '=', 2)
            ->select('users.*', DB::raw('CONCAT(users.first_name," ",users.last_name) AS name'));

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })
            ->editColumn('created_at', function ($row) use ($status) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    if ((bool)strtotime($search)) {
                        $instance->whereRaw("DATE_FORMAT(created_at, '%d.%m.%Y') LIKE '%{$search}%'");
                    } else {
                        $instance->where(function ($w) use ($search) {
                            $w->orWhere('first_name', 'LIKE', "%$search%")
                                ->orWhere('last_name', 'LIKE', "%$search%")
                                ->orWhere('company_name', 'LIKE', "%$search%")
                                ->orWhere('email', 'LIKE', "%$search%")
                                ->orWhere('phone', 'LIKE', "%$search%");
                        });
                    }
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }
    public function updatefield($id)
    {
        $user = User::where('id', $id)->first();
        $last_status = (isset($user->id)) ? $user->status : 0;
        if ($user) {
            $user->status = (in_array($user->status, [0, 2])) ? 1 : 2;
            if ($user->save()) {
                if ($last_status == 0) {
                    $subject = trans('translation.dealerregister_email_subject');
                    $body = trans('translation.dealerregister_email_body', ['url' => '<a href="' . route('dealer.home') . '">' . route('dealer.home') . '</a>', 'emailto' => '<a href="mailto:' . env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') . '">' . env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') . '</a>']);
                    $this->mailer->sendGeneralEmail($user, $subject, $body, $documents = []);
                }
            }
        }
        if (isset($user->id) && $last_status == 0) {
            $user = User::where('id', $id)->first();
        }
        $result = ['status' => true, 'message' => trans('translation.Status Changed successfully')];
        return response()->json($result);
    }
    public function addupdate(Request $request)
    {
        if ($request->ajax()) {
            $contract_startdate = $contract_enddate = null;
            $rules = array(
                'company_name' => 'required|string|max:100',
                'street' => 'required',
                'house_number' => 'required|max:10',
                'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                'city' => 'required|string',
                'country' => 'required|string',
                'vat_number' => 'required|string|max:50',
                'vat' => 'required|numeric:max:5|gt:0',
                'shop_start_time' => 'required',
                'shop_end_time' => 'required|after:shop_start_time',
                'phone' => "required|string|min:10",
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'bank_name' => 'required',
                'iban' => 'required',
                'bic' => 'required',
            );
            if ($request->id) {
                $rules['email'] = 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $request->id . ',id,deleted_at,NULL';
                // unique:users,email,' . $request->user_id . ',id,deleted_at,NULL
            } else {
                $rules['email'] = 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,NULL,id,deleted_at,NULL';
                $rules['password'] = 'required|string|min:8|confirmed';
                $rules['password_confirmation'] = 'required';

                $contract_startdate = Carbon::now()->format('Y-m-d');
                $contract_enddate  = Carbon::now()->addMonths(13)->format('Y-m-d');
            }
            $messsages = array();

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Dealer added successfully');
                if ($request->id) {
                    $model = User::where('id', $request->id)->first();
                    if ($model) {
                        $user = $model;
                        $succssmsg = trans('translation.Dealer updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $user = new User;
                    $user->role_type = 2;
                    $user->status = 1;

                    $user->contract_startdate = $contract_startdate;
                    $user->contract_enddate = $contract_enddate;
                }

                $phone = str_replace(array("(", ")", "-", " "), array("", "", "", "", ""), $request->phone);
                $user->phone = $phone;

                $user->display_id = createDisplayId(2);
                $user->company_name = ($request->company_name) ? $request->company_name : NULL;
                $user->vat_number = ($request->vat_number) ? $request->vat_number : NULL;
                $user->vat = ($request->vat) ? $request->vat : NULL;
                $user->street = ($request->street) ? $request->street : NULL;
                $user->house_number = ($request->house_number) ? $request->house_number : NULL;
                $user->zipcode = ($request->zipcode) ? $request->zipcode : NULL;
                $user->city = ($request->city) ? $request->city : NULL;
                $user->country = ($request->country) ? $request->country : NULL;
                $user->bank_name = ($request->bank_name) ? $request->bank_name : NULL;
                $user->iban = ($request->iban) ? $request->iban : NULL;
                $user->bic = ($request->bic) ? $request->bic : NULL;
                $user->shop_start_time = ($request->shop_start_time) ? date('H:i', strtotime($request->shop_start_time)) : NULL;
                $user->shop_end_time = ($request->shop_end_time) ? date('H:i', strtotime($request->shop_end_time)) : NULL;

                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->email_verified_at = Carbon::now();
                $user->updated_at = Carbon::now();
                if ($request->password != "") {
                    $user->password = Hash::make($request->password);
                }
                if ($user->save()) {
                    if (!$request->id) {
                        $subject = trans('translation.dealerregister_email_subject');
                        $body = trans('translation.dealerregister_email_body', ['url' => '<a href="' . route('dealer.home') . '">' . route('dealer.home') . '</a>', 'emailto' => '<a href="mailto:' . env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') . '">' . env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') . '</a>']);
                        $this->mailer->sendGeneralEmail($user, $subject, $body, $documents = []);

                        $subject = trans('translation.admin_dealerregister_email_subject');
                        $body = trans('translation.admin_dealerregister_email_body', ['name' => $user->first_name . ' ' . $user->last_name, 'url' => '<a href="' . route('dealers') . '">' . route('dealers') . '</a>']);
                        $adminuser = User::where('role_type', 1)->whereNull('deleted_at')->first();
                        $this->mailer->sendGeneralEmail($adminuser, $subject, $body, $documents = []);
                    }
                    $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Error in saving data'), 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
        }
        return response()->json($result);
    }
    public function detail(Request $request)
    {
        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            $user = User::find($request->id);
            $user->document_file = $user->getDocumentFileUrl($user->document_file);
            $user->company_logo = $user->getCompanyLogoUrl($user->company_logo);
            $user->shop_start_time = ($user->shop_start_time) ? date('H:i', strtotime($user->shop_start_time)) : "";
            $user->shop_end_time = ($user->shop_end_time) ? date('H:i', strtotime($user->shop_end_time)) : "";

            $result = ['status' => true, 'message' => '', 'data' => $user];
        }
        return response()->json($result);
        exit();
    }

    public function ContractUpdate(Request $request)
    {
        $user = Auth::user();
        $model = User::find($request->id);

        if (isset($user) && !empty($user) && $request->id == $user->id) {
            if (isset($request->ctype) && !empty($request->ctype) && $request->ctype == 'cancelcontract') {
                $contract_extend_upto  = Carbon::now()->addMonths(3)->format('Y-m-d');
                if ($model->contract_enddate < $contract_extend_upto) {
                    $model->contract_enddate = date("Y-m-d", strtotime("+12 month", strtotime($model->contract_enddate)));
                    $model->contract_canceldate = Carbon::now();
                } else {
                    $model->contract_canceldate = Carbon::now();
                }
                $model->save();
                $model->contract_startdate = ($model->contract_startdate) ? date('F d, Y', strtotime($model->contract_startdate)) : "";
                $model->contract_enddate = ($model->contract_enddate) ? date('F d, Y', strtotime($model->contract_enddate)) : "";
                $model->contract_canceldate = ($model->contract_canceldate) ? date('F d, Y', strtotime($model->contract_canceldate)) : "";

                $result = ['status' => true, 'message' => trans('translation.Contract cancel successfully'), 'data' => $model];
            } elseif (isset($request->ctype) && !empty($request->ctype) && $request->ctype == 'withdrawcontract') {
                $model->contract_canceldate = null;
                $model->contract_enddate = date("Y-m-d", strtotime("-12 month", strtotime($model->contract_enddate)));
                $model->save();

                $model->contract_startdate = ($model->contract_startdate) ? date('F d, Y', strtotime($model->contract_startdate)) : "";
                $model->contract_enddate = ($model->contract_enddate) ? date('F d, Y', strtotime($model->contract_enddate)) : "";
                $model->contract_canceldate = ($model->contract_canceldate) ? date('F d, Y', strtotime($model->contract_canceldate)) : "";

                $result = ['status' => true, 'message' => trans('translation.Contract withdraw successfully'), 'data' => $model];
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong'), 'data' => ''];
                return response()->json($result, 400);
            }
        }

        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $user = User::where('id', $request->id);
        if ($user->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Delete fail')];
        }
        return response()->json($result);
    }

    public function feedback()
    {
        $loginUser = Auth::user()->id;

        $appointmentData = AppointmentDealer::query()
            ->join('users', 'users.id', '=', 'appointment_dealer.customer_id')
            ->select('users.first_name', 'users.last_name', 'rating', DB::raw('1 as feedback_type'))
            ->where('dealer_id', $loginUser)
            ->where('appointment_dealer.status', 3)
            ->get()
            ->toArray();

        $orderData = Order::query()
            ->join('users', 'users.id', '=', 'orders.customer_id')
            ->select('users.first_name', 'users.last_name', 'average', DB::raw('2 as feedback_type'))
            ->join('order_rating', 'order_rating.order_id', '=', 'orders.id')
            ->where('dealer_id', $loginUser)
            ->where('orders.status', 1)
            ->get()
            ->toArray();

        return view('dealer.feedback', compact('appointmentData', 'orderData'));
    }
    public function updateDistributorStatus(Request $request)
    {
        $dealer_id = $request->dealer_id;
        $status = $request->status;
        if ($dealer_id) {
            $dealer = User::find($dealer_id);
            if ($dealer) {
                $dealer->is_distributor = $status;
                $dealer->save();
                $message = trans('translation.The dealer has been removed as a distributor.');
                if ($status == 1) {
                    $message = trans('translation.The dealer has been successfully marked as Distributor');
                }
                $result = ['status' => true, 'message' => $message];
                return response()->json($result);
            }
        }
        $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        return response()->json($result);
    }

    public function deleteAccount(Request $request)
    {
        //check delete_account value is equal to "delete"
        if ($request->delete_account === 'delete') {
            $user_id = $request->user_id;
            $user = User::find($user_id);
            if ($user) {
                //cancel stripe subscription from subscription controller
                $subscriptionController = new SubscriptionController();
                $subscriptionController->cancelSubscription($request);

                $user->delete();
                //logout user
                Auth::logout();
                //invalidate session
                $request->session()->invalidate();
                $result = ['status' => true, 'message' => trans('translation.Account_deleted_successfully')];
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.please_type_delete_to_confirm')];
        }
        return response()->json($result);
    }
}
