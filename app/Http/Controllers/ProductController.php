<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Product;
use App\Models\DealerBrand;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\PurchaseEnquiry;
use App\Models\ProductExecution;
use App\Models\ProductAttributes;
use App\Models\ProductConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('product.index');
    }

    public function create()
    {
        $productType = ProductType::query()
            ->get()
            ->pluck('type_name', 'id')
            ->toArray();

        $productCategory = ProductCategory::query()
            ->get()
            ->pluck('category_name', 'id')
            ->toArray();

        $productConnections = ProductConnection::query()
            ->get()
            ->pluck('connection_name', 'id')
            ->toArray();

        $productExecution = ProductExecution::query()
            ->get()
            ->pluck('execution_name', 'id')
            ->toArray();

        $brandData = Brand::query()
            ->get()
            ->pluck('brand_name', 'id')
            ->toArray();

        $model = new Product;
        return view('product.form', compact('productType', 'productCategory', 'brandData', 'model', 'productConnections', 'productExecution'));
    }
    public function view($id)
    {
        $model = Product::query()
            ->join('product_category', 'product_category.id', '=', 'product.category_id')
            ->join('product_type', 'product_type.id', '=', 'product.type_id')
            ->join('brand', 'brand.id', '=', 'product.brand_id')
            ->select('product.*', 'brand.brand_name', 'product_category.category_name', 'product_type.type_name')
            ->where('product.id', $id)
            ->first();
        if (isset($model->id)) {
            return view('product.view', compact('model'));
        } else {
            return abort('404');
        }
    }

    public function edit($id)
    {
        $productType = ProductType::query()
            ->get()
            ->pluck('type_name', 'id')
            ->toArray();

        $productCategory = ProductCategory::query()
            ->get()
            ->pluck('category_name', 'id')
            ->toArray();

        $productConnections = ProductConnection::query()
            ->get()
            ->pluck('connection_name', 'id')
            ->toArray();

        $productExecution = ProductExecution::query()
            ->get()
            ->pluck('execution_name', 'id')
            ->toArray();

        $brandData = Brand::query()
            ->get()
            ->pluck('brand_name', 'id')
            ->toArray();

        $model = Product::find($id);
        if (isset($model->id)) {
            return view('product.form', compact('productType', 'productCategory', 'brandData', 'model', 'productConnections', 'productExecution'));
        } else {
            return abort('404');
        }
    }
    public function get(Request $request)
    {
        $data = Product::query()
            ->join('product_category', 'product_category.id', '=', 'product.category_id')
            ->join('product_type', 'product_type.id', '=', 'product.type_id')
            ->join('brand', 'brand.id', '=', 'product.brand_id')
            ->select('product.*', 'product_name', 'brand.brand_name', 'product_category.category_name', 'product_type.type_name');

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('url', function ($row) {
                return ($row->url) ? $row->url : "";
            })
            ->editColumn('remark', function ($row) {
                return ($row->remark) ? $row->remark : "";
            })
            ->editColumn('retail', function ($row) {
                return ($row->retail) ? number_format($row->retail, 2, '.', '') : '0.00';
            })
            ->addColumn('connections_html', function ($row) {
                return ($row->connections) ? $row->connectionsView($row->connections) : '';
            })
            ->addColumn('execution_html', function ($row) {
                return ($row->execution) ? $row->executionView($row->execution) : '';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('brand_name', 'LIKE', "%$search%");
                        $w->orWhere('type_name', 'LIKE', "%$search%");
                        $w->orWhere('category_name', 'LIKE', "%$search%");
                        $w->orWhere('product_name', 'LIKE', "%$search%");
                        $w->orWhere('retail', 'LIKE', "%$search%");
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
                'brand_id' => 'required',
                'type_id' => 'required',
                'category_id' => 'required',
                'product_name' => 'required',
                'retail' => 'required|numeric',
                'url' => 'required|url',
            );

            $messages['brand_id.required'] = trans('validation.custom.brand_id.required');
            $messages['type_id.required'] = trans('validation.custom.type_id.required');
            $messages['category_id.required'] = trans('validation.custom.category_id.required');
            $messages['product_name.required'] = trans('validation.custom.product_name.required');
            $messages['retail.required'] = trans('validation.custom.retail.required');
            $messages['numeric.required'] = trans('validation.custom.numeric.required');
            $messages['url.required'] = trans('validation.custom.url.required');

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Product added successfully');
                if ($request->id) {
                    $model = Product::where('id', $request->id)->first();
                    if ($model) {
                        $product = $model;
                        $succssmsg = trans('translation.Product updated successfully');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $product = new Product;
                    $product->created_at = Carbon::now();
                }

                $product->brand_id = $request->brand_id;
                $product->type_id = $request->type_id;
                $product->category_id = $request->category_id;
                $product->product_name = $request->product_name;
                $product->url = $request->url;
                $product->retail = $request->retail;
                $product->remark = $request->remark;
                $product->execution = (isset($request->execution) && !empty($request->execution)) ? implode(', ', $request->execution) : null;
                $product->connections = (isset($request->connections) && !empty($request->connections)) ? implode(', ', $request->connections) : null;
                $product->updated_at = Carbon::now();
                if ($product->save()) {
                    $result = ['status' => true, 'message' => $succssmsg, 'data' => []];
                } else {
                    $result = ['status' => false, 'message' => 'Error in saving data', 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request', 'data' => []];
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
        $product = Product::where('id', $request->id);
        if ($product->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }

    public function import(Request $request)
    {
        if ($request->ajax()) {
            $rules = array(
                'product_csv' => 'required',
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                if ($request->product_csv) {
                    $filename = $request->product_csv->getClientOriginalName();
                    $extension = $request->product_csv->getClientOriginalExtension();
                    $extension_array = array('xlsx', 'xls');
                    if (!in_array($extension, $extension_array)) {
                        $result = ['status' => false, 'message' => trans('translation.Please upload xlsx file only')];
                        return $result;
                    }

                    $spreadsheet = readAsArray($request->product_csv);

                    $i = 0;
                    $invalid_rows = [];
                    $invalidRowMessage = '';
                    if (count($spreadsheet[0]) == '9') {
                        foreach ($spreadsheet as $list) {
                            if ($i > 0) {
                                if (empty(trim($list[0])) && trim($list[0]) == '') {
                                    $invalid_rows[$i][] = trans('translation.Brand name at row') . $i . trans('translation.can not be blank');
                                }
                                if (empty(trim($list[1])) && trim($list[1]) == '') {
                                    $invalid_rows[$i][] = trans('translation.Product type at row') . $i . trans('translation.can not be blank');
                                }
                                if (empty(trim($list[2])) && trim($list[2]) == '') {
                                    $invalid_rows[$i][] = trans('translation.Product category at row') . $i . trans('translation.can not be blank');
                                }
                                if (empty(trim($list[3])) && trim($list[3]) == '') {
                                    $invalid_rows[$i][] = trans('translation.Product name at row') . $i . trans('translation.can not be blank');
                                }
                                if (!empty(trim($list[6])) && trim($list[6]) != '' && !preg_match("/^\d+$/", trim($list[6]))) {
                                    $invalid_rows[$i][] = trans('translation.Retail at row') . $i . trans('translation.must be a number');
                                }
                                if (empty(trim($list[7])) && trim($list[7]) == '') {
                                    $invalid_rows[$i][] = trans('translation.Product URL at row') . $i . trans('translation.can not be blank');
                                }
                                if (!empty(trim($list[7])) && trim($list[7]) != '' && !preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", trim($list[7]))) {
                                    $invalid_rows[$i][] = trans('translation.Product URL at row') . $i . trans('translation.must be a valid url');
                                }

                                if (!array_key_exists($i, $invalid_rows)) {

                                    $brand_name = ($list[0] && trim($list[0])) ? ((mb_detect_encoding($list[0])) ? mb_convert_encoding($list[0], 'UTF-8', mb_detect_encoding($list[0])) : utf8_encode($list[0])) : '';
                                    $type_name = ($list[1] && trim($list[1])) ? ((mb_detect_encoding($list[1])) ? mb_convert_encoding($list[1], 'UTF-8', mb_detect_encoding($list[1])) : utf8_encode($list[1])) : '';
                                    $category_name = ($list[2] && trim($list[2])) ? ((mb_detect_encoding($list[2])) ? mb_convert_encoding($list[2], 'UTF-8', mb_detect_encoding($list[2])) : utf8_encode($list[2])) : '';
                                    $product_name = ($list[3] && trim($list[3])) ? ((mb_detect_encoding($list[3])) ? mb_convert_encoding($list[3], 'UTF-8', mb_detect_encoding($list[3])) : utf8_encode($list[3])) : '';
                                    $product_connections = ($list[4] && trim($list[4])) ? ((mb_detect_encoding($list[4])) ? mb_convert_encoding($list[4], 'UTF-8', mb_detect_encoding($list[4])) : utf8_encode($list[4])) : '';
                                    $product_executions = ($list[5] && trim($list[5])) ? ((mb_detect_encoding($list[5])) ? mb_convert_encoding($list[5], 'UTF-8', mb_detect_encoding($list[5])) : utf8_encode($list[5])) : '';
                                    $remark = ($list[8] && trim($list[8])) ? ((mb_detect_encoding($list[8])) ? mb_convert_encoding($list[8], 'UTF-8', mb_detect_encoding($list[8])) : utf8_encode($list[8])) : '';

                                    $brandData = Brand::query()
                                        ->where('brand_name', trim($brand_name))
                                        ->first();

                                    if (empty($brandData)) {
                                        $brand = new Brand;
                                        $brand->brand_name = trim($brand_name);
                                        $brand->created_at = Carbon::now();
                                        $brand->updated_at = Carbon::now();
                                        $brand->save();

                                        $brand_id = $brand->id;
                                    } else {
                                        $brand_id = $brandData->id;
                                    }

                                    $productTypeData = ProductType::query()
                                        ->where('type_name', trim($type_name))
                                        ->first();

                                    if (empty($productTypeData)) {
                                        $type_modal = new ProductType;
                                        $type_modal->type_name = trim($type_name);
                                        $type_modal->created_at = Carbon::now();
                                        $type_modal->updated_at = Carbon::now();
                                        $type_modal->save();

                                        $type_id = $type_modal->id;
                                    } else {
                                        $type_id = $productTypeData->id;
                                    }

                                    $productCategoryData = ProductCategory::query()
                                        ->where('category_name', trim($category_name))
                                        ->first();

                                    if (empty($productCategoryData)) {
                                        $category_modal = new ProductCategory;
                                        $category_modal->category_name = trim($category_name);
                                        $category_modal->created_at = Carbon::now();
                                        $category_modal->updated_at = Carbon::now();
                                        $category_modal->save();

                                        $category_id = $category_modal->id;
                                    } else {
                                        $category_id = $productCategoryData->id;
                                    }

                                    $product_connections_ids = [];
                                    foreach (array_filter(explode(',', trim($product_connections))) as $product_connection) {
                                        $productConnectionsData = ProductConnection::query()
                                            ->where('connection_name', trim($product_connection))
                                            ->first();

                                        if (empty($productConnectionsData)) {
                                            $connection_modal = new ProductConnection;
                                            $connection_modal->connection_name = trim($product_connection);
                                            $connection_modal->created_at = Carbon::now();
                                            $connection_modal->updated_at = Carbon::now();
                                            $connection_modal->save();

                                            $product_connections_ids[] = $connection_modal->id;
                                        } else {
                                            $product_connections_ids[] = $productConnectionsData->id;
                                        }
                                    }

                                    $product_executions_ids = [];
                                    foreach (array_filter(explode(',', trim($product_executions))) as $product_execution) {
                                        $productExecutionsData = ProductExecution::query()
                                            ->where('execution_name', trim($product_execution))
                                            ->first();

                                        if (empty($productExecutionsData)) {
                                            $execution_modal = new ProductExecution;
                                            $execution_modal->execution_name = trim($product_execution);
                                            $execution_modal->created_at = Carbon::now();
                                            $execution_modal->updated_at = Carbon::now();
                                            $execution_modal->save();

                                            $product_executions_ids[] = $execution_modal->id;
                                        } else {
                                            $product_executions_ids[] = $productExecutionsData->id;
                                        }
                                    }

                                    $product_data = Product::query()
                                        ->where('brand_id', $brand_id)
                                        ->where('type_id', $type_id)
                                        ->where('category_id', $category_id)
                                        ->where('product_name', $product_name)
                                        ->first();

                                    if (empty($product_data)) {
                                        $product = new Product;
                                        $product->brand_id = $brand_id;
                                        $product->type_id = $type_id;
                                        $product->category_id = $category_id;
                                        $product->product_name = $product_name;
                                        if (isset($product_connections_ids) && !empty($product_connections_ids)) {
                                            $product->connections = implode(', ', $product_connections_ids);
                                        }
                                        if (isset($product_executions_ids) && !empty($product_executions_ids)) {
                                            $product->execution = implode(', ', $product_executions_ids);
                                        }
                                        if (!empty(trim($list[6])))
                                            $product->retail = trim($list[6]);
                                        if (!empty(trim($list[7])))
                                            $product->url = trim($list[7]);
                                        $product->remark = $remark;
                                        $product->created_at = Carbon::now();
                                        $product->updated_at = Carbon::now();
                                        $product->save();
                                    }
                                }
                            }
                            $i++;
                        }
                        if (isset($invalid_rows) && !empty($invalid_rows)) {
                            $invalidRowMessage = trans('translation.Can not import row number') . implode(', ', array_keys($invalid_rows)) . trans('translation.because of incomplete or invalid data');
                        }
                        if ((count($spreadsheet) - 1) !== count($invalid_rows)) {
                            $result = ['status' => true, 'message' => trans('translation.File imported successfully'), 'error' => ['product_csv' =>  $invalidRowMessage]];
                            return $result;
                        } else {
                            $invalidRowMessage = trans('translation.Can not import any rows because of incomplete or invalid data');
                            $result = ['status' => false, 'message' => trans('translation.File not imported'), 'error' => ['product_csv' =>  $invalidRowMessage]];
                            return $result;
                        }
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.File not imported'), 'error' => ['product_csv' => trans('translation.Import sheet data is not valid please use data same like sample sheet')]];
                        return $result;
                    }
                }

                $result = ['status' => true, 'message' => trans('translation.File imported successfully'), 'error' => []];
            }
            return $result;
        }
    }

    public function productlist(Request $request)
    {
        $enquiryType = PurchaseEnquiry::$enquiryType;
        return view('product.list_product', compact('enquiryType'));
    }

    public function dealerProduct(Request $request)
    {
        $loginUser = Auth::user();
        if ($loginUser->role_type != 2)
            abort('404');

        $UserBrands = DealerBrand::query()
            ->where('dealer_id', $loginUser->id)
            ->where('deleted_at', null)
            ->get()
            ->pluck('brand_id')
            ->toArray();

        $allBrands = Brand::query()
            ->select('id', 'brand_name')
            ->whereIn('id', $UserBrands)
            ->orderBy('brand_name')
            ->get();

        $allProductType = ProductType::query()
            ->select('product_type.id', 'product_type.type_name')
            ->join('product', 'product.type_id', '=', 'product_type.id')
            ->whereIn('product.brand_id', $UserBrands)
            ->groupBy('product_type.id')
            ->orderBy('product_type.type_name')
            ->get();

        $allProductCategory = ProductCategory::query()
            ->select('product_category.id', 'product_category.category_name')
            ->join('product', 'product.category_id', '=', 'product_category.id')
            ->whereIn('product.brand_id', $UserBrands)
            ->groupBy('product_category.id')
            ->orderBy('product_category.category_name')
            ->get();

        $allProductConnections = ProductConnection::query()
            ->select('product_connections.id', 'product_connections.connection_name')
            ->join('product', DB::raw("FIND_IN_SET(product_connections.id,REPLACE(product.connections, ', ', ','))"), ">", DB::raw("'0'"))
            ->whereIn('product.brand_id', $UserBrands)
            ->groupBy('product_connections.id')
            ->orderBy('product_connections.connection_name')
            ->get();

        $allProductExecutions = ProductExecution::query()
            ->select('product_executions.id', 'product_executions.execution_name')
            ->join('product', DB::raw("FIND_IN_SET(product_executions.id,REPLACE(product.execution, ', ', ','))"), ">", DB::raw("'0'"))
            ->whereIn('product.brand_id', $UserBrands)
            ->groupBy('product_executions.id')
            ->orderBy('product_executions.execution_name')
            ->get();

        $allProducts = Product::query()
            ->select('id', 'product_name')
            ->whereIn('brand_id', $UserBrands)
            ->orderBy('product_name')
            ->get();

        $attributes = PurchaseEnquiry::$enquiryType;

        $attributesQuery = Product::query()
            ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'product.id')
            ->whereIn('product.brand_id', $UserBrands)
            ->orderBy('product.product_name')
            ->groupBy('product.id')
            ->select(
                'product.id',
                DB::raw('group_concat(IFNULL(product_attributes.connection_id, "null")) as attribute_connection_ids'),
                DB::raw('group_concat(IFNULL(product_attributes.execution_id, "null")) as attribute_execution_ids'),
                DB::raw('group_concat(product_attributes.in_stock) as attribute_in_stocks'),
                DB::raw('group_concat(product_attributes.is_used) as attribute_is_useds'),
                DB::raw('group_concat(product_attributes.ready_for_demo) as attribute_ready_for_demos')
            );

        if (isset($request->producttype_id) && !empty($request->producttype_id)) {
            $attributesQuery->whereIn('product.type_id', $request->producttype_id);
        }

        if (isset($request->productcategory_id) && !empty($request->productcategory_id)) {
            $attributesQuery->whereIn('product.category_id', $request->productcategory_id);
        }

        if (isset($request->product_id) && !empty($request->product_id)) {
            $attributesQuery->whereIn('product.id', $request->product_id);
        }

        $selectAttributes = [
            1 => false,
            2 => false,
            3 => false,
        ];
        foreach ($attributesQuery->get() as $attributeCheck) {
            if (!empty($attributeCheck->attribute_in_stocks) && str_contains($attributeCheck->attribute_in_stocks, 1)) {
                $selectAttributes[1] = true;
            }
            if (!empty($attributeCheck->attribute_is_useds) && str_contains($attributeCheck->attribute_is_useds, 1)) {
                $selectAttributes[2] = true;
            }
            if (!empty($attributeCheck->attribute_ready_for_demos) && str_contains($attributeCheck->attribute_ready_for_demos, 1)) {
                $selectAttributes[3] = true;
            }
        }

        if (!$selectAttributes[1]) {
            unset($attributes[1]);
        }
        if (!$selectAttributes[2]) {
            unset($attributes[2]);
        }
        if (!$selectAttributes[3]) {
            unset($attributes[3]);
        }

        return view('product.dealer_product', compact('allBrands', 'allProductType', 'allProductCategory', 'allProducts', 'attributes', 'allProductConnections', 'allProductExecutions'));
    }

    public function getFilterOptions(Request $request)
    {
        $loginUser = Auth::user();

        $userBrandsQuery = DealerBrand::query()
            ->select('dealer_brand.brand_id as userbrand_id')
            ->where('dealer_brand.dealer_id', $loginUser->id)
            ->where('dealer_brand.deleted_at', null);

        if (!isset($request->brand_id) && ((isset($request->producttype_id) && !empty($request->producttype_id)) || (isset($request->productcategory_id) && !empty($request->productcategory_id)) || (isset($request->product_id) && !empty($request->product_id)) || (isset($request->productconnection_id) && !empty($request->productconnection_id)) || (isset($request->productexecution_id) && !empty($request->productexecution_id)) || (isset($request->productattributes_id) && !empty($request->productattributes_id)))) {
            $userBrandsQuery->join('product', 'product.brand_id', '=', 'dealer_brand.brand_id');

            if (isset($request->productattributes_id) && !empty($request->productattributes_id)) {
                $userBrandsQuery->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'product.id');

                $attribute = $request->productattributes_id;
                $userBrandsQuery->where(function ($query) use ($attribute) {
                    if (in_array(1, $attribute)) {
                        $query->orWhere('product_attributes.in_stock', 1);
                    }
                    if (in_array(2, $attribute)) {
                        $query->orWhere('product_attributes.is_used', 1);
                    }
                    if (in_array(3, $attribute)) {
                        $query->orWhere('product_attributes.ready_for_demo', 1);
                    }
                });
            }

            if (isset($request->producttype_id) && !empty($request->producttype_id)) {
                $userBrandsQuery->whereIn('product.type_id', $request->producttype_id);
            }

            if (isset($request->productcategory_id) && !empty($request->productcategory_id)) {
                $userBrandsQuery->whereIn('product.category_id', $request->productcategory_id);
            }

            if (isset($request->productconnection_id) && !empty($request->productconnection_id)) {
                foreach ($request->productconnection_id as $connection_id) {
                    $userBrandsQuery->whereRaw(DB::raw("FIND_IN_SET(" . $connection_id . ",REPLACE(product.connections, ', ', ','))"));
                }
            }

            if (isset($request->productexecution_id) && !empty($request->productexecution_id)) {
                foreach ($request->productexecution_id as $execution_id) {
                    $userBrandsQuery->whereRaw(DB::raw("FIND_IN_SET(" . $execution_id . ",REPLACE(product.execution, ', ', ','))"));
                }
            }

            if (isset($request->product_id) && !empty($request->product_id)) {
                $userBrandsQuery->whereIn('product.id', $request->product_id);
            }

            $userBrandsQuery->groupBy('product.id');
        }

        $UserBrands = $userBrandsQuery->get()
            ->pluck('userbrand_id')
            ->toArray();

        if (isset($UserBrands) && !empty($UserBrands)) {
            $brandsQuery = Brand::query()
                ->select('id', 'brand_name as name')
                ->orderBy('brand_name')
                ->whereIn('id', $UserBrands);

            $brands = $brandsQuery->get();

            if (isset($request->brand_id) && !empty($request->brand_id)) {
                $UserBrands = $request->brand_id;
            }

            $productTypesQuery = ProductType::query()
                ->select('product_type.id', 'product_type.type_name as name')
                ->groupBy('product_type.id')
                ->orderBy('product_type.type_name')
                ->join('product', 'product.type_id', '=', 'product_type.id')
                ->whereIn('product.brand_id', $UserBrands);

            if (isset($request->productcategory_id) && !empty($request->productcategory_id)) {
                $productTypesQuery->whereIn('product.category_id', $request->productcategory_id);
            }

            if (isset($request->productconnection_id) && !empty($request->productconnection_id)) {
                foreach ($request->productconnection_id as $connection_id) {
                    $productTypesQuery->whereRaw(DB::raw("FIND_IN_SET(" . $connection_id . ",REPLACE(product.connections, ', ', ','))"));
                }
            }

            if (isset($request->productexecution_id) && !empty($request->productexecution_id)) {
                foreach ($request->productexecution_id as $execution_id) {
                    $productTypesQuery->whereRaw(DB::raw("FIND_IN_SET(" . $execution_id . ",REPLACE(product.execution, ', ', ','))"));
                }
            }

            if (isset($request->product_id) && !empty($request->product_id)) {
                $productTypesQuery->whereIn('product.id', $request->product_id);
            }

            $productTypes = $productTypesQuery->get();

            $productCategoryQuery = ProductCategory::query()
                ->select('product_category.id', 'product_category.category_name as name')
                ->join('product', 'product.category_id', '=', 'product_category.id')
                ->whereIn('product.brand_id', $UserBrands)
                ->groupBy('product_category.id')
                ->orderBy('product_category.category_name');

            if (isset($request->producttype_id) && !empty($request->producttype_id)) {
                $productCategoryQuery->whereIn('product.type_id', $request->producttype_id);
            }

            if (isset($request->productconnection_id) && !empty($request->productconnection_id)) {
                foreach ($request->productconnection_id as $connection_id) {
                    $productCategoryQuery->whereRaw(DB::raw("FIND_IN_SET(" . $connection_id . ",REPLACE(product.connections, ', ', ','))"));
                }
            }

            if (isset($request->productexecution_id) && !empty($request->productexecution_id)) {
                foreach ($request->productexecution_id as $execution_id) {
                    $productCategoryQuery->whereRaw(DB::raw("FIND_IN_SET(" . $execution_id . ",REPLACE(product.execution, ', ', ','))"));
                }
            }

            if (isset($request->product_id) && !empty($request->product_id)) {
                $productCategoryQuery->whereIn('product.id', $request->product_id);
            }

            $productCategories = $productCategoryQuery->get();

            $productConnectionQuery = ProductConnection::query()
                ->select('product_connections.id', 'product_connections.connection_name as name')
                ->join('product', DB::raw("FIND_IN_SET(product_connections.id,REPLACE(product.connections, ', ', ','))"), ">", DB::raw("'0'"))
                ->whereIn('product.brand_id', $UserBrands)
                ->groupBy('product_connections.id')
                ->orderBy('product_connections.connection_name');

            if (isset($request->producttype_id) && !empty($request->producttype_id)) {
                $productConnectionQuery->whereIn('product.type_id', $request->producttype_id);
            }

            if (isset($request->productexecution_id) && !empty($request->productexecution_id)) {
                foreach ($request->productexecution_id as $execution_id) {
                    $productConnectionQuery->whereRaw(DB::raw("FIND_IN_SET(" . $execution_id . ",REPLACE(product.execution, ', ', ','))"));
                }
            }

            if (isset($request->product_id) && !empty($request->product_id)) {
                $productConnectionQuery->whereIn('product.id', $request->product_id);
            }

            $productConnections = $productConnectionQuery->get();

            $productExecutionQuery = ProductExecution::query()
                ->select('product_executions.id', 'product_executions.execution_name as name')
                ->join('product', DB::raw("FIND_IN_SET(product_executions.id,REPLACE(product.execution, ', ', ','))"), ">", DB::raw("'0'"))
                ->whereIn('product.brand_id', $UserBrands)
                ->groupBy('product_executions.id')
                ->orderBy('product_executions.execution_name');

            if (isset($request->producttype_id) && !empty($request->producttype_id)) {
                $productExecutionQuery->whereIn('product.type_id', $request->producttype_id);
            }

            if (isset($request->productconnection_id) && !empty($request->productconnection_id)) {
                foreach ($request->productconnection_id as $connection_id) {
                    $productExecutionQuery->whereRaw(DB::raw("FIND_IN_SET(" . $connection_id . ",REPLACE(product.connections, ', ', ','))"));
                }
            }

            if (isset($request->product_id) && !empty($request->product_id)) {
                $productExecutionQuery->whereIn('product.id', $request->product_id);
            }

            $productExecutions = $productExecutionQuery->get();

            $productsQuery = Product::query()
                ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'product.id')
                ->whereIn('product.brand_id', $UserBrands)
                ->orderBy('product.product_name')
                ->groupBy('product.id')
                ->select(
                    'product.id',
                    'product.product_name as name',
                    DB::raw('group_concat(IFNULL(product_attributes.connection_id, "null")) as attribute_connection_ids'),
                    DB::raw('group_concat(IFNULL(product_attributes.execution_id, "null")) as attribute_execution_ids'),
                    DB::raw('group_concat(product_attributes.in_stock) as attribute_in_stocks'),
                    DB::raw('group_concat(product_attributes.is_used) as attribute_is_useds'),
                    DB::raw('group_concat(product_attributes.ready_for_demo) as attribute_ready_for_demos')
                );

            if (isset($request->producttype_id) && !empty($request->producttype_id)) {
                $productsQuery->whereIn('product.type_id', $request->producttype_id);
            }

            if (isset($request->productconnection_id) && !empty($request->productconnection_id)) {
                foreach ($request->productconnection_id as $connection_id) {
                    $productsQuery->whereRaw(DB::raw("FIND_IN_SET(" . $connection_id . ",REPLACE(product.connections, ', ', ','))"));
                }
            }

            if (isset($request->productexecution_id) && !empty($request->productexecution_id)) {
                foreach ($request->productexecution_id as $execution_id) {
                    $productsQuery->whereRaw(DB::raw("FIND_IN_SET(" . $execution_id . ",REPLACE(product.execution, ', ', ','))"));
                }
            }

            if (isset($request->productcategory_id) && !empty($request->productcategory_id)) {
                $productsQuery->whereIn('product.category_id', $request->productcategory_id);
            }

            if (isset($request->product_id) && !empty($request->product_id)) {
                $productsQuery->whereIn('product.id', $request->product_id);
            }

            if (isset($request->productattributes_id) && !empty($request->productattributes_id)) {
                $attribute = $request->productattributes_id;
                $productsQuery->where(function ($query) use ($attribute) {
                    if (in_array(1, $attribute)) {
                        $query->orWhere('product_attributes.in_stock', 1);
                    }
                    if (in_array(2, $attribute)) {
                        $query->orWhere('product_attributes.is_used', 1);
                    }
                    if (in_array(3, $attribute)) {
                        $query->orWhere('product_attributes.ready_for_demo', 1);
                    }
                });
            }

            $products = $productsQuery->get();

            $attributes = PurchaseEnquiry::$enquiryType;

            $attributesQuery = Product::query()
                ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'product.id')
                ->whereIn('product.brand_id', $UserBrands)
                ->orderBy('product.product_name')
                ->groupBy('product.id')
                ->select(
                    'product.id',
                    DB::raw('group_concat(IFNULL(product_attributes.connection_id, "null")) as attribute_connection_ids'),
                    DB::raw('group_concat(IFNULL(product_attributes.execution_id, "null")) as attribute_execution_ids'),
                    DB::raw('group_concat(product_attributes.in_stock) as attribute_in_stocks'),
                    DB::raw('group_concat(product_attributes.is_used) as attribute_is_useds'),
                    DB::raw('group_concat(product_attributes.ready_for_demo) as attribute_ready_for_demos')
                );

            if (isset($request->producttype_id) && !empty($request->producttype_id)) {
                $attributesQuery->whereIn('product.type_id', $request->producttype_id);
            }

            if (isset($request->productcategory_id) && !empty($request->productcategory_id)) {
                $attributesQuery->whereIn('product.category_id', $request->productcategory_id);
            }

            if (isset($request->product_id) && !empty($request->product_id)) {
                $attributesQuery->whereIn('product.id', $request->product_id);
            }

            $selectAttributes = [
                1 => false,
                2 => false,
                3 => false,
            ];
            foreach ($attributesQuery->get() as $attributeCheck) {
                if (!empty($attributeCheck->attribute_in_stocks) && str_contains($attributeCheck->attribute_in_stocks, 1)) {
                    $selectAttributes[1] = true;
                }
                if (!empty($attributeCheck->attribute_is_useds) && str_contains($attributeCheck->attribute_is_useds, 1)) {
                    $selectAttributes[2] = true;
                }
                if (!empty($attributeCheck->attribute_ready_for_demos) && str_contains($attributeCheck->attribute_ready_for_demos, 1)) {
                    $selectAttributes[3] = true;
                }
            }

            if (!$selectAttributes[1]) {
                unset($attributes[1]);
            }
            if (!$selectAttributes[2]) {
                unset($attributes[2]);
            }
            if (!$selectAttributes[3]) {
                unset($attributes[3]);
            }

            $data = [
                'brand_id' => $brands,
                'producttype_id' => $productTypes,
                'productcategory_id' => $productCategories,
                'product_id' => $products,
                'productattributes_id' => $attributes,
                'productconnection_id' => $productConnections,
                'productexecution_id' => $productExecutions
            ];

            $result = ['status' => true, 'message' => trans('translation.Data found'), 'data' => $data];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Product not found')];
        }

        return response()->json($result);
    }
    public function getDealerProduct(Request $request)
    {
        $user = Auth::user();
        $all_connections = ProductConnection::query()
            ->select('id', 'connection_name')
            ->get()
            ->toArray();
        $connection_collector = collect($all_connections);
        $all_executions = ProductExecution::query()
            ->select('id', 'execution_name')
            ->get()
            ->toArray();
        $executions_collector = collect($all_executions);
        $data = Product::query()
            ->join('product_category', 'product_category.id', '=', 'product.category_id')
            ->join('product_type', 'product_type.id', '=', 'product.type_id')
            ->join('brand', 'brand.id', '=', 'product.brand_id')
            ->leftJoin('product_attributes', function ($join) use($user) {
                $join->on('product_attributes.product_id', '=', 'product.id')->where('dealer_id', $user->id);
            })
            ->join('dealer_brand', function ($join) use ($user) {
                $join->on('brand.id', '=', 'dealer_brand.brand_id')
                    ->whereNull('dealer_brand.deleted_at')
                    ->where('dealer_brand.dealer_id', $user->id);
            })
            ->groupBy('product.id')
            ->select(
                'product.*',
                'brand.brand_name',
                'product_category.category_name',
                'product_type.type_name',
                DB::raw('group_concat(IFNULL(product_attributes.connection_id, "null")) as attribute_connection_ids'),
                DB::raw('group_concat(IFNULL(product_attributes.execution_id, "null")) as attribute_execution_ids'),
                DB::raw('group_concat(product_attributes.in_stock) as attribute_in_stocks'),
                DB::raw('group_concat(product_attributes.is_used) as attribute_is_useds'),
                DB::raw('group_concat(product_attributes.ready_for_demo) as attribute_ready_for_demos')
            );

        if (isset($request->brand_id) && !empty($request->brand_id)) {
            $data->whereIn('product.brand_id', explode(',', $request->brand_id));
        }

        if (isset($request->producttype_id) && !empty($request->producttype_id)) {
            $data->whereIn('product.type_id', explode(',', $request->producttype_id));
        }

        if (isset($request->productcategory_id) && !empty($request->productcategory_id)) {
            $data->whereIn('product.category_id', explode(',', $request->productcategory_id));
        }

        if (isset($request->product_id) && !empty($request->product_id)) {
            $data->whereIn('product.id', explode(',', $request->product_id));
        }

        if (isset($request->productconnection_id) && !empty($request->productconnection_id)) {
            foreach (explode(',', $request->productconnection_id) as $connection_id) {
                $data->whereRaw(DB::raw("FIND_IN_SET(" . $connection_id . ",REPLACE(product.connections, ', ', ','))"));
            }
        }

        if (isset($request->productexecution_id) && !empty($request->productexecution_id)) {
            foreach (explode(',', $request->productexecution_id) as $execution_id) {
                $data->whereRaw(DB::raw("FIND_IN_SET(" . $execution_id . ",REPLACE(product.execution, ', ', ','))"));
            }
        }

        if (isset($request->productattributes_id) && !empty($request->productattributes_id)) {
            $attribute = $request->productattributes_id;
            $data->where(function ($query) use ($attribute) {
                if (in_array(1, explode(',', $attribute))) {
                    $query->orWhere('product_attributes.in_stock', 1);
                }
                if (in_array(2, explode(',', $attribute))) {
                    $query->orWhere('product_attributes.is_used', 1);
                }
                if (in_array(3, explode(',', $attribute))) {
                    $query->orWhere('product_attributes.ready_for_demo', 1);
                }
            });
        }

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('url', function ($row) {
                return ($row->url) ? $row->url : "";
            })
            ->editColumn('remark', function ($row) {
                return ($row->remark) ? $row->remark : "";
            })
            ->editColumn('retail', function ($row) {
                return ($row->retail) ? number_format($row->retail, 2, '.', '') : '0.00';
            })
            ->addColumn('connections_html', function ($row) {
                return ($row->connections) ? $row->connectionsView($row->connections) : '';
            })
            ->addColumn('execution_html', function ($row) {
                return ($row->execution) ? $row->executionView($row->execution) : '';
            })
            ->addColumn('product_attributes', function ($row) use ($connection_collector, $executions_collector) {
                $product_attributes = [];
                if ((isset($row->attribute_in_stocks) && !empty($row->attribute_in_stocks)) || (isset($row->attribute_is_useds) && !empty($row->attribute_is_useds)) || (isset($row->attribute_ready_for_demos) && !empty($row->attribute_ready_for_demos))) {
                    if (str_contains($row->attribute_connection_ids, ',')) {
                        $connections = explode(",", $row->attribute_connection_ids);
                    } else {
                        $connections = [];
                        $connections[] = $row->attribute_connection_ids;
                    }
                    if (str_contains($row->attribute_execution_ids, ',')) {
                        $executions = explode(",", $row->attribute_execution_ids);
                    } else {
                        $executions = [];
                        $executions[] = $row->attribute_execution_ids;
                    }
                    if (str_contains($row->attribute_in_stocks, ',')) {
                        $in_stocks = explode(",", $row->attribute_in_stocks);
                    } else {
                        $in_stocks = [];
                        $in_stocks[] = $row->attribute_in_stocks;
                    }
                    if (str_contains($row->attribute_is_useds, ',')) {
                        $is_useds = explode(",", $row->attribute_is_useds);
                    } else {
                        $is_useds = [];
                        $is_useds[] = $row->attribute_is_useds;
                    }
                    if (str_contains($row->attribute_ready_for_demos, ',')) {
                        $ready_for_demos = explode(",", $row->attribute_ready_for_demos);
                    } else {
                        $ready_for_demos = [];
                        $ready_for_demos[] = $row->attribute_ready_for_demos;
                    }
                    for ($i = 0; $i <= (count($ready_for_demos) - 1); $i++) {
                        if (isset($connections[$i]) && !empty($connections[$i]) && $connections[$i] != 'null') {
                            $product_attributes[$i]['connection'] = $connection_collector->where('id', $connections[$i]);
                        } else {
                            $product_attributes[$i]['connection'] = null;
                        }
                        if (isset($executions[$i]) && !empty($executions[$i]) && $executions[$i] != 'null') {
                            $product_attributes[$i]['execution'] = $executions_collector->where('id', $executions[$i]);
                        } else {
                            $product_attributes[$i]['execution'] = null;
                        }
                        $product_attributes[$i]['in_stocks'] = $in_stocks[$i];
                        $product_attributes[$i]['is_useds'] = $is_useds[$i];
                        $product_attributes[$i]['ready_for_demos'] = $ready_for_demos[$i];
                    }
                }
                return (isset($product_attributes) && !empty($product_attributes)) ? $product_attributes : '';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('brand_name', 'LIKE', "%$search%");
                        $w->orWhere('type_name', 'LIKE', "%$search%");
                        $w->orWhere('category_name', 'LIKE', "%$search%");
                        $w->orWhere('product_name', 'LIKE', "%$search%");
                        $w->orWhere('retail', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }
    public function getDealerProductAttributes(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        $connections = Product::query()
            ->join('product_connections', DB::raw("FIND_IN_SET(product_connections.id,REPLACE(product.connections, ', ', ','))"), ">", DB::raw("'0'"))
            ->where('product.id', $id)
            ->select('product_connections.id', 'product_connections.connection_name')
            ->groupBy("product_connections.id")
            ->get()
            ->toArray();

        $executions = Product::query()
            ->join('product_executions', DB::raw("FIND_IN_SET(product_executions.id,REPLACE(product.execution, ', ', ','))"), ">", DB::raw("'0'"))
            ->where('product.id', $id)
            ->select('product_executions.id', 'product_executions.execution_name')
            ->groupBy("product_executions.id")
            ->get()
            ->toArray();

        $attributes = PurchaseEnquiry::$enquiryType;

        $existing = ProductAttributes::query()
            ->where('product_id', $id)
            ->where('dealer_id', $user->id)
            ->get();

        $data = [
            'connections' => $connections,
            'executions' => $executions,
            'attributes' => $attributes,
            'existing' => $existing
        ];

        $result = ['status' => true, 'message' => '', 'data' => $data];

        return response()->json($result);
        exit();
    }
    public function addDealerProductAttributes(Request $request)
    {
        $connections = Product::query()
            ->join('product_connections', DB::raw("FIND_IN_SET(product_connections.id,REPLACE(product.connections, ', ', ','))"), ">", DB::raw("'0'"))
            ->where('product.id', $request->product_id)
            ->select('product_connections.id', 'product_connections.connection_name')
            ->groupBy("product_connections.id")
            ->get();

        $executions = Product::query()
            ->join('product_executions', DB::raw("FIND_IN_SET(product_executions.id,REPLACE(product.execution, ', ', ','))"), ">", DB::raw("'0'"))
            ->where('product.id', $request->product_id)
            ->select('product_executions.id', 'product_executions.execution_name')
            ->groupBy("product_executions.id")
            ->get();

        if (!isset($request->product_attribute) && isset($request->product_id) && !empty($request->product_id)) {
            $user = Auth::user();
            ProductAttributes::query()
                ->where('product_id', $request->product_id)
                ->where('dealer_id', $user->id)
                ->delete();

            $result = ['status' => true, 'message' => trans('translation.Attributes saved successfully'), 'data' => []];
            return response()->json($result);
            exit();
        }

        if (isset($request->product_attribute) && (array_column($request->product_attribute, 'connection') || array_column($request->product_attribute, 'execution'))) {
            $rules = array(
                'product_attribute.*.attribute.*' => 'required',
                'product_attribute.*.attribute' => 'required',
            );
        } else {
            $rules = array(
                'product_attribute.*.attribute.*' => 'required|distinct',
                'product_attribute.*.attribute' => 'required|distinct',
            );
        }

        if (isset($request->product_attribute) && !empty($request->product_attribute) && count($request->product_attribute)) {
            foreach (array_keys($request->product_attribute) as $iKey) {
                if (isset($request->product_attribute[$iKey])) {
                    $product_attribute = $request->product_attribute[$iKey];
                    if ($product_attribute['attribute'] != '3') {
                        if (isset($connections) && !empty($connections) && count($connections) > 0) {
                            $rules['product_attribute.' . $iKey . '.connection'] = 'required';
                        }
                        if (isset($executions) && !empty($executions) && count($executions) > 0) {
                            $rules['product_attribute.' . $iKey . '.execution'] = 'required';
                        }
                    }
                }
            }
        }

        $messsages = array(
            'product_attribute.*.connection.required' => trans('translation.The connection field is required'),
            'product_attribute.*.execution.required' => trans('translation.The execution field is required'),
            'product_attribute.*.attribute.required' => trans('translation.The attribute field is required'),
            'product_attribute.*.attribute.distinct' => trans('translation.The attribute has a duplicate value')
        );

        $validator = Validator::make($request->all(), $rules, $messsages);
        if ($validator->fails()) {
            $result = ['status' => false, 'error' => $validator->errors()];
            return response()->json($result);
        } else {
            if (isset($request->product_attribute) && (array_column($request->product_attribute, 'connection') || array_column($request->product_attribute, 'execution'))) {
                $duplicate = $duplicate_pair = $pair = $execution = $connection = $attributr = [];
                foreach ($request->product_attribute as $key => $value) {
                    $pair_temp = (isset($value['connection']) && !empty($value['connection']) ? $value['connection'] : '') . ',' . (isset($value['execution']) && !empty($value['execution']) ? $value['execution'] : '') . ',' . $value['attribute'];
                    if (array_search($pair_temp, $pair) !== false) {
                        $duplicate_pair[] = (isset($value['connection']) && !empty($value['connection']) ? $value['connection'] : '') . ',' . (isset($value['execution']) && !empty($value['execution']) ? $value['execution'] : '') . ',' . $value['attribute'];
                        $duplicate[] = $key;
                    } else {
                        if (isset($value['connection']) && !empty($value['connection'])) {
                            $connection[] = $value['connection'];
                        }
                        if (isset($value['execution']) && !empty($value['execution'])) {
                            $execution[] = $value['execution'];
                        }
                        $attributr[] = $value['attribute'];
                        $pair[] = (isset($value['connection']) && !empty($value['connection']) ? $value['connection'] : '') . ',' . (isset($value['execution']) && !empty($value['execution']) ? $value['execution'] : '') . ',' . $value['attribute'];
                    }
                }
                if (isset($duplicate) && !empty($duplicate)) {
                    $result = ['status' => false, 'errorduplicate' => $duplicate];
                    return response()->json($result);
                } else {
                    $user = Auth::user();
                    ProductAttributes::query()
                        ->where('product_id', $request->product_id)
                        ->where('dealer_id', $user->id)
                        ->delete();

                    foreach ($request->product_attribute as $product_attribute) {
                        $model = new ProductAttributes;
                        $model->product_id = $request->product_id;
                        $model->dealer_id = $user->id;
                        $model->connection_id = isset($product_attribute['connection']) && !empty($product_attribute['connection']) ? $product_attribute['connection'] : null;
                        $model->execution_id = isset($product_attribute['execution']) && !empty($product_attribute['execution']) ? $product_attribute['execution'] : null;
                        if ($product_attribute['attribute'] == '1') {
                            $model->in_stock = 1;
                        } else {
                            $model->in_stock = 0;
                        }

                        if ($product_attribute['attribute'] == '2') {
                            $model->is_used = 1;
                        } else {
                            $model->is_used = 0;
                        }

                        if ($product_attribute['attribute'] == 3) {
                            $model->ready_for_demo = 1;
                        } else {
                            $model->ready_for_demo = 0;
                        }
                        $model->save();
                    }
                }
            } else {
                $user = Auth::user();
                ProductAttributes::query()
                    ->where('product_id', $request->product_id)
                    ->where('dealer_id', $user->id)
                    ->delete();

                foreach ($request->product_attribute as $product_attribute) {
                    $model = new ProductAttributes;
                    $model->product_id = $request->product_id;
                    $model->dealer_id = $user->id;
                    $model->connection_id = null;
                    $model->execution_id = null;

                    if ($product_attribute['attribute'] == '1') {
                        $model->in_stock = 1;
                    } else {
                        $model->in_stock = 0;
                    }

                    if ($product_attribute['attribute'] == '2') {
                        $model->is_used = 1;
                    } else {
                        $model->is_used = 0;
                    }

                    if ($product_attribute['attribute'] == 3) {
                        $model->ready_for_demo = 1;
                    } else {
                        $model->ready_for_demo = 0;
                    }

                    $model->save();
                }
            }
        }

        $result = ['status' => true, 'message' => trans('translation.Attributes saved successfully'), 'data' => []];

        return response()->json($result);
        exit();
    }

    public function get_keys_for_duplicate_values($my_arr, $clean = false)
    {
        if ($clean) {
            return array_unique($my_arr);
        }

        $dups = $new_arr = array();
        foreach ($my_arr as $key => $val) {
            if (!isset($new_arr[$val])) {
                $new_arr[$val] = $key;
            } else {
                if (isset($dups[$val])) {
                    $dups[$val][] = $key;
                } else {
                    $dups[$val] = array($key);
                }
            }
        }
        return $dups;
    }
}
