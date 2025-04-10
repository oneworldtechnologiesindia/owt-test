<?php

namespace App\Http\Middleware;

use App\Models\SubscriptionLog;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckDealerSubcription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if(empty($user)){
            return redirect('/login')->with('error','You are not logged in, please login to continue.');
        }
        if(!empty($user) && isset($user->id) && $user->role_type == 2 && !empty($user->is_active_subscription == 1)){
            \Log::info('User is logged in and has an active subscription');
            return $next($request);
        }
        return redirect()->route('dealer.subscription')->with('error','You have not subscribed to any plan, please subscribe to a plan to continue.');
    }
}
