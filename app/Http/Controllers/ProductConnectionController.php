<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProductConnection;
use Illuminate\Support\Facades\Validator;

class ProductConnectionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('ProductConnection.index');
    }

    public function get(Request $request)
    {
        $data = ProductConnection::query();

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('connection_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function addupdate(Request $request)
    {
        if ($request->ajax()) {
            $rules = array(
                'connection_name' => 'required|string|max:100',
            );
            $messages['connection_name.required'] = trans('validation.custom.connection_name.required');
            $messages['connection_name.max'] = trans('validation.custom.connection_name.max');

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Connection added successfully');
                if ($request->id) {
                    $model = ProductConnection::where('id', $request->id)->first();
                    if ($model) {
                        $ProductConnection = $model;
                        $succssmsg = trans('translation.Connection updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $ProductConnection = new ProductConnection;
                    $ProductConnection->created_at = Carbon::now();
                }

                $ProductConnection->connection_name = $request->connection_name;
                $ProductConnection->updated_at = Carbon::now();

                if ($ProductConnection->save()) {
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
            $ProductConnection = ProductConnection::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $ProductConnection];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $ProductConnection = ProductConnection::where('id', $request->id);
        if ($ProductConnection->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }
}
