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
use App\Models\OrderPaymentLog;

class OrderPaymentLogController extends Controller
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
        $loginUser = Auth::user();
        if($loginUser->role_type == 1) {
            return view('order-payment-log.adminindex', compact('status'));
        } elseif($loginUser->role_type == 2) {
            return view('order-payment-log.dealerindex', compact('status'));
        }
        return abort(404);
    }
    public function get(Request $request)
    {
        $role = User::$role;
        $status = User::$status;
        $loginUser=Auth::user();
        $data = OrderPaymentLog::query()
            ->select('order_payment_log.*', DB::raw('CONCAT(customer.first_name," ",customer.last_name) AS customer_name'), DB::raw('CONCAT(dealer.first_name," ",dealer.last_name) AS dealer_name'),'orders.invoice_number', 'orders.currency as currency')
            ->join('orders','orders.id', '=','order_payment_log.order_id')
            ->join('users as dealer', 'dealer.id', '=', 'orders.dealer_id')
            ->join('users as customer', 'customer.id','=', 'order_payment_log.customer_id')
            ->when($loginUser->role_type==2,function($q) use($loginUser){
                return $q->where('orders.dealer_id',$loginUser->id);
            })
            ->when($loginUser->role_type == 3, function ($q) use ($loginUser) {
                return $q->where('order_payment_log.customer_id', $loginUser->id);
            });
        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) use ($status) {
                return getDateFormateView($row->created_at);
            })
            ->editColumn('dealer_payout', function ($row) use ($status) {
                return formatCurrencyOutput($row->dealer_payout, $row->currency, true, 'before');
                // return "€".$row->dealer_payout;
            })
            ->editColumn('site_fees', function ($row) use ($status) {
                return formatCurrencyOutput($row->site_fees, $row->currency, true, 'before');
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    if ((bool)strtotime($search)) {
                        $instance->whereRaw("DATE_FORMAT(order_payment_log.created_at, '%d.%m.%Y') LIKE '%{$search}%'");
                    } else {
                        $instance->where(function ($w) use ($search) {
                            $w->orWhere('orders.invoice_number', 'LIKE', "%$search%")
                                // ->orWhereRaw('(CONCAT(customer.first_name," ",customer.last_name))', 'LIKE', "%$search%")
                                // ->orWhereRaw('(CONCAT(dealer.first_name," ",dealer.last_name))', 'LIKE', "%$search%")
                                ->orWhere('dealer_payout', 'LIKE', "%$search%")
                                ->orWhere('site_fees', 'LIKE', "%$search%");
                        });
                    }
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
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
}
