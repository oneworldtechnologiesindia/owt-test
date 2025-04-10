<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmaillogController extends Controller
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
        if ($loginUser->role_type == 1) {
            return view('emaillog.index');
        } elseif ($loginUser->role_type == 2) {
            return view('emaillog.dealerindex');
        } else {
            return view('emaillog.customerindex');
        }
    }

    public function get(Request $request)
    {
        $loginUser = Auth::user();

        $data = EmailLog::query()
            ->where('user_id', $loginUser->id);

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return getDateTimeFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('subject', 'LIKE', "%$search%");
                    });
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
            $emailLog = EmailLog::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $emailLog];
        }
        return response()->json($result);
        exit();
    }
}
