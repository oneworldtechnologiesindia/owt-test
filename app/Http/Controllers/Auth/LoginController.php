<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        switch (Auth::user()->role_type) {
            case 1:
                return route('home');
            case 2:
                return route('dealer.home');
            case 3:
                return route('customer.home');
            default:
                return route('home');
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function vliewLogin()
    {
        return view('auth.login');
    }

    protected function validateLogin(Request $request)
    {
        $messages = [];
        $this->validate($request, [
            'email' => "required|exists:users,email",
            'password' => 'required',
        ], $messages);
    }
    protected function credentials(Request $request)
    {
        return [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'role_type' => [1, 2, 3],
        ];
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $user = User::where('email', $request->get('email'))->whereNull('deleted_at')->first();
        if (isset($user) && !empty($user) && !empty($user->email_verified_at) && $user->status == 0) {
            if ($user->role_type != 1) {
                Auth::logout();
                return Redirect::to("login")->with('status', trans('translation.registered_waiting_for_your_approval_account'));
            } else {
                $request->session()->regenerate();

                $this->clearLoginAttempts($request);

                if ($response = $this->authenticated($request, $this->guard()->user())) {
                    return $response;
                }

                return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect()->intended($this->redirectPath());
            }
        } else {
            $request->session()->regenerate();

            $this->clearLoginAttempts($request);

            if ($response = $this->authenticated($request, $this->guard()->user())) {
                return $response;
            }
            if($user->role_type==2){
                if($user->is_active_subscription == 1) {
                    $this->redirectPath=route('dealer.home');
                } else{
                    $this->redirectPath=route('dealer.subscription');
                }
            } elseif($user->role_type==3){
                $this->redirectPath=route('customer.home');
            }
            elseif($user->role_type==1){
                $this->redirectPath=route('home');
            }
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect()->intended($this->redirectPath);
        }
    }

    public function authenticate(Request $request)
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            return redirect()->intended($this->redirectPath());
        }
        return Redirect::to("login")->withSuccess(trans('translation.You_have_entered_invalid_credentials'));
    }
    public function showverify(Request $request)
    {
        $id = decrypt($request->id);
        if(!$id) {
            return abort(404);
        }
        return view('auth.verify', compact('id'));
    }

    public function verficationResend(Request $request)
    {
        $user = User::whereNull('email_verified_at')->where('id', $request->id)->first();
        Auth::loginUsingId($user->id);
        $user->sendEmailVerificationNotification();
        Auth::logout();
        return redirect()->back()->with('resent', 'A fresh verification link has been sent to your email address.');

    }
    public function logout()
    {
        $locale = session()->get('lang');
        $locale = isset($locale) && !empty($locale) ? $locale : 'en';
        Session::flush();
        App::setLocale($locale);
        Session::put('lang', $locale);
        Session::save();
        Auth::logout();
        return Redirect('/login');
    }
}
