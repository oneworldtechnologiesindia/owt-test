<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Helpers\MailerFactory;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Access\AuthorizationException;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $mailer;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MailerFactory $mailer)
    {
        // $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->mailer = $mailer;
    }

    public function show(Request $request)
    {
        Auth::logout();
        return redirect()->back()->with('error', trans('translation.please_verify_your_email_first'));
    }
    public function verify(Request $request)
    {
        if (!auth()->check()) {
            auth()->loginUsingId($request->route('id'));
        }
        $user = $request->user();
        if (isset($user) && !empty($user)) {
            $user_id = $request->route('id');
            if ($user_id != $request->user()->getKey()) {
                return redirect()->back()->with('error', 'You dont have permission to perform this action .');
            }
            if (!$request->user()->hasVerifiedEmail()) {
                if ($request->user()->markEmailAsVerified()) {
                    event(new Verified($request->user()));
                    if ($user->role_type == 3) {
                        $user->status = 1;
                    } else {
                        $user->status = 0;
                    }
                    $user->save();
                    if (isset($user) && !empty($user)) {
                        if ($user->role_type != 1) {
                            if ($user->role_type == 3) {
                                $subject = trans('translation.customerregister_email_subject');
                                $body = trans('translation.customerregister_email_body', ['url' => '<a href="' . route('customer.home') . '">' . route('customer.home') . '</a>', 'emailto' => '<a href="mailto:' . env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') . '">' . env('APP_CONTACT_EMAIL', 'contact@HiFi-Quest.com') . '</a>']);
                                $this->mailer->sendGeneralEmail($user, $subject, $body, $documents = []);
                            }
                            if ($user->role_type == 2) {
                                $subject = trans('translation.admin_dealerregister_email_subject');
                                $body = trans('translation.admin_dealerregister_email_body', ['name' => $user->first_name . ' ' . $user->last_name, 'url' => '<a href="' . route('dealers') . '">' . route('dealers') . '</a>']);
                            } else {
                                $subject = trans('translation.admin_customerregister_email_subject');
                                $body = trans('translation.admin_customerregister_email_body', ['name' => $user->first_name . ' ' . $user->last_name, 'url' => '<a href="' . route('customers') . '">' . route('customers') . '</a>']);
                            }
                            $adminuser = User::where('role_type', 1)->whereNull('deleted_at')->first();
                            $this->mailer->sendGeneralEmail($adminuser, $subject, $body, $documents = []);
                        }
                    }
                    if ($user->status == 1) {
                        Auth::loginUsingId($user->id);
                        return redirect()->route('customer.home');
                    } else {
                        Auth::logout();
                        return Redirect::to("login")->with('status', trans('translation.You_have_successfully_registered_waiting_for_your_approval_account'));
                    }
                }
            } else {
                return Redirect::to("login");
            }
        } else {
            return Redirect::to("login");
        }
    }
}
