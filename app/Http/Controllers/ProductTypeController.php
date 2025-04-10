<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('productType.index');
    }

    public function get(Request $request)
    {
        $data = ProductType::query();

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('type_name', 'LIKE', "%$search%");
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
                'type_name' => 'required|string|max:100',
            );

            $messages['type_name.required'] = trans('validation.custom.type_name.required');
            $messages['type_name.max'] = trans('validation.custom.type_name.max');

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Type added successfully');
                if ($request->id) {
                    $model = ProductType::where('id', $request->id)->first();
                    if ($model) {
                        $productType = $model;
                        $succssmsg = trans('translation.Type updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $productType = new ProductType;
                    $productType->created_at = Carbon::now();
                }

                $productType->type_name = $request->type_name;
                $productType->updated_at = Carbon::now();

                if ($productType->save()) {
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
            $productType = ProductType::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $productType];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $productType = ProductType::where('id', $request->id);
        if ($productType->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }
}
