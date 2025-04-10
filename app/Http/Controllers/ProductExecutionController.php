<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProductExecution;
use Illuminate\Support\Facades\Validator;

class ProductExecutionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('ProductExecution.index');
    }

    public function get(Request $request)
    {
        $data = ProductExecution::query();

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('execution_name', 'LIKE', "%$search%");
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
                'execution_name' => 'required|string|max:100',
            );

            $messages['execution_name.required'] = trans('validation.custom.execution_name.required');
            $messages['execution_name.max'] = trans('validation.custom.execution_name.max');

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Execution added successfully');
                if ($request->id) {
                    $model = ProductExecution::where('id', $request->id)->first();
                    if ($model) {
                        $ProductExecution = $model;
                        $succssmsg = trans('translation.Execution updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $ProductExecution = new ProductExecution;
                    $ProductExecution->created_at = Carbon::now();
                }

                $ProductExecution->execution_name = $request->execution_name;
                $ProductExecution->updated_at = Carbon::now();

                if ($ProductExecution->save()) {
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
            $ProductExecution = ProductExecution::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $ProductExecution];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $ProductExecution = ProductExecution::where('id', $request->id);
        if ($ProductExecution->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }
}
