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

class CustomerController extends Controller
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
        $countries = User::$countries;
        return view('customer.index', compact('status', 'countries'));
    }
    public function get(Request $request)
    {
        $role = User::$role;
        $status = User::$status;

        $data = User::query()->where('role_type', '=', 3)
            ->select('users.*', DB::raw('CONCAT(users.first_name," ",users.last_name) AS name'));

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->status]) ? trans('translation.' . $status[$row->status]) : "";
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
            $user->save();
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
            $rules = array(
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'street' => 'required',
                'house_number' => 'required|max:10',
                'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                'city' => 'required|string',
                'country' => 'required|string',
                'phone' => "required|string|min:10",
                'birth_date' => 'required|before:18 years ago',
                'salutation'  => 'required',
                'customer_company_name' => 'required_if:salutation,==,firma|nullable|string|max:100',
                'customer_vat_number' => 'required_if:salutation,==,firma|nullable|string|max:50'
            );
            if ($request->id) {
                $rules['email'] = 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,' . $request->id . ',id,deleted_at,NULL';
            } else {
                $rules['email'] = 'required|string|email|max:50|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|unique:users,email,NULL,id,deleted_at,NULL';
                $rules['password'] = 'required|string|min:8|confirmed';
                $rules['password_confirmation'] = 'required';
            }
            $messages['first_name.required'] = trans('validation.custom.first_name.required');
            $messages['first_name.max'] = trans('validation.custom.first_name.max');
            $messages['first_name.string'] = trans('validation.string');
            $messages['last_name.required'] = trans('validation.custom.last_name.required');
            $messages['last_name.max'] = trans('validation.custom.last_name.max');
            $messages['last_name.string'] = trans('validation.string');
            $messages['street.required'] = trans('validation.custom.street.required');
            $messages['house_number.required'] = trans('validation.custom.house_number.required');
            $messages['house_number.max'] = trans('validation.custom.house_number.max');
            $messages['zipcode.required'] = trans('validation.custom.zipcode.required');
            $messages['zipcode.numeric'] = trans('validation.custom.zipcode.numeric');
            $messages['city.required'] = trans('validation.custom.city.required');
            $messages['city.string'] = trans('validation.string');
            $messages['country.required'] = trans('validation.custom.country.required');
            $messages['country.string'] = trans('validation.string');
            $messages['phone.required'] = trans('validation.custom.phone.required');
            $messages['phone.min'] = trans('validation.custom.phone.min');
            $messages['birth_date.before'] = trans('validation.custom.birth_date.before');
            $messages['salutation.required'] = trans('validation.custom.salutation.required');
            $messages['customer_company_name.required_if'] = trans('validation.custom.customer_company_name.required_if');
            $messages['customer_company_name.max'] = trans('validation.custom.customer_company_name.max');
            $messages['customer_vat_number.required_if'] = trans('validation.custom.customer_vat_number.required_if');
            $messages['customer_vat_number.max'] = trans('validation.custom.customer_vat_number.max');
            $messages['email.required'] = trans('validation.custom.email.required');
            $messages['email.email'] = trans('validation.custom.email.email');
            $messages['email.unique'] = trans('validation.unique');
            $messages['password.required'] = trans('validation.custom.password.required');
            $messages['password.min'] = trans('validation.custom.password.min');
            $messages['password.confirmed'] = trans('validation.custom.password.confirmed');
            $messages['password_confirmation.required'] = trans('validation.custom.password_confirmation.required');


            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Customer added successfully');
                if ($request->id) {
                    $model = User::where('id', $request->id)->first();
                    if ($model) {
                        $user = $model;
                        $succssmsg = trans('translation.Customer updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $user = new User;
                    $user->role_type = 3;
                    $user->status = 1;
                }

                $phone = str_replace(array("(", ")", "-", " "), array("", "", "", "", ""), $request->phone);
                $user->phone = $phone;
                $user->display_id = createDisplayId(3);
                $user->street = ($request->street) ? $request->street : NULL;
                $user->house_number = ($request->house_number) ? $request->house_number : NULL;
                $user->zipcode = ($request->zipcode) ? $request->zipcode : NULL;
                $user->city = ($request->city) ? $request->city : NULL;
                $user->country = ($request->country) ? $request->country : NULL;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->company_name = ($request->customer_company_name) ? $request->customer_company_name : NULL;
                $user->vat_number = ($request->customer_vat_number) ? $request->customer_vat_number : NULL;
                $user->salutation = ($request->salutation) ? $request->salutation : NULL;
                $user->birth_date = ($request->birth_date) ? date('Y-m-d', strtotime($request->birth_date)) : NULL;
                $user->email_verified_at = Carbon::now();
                $user->updated_at = Carbon::now();
                if ($request->password != "") {
                    $user->password = Hash::make($request->password);
                }
                if ($user->save()) {
                    if (!$request->id) {
                        $subject = trans('translation.customerregister_email_subject');
                        $body = trans('translation.customerregister_email_body', ['url' => '<a href="' . route('home') . '">' . route('home') . '</a>', 'emailto' => '<a href="mailto:' . env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') . '">' . env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') . '</a>']);
                        $this->mailer->sendGeneralEmail($user, $subject, $body, $documents = []);

                        $loginUser = Auth::user();
                        $subject = trans('translation.admin_customerregister_email_subject');
                        $body = trans('translation.admin_customerregister_email_body', ['name' => $user->first_name . ' ' . $user->last_name, 'url' => '<a href="' . route('customers') . '">' . route('customers') . '</a>']);
                        $this->mailer->sendGeneralEmail($loginUser, $subject, $body, $documents = []);
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
            $user->birth_date = date('d.m.Y', strtotime($user->birth_date));
            $result = ['status' => true, 'message' => '', 'data' => $user];
        }
        return response()->json($result);
        exit();
    }
    public function delete(Request $request)
    {
        $user = User::where('id', $request->id);
        if ($user->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => 'Delete fail'];
        }
        return response()->json($result);
    }

    public function deleteAccount(Request $request)
    {
        //check delete_account value is equal to "delete"
        if ($request->delete_account === 'delete') {
            $user_id = $request->user_id;
            $user = User::find($user_id);
            if ($user) {
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
