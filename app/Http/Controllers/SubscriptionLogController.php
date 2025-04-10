<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Packages;
use App\Models\SubscriptionLog;
use Facade\Ignition\Support\Packagist\Package;
use Illuminate\Support\Facades\Log;
use App\Models\Order;



class SubscriptionLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    public function index()
    {
        $status = User::$status;
        $loginUser = Auth::user();
        if ($loginUser->role_type == 1) {
            return view('subscription-log.index', compact('status'));
        } else if ($loginUser->role_type == 2) {
            return view('subscription-log.dealerindex', compact('status'));
        }
        return abort(404);
    }
    public function get(Request $request)
    {
        $role = User::$role;
        $status = User::$status;
        $loginUser = Auth::user();
        $data = SubscriptionLog::query()
            ->select('subscription_log.*', DB::raw('CONCAT(dealer.first_name," ",dealer.last_name) AS dealer_name'), 'packages.name as package_name', 'packages.plan_currency as plan_currency')
            ->join('users as dealer', 'dealer.id', '=', 'subscription_log.user_id')
            ->join('packages', 'packages.id', '=', 'subscription_log.package_id')
            ->when($loginUser->role_type == 2, function ($q) use ($loginUser) {
                return $q->where('subscription_log.user_id', $loginUser->id);
            });

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) use ($status) {
                return getDateFormateView($row->created_at);
            })
            ->editColumn('amount', function ($row) use ($status) {
                $currency = '€';
                if ($row->plan_currency == "2") {
                    $currency = "$";
                }
                return $currency . $row->amount;
            })
            ->editColumn('tax', function ($row) use ($status) {
                $currency = '€';
                if ($row->plan_currency == "2") {
                    $currency = "$";
                }
                return $row->tax ? $currency . $row->tax : '-';
            })
            ->editColumn('amount_total', function ($row) use ($status) {
                $currency = '€';
                if ($row->plan_currency == "2") {
                    $currency = "$";
                }
                return $row->amount_total ? $currency . $row->amount_total : (($row->amount + $row->tax) ? $currency . ($row->amount + $row->tax) : '-');
            })
            ->editColumn('refundable_amount', function ($row) use ($status) {
                if ($row->refundable_amount) {
                    $currency = '€';
                    if ($row->plan_currency == "2") {
                        $currency = "$";
                    }
                    return $currency . $row->refundable_amount;
                }
                return '-';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    if ((bool)strtotime($search)) {
                        $instance->whereRaw("DATE_FORMAT(subscription_log.created_at, '%d.%m.%Y') LIKE '%{$search}%'");
                    } else {
                        $instance->where(function ($w) use ($search) {
                            $w->orWhere('subscription_log.package_id', 'LIKE', "%$search%")
                                // ->orWhereRaw('(CONCAT(customer.first_name," ",customer.last_name))', 'LIKE', "%$search%")
                                // ->orWhereRaw('(CONCAT(dealer.first_name," ",dealer.last_name))', 'LIKE', "%$search%")
                                ->orWhere('subscription_log.amount', 'LIKE', "%$search%");
                        });
                    }
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }
    public function getInvoice(Request $request)
    {
        $loginUser = Auth::user();
        if ($request->id) {
            $s_log = SubscriptionLog::find($request->id);
            if ($s_log) {
                if ($loginUser->role_type == 2 && $s_log->user_id != $loginUser->id) {
                    throw new Exception("you can not access this invoice", 403);
                }
                $orders = Order::where(['commision_invoice_id' => $s_log->invoice_id, 'is_deducted_commission' => 1])->get();
                $package = Packages::find($s_log->package_id);
                $loggedInUserCountry = $loginUser->country;
                //if role is admin then get user with trashed
                if ($loginUser->role_type == 1) {
                    $dealer = User::withTrashed()->find($s_log->user_id);
                } else {
                    $dealer = User::find($s_log->user_id);
                }

                header('Content-Type: text/html; charset=utf-8');
                $invoice_pdf = PDF::loadView('subscription.invoice.invoice', compact('orders', 's_log', 'package', 'dealer', 'loggedInUserCountry'  ));
                $pdf_filename = 'Invoice.pdf';
                return $invoice_pdf->stream($pdf_filename);
            }
        }
    }
}
