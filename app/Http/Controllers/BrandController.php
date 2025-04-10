<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('brand.index');
    }

    public function get(Request $request)
    {
        $data = Brand::query();

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('brand_name', 'LIKE', "%$search%");
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
                'brand_name' => 'required|string|max:100',
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Brand added successfully');
                if ($request->id) {
                    $model = Brand::where('id', $request->id)->first();
                    if ($model) {
                        $brand = $model;
                        $succssmsg = trans('translation.Brand updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $brand = new Brand;
                    $brand->created_at = Carbon::now();
                }

                $brand->brand_name = $request->brand_name;
                $brand->updated_at = Carbon::now();

                if ($brand->save()) {
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
            $brand = Brand::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $brand];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $brand = Brand::where('id', $request->id);
        if ($brand->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }
}
