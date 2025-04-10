<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ContactNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Recipients\AlertRecipient;


class SiteController extends Controller
{
    public function index()
    {
        $loginUser = Auth::user();
        $productCount = Product::all()->count();
        $brandCount = Brand::all()->count();
        return view('site.index', compact('loginUser', 'productCount', 'brandCount'));
    }
    public function contact(Request $request){
        $to_email=[env('APP_CONTACT_EMAIL')];
        $recipients = collect($to_email)->map(function ($item, $key) {
            return new AlertRecipient($item);
        });
        Notification::send($recipients, new ContactNotification($request->all()));
        $data=['status'=>'success','message'=>'Contact has been sent successfully'];
        return response()->json($data, 200);
    }
}
