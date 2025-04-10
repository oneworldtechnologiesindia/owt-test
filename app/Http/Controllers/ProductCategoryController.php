<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('productCategory.index');
    }

    public function get(Request $request)
    {
        $data = ProductCategory::query();

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('category_name', 'LIKE', "%$search%");
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
                'category_name' => 'required|string|max:100',
            );
            $messages['category_name.required'] = trans('validation.custom.category_name.required');
            $messages['category_name.max'] = trans('validation.custom.category_name.max');

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Category added successfully');
                if ($request->id) {
                    $model = ProductCategory::where('id', $request->id)->first();
                    if ($model) {
                        $productCategory = $model;
                        $succssmsg = trans('translation.Category updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $productCategory = new ProductCategory;
                    $productCategory->created_at = Carbon::now();
                }

                $productCategory->category_name = $request->category_name;
                $productCategory->updated_at = Carbon::now();

                if ($productCategory->save()) {
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
            $productCategory = ProductCategory::find($request->id);
            $result = ['status' => true, 'message' => '', 'data' => $productCategory];
        }
        return response()->json($result);
        exit();
    }

    public function delete(Request $request)
    {
        $productCategory = ProductCategory::where('id', $request->id);
        if ($productCategory->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }
}
