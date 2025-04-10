<?php

namespace App\Http\Controllers\Auth;

use Session;
use App\User;
use Illuminate\Http\Request;
use App\Helpers\MailerFactory;
use Illuminate\Support\Carbon;
use Validator, Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->middleware('guest');
        $this->mailer = $mailer;
    }
    public function register()
    {
        $countries = User::$countries;
        return view('auth.register', compact('countries'));
    }

    public function store(Request $request)
    {
        $role_type = (isset($request->role_type)) ? $request->role_type : 3;

        $contract_startdate = $contract_enddate = null;
        if ($role_type == 2) {
            $rules = array(
                'company_name' => 'required|string|max:100',
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'street' => 'required',
                'house_number' => 'required|string|max:10',
                'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                'city' => 'required|string',
                'country' => 'required|string',
                'vat_number' => 'required|string|max:50',
                'vat'=> 'required|numeric:max:5|gt:0',
                'shop_start_time' => 'required',
                'shop_end_time' => 'required|after:shop_start_time',
                'email' => 'required|string|email|max:50|unique:users,email,NULL,id,deleted_at,NULL|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                'phone' => "required|string|min:10",
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
                'gender' => 'required',
                'bank_name' => 'required',
                'iban' => 'required',
                'bic' => 'required',
                'agbs_terms' => 'required',
                'dsgvo_terms' => 'required',
                'sepa_terms' => 'required',
            );

            $contract_startdate = Carbon::now()->format('Y-m-d');
            $contract_enddate  = Carbon::now()->addMonths(13)->format('Y-m-d');
        } else {
            $rules = array(
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'street' => 'required',
                'house_number' => 'required|max:10',
                'zipcode' => ['required', 'numeric', 'digits_between:4,10'],
                'city' => 'required|string',
                'country' => 'required|string',
                'email' => 'required|string|email|max:50|unique:users,email,NULL,id,deleted_at,NULL|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
                'phone' => "required|string|min:10",
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
                'gender' => 'required',
                'agbs_terms' => 'required',
                'dsgvo_terms' => 'required',
                'birth_date' => 'required|before:18 years ago',
                'salutation'  => 'required',
                'customer_company_name' => 'required_if:salutation,==,firma|nullable|string|max:100',
                'customer_vat_number' => 'required_if:salutation,==,firma|nullable|string|max:50',
            );
        }
        $messsages = array();
        $messages['vat.numeric'] = trans('validation.custom.vat_number.numeric');
        $messages['vat.gt'] = trans('validation.custom.vat_number.gt');
        $request->validate($rules, $messsages);

        $phone = str_replace(array("(", ")", "-", " "), array("", "", "", "", ""), $request->phone);
        $user = User::create([
            'display_id' => createDisplayId($role_type),
            'company_name' => ($request->company_name) ? $request->company_name : (($request->customer_company_name) ? $request->customer_company_name : NULL),
            'vat_number' => ($request->vat_number) ? $request->vat_number : (($request->customer_vat_number) ? $request->customer_vat_number : NULL),
            'vat' => ($request->vat) ? $request->vat : NULL,
            'street' => ($request->street) ? $request->street : NULL,
            'house_number' => ($request->house_number) ? $request->house_number : NULL,
            'zipcode' => ($request->zipcode) ? $request->zipcode : NULL,
            'city' => ($request->city) ? $request->city : NULL,
            'country' => ($request->country) ? $request->country : NULL,
            'bank_name' => ($request->bank_name) ? $request->bank_name : NULL,
            'iban' => ($request->iban) ? $request->iban : NULL,
            'bic' => ($request->bic) ? $request->bic : NULL,
            'shop_start_time' => ($request->shop_start_time) ? date('H:i', strtotime($request->shop_start_time)) : NULL,
            'shop_end_time' => ($request->shop_end_time) ? date('H:i', strtotime($request->shop_end_time)) : NULL,
            'role_type' => $request->role_type,
            'phone' => $phone,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contract_startdate' => $contract_startdate,
            'contract_enddate' => $contract_enddate,
            'birth_date' => ($request->birth_date) ? date('Y-m-d', strtotime($request->birth_date)) : NULL,
            'gender' => $request->gender,
            'salutation' => ($request->salutation) ? $request->salutation : NULL,
            'status' => 1,
        ]);
        Auth::loginUsingId($user->id);
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            \Log::error('Error sending email verification notification: ' . $e->getMessage());
        }
        Auth::logout();
        $encodedId = encrypt($user->id);

        return redirect()->route('email.verify', ["id" => $encodedId]);
    }
}
