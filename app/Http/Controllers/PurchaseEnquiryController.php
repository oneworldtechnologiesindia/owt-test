<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Document;
use App\Models\DealerBrand;
use App\Models\ProductType;
use App\Models\OfferDetails;
use Illuminate\Http\Request;
use App\Helpers\MailerFactory;
use App\Models\ProductCategory;
use App\Models\PurchaseEnquiry;
use App\Models\ProductExecution;
use App\Models\ProductAttributes;
use App\Models\ProductConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Models\OfferPurchaseEnquiry;
use Illuminate\Support\Facades\Auth;
use App\Models\DealerPurchaseEnquiry;
use App\Models\OfferCheckoutDetail;
use App\Models\PurchaseEnquiryProduct;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PurchaseEnquiryNotification;
use Stripe\Stripe;
use Stripe\Checkout\Session as stripeSession;
use App\Models\OrderPaymentLog;
use App\Models\Packages;
use App\Models\StripeTaxRates;
use App\Models\SubscriptionLog;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Notification as NotificationModel;
use App\Models\PlanType;

class PurchaseEnquiryController extends Controller
{
    protected $mailer;
    public function __construct(MailerFactory $mailer)
    {
        $this->middleware(['auth']);
        $this->mailer = $mailer;
    }

    public function index()
    {
        $loginUser = Auth::user();
        if ($loginUser->role_type == 3) {
            $payment_method = OfferPurchaseEnquiry::$payment_method;
            $enquiryblockinfo = checkEnquiryBlock($loginUser);
            $enquiryblock = $enquiryblockinfo['enquiryblock'];
            $blockupto = $enquiryblockinfo['blockupto'];
            return view('purchaseEnquiry.index', compact('payment_method', 'enquiryblock', 'blockupto'));
        }
        return abort('404');
    }

    public function create()
    {
        $loginUser = Auth::user();
        $enquiryType = PurchaseEnquiry::$enquiryType;

        $allBrands = Brand::query()
            ->select('id', 'brand_name')
            ->orderBy('brand_name')
            ->get();

        $allProductType = ProductType::query()
            ->select('id', 'type_name')
            ->orderBy('type_name')
            ->get();

        $allProductCategory = ProductCategory::query()
            ->select('id', 'category_name')
            ->orderBy('category_name')
            ->get();

        $allProductConnections = ProductConnection::query()
            ->select('id', 'connection_name')
            ->orderBy('connection_name')
            ->get();

        $allProductExecutions = ProductExecution::query()
            ->select('id', 'execution_name')
            ->orderBy('execution_name')
            ->get();

        $productBrand = Product::query()
            ->join('brand', 'brand.id', '=', 'product.brand_id')
            ->select('product.id', DB::raw('CONCAT(product_name, " (", brand.brand_name,")") as product_name'))
            ->get()
            ->pluck('product_name', 'id')
            ->toArray();

        $model = new PurchaseEnquiry;
        $enquiryblockinfo = checkEnquiryBlock($loginUser);
        $enquiryblock = $enquiryblockinfo['enquiryblock'];

        if ($loginUser->role_type == 3 && isset($enquiryblock) && !empty($enquiryblock) && $enquiryblock == 'false') {
            return view('purchaseEnquiry.form', compact('model', 'productBrand', 'enquiryType', 'allBrands', 'allProductType', 'allProductCategory', 'allProductConnections', 'allProductExecutions'));
        } else {
            return redirect()->route('enquiry');
        }
        return abort('404');
    }

    public function getFilterOptions(Request $request)
    {
        $brandsQuery = Brand::query()
            ->select('brand.id', 'brand.brand_name as text')
            ->groupBy('brand.brand_name')
            ->orderBy('brand.brand_name')
            ->join('product', 'product.brand_id', '=', 'brand.id');

        if (isset($request->producttype_id) && !empty($request->producttype_id)) {
            $brandsQuery->whereIn('product.type_id', $request->producttype_id);
        }

        if (isset($request->productcategory_id) && !empty($request->productcategory_id)) {
            $brandsQuery->whereIn('product.category_id', $request->productcategory_id);
        }

        if (isset($request->productconnection_id) && !empty($request->productconnection_id)) {
            foreach ($request->productconnection_id as $connection_id) {
                $brandsQuery->whereRaw(DB::raw("FIND_IN_SET(" . $connection_id . ",REPLACE(product.connections, ', ', ','))"));
            }
        }

        if (isset($request->productexecution_id) && !empty($request->productexecution_id)) {
            foreach ($request->productexecution_id as $execution_id) {
                $brandsQuery->whereRaw(DB::raw("FIND_IN_SET(" . $execution_id . ",REPLACE(product.execution, ', ', ','))"));
            }
        }

        $brands = $brandsQuery->get()->toArray();

        $productTypesQuery = ProductType::query()
            ->select('product_type.id', 'product_type.type_name as text')
            ->groupBy('product_type.type_name')
            ->orderBy('product_type.type_name')
            ->join('product', 'product.type_id', '=', 'product_type.id');

        if (isset($request->brand_id) && !empty($request->brand_id)) {
            $productTypesQuery->whereIn('product.brand_id', (array) $request->brand_id);
        }

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

        $productTypes = $productTypesQuery->get()->toArray();

        $productCategoryQuery = ProductCategory::query()
            ->select('product_category.id', 'product_category.category_name as text')
            ->join('product', 'product.category_id', '=', 'product_category.id')
            ->groupBy('product_category.category_name')
            ->orderBy('product_category.category_name');

        if (isset($request->brand_id) && !empty($request->brand_id)) {
            $productCategoryQuery->whereIn('product.brand_id', (array) $request->brand_id);
        }

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

        $productCategories = $productCategoryQuery->get()->toArray();

        $productsQuery = Product::query()
            ->orderBy('product.product_name')
            ->join('brand', 'brand.id', '=', 'product.brand_id')
            ->groupBy('product.id')
            ->select(
                'product.id',
                'product.brand_id',
                DB::raw('CONCAT(product.product_name, " (", brand.brand_name,")") as text')
            );

        if (isset($request->brand_id) && !empty($request->brand_id)) {
            $productsQuery->whereIn('product.brand_id', (array) $request->brand_id);
        }

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

        if (isset($request->product_id) && !empty($request->product_id) && $request->product_id[0] != null) {
            foreach ((array) $request->product_id as $product_id) {
                $productsQuery->orWhere('product.id', $product_id);
            }
        }

        if ((isset($request->brand_id) && !empty($request->brand_id)) || (isset($request->producttype_id) && !empty($request->producttype_id)) || (isset($request->productconnection_id) && !empty($request->productconnection_id)) || (isset($request->productexecution_id) && !empty($request->productexecution_id)) || (isset($request->productcategory_id) && !empty($request->productcategory_id)) || isset($request->product_id) && !empty($request->product_id) && $request->product_id[0] != null) {
            $products = $productsQuery->get()->toArray();
        } else {
            $products = [];
        }

        $data = [
            'brand_id' => $brands,
            'producttype_id' => $productTypes,
            'productcategory_id' => $productCategories,
            'product_id' => $products,
        ];

        if (isset($products) && !empty($products) && count($products) > 0) {
            $result = ['status' => true, 'message' => trans('translation.Data found'), 'data' => $data];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Product not found.'), 'data' => $data];
        }

        return response()->json($result);
    }

    public function getProduct(Request $request)
    {
        $getProduct = [];

        if ((isset($request->brand_id) && !empty($request->brand_id)) || (isset($request->producttype_id) && !empty($request->producttype_id)) || (isset($request->productcategory_id) && !empty($request->productcategory_id)) || (isset($request->enquiry_type) && !empty($request->enquiry_type))) {
            $getProduct = Product::query()
                ->join('brand', 'brand.id', '=', 'product.brand_id')
                ->select('product.id', DB::raw('CONCAT(product_name, " (", brand.brand_name,")") as product_name'));
        }

        if (isset($request->brand_id) && !empty($request->brand_id)) {
            $getProduct->whereIn('brand_id', (array) $request->brand_id);
        }

        if (isset($request->producttype_id) && !empty($request->producttype_id)) {
            $getProduct->whereIn('type_id', $request->producttype_id);
        }

        if (isset($request->productcategory_id) && !empty($request->productcategory_id)) {
            $getProduct->whereIn('category_id', $request->productcategory_id);
        }

        if (isset($request->enquiry_type) && !empty($request->enquiry_type)) {
            $filterProducts = ProductAttributes::query();
            if (in_array(1, $request->enquiry_type)) {
                $filterProducts->where('in_stock', true);
            }
            if (in_array(2, $request->enquiry_type)) {
                $filterProducts->where('is_used', true);
            }
            if (in_array(3, $request->enquiry_type)) {
                $filterProducts->where('ready_for_demo', true);
            }
            $filterProducts->select('product_id');
            $filterProducts->get()->toArray();
            $productIds = array();
            foreach ($filterProducts->get() as $filterProduct) {
                $productIds[] = $filterProduct->product_id;
            }

            if (isset($productIds) && !empty($productIds)) {
                $getProduct->whereIn('product.id', $productIds);
            }
        }

        if ((isset($request->brand_id) && !empty($request->brand_id)) || (isset($request->producttype_id) && !empty($request->producttype_id)) || (isset($request->productcategory_id) && !empty($request->productcategory_id)) || (isset($request->enquiry_type) && !empty($request->enquiry_type))) {
            $all_products = $getProduct->get()->pluck('product_name', 'id')->toArray();
        } else {
            $all_products = [];
        }

        if (isset($all_products) && !empty($all_products)) {
            return response()->json(['status' => 200, 'data' => $all_products], 200);
        } else {
            return response()->json(['status' => 400, 'message' => trans('translation.Product not found.')], 200);
        }
    }

    public function getProductInfo(Request $request)
    {
        if (isset($request->id) && !empty($request->id)) {
            $productdata = Product::query()
                ->where('id', $request->id)
                ->first();

            $productConnection = ProductConnection::query()
                ->select('id', 'connection_name')
                ->wherein('id', explode(', ', $productdata->connections))
                ->orderBy('id', 'ASC')
                ->get();

            $productExecution = ProductExecution::query()
                ->select('id', 'execution_name')
                ->wherein('id', explode(', ', $productdata->execution))
                ->orderBy('id', 'ASC')
                ->get();

            $enquiryType = PurchaseEnquiry::$enquiryType;

            return response()->json(['status' => true, 'message' => trans('translation.Added product successfully'), 'data' => ['connection' => $productConnection, 'execution' => $productExecution, 'enquiry' => $enquiryType]]);
        } else {
            return response()->json(['status' => false, 'message' => trans('translation.Something went wrong'), 'data' => []], 200);
        }
    }

    public function get(Request $request)
    {
        $user = Auth::user();
        $status = PurchaseEnquiry::$statusc;
        $data = PurchaseEnquiry::query()
            ->join("purchase_enquiry_products", "purchase_enquiry_products.customer_enquiry_id", "=", "purchase_enquiry.id")
            ->leftJoin("product", DB::raw("FIND_IN_SET(product.id,purchase_enquiry_products.product_id)"), ">", DB::raw("'0'"))
            ->where('purchase_enquiry.customer_id', $user->id)
            ->where('purchase_enquiry.status', '!=', 2)
            ->select(
                'purchase_enquiry.*',
                DB::raw("GROUP_CONCAT(product.product_name) as product_name"),
                DB::raw("GROUP_CONCAT(DISTINCT product.id) as pro_id")
            )
            ->groupBy("purchase_enquiry.id");

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function ($row) use ($status, $user) {
                $offerStatus = OfferPurchaseEnquiry::query()
                    ->where('customer_enquiry_id', $row->id)
                    ->where('customer_id', $user->id)
                    ->get();

                if (isset($offerStatus) && !empty($offerStatus) && count($offerStatus) > 0 && $row->status == 1) {
                    $offer_expired = true;
                    foreach ($offerStatus as $offer) {
                        if ($offer->status == '1' && $offer->created_at > Carbon::parse('-12 hours')) {
                            $offer_expired = false;
                        }
                    }
                    if (isset($offer_expired) && $offer_expired) {
                        $row->status = 5;
                    } else {
                        $row->status = 2;
                    }
                } elseif ($row->created_at < Carbon::parse('-24 hours')) {
                    $row->status = 4;
                }
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })
            ->editColumn('offers', function ($row) use ($user) {
                $offers = OfferPurchaseEnquiry::query()
                    ->select(
                        DB::raw('CONCAT(dealer.first_name," ",dealer.last_name) as dealer_name'),
                        DB::raw("ROUND(SUM(offer_details.offer_amount*purchase_enquiry_products.qty), 2) as amount"),
                        'offer_details.vat_rate',
                        'offer_purchase_enquiry.id',
                        'offer_purchase_enquiry.created_at',
                        DB::raw("ADDTIME(offer_purchase_enquiry.created_at, '12:00:00') as valid_upto")
                    )
                    ->join('purchase_enquiry_products', 'purchase_enquiry_products.customer_enquiry_id', 'offer_purchase_enquiry.customer_enquiry_id')
                    ->join('offer_details', function ($q) {
                        $q->on('offer_details.offer_id', '=', 'offer_purchase_enquiry.id');
                        $q->on('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
                    })
                    ->join('users as dealer', 'dealer.id', 'offer_purchase_enquiry.dealer_id')
                    ->where('offer_purchase_enquiry.customer_enquiry_id', $row->id)
                    ->groupBy("offer_purchase_enquiry.id")
                    ->get()
                    ->toArray();
                $final_offers_arr = [];
                if ($offers) {
                    foreach ($offers as $key => $offer) {
                        $temp = $offer;
                        if (!empty($offer['vat_rate'])) {
                            $total_vat_amount = ($offer['amount'] * $offer['vat_rate']) / 100;
                            $temp['amount'] = round($total_vat_amount + $offer['amount'], 2);
                            $temp['amount'] = number_format((float)$temp['amount'], 2, '.', '');
                        }
                        $final_offers_arr[] = $temp;
                    }
                }
                return $final_offers_arr;
            })
            ->addColumn('now_time', function ($row) {
                return Carbon::now()->format('Y-m-d H:i:s');
            })
            ->editColumn('products', function ($row) use ($user) {
                $pro_ids = explode(',', $row->pro_id);
                $productData = [];
                foreach ($pro_ids as $pro_id) {
                    $productData[] = Product::query()
                        ->join('product_category', 'product_category.id', '=', 'product.category_id')
                        ->join('product_type', 'product_type.id', '=', 'product.type_id')
                        ->join('brand', 'brand.id', '=', 'product.brand_id')
                        ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'product.id')
                        ->where('product.id', $pro_id)
                        ->groupBy('product.id')
                        ->select(
                            'product.*',
                            DB::raw('CONCAT(product.product_name," (",brand.brand_name,")") AS product_name'),
                            'brand.brand_name',
                            'product_category.category_name',
                            'product_type.type_name',
                            DB::raw('group_concat(IFNULL(product_attributes.connection_id, "null")) as attribute_connection_ids'),
                            DB::raw('group_concat(IFNULL(product_attributes.execution_id, "null")) as attribute_execution_ids'),
                            DB::raw('group_concat(product_attributes.in_stock) as attribute_in_stocks'),
                            DB::raw('group_concat(product_attributes.is_used) as attribute_is_useds'),
                            DB::raw('group_concat(product_attributes.ready_for_demo) as attribute_ready_for_demos')
                        )
                        ->get();
                }
                return (isset($productData) && $productData) ? $productData : "";
            })
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('enquiry_description', 'LIKE', "%$search%");
                        $w->orWhere('product.product_name', 'LIKE', "%$search%");
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
            $loginUser = Auth::User();
            // $customer_country = getCustomerCountry($loginUser);
            $customer_country = $request->country;
            $rules = array(
                'selected_product_ids' => 'required',
            );

            if (isset($request->extrafields) && !empty($request->extrafields)) {
                foreach (explode(',', $request->extrafields) as $extrafield) {
                    if (!str_contains($extrafield, 'productenquiry')) {
                        $rules[$extrafield] = 'required';
                    }
                }
            }

            $messsages = array();

            if (isset($request->extrafields) && !empty($request->extrafields)) {
                foreach (explode(',', $request->extrafields) as $extrafield) {
                    if (str_contains($extrafield, 'productenquiry')) {
                        $messsages[$extrafield . '.required'] = trans('translation.The product attribute field is required');
                    } elseif (str_contains($extrafield, 'productconnection')) {
                        $messsages[$extrafield . '.required'] = trans('translation.The product connection field is required');
                    } elseif (str_contains($extrafield, 'productexecution')) {
                        $messsages[$extrafield . '.required'] = trans('translation.The product execution field is required');
                    } elseif (str_contains($extrafield, 'product_qty')) {
                        $messsages[$extrafield . '.required'] = trans('translation.The product qty field is required');
                    } else {
                        $messsages[$extrafield . '.required'] = trans('translation.The field is required');
                    }
                }
            }

            $validator = Validator::make($request->all(), $rules, $messsages);


            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $purchaseEnquiry = new PurchaseEnquiry;
                $purchaseEnquiry->customer_id = $loginUser->id;
                $purchaseEnquiry->enquiry_description = (isset($request->enquiry_description) && !empty($request->enquiry_description)) ? $request->enquiry_description : NULL;
                $purchaseEnquiry->updated_at = Carbon::now();

                if ($purchaseEnquiry->save()) {
                    if (isset($request->selected_product_ids) && !empty($request->selected_product_ids)) {
                        foreach ($request->selected_product_ids as $enquiry_product_id) {
                            $purchaseEnquiryProduct = new PurchaseEnquiryProduct;
                            $purchaseEnquiryProduct->customer_enquiry_id = $purchaseEnquiry->id;
                            $purchaseEnquiryProduct->product_id = $enquiry_product_id;
                            $purchaseEnquiryProduct->qty = (isset($request->{'product_qty_' . $enquiry_product_id}) && !empty($request->{'product_qty_' . $enquiry_product_id})) ? $request->{'product_qty_' . $enquiry_product_id} : 1;
                            $purchaseEnquiryProduct->connection_ids = (isset($request->{'productconnection_ids_' . $enquiry_product_id}) && !empty($request->{'productconnection_ids_' . $enquiry_product_id})) ? implode(",", (array) $request->{'productconnection_ids_' . $enquiry_product_id}) : null;
                            $purchaseEnquiryProduct->execution_ids = (isset($request->{'productexecution_ids_' . $enquiry_product_id}) && !empty($request->{'productexecution_ids_' . $enquiry_product_id})) ? implode(",", (array) $request->{'productexecution_ids_' . $enquiry_product_id}) : null;
                            $purchaseEnquiryProduct->attribute_ids = (isset($request->{'productenquiry_ids_' . $enquiry_product_id}) && !empty($request->{'productenquiry_ids_' . $enquiry_product_id})) ? implode(",", (array) $request->{'productenquiry_ids_' . $enquiry_product_id}) : null;
                            $purchaseEnquiryProduct->updated_at = Carbon::now();
                            $purchaseEnquiryProduct->save();
                        }
                    }
                }

                if ($purchaseEnquiry->save()) {
                    $dealer_pro = [];

                    foreach ($request->selected_product_ids as $enquiry_product_id) {
                        $dealer_query = Product::query()
                            ->select(
                                'product.id',
                                DB::raw('GROUP_CONCAT(brand.brand_name, " ", product.product_name)  as name'),
                                'product.brand_id',
                                'dealer_brand.dealer_id'
                            )
                            ->where('product.id', $enquiry_product_id)
                            ->join('dealer_brand', 'dealer_brand.brand_id', 'product.brand_id')
                            ->join('brand', 'brand.id', 'product.brand_id')
                            ->whereNull('dealer_brand.deleted_at')
                            ->whereNull('product.deleted_at');

                        if (isset($request->{'productenquiry_ids_' . $enquiry_product_id}) && !empty($request->{'productenquiry_ids_' . $enquiry_product_id})) {
                            $dealer_query->join('product_attributes', function ($query) {
                                $query->on('product_attributes.product_id', '=', 'product.id');
                                $query->on('product_attributes.dealer_id', '=', 'dealer_brand.dealer_id');
                            });
                            if (isset($request->{'connection_ids' . $enquiry_product_id}) && !empty($request->{'connection_ids' . $enquiry_product_id})) {
                                $dealer_query->where('product_attributes.connection_id', $request->{'connection_ids' . $enquiry_product_id});
                            }

                            if (isset($request->{'execution_ids' . $enquiry_product_id}) && !empty($request->{'execution_ids' . $enquiry_product_id})) {
                                $dealer_query->where('product_attributes.execution_id', $request->{'execution_ids' . $enquiry_product_id});
                            }
                            $dealer_query->where(function ($q) use ($enquiry_product_id, $request) {
                                if (in_array(1, $request->{'productenquiry_ids_' . $enquiry_product_id})) {
                                    $q->where('product_attributes.in_stock', true);
                                }
                                if (in_array(2, $request->{'productenquiry_ids_' . $enquiry_product_id})) {
                                    $q->orWhere('product_attributes.is_used', true);
                                }
                                if (in_array(3, $request->{'productenquiry_ids_' . $enquiry_product_id})) {
                                    $q->orWhere('product_attributes.ready_for_demo', true);
                                }
                            });
                        }
                        $dealer_query->join('users as dealer', 'dealer_brand.dealer_id', 'dealer.id');
                        $dealer_query->where('country', $customer_country);

                        $data_enq = $dealer_query->groupBy('dealer_brand.dealer_id')->get()->toArray();

                        if (isset($data_enq) && !empty($data_enq)) {
                            $dealer_pro[] = $data_enq;
                        }
                    }
                    $dealer_enquiry_list = [];
                    foreach ($dealer_pro as $dealer_enquiry_value) {
                        $count = count($dealer_enquiry_value);
                        for ($i = 0; $i <= ($count - 1); $i++) {
                            $dealer_enquiry_list[$dealer_enquiry_value[$i]['dealer_id']]['pid'][] = $dealer_enquiry_value[$i]['id'];
                            $dealer_enquiry_list[$dealer_enquiry_value[$i]['dealer_id']]['pname'][] = $dealer_enquiry_value[$i]['name'];
                            $dealer_enquiry_list[$dealer_enquiry_value[$i]['dealer_id']]['brand_id'][] = $dealer_enquiry_value[$i]['brand_id'];
                        }
                    }
                    foreach ($dealer_enquiry_list as $dkey => $list) {
                        $dealer_brand_model = new DealerPurchaseEnquiry;
                        $dealer_brand_model->customer_enquiry_id = $purchaseEnquiry->id;
                        $dealer_brand_model->dealer_id = $dkey;
                        $dealer_brand_model->for_brand_id = implode(',', array_unique($list['brand_id']));
                        $dealer_brand_model->for_product_id = implode(',', array_unique($list['pid']));
                        $product_enquiry_attribute = explode(",", $list['pname']['0']);
                        $dealer_brand_model->save();

                        $userSchema = User::find($dkey);
                        if (isset($userSchema) && !empty($userSchema)) {
                            for ($i = 0; $i <= (count($product_enquiry_attribute) - 1); $i++) {
                                $dataNotify = [
                                    'name' => $userSchema->company_name,
                                    'mailGretting' => trans('translation.Hello'),
                                    'mailSubject' => trans('translation.enquirycreate_email_subject', ['product' => $product_enquiry_attribute[$i]]),
                                    'mailBody' => trans('translation.enquirycreate_email_body', ['product' => $product_enquiry_attribute[$i], 'url' => '<a href="' . route('dealer.dealerEnquiryView', $purchaseEnquiry->id) . '">' . route('dealer.dealerEnquiryView', $purchaseEnquiry->id) . '</a>']),
                                    'title' => trans('translation.purchaseenquiry_created_title'),
                                    'type' => 'purchaseEnquiry',
                                    'status' => 'created',
                                    'body' => trans('translation.purchaseenquiry_created_body', ['name' => trans('translation.Customer')]),
                                    'senderId' => $loginUser->id,
                                    'url' => route('dealer.dealerEnquiryView', $purchaseEnquiry->id),
                                    'dealer_id' => $userSchema->id,
                                    'id' => $purchaseEnquiry->id
                                ];
                                Notification::send($userSchema, new PurchaseEnquiryNotification($dataNotify));
                            }
                        }
                    }

                    $result = ['status' => true, 'message' => trans('translation.Purchase enquiry sending successfully'), 'data' => []];
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Error in purchase enquiry sending'), 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
        }
        return response()->json($result);
    }

    public function dealerEnquiry()
    {
        $loginUser = Auth::user();
        if ($loginUser->role_type == 2) {
            return view('purchaseEnquiry.dealer_purchase_enquiry');
        } elseif ($loginUser->role_type == 1) {
            $dealers = User::where('role_type', 2)->get();
            $countries = User::$countries;
            return view('purchaseEnquiry.admin_purchase_enquiry', compact('dealers', 'countries'));
        }
        return abort('404');
    }

    public function dealerEnquiryView(Request $request)
    {
        $id = $request->id;
        $fullUrl = $request->fullUrl();
        if (str_contains($fullUrl, '?oid=')) {
            return redirect()->route('dealer.dealerEnquiryView', $id);
        }
        if (isset($id) && !empty($id)) {
            $loginUser = Auth::user();
            if ($loginUser->role_type == 2) {
                $brandIds = DealerBrand::query()
                    ->where('dealer_id', $loginUser->id)
                    ->select('brand_id')
                    ->distinct()
                    ->get()
                    ->pluck('brand_id')
                    ->toArray();
                $qry = PurchaseEnquiryProduct::query()
                    ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
                    ->leftJoin('dealer_purchase_enquiry as dpe', 'purchase_enquiry.id', '=', 'dpe.customer_enquiry_id')
                    ->join('users as customer', 'purchase_enquiry.customer_id', '=', 'customer.id')
                    ->leftJoin('users as dealer', 'dpe.dealer_id', '=', 'dealer.id')
                    ->join("product", DB::raw("FIND_IN_SET(product.id,purchase_enquiry_products.product_id)"), ">", DB::raw("'0'"))
                    ->where('purchase_enquiry_products.customer_enquiry_id', $request->id)
                    ->whereIn('product.brand_id', $brandIds)
                    ->select(
                        'purchase_enquiry.*',
                        DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name"),
                        DB::raw("GROUP_CONCAT(DISTINCT product.id SEPARATOR ', ') as pro_id"),
                        DB::raw("dealer.email as dealer_email"),
                        DB::raw("dealer.phone as dealer_phone"),
                        DB::raw("dealer.id as dealer_id"),
                        DB::raw("dpe.customer_enquiry_id as dealer_customer_enquiry_id"),
                        "dealer.company_name",
                        "dealer.shop_start_time",
                        "dealer.shop_end_time",
                        DB::raw('CONCAT(customer.first_name," ",customer.last_name) as customer_name'),
                        DB::raw("customer.email as customer_email"),
                        DB::raw("customer.phone as customer_phone"),
                        DB::raw("customer.street as customer_street"),
                        DB::raw("customer.house_number as customer_house_number"),
                        DB::raw("customer.zipcode as customer_zipcode"),
                        DB::raw("customer.city as customer_city"),
                        DB::raw("customer.country as customer_country"),
                        DB::raw("customer.id as customer_id"),
                    );
                $data = $qry->groupBy("purchase_enquiry.id")->first();

                if (isset($data) && !empty($data)) {
                    $checkoffer = OfferPurchaseEnquiry::query()
                        ->where('dealer_id', $loginUser->id)
                        ->where('customer_id', $data->customer_id)
                        ->where('customer_enquiry_id', $data->dealer_customer_enquiry_id)
                        ->count();

                    if ($data->status == 2) {
                        notificationMarkAsRead('purchaseEnquiry', $id, 'offerDeclined');
                    }
                    notificationMarkAsRead('purchaseEnquiry', $id, 'created');
                    $loginUser = Auth::user();
                    return view('purchaseEnquiry.dealer_purchase_view', compact('id', 'data', 'checkoffer', 'loginUser'));
                } else {
                    return abort('404');
                }
            } else {
                return abort('404');
            }
        } else {
            return abort('404');
        }
    }

    public function customerEnquiryView(Request $request)
    {
        $id = $request->id;
        $loginUser = Auth::user();
        if (isset($id) && !empty($id)) {
            if (isset($request->oid) && !empty($request->oid)) {
                Session::put('offerShow', $request->oid);
                Session::save();
                $urlr = route('customer.customerEnquiryView', $id) . '?notify=1';
                return redirect($urlr);
            } else {
                if (isset($request->notify) && !empty($request->notify) && session()->has('offerShow')) {
                    $offer_id = Session::get('offerShow');
                    Session::forget('offerShow');
                } else {
                    $offer_id = '';
                    Session::forget('offerShow');
                }
            }
            if ($loginUser->role_type == 3 || $loginUser->role_type == 1) {
                $qry = PurchaseEnquiryProduct::query()
                    ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
                    ->leftJoin('dealer_purchase_enquiry as dpe', 'purchase_enquiry.id', '=', 'dpe.customer_enquiry_id')
                    ->join('users as customer', 'purchase_enquiry.customer_id', '=', 'customer.id')
                    ->leftJoin('users as dealer', 'dpe.dealer_id', '=', 'dealer.id')
                    ->join("product", DB::raw("FIND_IN_SET(product.id,purchase_enquiry_products.product_id)"), ">", DB::raw("'0'"))
                    ->where('purchase_enquiry_products.customer_enquiry_id', $request->id);
                if ($loginUser->role_type == 3) {
                    $qry->where('purchase_enquiry.customer_id', $loginUser->id);
                }
                $qry->select(
                    'purchase_enquiry.*',
                    DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name"),
                    DB::raw("GROUP_CONCAT(DISTINCT product.id SEPARATOR ', ') as pro_id"),
                    DB::raw("dealer.email as dealer_email"),
                    DB::raw("dealer.phone as dealer_phone"),
                    DB::raw("dealer.shop_address as dealer_shop_address"),
                    "dealer.company_name",
                    "dealer.shop_start_time",
                    "dealer.shop_end_time",
                    DB::raw('CONCAT(customer.first_name," ",customer.last_name) as customer_name'),
                    DB::raw("customer.email as customer_email"),
                    DB::raw("customer.phone as customer_phone"),
                    DB::raw("customer.shop_address as customer_shop_address")
                );
                $data = $qry->groupBy("purchase_enquiry.id")->first();
                if (isset($data) && !empty($data)) {
                    $payment_method = OfferPurchaseEnquiry::$payment_method;
                    return view('purchaseEnquiry.customer_purchase_view', compact('id', 'data', 'payment_method', 'loginUser', 'offer_id'));
                } else {
                    return abort('404');
                }
            } else {
                return abort('404');
            }
        } else {
            return abort('404');
        }
    }
    public function adminEnquiryView(Request $request)
    {
        $id = $request->id;
        $loginUser = Auth::user();
        if (isset($id) && !empty($id)) {
            if (isset($request->oid) && !empty($request->oid)) {
                Session::put('offerShow', $request->oid);
                Session::save();
                $urlr = route('adminEnquiryView', $id) . '?notify=1';
                return redirect($urlr);
            } else {
                if (isset($request->notify) && !empty($request->notify) && session()->has('offerShow')) {
                    $offer_id = Session::get('offerShow');
                    Session::forget('offerShow');
                } else {
                    $offer_id = '';
                    Session::forget('offerShow');
                }
            }
            if ($loginUser->role_type == 1) {
                $qry = PurchaseEnquiryProduct::query()
                    ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
                    ->leftJoin('dealer_purchase_enquiry as dpe', 'purchase_enquiry.id', '=', 'dpe.customer_enquiry_id')
                    ->join('users as customer', 'purchase_enquiry.customer_id', '=', 'customer.id')
                    ->leftJoin('users as dealer', 'dpe.dealer_id', '=', 'dealer.id')
                    ->join("product", DB::raw("FIND_IN_SET(product.id,purchase_enquiry_products.product_id)"), ">", DB::raw("'0'"))
                    ->where('purchase_enquiry_products.customer_enquiry_id', $request->id);
                if ($loginUser->role_type == 3) {
                    $qry->where('purchase_enquiry.customer_id', $loginUser->id);
                }
                $qry->select(
                    'purchase_enquiry.*',
                    DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name"),
                    DB::raw("GROUP_CONCAT(DISTINCT product.id SEPARATOR ', ') as pro_id"),
                    DB::raw("dealer.email as dealer_email"),
                    DB::raw("dealer.phone as dealer_phone"),
                    DB::raw("dealer.shop_address as dealer_shop_address"),
                    "dealer.company_name",
                    "dealer.shop_start_time",
                    "dealer.shop_end_time",
                    DB::raw('CONCAT(customer.first_name," ",customer.last_name) as customer_name'),
                    DB::raw("customer.email as customer_email"),
                    DB::raw("customer.phone as customer_phone"),
                    DB::raw("customer.shop_address as customer_shop_address")
                );
                $data = $qry->groupBy("purchase_enquiry.id")->first();
                if (isset($data) && !empty($data) && $data->status != 2) {
                    $payment_method = OfferPurchaseEnquiry::$payment_method;
                    return view('purchaseEnquiry.admin_purchase_view', compact('id', 'data', 'payment_method', 'loginUser', 'offer_id'));
                } else {
                    return abort('404');
                }
            } else {
                return abort('404');
            }
        } else {
            return abort('404');
        }
    }

    public function getDealerEnquiry(Request $request)
    {
        $user = Auth::user();
        if ($user->role_type == 2) {
            $brandIds = DealerBrand::query()
                ->where('dealer_id', $user->id)
                ->select('brand_id')
                ->distinct()
                ->get()
                ->toArray();
        }
        $status = PurchaseEnquiry::$status;
        $data = PurchaseEnquiry::query()
            ->join('users', 'purchase_enquiry.customer_id', '=', 'users.id')
            ->join('dealer_purchase_enquiry as dpe', 'purchase_enquiry.id', '=', 'dpe.customer_enquiry_id')
            ->join('users as dealer', 'dpe.dealer_id', '=', 'dealer.id')
            ->join('purchase_enquiry_products', 'purchase_enquiry_products.customer_enquiry_id', '=', 'purchase_enquiry.id')
            ->join("product", DB::raw("FIND_IN_SET(product.id,dpe.for_product_id)"), ">", DB::raw("'0'"))
            ->leftjoin("brand", DB::raw("FIND_IN_SET(brand.id,dpe.for_brand_id)"), ">", DB::raw("'0'"))
            ->select(
                'purchase_enquiry.*',
                DB::raw("GROUP_CONCAT(DISTINCT product.id) as pro_id"),
                DB::raw('CONCAT(users.first_name," ",users.last_name) AS name'),
                'users.country'
            )
            ->whereNull('dpe.deleted_at')
            ->where('purchase_enquiry.status', '!=', 2)
            ->groupBy("purchase_enquiry.id");


        if ($user->role_type != 1) {
            $data->where('dpe.dealer_id', $user->id);
        }

        if ($user->role_type == 2) {
            $data->whereIn('product.brand_id', $brandIds);
        }

        if (isset($request->country) && !empty($request->country)) {
            $country = $request->country;
            $data->where(function ($q) use ($country) {
                $q->orWhere('dealer.country', $country);
                $q->orWhere('users.country', $country);
            });
        }


        if (isset($request->dealer) && !empty($request->dealer)) {
            $data->where('dpe.dealer_id', $request->dealer);
        }

        if (isset($request->start_date) && !empty($request->start_date)) {
            $data->whereDate('purchase_enquiry.created_at', '>=', date('Y-m-d', strtotime($request->start_date)));
        }

        if (isset($request->end_date) && !empty($request->end_date)) {
            $data->whereDate('purchase_enquiry.created_at', '<=', date('Y-m-d', strtotime($request->end_date)));
        }

        return datatables()::of($data)
            ->addIndexColumn()
            ->addColumn('status_name', function ($row) use ($status, $user) {
                $offerStatus = OfferPurchaseEnquiry::query()
                    ->where('customer_enquiry_id', $row->id)
                    ->get();

                if ($offerStatus->toArray()) {
                    $collection = collect($offerStatus);
                    $result_new = $collection->where('dealer_id', $user->id)->first();
                    if (!isset($result_new->id)) {
                        if ($row->created_at < Carbon::parse('-24 hours')) {
                            $row->status = 4;
                            return isset($status[4]) ? $status[4] : "";
                        } else {
                            return isset($status[0]) ? $status[0] : "";
                        }
                    }
                    $collection = collect($offerStatus);
                    $result_Accepted = $collection->where('status', 2)->first();
                    if (isset($result_Accepted->id)) {
                        if ($result_Accepted->dealer_id == $user->id) {
                            return isset($status[2]) ? $status[2] : "";
                        } else {
                            return isset($status[3]) ? $status[3] : "";
                        }
                    }

                    $collection = collect($offerStatus);
                    $result_expired = $collection->where('dealer_id', $user->id)->where('status', 1)->where('created_at', '<', Carbon::parse('-12 hours'))->first();
                    if (isset($result_expired->id)) {
                        return isset($status[5]) ? $status[5] : "";
                    } else {
                        return isset($status[1]) ? $status[1] : "";
                    }
                } else {
                    if ($row->created_at < Carbon::parse('-24 hours')) {
                        $row->status = 4;
                        return isset($status[4]) ? $status[4] : "";
                    } else {
                        return isset($status[0]) ? $status[0] : "";
                    }
                }
                if ($row->created_at < Carbon::parse('-24 hours')) {
                    $row->status = 4;
                    return isset($status[4]) ? $status[4] : "";
                }
                return isset($status[$row->status]) ? $status[$row->status] : "";
            })
            ->editColumn('products', function ($row) use ($user) {
                $pro_ids = explode(',', $row->pro_id);
                $productData = [];
                foreach ($pro_ids as $pro_id) {
                    $product_query = Product::query()
                        ->join('product_category', 'product_category.id', '=', 'product.category_id')
                        ->join('product_type', 'product_type.id', '=', 'product.type_id')
                        ->join('brand', 'brand.id', '=', 'product.brand_id')
                        ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'product.id');
                    if ($user->role_type != 1) {
                        $product_query->join('dealer_brand', function ($join) use ($user) {
                            $join->on('brand.id', '=', 'dealer_brand.brand_id')
                                ->whereNull('dealer_brand.deleted_at')
                                ->where('dealer_brand.dealer_id', $user->id);
                        });
                    }
                    $productData[] = $product_query->where('product.id', $pro_id)
                        ->groupBy('product.id')
                        ->select(
                            'product.*',
                            DB::raw('CONCAT(product.product_name," (",brand.brand_name,")") AS product_name'),
                            'brand.brand_name',
                            'product_category.category_name',
                            'product_type.type_name',
                            DB::raw('group_concat(IFNULL(product_attributes.connection_id, "null")) as attribute_connection_ids'),
                            DB::raw('group_concat(IFNULL(product_attributes.execution_id, "null")) as attribute_execution_ids'),
                            DB::raw('group_concat(product_attributes.in_stock) as attribute_in_stocks'),
                            DB::raw('group_concat(product_attributes.is_used) as attribute_is_useds'),
                            DB::raw('group_concat(product_attributes.ready_for_demo) as attribute_ready_for_demos')
                        )
                        ->get();
                }
                return (isset($productData) && $productData) ? $productData : "";
            })
            ->addColumn('now_time', function ($row) {
                return Carbon::now()->format('Y-m-d H:i:s');
            })
            ->addColumn('role_type', function ($row) use ($user) {
                return $user->role_type;
            })
            ->addColumn('valid_upto', function ($row) {
                return Carbon::parse($row->created_at)->addHour(24)->format('Y-m-d H:i:s');
            })
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('enquiry_description', 'LIKE', "%$search%");
                        $w->orWhere('product.product_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function getDetailEnquiry(Request $request)
    {
        $loginUser = Auth::user();
        if ($loginUser->role_type == 2) {
            return response()->json(['url' => route('dealer.dealerEnquiryView', ['id' => $request->id])]);
        } elseif ($loginUser->role_type == 3) {
            return response()->json(['url' => route('customer.customerEnquiryView', ['id' => $request->id])]);
        } elseif ($loginUser->role_type == 1) {
            return response()->json(['url' => route('adminEnquiryView', ['id' => $request->id])]);
        }

        $result = ['status' => false, 'message' => ""];
        if ($request->ajax()) {
            if ($loginUser->role_type == 2) {
                $brandIds = DealerBrand::query()
                    ->where('dealer_id', $loginUser->id)
                    ->select('brand_id')
                    ->distinct()
                    ->get()
                    ->pluck('brand_id')
                    ->toArray();
            }

            $qry = PurchaseEnquiryProduct::query()
                ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
                ->leftJoin('dealer_purchase_enquiry as dpe', 'purchase_enquiry.id', '=', 'dpe.customer_enquiry_id')
                ->join('users as customer', 'purchase_enquiry.customer_id', '=', 'customer.id')
                ->leftJoin('users as dealer', 'dpe.dealer_id', '=', 'dealer.id')
                ->join("product", DB::raw("FIND_IN_SET(product.id,purchase_enquiry_products.product_id)"), ">", DB::raw("'0'"))
                ->select(
                    'purchase_enquiry.*',
                    DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name"),
                    DB::raw("GROUP_CONCAT(DISTINCT product.id SEPARATOR ', ') as pro_id"),
                    DB::raw("dealer.email as dealer_email"),
                    DB::raw("dealer.phone as dealer_phone"),
                    DB::raw("dealer.shop_address as dealer_shop_address"),
                    "dealer.company_name",
                    "dealer.shop_start_time",
                    "dealer.shop_end_time",
                    DB::raw('CONCAT(customer.first_name," ",customer.last_name) as customer_name'),
                    DB::raw("customer.email as customer_email"),
                    DB::raw("customer.phone as customer_phone"),
                    DB::raw("customer.shop_address as customer_shop_address")
                );
            $qry->where('purchase_enquiry_products.customer_enquiry_id', $request->id);
            if ($loginUser->role_type == 2) {
                $qry->whereIn('product.brand_id', $brandIds);
            }
            $data = $qry->groupBy("purchase_enquiry.id")->first();

            if (isset($data->id)) {
                $data->date = date("d.m.Y H:i", strtotime($data->created_at));
                $pro_data = [];
                if ($data->pro_id && $data->product_name) {
                    $pro_id_array = explode(',', $data->pro_id);
                    $product_name_array = explode(',', $data->product_name);
                    foreach ($pro_id_array as $key => $pro_id) {
                        if (isset($product_name_array[$key])) {
                            $pro_data[$pro_id] = $product_name_array[$key];
                        }
                    }
                }
                $result = ['status' => true, 'message' => '', 'data' => $data, 'pro_data' => $pro_data];
            } else {
                $result = ['status' => false, 'message' => '', 'data' => []];
            }
        }
        return response()->json($result);
        exit();
    }

    public function sendOffer(Request $request)
    {
        if ($request->ajax()) {
            $loginUser = Auth::User();
            if ($loginUser->role_type == 2) {
                $brandIds = DealerBrand::query()
                    ->where('dealer_id', $loginUser->id)
                    ->select('brand_id')
                    ->distinct()
                    ->get()
                    ->pluck('brand_id')
                    ->toArray();
            }
            $currency = getDealerCurrencyType($loginUser);
            $offer_amount = [];
            foreach ($request->offer_amount as $key => $value) {
                if ($currency == 'eur') {
                    $value = str_replace('.', '', $value);
                    $value = str_replace(',', '.', $value);
                    $value = str_replace('€', '', $value);
                    $value = trim($value);
                    $offer_amount[] = $value;
                } else {
                    $value = str_replace(',', '', $value);
                    $value = str_replace('$', '', $value);
                    $value = trim($value);
                    $offer_amount[] = $value;
                }
            }
            $request->merge([
                'offer_amount' => $offer_amount
            ]);
            $rules = array(
                'customer_enquiry_id' => 'required',
                'offer_amount.*' => 'required|numeric',
                'offer_description' => 'required|string|max:1000',
                'delivery_time' => 'required|numeric|max:99|min:1',
                'delivery_time_type' => 'required',
                'total_vat_amount' => 'required|numeric|gt:0',
            );
            $messsages['delivery_time.required'] = trans('validation.custom.delivery_time.required');
            $messsages['delivery_time.min'] = trans('validation.custom.delivery_time.min');
            $messsages['delivery_time.max'] = trans('validation.custom.delivery_time.max');
            $messsages['delivery_time.numeric'] = trans('validation.custom.delivery_time.numeric');
            $messsages['customer_enquiry_id.required'] = trans('validation.custom.customer_enquiry_id.required');
            $messsages['offer_amount.required'] = trans('validation.custom.offer_amount.required');
            $messsages['offer_amount.numeric'] = trans('validation.custom.offer_amount.numeric');
            $messsages['offer_description.required'] = trans('validation.custom.offer_description.required');
            $messsages['offer_description.max'] = trans('validation.custom.offer_description.max');
            $messsages['total_vat_amount.required'] = trans('validation.custom.total_vat_amount.required');
            $messsages['total_vat_amount.numeric'] = trans('validation.custom.total_vat_amount.numeric');
            $messsages['total_vat_amount.gt'] = trans('validation.custom.total_vat_amount.gt');

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $qry = PurchaseEnquiryProduct::query()
                    ->where('purchase_enquiry_products.customer_enquiry_id', $request->id)
                    ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
                    ->select(
                        "purchase_enquiry.*",
                        DB::raw("GROUP_CONCAT(DISTINCT product.id SEPARATOR ', ') as product_id"),
                        DB::raw("GROUP_CONCAT(product.product_name SEPARATOR ',') as product_name"),
                        DB::raw("GROUP_CONCAT(brand.brand_name SEPARATOR ',') as brand_name"),
                    );

                if ($loginUser->role_type == 2) {
                    $qry->join('dealer_purchase_enquiry', 'dealer_purchase_enquiry.customer_enquiry_id', '=', 'purchase_enquiry_products.customer_enquiry_id');
                    $qry->join('product', function ($query) use ($brandIds) {
                        $query->on('product.id', '=', 'purchase_enquiry_products.product_id');
                        $query->on(DB::raw("FIND_IN_SET(product.id,dealer_purchase_enquiry.for_product_id)"), ">", DB::raw("'0'"));
                        $query->whereIn('product.brand_id', $brandIds);
                    });
                    $qry->where('dealer_purchase_enquiry.dealer_id', $loginUser->id);
                } else if ($loginUser->role_type == 3) {
                    $qry->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id');
                    $qry->where('purchase_enquiry.customer_id', $loginUser->id);
                }

                $purchaseEnquiry = $qry->join('brand', 'brand.id', '=', 'product.brand_id')->groupBy('purchase_enquiry.id')->first();

                if (isset($purchaseEnquiry->id)) {
                    if ($purchaseEnquiry->status == 1) {
                        $offerPE = new OfferPurchaseEnquiry;

                        $offerPE->dealer_id = $loginUser->id;

                        $offerPE->customer_enquiry_id = (!empty($purchaseEnquiry->id)) ? $purchaseEnquiry->id : "";
                        $offerPE->product_id = (!empty($purchaseEnquiry->product_id)) ? $purchaseEnquiry->product_id : "";
                        $offerPE->customer_id = (!empty($purchaseEnquiry->customer_id)) ? $purchaseEnquiry->customer_id : "";

                        $offerPE->offer_description = $request->offer_description;
                        $offerPE->delivery_time = $request->delivery_time;
                        $offerPE->delivery_time_type = $request->delivery_time_type;

                        $productList = [];
                        $productNames = explode(',', $purchaseEnquiry->product_name);
                        $brandNames = explode(',', $purchaseEnquiry->brand_name);

                        for ($i = 0; $i < count($productNames); $i++) {
                            $productList[] = $brandNames[$i] . ' ' . $productNames[$i];
                        }

                        if ($offerPE->save()) {
                            $userSchema = User::find($purchaseEnquiry->customer_id);
                            $dataNotify = [
                                'name' => $userSchema->first_name . ' ' . $userSchema->last_name,
                                'mailGretting' => trans('translation.Hello'),
                                'mailSubject' => trans('translation.offercreate_email_subject', ['product' => implode(',', $productList)]),
                                'mailBody' => trans('translation.offercreate_email_body', ['product' => implode(',', $productList), 'url' => '<a href="' . route('customer.customerEnquiryView', $purchaseEnquiry->id) . '?oid=' . $offerPE->id . '">' . route('customer.customerEnquiryView', $purchaseEnquiry->id) . '</a>']),
                                'title' => trans('translation.purchaseenquiry_offer_created_title'),
                                'type' => 'purchaseEnquiry',
                                'status' => 'offerCreated',
                                'body' => trans('translation.purchaseenquiry_offer_created_body', ['name' => trans('translation.Dealer')]),
                                'senderId' => $loginUser->id,
                                'url' => route('customer.customerEnquiryView', $purchaseEnquiry->id),
                                'dealer_id' => $userSchema->id,
                                'id' => $offerPE->id
                            ];
                            Notification::send($userSchema, new PurchaseEnquiryNotification($dataNotify));
                            if ($request->offer_amount) {
                                $proId = (!empty($purchaseEnquiry->product_id)) ? explode(",", $purchaseEnquiry->product_id) : [];

                                foreach ($request->offer_amount as $key2 => $offer_amount) {
                                    if (isset($proId[$key2]) && $proId[$key2]) {
                                        $offerDetails = new OfferDetails();
                                        if (config('common.is_exclude_vat_from_offer')) {
                                            $vat_rate = $loginUser->vat;
                                            if ($offer_amount > 0 && !empty($vat_rate)) {
                                                $offerDetails->vat_amount = round(($offer_amount * $vat_rate) / 100, 2);
                                                $offerDetails->vat_rate = $vat_rate;
                                            }
                                        }
                                        $offerDetails->offer_id = $offerPE->id;
                                        $offerDetails->offer_amount = ($offer_amount) ? $offer_amount : 0;
                                        $offerDetails->product_id = $proId[$key2];
                                        $offerDetails->offer_currency = getDealerCurrencyType($loginUser);
                                        $offerDetails->save();
                                    }
                                }
                            }
                            $result = ['status' => true, 'message' => trans('translation.Price offer sending successfully'), 'data' => []];
                        } else {
                            $result = ['status' => false, 'message' => trans('translation.Error in price offer sending'), 'data' => []];
                        }
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.The enquiry is closed'), 'data' => []];
                    }
                } else {
                    $result = ['status' => false, 'message' => trans('translation.The offer send valid for 24 hours'), 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
        }
        return response()->json($result);
    }

    public function getOfferList(Request $request)
    {
        $loginUser = Auth::user();
        $customer_enquiry_id = ($request->customer_enquiry_id) ? $request->customer_enquiry_id : 0;

        $status = OfferPurchaseEnquiry::$status;

        $qry = OfferPurchaseEnquiry::query()
            ->join('users as dealer', 'offer_purchase_enquiry.dealer_id', '=', 'dealer.id')
            ->select(
                'offer_purchase_enquiry.*',
                DB::raw('CONCAT(dealer.first_name," ",dealer.last_name) as dealer_name'),
                DB::raw("dealer.email as dealer_email"),
                DB::raw("dealer.phone as dealer_phone"),
                DB::raw("dealer.shop_address as dealer_shop_address"),
                "dealer.company_name",
                "dealer.shop_start_time",
                "dealer.shop_end_time",
                DB::raw("CURRENT_TIMESTAMP as currunt_time"),
            )
            ->where('offer_purchase_enquiry.customer_enquiry_id', $customer_enquiry_id);
        if ($loginUser->role_type == 2) {
            $qry = $qry->where('offer_purchase_enquiry.dealer_id', $loginUser->id);
        } else if ($loginUser->role_type == 3) {
            $qry = $qry->where('offer_purchase_enquiry.customer_id', $loginUser->id);
        }

        $data = $qry->groupBy("offer_purchase_enquiry.id");
        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d.m.Y H:i');
            })
            ->addColumn('status_name', function ($row) use ($status) {
                $status_indicate = $row->status;
                if ($row->created_at < Carbon::parse('-12 hours') && $row->status == '1') {
                    $status_indicate = 4;
                }
                return isset($status[$status_indicate]) ? $status[$status_indicate] : "";
            })
            ->addColumn('total_amount', function ($row) {
                $getprice = PurchaseEnquiryProduct::query()
                    ->select(
                        'offer_details.product_id',
                        'offer_details.offer_amount',
                        'purchase_enquiry_products.qty',
                        DB::raw("offer_details.offer_amount*purchase_enquiry_products.qty as amount"),
                        'offer_details.vat_rate',
                        'offer_details.offer_currency'
                    )
                    ->join('offer_purchase_enquiry', 'offer_purchase_enquiry.customer_enquiry_id', '=', 'purchase_enquiry_products.customer_enquiry_id')
                    ->join('offer_details', function ($q) {
                        $q->on('offer_details.offer_id', '=', 'offer_purchase_enquiry.id');
                        $q->on('offer_details.product_id', '=', 'purchase_enquiry_products.product_id');
                    })
                    ->where('purchase_enquiry_products.customer_enquiry_id', $row->customer_enquiry_id)
                    ->where('offer_purchase_enquiry.id', $row->id)
                    ->groupBy("purchase_enquiry_products.product_id")
                    ->get()
                    ->toArray();

                $amount = 0;
                $offer_currency = '';
                foreach ($getprice as $amountp) {
                    if (!empty($amountp['vat_rate'])) {
                        $total_vat_amount = ($amountp['amount'] * $amountp['vat_rate']) / 100;
                        $amountp['amount'] = round($total_vat_amount + $amountp['amount'], 2);
                        $amountp['amount'] = number_format((float)$amountp['amount'], 2, '.', '');
                    }
                    $amount += $amountp['amount'];
                    $offer_currency = $amountp['offer_currency'];
                }
                $amount = round($amount, 2);
                $currency_type = ($offer_currency == 'eur') ? 1 : 2;
                $amount = formatCurrencyOutput($amount, $currency_type, true);
                return $amount;
            })
            ->addColumn('role_type', function ($row) use ($loginUser) {
                return $loginUser->role_type;
            })
            ->addColumn('now_time', function ($row) {
                return Carbon::now()->format('Y-m-d H:i:s');
            })
            ->addColumn('valid_upto', function ($row) {
                return Carbon::parse($row->created_at)->addHour(12)->format('Y-m-d H:i:s');
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('offer_description', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function getEnquiryProductList(Request $request)
    {
        $loginUser = Auth::user();
        if ($loginUser->role_type == 2) {
            $brandIds = DealerBrand::query()
                ->where('dealer_id', $loginUser->id)
                ->select('brand_id')
                ->distinct()
                ->get()
                ->toArray();
        }
        $customer_enquiry_id = ($request->customer_enquiry_id) ? $request->customer_enquiry_id : 0;

        $qry = PurchaseEnquiryProduct::query()
            ->where('purchase_enquiry_products.customer_enquiry_id', $customer_enquiry_id)
            ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
            ->select(
                'product.*',
                'brand.brand_name',
                'purchase_enquiry_products.customer_enquiry_id',
                'purchase_enquiry_products.qty',
                'purchase_enquiry_products.connection_ids',
                'purchase_enquiry_products.execution_ids',
                'purchase_enquiry_products.attribute_ids',
            );

        if ($loginUser->role_type == 2) {
            $qry->join('dealer_purchase_enquiry', 'dealer_purchase_enquiry.customer_enquiry_id', '=', 'purchase_enquiry_products.customer_enquiry_id');
            $qry->join('product', function ($query) use ($brandIds) {
                $query->on('product.id', '=', 'purchase_enquiry_products.product_id');
                $query->on(DB::raw("FIND_IN_SET(product.id,dealer_purchase_enquiry.for_product_id)"), ">", DB::raw("'0'"));
                $query->whereIn('product.brand_id', $brandIds);
            });
            $qry->where('dealer_purchase_enquiry.dealer_id', $loginUser->id);
        } else if ($loginUser->role_type == 3) {
            $qry->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id');
            $qry->where('purchase_enquiry.customer_id', $loginUser->id);
        } else if ($loginUser->role_type == 1) {
            $qry->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id');
        }

        $qry->leftJoin('brand', 'brand.id', '=', 'product.brand_id')->groupBy('product.id');

        return datatables()::of($qry)
            ->addIndexColumn()
            ->editColumn('connections', function ($row) {
                if (isset($row->connection_ids) && !empty($row->connection_ids)) {
                    $connection_ids = explode(',', $row->connection_ids);

                    $connection_names = ProductConnection::query()
                        ->whereIn('id', $connection_ids)
                        ->select('connection_name')
                        ->get()
                        ->toArray();

                    if (isset($connection_names) && !empty($connection_names)) {
                        foreach ($connection_names as $connection_name) {
                            $connections[] = $connection_name['connection_name'];
                        }
                        return implode(', ', $connections);
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('executions', function ($row) {
                if (isset($row->execution_ids) && !empty($row->execution_ids)) {
                    $execution_ids = explode(',', $row->execution_ids);

                    $execution_names = ProductExecution::query()
                        ->whereIn('id', $execution_ids)
                        ->select('execution_name')
                        ->get()
                        ->toArray();

                    if (isset($execution_names) && !empty($execution_names)) {
                        foreach ($execution_names as $execution_name) {
                            $executions[] = $execution_name['execution_name'];
                        }
                        return implode(', ', $executions);
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('attributes', function ($row) {
                if (isset($row->attribute_ids) && !empty($row->attribute_ids)) {
                    $enquiry_type_array = explode(',', $row->attribute_ids);
                    $enquiryTypes = PurchaseEnquiry::$enquiryType;
                    foreach ($enquiry_type_array as $elist) {
                        $type_name[] = $enquiryTypes[$elist];
                    }
                    if (isset($type_name) && !empty($type_name)) {
                        return implode(', ', $type_name);
                    } else {
                        return '-';
                    }
                } else {
                    return '-';
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function getOfferProductList(Request $request)
    {
        $loginUser = Auth::user();
        if ($loginUser->role_type == 2) {
            $brandIds = DealerBrand::query()
                ->where('dealer_id', $loginUser->id)
                ->select('brand_id')
                ->distinct()
                ->get()
                ->toArray();
        } else {
            return response()->json(['status' => false, 'message' => trans('translation.you can not access')]);
        }

        $customer_enquiry_id = ($request->id) ? $request->id : 0;

        $qry = PurchaseEnquiryProduct::query()
            ->where('purchase_enquiry_products.customer_enquiry_id', $customer_enquiry_id)
            ->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id')
            ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
            ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
            ->select(
                'purchase_enquiry.id as enquiry_id',
                'product.*',
                DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name"),
                DB::raw("GROUP_CONCAT(DISTINCT product.id SEPARATOR ', ') as pro_id"),
            );

        if ($loginUser->role_type == 2) {
            $qry->whereIn('product.brand_id', $brandIds);
        } else if ($loginUser->role_type == 3) {
            $qry->where('purchase_enquiry.customer_id', $loginUser->id);
        }

        $data = $qry->groupBy("purchase_enquiry.id")->first();

        $qry = PurchaseEnquiryProduct::query()
            ->where('purchase_enquiry_products.customer_enquiry_id', $customer_enquiry_id)
            ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
            ->select(
                'purchase_enquiry.id as enquiry_id',
                'product.*',
                DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name"),
                DB::raw("GROUP_CONCAT(DISTINCT product.id SEPARATOR ', ') as pro_id"),
            );

        if ($loginUser->role_type == 2) {
            $qry->join('dealer_purchase_enquiry', 'dealer_purchase_enquiry.customer_enquiry_id', '=', 'purchase_enquiry_products.customer_enquiry_id');
            $qry->join('product', function ($query) use ($brandIds) {
                $query->on('product.id', '=', 'purchase_enquiry_products.product_id');
                $query->on(DB::raw("FIND_IN_SET(product.id,dealer_purchase_enquiry.for_product_id)"), ">", DB::raw("'0'"));
                $query->whereIn('product.brand_id', $brandIds);
            });
            $qry->where('dealer_purchase_enquiry.dealer_id', $loginUser->id);
        } else if ($loginUser->role_type == 3) {
            $qry->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id');
            $qry->where('purchase_enquiry.customer_id', $loginUser->id);
        }

        $data = $qry->leftJoin('brand', 'brand.id', '=', 'product.brand_id')->groupBy('enquiry_id')->first();

        if (isset($data->enquiry_id)) {
            $data->date = date("d.m.Y H:i", strtotime($data->created_at));
            $pro_data = [];
            if ($data->pro_id && $data->product_name) {
                $pro_id_array = explode(',', $data->pro_id);
                $product_name_array = explode(',', $data->product_name);
                foreach ($pro_id_array as $key => $pro_id) {
                    if (isset($product_name_array[$key])) {
                        $pro_data[$pro_id] = $this->getProductEnquiryInfo($pro_id, $data->enquiry_id);
                    }
                }
            }
        }

        return response()->json(['status' => true, 'data' => $data, 'pro_data' => $pro_data]);
    }

    public function getProductEnquiryInfo($pid, $enquiry_id)
    {
        if (isset($pid) && !empty($pid) && isset($enquiry_id) && !empty($enquiry_id)) {
            $productInfo = PurchaseEnquiryProduct::query()
                ->select(
                    'purchase_enquiry_products.*',
                    'product.product_name'
                )
                ->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id')
                ->where('customer_enquiry_id', $enquiry_id)
                ->where('product_id', $pid)
                ->first();
            if (isset($productInfo) && !empty($productInfo)) {
                return [
                    'product' => $productInfo->product_name,
                    'qty' => $productInfo->qty
                ];
            }
            return [];
        }
        return [];
    }

    public function getofferDetail(Request $request)
    {
        $status = OfferDetails::$status;

        $qry = OfferDetails::query()
            ->join('product', 'offer_details.product_id', '=', 'product.id')
            ->join('brand', 'product.brand_id', '=', 'brand.id')
            ->join('offer_purchase_enquiry', 'offer_purchase_enquiry.id', '=', 'offer_details.offer_id')
            ->join('purchase_enquiry_products', function ($join) {
                $join->on('purchase_enquiry_products.customer_enquiry_id', '=', 'offer_purchase_enquiry.customer_enquiry_id');
                $join->on('purchase_enquiry_products.product_id', '=', 'offer_details.product_id');
            })
            ->select(
                'offer_details.*',
                'product.product_name',
                'brand.brand_name',
                'purchase_enquiry_products.qty',
                'offer_purchase_enquiry.delivery_time',
                'offer_purchase_enquiry.delivery_time_type',
            )
            ->where('offer_details.offer_id', $request->offer_id);

        $data = $qry->groupBy("offer_details.id");

        notificationMarkAsRead('purchaseEnquiry', $request->offer_id, 'offerCreated');

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('offer_amount', function ($row) {
                $currency_type = ($row->offer_currency == 'eur') ? 1 : 2;
                $amount = formatCurrencyOutput($row->offer_amount, $currency_type, false);
                return $amount;
                // return ($row->offer_amount) ? round($row->offer_amount, 2, '.', '') : '0.00';
            })
            ->editColumn('offer_amount_without_format', function ($row) {
                return ($row->offer_amount) ? number_format($row->offer_amount, 2, '.', '') : '0.00';
            })
            ->addColumn('status_name', function ($row) use ($status) {
                return isset($status[$row->offer_status]) ? $status[$row->offer_status] : "";
            })
            ->addColumn('delivery_time_type', function ($row) use ($status) {
                return isset($row->delivery_time_type) && $row->delivery_time_type == 1 ? trans('translation.Days') : trans('translation.Weeks');
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                    $instance->where(function ($w) use ($search) {
                        $w->orWhere('offer_amount', 'LIKE', "%$search%");
                        $w->orWhere('product.product_name', 'LIKE', "%$search%");
                        $w->orWhere('brand.brand_name', 'LIKE', "%$search%");
                    });
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function updateOfferStatus(Request $request)
    {
        $id = (isset($request->offerid) && $request->offerid != "") ? $request->offerid : 0;
        $status_from = (isset($request->offerstatus) && $request->offerstatus != "") ? $request->offerstatus : "";
        $currentlang = App::getLocale();

        $rules = array(
            'payment_method' => 'required',
            'dsgvo_terms' => 'required',
            'withdrawal_declaration' => 'required',
        );

        $messages = array();

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $result = ['status' => false, 'error' => $validator->errors()];
        } else {
            $payment_method = (isset($request->payment_method) && $request->payment_method != "") ? $request->payment_method : "";

            if (isset($payment_method) && !empty($payment_method)) {
                $loginUser = Auth::User();
                $statusOP = OfferPurchaseEnquiry::$status;

                $condition = [];
                $condition_2 = [];
                $condition[] = ['id', '=', $id];
                if ($loginUser->role_type == 2) {
                    $condition[] = ['dealer_id', '=', $loginUser->id];
                    $condition_2[] = ['dealer_id', '=', $loginUser->id];
                }
                if ($loginUser->role_type == 3) {
                    $condition[] = ['customer_id', '=', $loginUser->id];
                    $condition_2[] = ['customer_id', '=', $loginUser->id];
                }

                $offerPurchaseEnquiry = OfferPurchaseEnquiry::where($condition)
                    ->where('created_at', '>', Carbon::parse('-12 hours'))
                    ->first();

                if (isset($offerPurchaseEnquiry->id)) {
                    $checkOrder = Order::query()
                        ->where('enquiry_id', $offerPurchaseEnquiry->customer_enquiry_id)
                        ->where('offer_id', $offerPurchaseEnquiry->id)
                        ->first();
                    // pr($checkOrder);die;

                    $dealerinfo = OfferPurchaseEnquiry::query()
                        ->select('dealer.*')
                        ->join('users as dealer', 'dealer.id', '=', 'offer_purchase_enquiry.dealer_id')
                        ->where('offer_purchase_enquiry.id', $id)
                        ->where('offer_purchase_enquiry.customer_id', $loginUser->id)
                        ->where('offer_purchase_enquiry.status', '1')
                        ->first();


                    if (!isset($checkOrder) && isset($offerPurchaseEnquiry->id) && $status_from && isset($dealerinfo) && !empty($dealerinfo->id)) {

                        $offerPurchaseEnquiry->status = $status_from;

                        if ($offerPurchaseEnquiry->save()) {
                            $condition_2[] = ['status', '=', 1];
                            $condition_2[] = ['customer_enquiry_id', '=', $offerPurchaseEnquiry->customer_enquiry_id];
                            $offerPurchaseEnquiresRemaining = OfferPurchaseEnquiry::where($condition_2)->get();

                            foreach ($offerPurchaseEnquiresRemaining as $offerPurchaseEnquiryRemaining) {
                                $offerPurchaseEnquiryRemaining->status = 3;
                                if ($offerPurchaseEnquiryRemaining->save()) {
                                    $userSchema = User::find($offerPurchaseEnquiryRemaining->dealer_id);
                                    $dataNotify = [
                                        'name' => $userSchema->company_name,
                                        'title' => trans('translation.offer_declined_title'),
                                        'type' => 'purchaseEnquiry',
                                        'status' => 'offerDeclined',
                                        'body' => trans('translation.offer_declined_body', ['name' => trans('translation.Customer')]),
                                        'senderId' => $loginUser->id,
                                        'url' => route('dealerEnquiryView', $offerPurchaseEnquiryRemaining->customer_enquiry_id),
                                        'dealer_id' => $userSchema->id,
                                        'id' => $offerPurchaseEnquiryRemaining->customer_enquiry_id
                                    ];
                                    Notification::send($userSchema, new PurchaseEnquiryNotification($dataNotify));
                                }
                            }

                            OfferDetails::query()
                                ->where('offer_id', $offerPurchaseEnquiry->id)
                                ->where('offer_status', '1')
                                ->update(['offer_status' => 2]);

                            OfferDetails::query()
                                ->join('offer_purchase_enquiry', 'offer_purchase_enquiry.id', '=', "offer_details.offer_id")
                                ->where('offer_purchase_enquiry.customer_enquiry_id', '=', $offerPurchaseEnquiry->customer_enquiry_id)
                                ->where('offer_purchase_enquiry.status', '=', 3)
                                ->where('offer_details.offer_status', 1)
                                ->update(['offer_details.offer_status' => 3]);

                            $purchaseEnquiry = PurchaseEnquiry::where("id", "=", $offerPurchaseEnquiry->customer_enquiry_id)->first();
                            if (isset($purchaseEnquiry->id)) {
                                $purchaseEnquiry->status = 2;
                                $purchaseEnquiry->save();
                            }

                            $totalAmount = OfferDetails::query()
                                ->select(
                                    'offer_purchase_enquiry.customer_enquiry_id',
                                    DB::raw("sum(offer_details.offer_amount * purchase_enquiry_products.qty) as amonut"),
                                    'offer_details.offer_currency',
                                )
                                ->join('offer_purchase_enquiry', 'offer_purchase_enquiry.id', '=', 'offer_details.offer_id')
                                ->join('purchase_enquiry_products', function ($query) {
                                    $query->on('purchase_enquiry_products.customer_enquiry_id', '=', 'offer_purchase_enquiry.customer_enquiry_id');
                                    $query->on('purchase_enquiry_products.product_id', '=', 'offer_details.product_id');
                                })
                                ->where('offer_details.offer_id', $offerPurchaseEnquiry->id)
                                ->where('offer_details.offer_status', 2)
                                ->groupBy('offer_purchase_enquiry.customer_enquiry_id')
                                ->first()
                                ->toArray();
                            // $site_charge_rate=config('common.site_charge_rate');
                            $site_commission = 0;
                            $dealero = User::find($offerPurchaseEnquiry->dealer_id);
                            // $rate_arr = config('common.commision_rate');
                            $rate_arr = PlanType::query();
                            if ($dealero->is_distributor == 1) {
                                // $rate_arr = config('common.commision_rate_for_distributer');
                                $rate_arr = $rate_arr->where('type', 'DISTRIBUTER')->first();
                            } else {
                                $rate_arr = $rate_arr->where('type', 'DEALER')->first();
                            }

                            //dealer lavels
                            $dealer_levels = [
                                0 => 'silver_level',
                                1 => 'gold_level',
                                2 => 'platinum_level',
                                3 => 'diamond_level',
                            ];

                            $dealer_current_level = ($dealero->status_level >= 0) ? $dealero->status_level : 0;
                            $dealer_level = $dealer_levels[$dealer_current_level] ?? 0;

                            $site_charge_rate = $rate_arr->where('plan_type', 'BASIC')->first()->$dealer_level ?? 0;
                            if ($dealero->package_id) {
                                $package = Packages::find($dealero->package_id);
                                if ($package->plan_type == 1) {
                                    $site_charge_rate = $rate_arr->where('plan_type', 'BASIC')->first()->$dealer_level ?? 0;
                                } else if ($package->plan_type == 2) {
                                    $site_charge_rate = $rate_arr->where('plan_type', 'PREMIUM')->first()->$dealer_level ?? 0;
                                } else {
                                    $site_charge_rate = $rate_arr->where('plan_type', 'SUPREME')->first()->$dealer_level ?? 0;
                                }
                            }
                            $totaloffer = isset($totalAmount) && !empty($totalAmount) ? round($totalAmount['amonut'], 2) : '0.00';

                            $site_commission = ($totaloffer * $site_charge_rate) / 100;
                            $resultd = updateDelaerStatus($dealero, $totaloffer, 'add');
                            if (isset($resultd) && !empty($resultd)) {
                                $dealero->turnover = $resultd['turnover'];
                                if ($dealero->status_level != $resultd['status']) {
                                    $this->mailer->sendDealerStatusLevelUpdateEmail($dealero, $resultd['status']);
                                }
                                $dealero->status_level = $resultd['status'];
                            }
                            $dealero->save();
                            $commision_invoice_id = $subscription_id = '';
                            if ($dealero->subscription_id && $dealero->is_active_subscription == 1) {
                                $subscription_id = $dealero->subscription_id;
                                $sub_log = SubscriptionLog::where('user_id', $dealero->id)->orderBy('id', 'DESC')->first();
                                if (!empty($sub_log)) {
                                    // $commision_invoice_id = $sub_log->invoice_id;
                                }
                                try {
                                    Stripe::setApiKey(config('services.stripe.secret'));
                                    try {
                                        $subscription = \Stripe\Subscription::retrieve($dealero->subscription_id);
                                        // $commision_invoice_id = $subscription->latest_invoice;
                                        $quantity = $subscription->quantity;
                                        $qty = round($site_commission, 2) / config('common.commision_unit');

                                        $subscription->quantity = $quantity + $qty;
                                        $subscription->prorate = false;
                                        $subscription->save();
                                    } catch (\Stripe\Exception\ApiErrorException $e) {
                                        Log::critical("Getting issue in upcoming invoice in stripe api: " . $dealero->subscription_id . ' Error: ' . $e->getMessage());
                                    } catch (\Exception $e) {
                                        Log::critical("Getting issue in upcoming invoice in stripe api: " . $dealero->subscription_id . ' Error: ' . $e->getMessage());
                                    }
                                } catch (Exception $e) {
                                    Log::critical("Dealer does not have subscription");
                                    // we can check it before order when we need.
                                }
                            } else {
                                Log::critical("Dealer does not have subscription");
                                // we can check it before order when we need.
                            }
                            // Create order
                            $order = new Order;
                            $order->customer_id = $offerPurchaseEnquiry->customer_id;
                            $order->dealer_id = $offerPurchaseEnquiry->dealer_id;
                            $order->currency = getDealerCurrencyType($dealero);
                            $order->enquiry_id = $offerPurchaseEnquiry->customer_enquiry_id;
                            $order->offer_id = $offerPurchaseEnquiry->id;
                            $order->status = 0;
                            $order->amount = isset($totalAmount) && !empty($totalAmount) ? round($totalAmount['amonut'], 2) : '0.00';
                            $order->site_commission = $site_commission;
                            $order->subscription_id = $subscription_id;
                            $order->payment_method = $payment_method;
                            $order->shipping_company = null;
                            $order->tracking_number = null;
                            $order->cancel_proof = null;
                            $order->invoice_number = rand(10000, 99999) . time();
                            $order->commision_invoice_id = $commision_invoice_id;

                            if ($order->save()) {
                                $order->invoice_number = 'OC' . ($order->id + 1000);
                                $order->save();
                            }

                            // Send confirmation email
                            $public_path = 'public/order_documents';
                            $absoulute_path = 'app/' . $public_path;
                            $pdf_store_folder = 'offer' . $offerPurchaseEnquiry->id;
                            $public_path_ori = 'storage/order_documents';

                            if (!file_exists(storage_path($absoulute_path))) {
                                Storage::makeDirectory($public_path);
                            }

                            $documents = [];

                            if (!file_exists(storage_path($absoulute_path . '/' . $pdf_store_folder))) {
                                Storage::makeDirectory($public_path . '/' . $pdf_store_folder);
                            }

                            $document_content = Document::find('3');
                            if ($currentlang == "de") {
                                if (isset($document_content->german_description) && !empty($document_content->german_description)) {
                                    $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->german_description);
                                    $terms_pdf = PDF::loadHtml($content);
                                } else {
                                    $terms_pdf = PDF::loadView('purchaseEnquiry.documents.termsandcondition', compact('dealerinfo'));
                                }
                            } else {
                                if (isset($document_content->description) && !empty($document_content->description)) {
                                    $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->description);
                                    $terms_pdf = PDF::loadHtml($content);
                                } else {
                                    $terms_pdf = PDF::loadView('purchaseEnquiry.documents.termsandcondition', compact('dealerinfo'));
                                }
                            }

                            $terms_and_condition = trans('translation.Terms and Condition');
                            $terms_pdf_filename = $terms_and_condition . '.pdf';
                            $terms_pdf_path = storage_path($absoulute_path . '/' . $pdf_store_folder . '/' . $terms_pdf_filename);
                            $terms_pdf->save($terms_pdf_path);
                            if (file_exists($terms_pdf_path)) {
                                $documents['terms']['path'] = $terms_pdf_path;
                                $documents['terms']['filename'] = $terms_pdf_filename;
                            }

                            $document_content = Document::find('4');
                            if ($currentlang == "de") {
                                if (isset($document_content->german_description) && !empty($document_content->german_description)) {
                                    $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->german_description);
                                    $withdrawal_pdf = PDF::loadHtml($content);
                                } else {
                                    $withdrawal_pdf = PDF::loadView('purchaseEnquiry.documents.withdrawaldeclaration', compact('dealerinfo'));
                                }
                            } else {
                                if (isset($document_content->description) && !empty($document_content->description)) {
                                    $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->description);
                                    $withdrawal_pdf = PDF::loadHtml($content);
                                } else {
                                    $withdrawal_pdf = PDF::loadView('purchaseEnquiry.documents.withdrawaldeclaration', compact('dealerinfo'));
                                }
                            }

                            $withdrawal_pdf_filename = 'Withdrawal Declaration.pdf';
                            $withdrawal_pdf_path = storage_path($absoulute_path . '/' . $pdf_store_folder . '/' . $withdrawal_pdf_filename);
                            $withdrawal_pdf->save($withdrawal_pdf_path);
                            if (file_exists($withdrawal_pdf_path)) {
                                $documents['withdrawal']['path'] = $withdrawal_pdf_path;
                                $documents['withdrawal']['filename'] = $withdrawal_pdf_filename;
                            }

                            // Invoice generate
                            $orders = Order::query()
                                ->select(
                                    'product.id',
                                    'orders.id as order_id',
                                    'orders.dealer_id',
                                    'orders.customer_id',
                                    'product.product_name',
                                    'orders.invoice_number',
                                    'orders.created_at',
                                    'purchase_enquiry_products.qty',
                                    'offer_details.offer_amount',
                                    'orders.amount',
                                    'orders.currency',
                                    'brand.brand_name'
                                )
                                ->join('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
                                ->join('product', 'product.id', '=', 'offer_details.product_id')
                                ->join('brand', 'brand.id', '=', 'product.brand_id')
                                ->join('purchase_enquiry_products', function ($q) {
                                    $q->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id');
                                    $q->on('purchase_enquiry_products.product_id', '=', 'product.id');
                                })
                                ->where('orders.id', $order->id)
                                ->where(function ($qry) use ($loginUser) {
                                    if ($loginUser->role_type == 2) {
                                        $qry->where('orders.dealer_id', $loginUser->id);
                                    }
                                    if ($loginUser->role_type == 3) {
                                        $qry->where('orders.customer_id', $loginUser->id);
                                    }
                                })
                                ->groupby('product.id')
                                ->get();

                            $dealer = User::find($orders[0]->dealer_id);
                            $customer = User::find($orders[0]->customer_id);
                            $taxpercentage = 19.00;

                            // Store Invoice
                            $invoice_public_path = 'public/invoice';
                            $invoice_absoulute_path = 'app/' . $invoice_public_path;

                            if (!file_exists(storage_path($invoice_absoulute_path))) {
                                Storage::makeDirectory($invoice_public_path);
                            }

                            header('Content-Type: text/html; charset=utf-8');
                            $invoice_pdf = PDF::loadView('orders.invoice.invoice', compact('orders', 'dealer', 'customer', 'taxpercentage'));

                            $invoice_pdf_filename = trans('translation.Invoice') . '-' . $order->invoice_number . '.pdf';
                            $invoice_pdf_path = storage_path($invoice_absoulute_path . '/' . $invoice_pdf_filename);
                            $invoice_pdf->save($invoice_pdf_path);
                            if (file_exists($invoice_pdf_path)) {
                                $documents['invoice']['path'] = $invoice_pdf_path;
                                $documents['invoice']['filename'] = $invoice_pdf_filename;
                            }

                            $products = Product::query()
                                ->select(
                                    DB::raw('CONCAT(brand.brand_name, " ", product.product_name) as product_name')
                                )
                                ->join('brand', 'brand.id', '=', 'product.brand_id')
                                ->whereIn('product.id', explode(', ', $offerPurchaseEnquiry->product_id))
                                ->groupBy('product.id')
                                ->get()
                                ->pluck('product_name')
                                ->toArray();

                            $userSchema = User::find($offerPurchaseEnquiry->dealer_id);
                            $dataNotify = [
                                'name' => $userSchema->company_name,
                                'title' => trans('translation.order_created_title'),
                                'type' => 'order',
                                'mailGretting' => trans('translation.congratulation'),
                                'mailSubject' => trans('translation.orderCreateDealer_email_subject', ['product' => implode(',', $products)]),
                                'mailBody' => trans('translation.orderCreateDealer_email_body', ['product' => implode(',', $products), 'url' => '<a href="' . route('dealer.sales') . '?oid=' . $order->id . '">' . route('dealer.sales') . '</a>']),
                                'status' => 'created',
                                'body' => trans('translation.order_created_body', ['name' => trans('translation.Customer')]),
                                'senderId' => $loginUser->id,
                                'url' => route('dealer.sales'),
                                'dealer_id' => $userSchema->id,
                                'id' => $order->id
                            ];
                            Notification::send($userSchema, new OrderNotification($dataNotify));

                            if (isset($documents) && !empty($documents) && count($documents) == 3) {
                                $dataNotify = [
                                    'name' => $loginUser->first_name . ' ' . $loginUser->last_name,
                                    'title' => trans('translation.order_created_title'),
                                    'type' => 'order',
                                    'mailGretting' => trans('translation.Hello'),
                                    'mailSubject' => trans('translation.orderCreateCustomer_email_subject', ['product' => implode(',', $products)]),
                                    'mailBody' => trans('translation.orderCreateCustomer_email_body', ['product' => implode(',', $products), 'url' => '<a href="' . route('customer.purchases') . '?oid=' . $order->id . '">' . route('customer.purchases') . '</a>']),
                                    'documents' => $documents,
                                    'status' => 'createdInvoices',
                                    'body' => trans('translation.order_created_body', ['name' => trans('translation.Dealer')]),
                                    'senderId' => $userSchema->id,
                                    'url' => route('customer.purchases'),
                                    'dealer_id' => $userSchema->id,
                                    'id' => $order->id
                                ];
                                Notification::send($loginUser, new OrderNotification($dataNotify));

                                $dataNotify = [
                                    'name' => $userSchema->company_name,
                                    'title' => trans('translation.order_created_title'),
                                    'type' => 'order',
                                    'mailGretting' => trans('translation.Hello'),
                                    'mailSubject' => trans('translation.orderCreateDealer2_email_subject', ['product' => implode(',', $products)]),
                                    'mailBody' => trans('translation.orderCreateDealer2_email_body', ['product' => implode(',', $products)]),
                                    'documents' => $documents,
                                    'status' => 'createdInvoices',
                                    'body' => trans('translation.order_created_body', ['name' => trans('translation.Customer')]),
                                    'senderId' => $loginUser->id,
                                    'url' => route('dealer.sales'),
                                    'dealer_id' => $userSchema->id,
                                    'id' => $order->id
                                ];
                                //Notification::send($userSchema, new OrderNotification($dataNotify));
                            }

                            $status_name = (isset($statusOP[$offerPurchaseEnquiry->status])) ? $statusOP[$offerPurchaseEnquiry->status] : trans('translation.Status Update');
                            $result = ['status' => true, 'message' => trans('translation.Offers') . ' ' . $status_name . ' ' . trans('translation.successfully')];
                        } else {
                            $result = ['status' => false, 'message' => 'Error in saving data'];
                        }
                    } else {
                        if (isset($checkOrder)) {
                            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                        } else {
                            $result = ['status' => false, 'message' => trans('translation.This offer is valid for 12 hours')];
                        }
                    }
                } else {
                    $result = ['status' => false, 'message' => trans('translation.This offer is valid for 12 hours')];
                }
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $purchaseEnquiry = PurchaseEnquiry::where('id', $request->id);

        PurchaseEnquiryProduct::query()
            ->where('customer_enquiry_id', $request->id)
            ->delete();

        $offerPurchaseEnquiryIds = OfferPurchaseEnquiry::where('customer_enquiry_id', $request->id)->pluck('id')->toArray();

        if (!empty($offerPurchaseEnquiryIds)) {
            NotificationModel::whereRaw("JSON_EXTRACT(data, '$.id') IN (" . implode(',', $offerPurchaseEnquiryIds) . ")")
                ->update(['deleted_at' => Carbon::now()]);

            OfferPurchaseEnquiry::whereIn('id', $offerPurchaseEnquiryIds)->delete();
        }

        // Update Purchase Enquiry Notification Modal
        NotificationModel::query()
            ->whereRaw("JSON_EXTRACT(data, '$.id')=" . $request->id)
            ->update(['deleted_at' => Carbon::now()]);

        if ($purchaseEnquiry->delete()) {
            $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }

    public function getEnquiryDocuments(Request $request)
    {
        $data = $request->all();
        $loginUser = Auth::user();
        $currentlang = App::getLocale();


        if (isset($loginUser) && !empty($loginUser) && isset($data['offerid']) && !empty($data['offerid'])) {
            $dealerinfo = OfferPurchaseEnquiry::query()
                ->select('dealer.*', 'offer_purchase_enquiry.created_at as offer_created_at')
                ->join('users as dealer', 'dealer.id', '=', 'offer_purchase_enquiry.dealer_id')
                ->where('offer_purchase_enquiry.id', $data['offerid'])
                ->where('offer_purchase_enquiry.customer_id', $loginUser->id)
                ->where('offer_purchase_enquiry.status', '1')
                ->first();

            if (isset($dealerinfo) && !empty($dealerinfo) && $dealerinfo->offer_created_at > Carbon::parse('-12 hours')) {
                header('Content-Type: text/html; charset=utf-8');
                if ($data['type'] == 'terms') {
                    $document_content = Document::find('3');

                    if ($currentlang == "de") {
                        if (isset($document_content->german_description) && !empty($document_content->german_description)) {
                            $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->german_description);
                            $pdf = PDF::loadHtml($content);
                        } else {
                            $pdf = PDF::loadView('purchaseEnquiry.documents.termsandcondition', compact('dealerinfo'));
                        }
                    } else {
                        if (isset($document_content->description) && !empty($document_content->description)) {
                            $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->description);
                            $pdf = PDF::loadHtml($content);
                        } else {
                            $pdf = PDF::loadView('purchaseEnquiry.documents.termsandcondition', compact('dealerinfo'));
                        }
                    }

                    $terms_condication_pdf = trans('translation.Terms and Condition');
                    $pdf_filename = $terms_condication_pdf . '.pdf';
                    return $pdf->stream($pdf_filename);
                } elseif ($data['type'] == 'withdrawal') {
                    $document_content = Document::find('4');
                    if ($currentlang == "de") {
                        if (isset($document_content->german_description) && !empty($document_content->german_description)) {
                            $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->german_description);
                            $pdf = PDF::loadHtml($content);
                        } else {
                            $pdf = PDF::loadView('purchaseEnquiry.documents.withdrawaldeclaration', compact('dealerinfo'));
                        }
                    } else {
                        if (isset($document_content->description) && !empty($document_content->description)) {
                            $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->description);
                            $pdf = PDF::loadHtml($content);
                        } else {
                            $pdf = PDF::loadView('purchaseEnquiry.documents.withdrawaldeclaration', compact('dealerinfo'));
                        }
                    }

                    $withdrawal_declaration = trans('translation.Withdrawal Declaration');
                    $pdf_filename = $withdrawal_declaration . '.pdf';
                    return $pdf->stream($pdf_filename);
                } else {
                    $pdf = '';
                    return abort('404');
                }
            } else {
                return abort('404');
            }
        } else {
            return abort('404');
        }
        return abort('404');
    }

    public function delaerDelete(Request $request)
    {
        $loginUser = Auth::user();

        if (isset($loginUser) && !empty($loginUser) && $loginUser->role_type == 2) {
            $purchaseEnquiry = DealerPurchaseEnquiry::where('customer_enquiry_id', $request->id)->where('dealer_id', $loginUser->id);

            if ($purchaseEnquiry->update(['deleted_at' => Carbon::now()])) {
                $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }
    public function getDealerRatingUrl(Request $request)
    {
        $offer_id = $request->offer_id;
        $offer_data = OfferPurchaseEnquiry::find($offer_id);
        $dealer_id = $offer_data->dealer_id;
        $feedBackData = getDealerFeedbackData($dealer_id);
        return $feedBackData;
    }
    public function getDealerInfo(Request $request)
    {
        $offer_id = $request->offer_id;
        $offer_data = OfferPurchaseEnquiry::find($offer_id);
        $dealer_id = $offer_data->dealer_id;
        $dealer = User::find($dealer_id);
        $arr = [];
        $is_connected = 0;
        if (!empty($dealer->stripe_account_id) && $dealer->stripe_account_status == 1) {
            $is_connected = 1;
        }
        $arr['account_connect_status'] = $is_connected;
        return $arr;
    }
    public function getCheckoutSession(Request $request)
    {
        $id = (isset($request->offerid) && $request->offerid != "") ? $request->offerid : 0;
        $status_from = (isset($request->offerstatus) && $request->offerstatus != "") ? $request->offerstatus : "";
        $currentlang = App::getLocale();

        $rules = array(
            'payment_method' => 'required',
            'dsgvo_terms' => 'required',
            'withdrawal_declaration' => 'required',
        );

        $messages['payment_method.required'] = trans('validation.custom.payment_method.required');
        $messages['dsgvo_terms.required'] = trans('validation.custom.dsgvo_terms.required');
        $messages['withdrawal_declaration.required'] = trans('validation.custom.withdrawal_declaration.required');

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $result = ['status' => false, 'error' => $validator->errors()];
        } else {
            $payment_method = (isset($request->payment_method) && $request->payment_method != "") ? $request->payment_method : "";

            if (isset($payment_method) && !empty($payment_method)) {
                $loginUser = Auth::User();
                // $loginUser=User::find($loginUser->id);
                $statusOP = OfferPurchaseEnquiry::$status;

                $condition = [];
                $condition_2 = [];
                $condition[] = ['id', '=', $id];
                if ($loginUser->role_type == 2) {
                    $condition[] = ['dealer_id', '=', $loginUser->id];
                    $condition_2[] = ['dealer_id', '=', $loginUser->id];
                }
                if ($loginUser->role_type == 3) {
                    $condition[] = ['customer_id', '=', $loginUser->id];
                    $condition_2[] = ['customer_id', '=', $loginUser->id];
                }

                $offerPurchaseEnquiry = OfferPurchaseEnquiry::where($condition)
                    ->where('created_at', '>', Carbon::parse('-12 hours'))
                    ->first();

                if (isset($offerPurchaseEnquiry->id)) {
                    $checkOrder = Order::query()
                        ->where('enquiry_id', $offerPurchaseEnquiry->customer_enquiry_id)
                        ->where('offer_id', $offerPurchaseEnquiry->id)
                        ->first();
                    // pr($checkOrder);die;

                    $dealerinfo = OfferPurchaseEnquiry::query()
                        ->select('dealer.*')
                        ->join('users as dealer', 'dealer.id', '=', 'offer_purchase_enquiry.dealer_id')
                        ->where('offer_purchase_enquiry.id', $id)
                        ->where('offer_purchase_enquiry.customer_id', $loginUser->id)
                        ->where('offer_purchase_enquiry.status', '1')
                        ->first();


                    // pr($dealerinfo);

                    // die;

                    if (!isset($checkOrder) && isset($offerPurchaseEnquiry->id) && $status_from && isset($dealerinfo) && !empty($dealerinfo->id)) {
                        // pr($dealerinfo);die;s
                        if (empty($dealerinfo->stripe_account_id) || $dealerinfo->stripe_account_status != 1) {
                            $result = ['status' => false, 'message' => trans('translation.This dealer has not connected their stripe account')];
                            return response()->json($result);
                        }
                        $offercheckout = new OfferCheckoutDetail;
                        Stripe::setApiKey(config('services.stripe.secret'));

                        $offerDetails = OfferDetails::query()
                            ->select(
                                'offer_details.offer_amount',
                                'offer_details.offer_status',
                                'product.product_name',
                                'purchase_enquiry_products.qty',
                                DB::raw("sum(offer_details.offer_amount * purchase_enquiry_products.qty) as amonut"),
                                'offer_details.vat_rate'
                            )
                            ->join('offer_purchase_enquiry', 'offer_purchase_enquiry.id', '=', 'offer_details.offer_id')
                            ->join('purchase_enquiry_products', function ($query) {
                                $query->on('purchase_enquiry_products.customer_enquiry_id', '=', 'offer_purchase_enquiry.customer_enquiry_id');
                                $query->on('purchase_enquiry_products.product_id', '=', 'offer_details.product_id');
                            })
                            ->join('product', 'product.id', '=', 'offer_details.product_id')
                            ->where('offer_details.offer_id', $offerPurchaseEnquiry->id)
                            ->groupBy('offer_details.product_id')
                            ->get();
                        // $final_total_amount= $offerDetails->sum('amonut');
                        $lineItems = [];
                        $vat_amount = $vat_rate = 0;
                        $currency = getDealerCurrencyType($dealerinfo);
                        $payment_method_types = ['card', 'sepa_debit'];
                        $currency_type = 1;
                        if ($currency == "usd") {
                            $currency_type = 2;
                            $payment_method_types = ['card'];
                        }
                        $total_amount = 0;
                        foreach ($offerDetails as $key => $offer_item) {
                            // here we calculate vat which is set by dealer.
                            // if($offer_item->vat_rate){

                            //     $vat_amount+=round((($offer_item->offer_amount*$offer_item->qty)*$offer_item->vat_rate)/100,2);
                            //     $vat_rate=$offer_item->vat_rate;
                            // }
                            // $offerAmount= round($offer_item->offer_amount, 2)+ $vat_amount;
                            $offerAmount = round($offer_item->offer_amount, 2);
                            $offerAmount = $offerAmount * 100;

                            $total_amount += $offerAmount;
                            if ($offer_item->vat_rate) {
                                $stripe_tax_rate = StripeTaxRates::where(['dealer_id' => $dealerinfo->id, 'tax_rate' => $offer_item->vat_rate])->first();
                                if (empty($stripe_tax_rate)) {
                                    try {
                                        $taxRate = \Stripe\TaxRate::create([
                                            'display_name' => 'VAT',
                                            'description' => 'VAT',
                                            'percentage' => $offer_item->vat_rate,
                                            'inclusive' => false,
                                        ]);
                                        $stripe_tax_rate = new StripeTaxRates;
                                        $stripe_tax_rate->tax_rate = $vat_rate;
                                        $stripe_tax_rate->tax_id = $taxRate->id;
                                        $stripe_tax_rate->dealer_id = $dealerinfo->id;
                                        $stripe_tax_rate->created_at = Carbon::now();
                                        $stripe_tax_rate->save();
                                    } catch (\Stripe\Exception\RateLimitException $e) {
                                        // Too many requests made to the API too quickly
                                        $result = ['status' => false, 'message' => $e->getMessage()];
                                        return response()->json($result);
                                    } catch (\Stripe\Exception\InvalidRequestException $e) {
                                        // Invalid parameters were supplied to Stripe's API
                                        $result = ['status' => false, 'message' => $e->getMessage()];
                                        return response()->json($result);
                                    } catch (\Stripe\Exception\AuthenticationException $e) {
                                        // Authentication with Stripe's API failed
                                        // (maybe you changed API keys recently)
                                        $result = ['status' => false, 'message' => $e->getMessage()];
                                        return response()->json($result);
                                    } catch (\Stripe\Exception\ApiConnectionException $e) {
                                        // Network communication with Stripe failed
                                        $result = ['status' => false, 'message' => $e->getMessage()];
                                        return response()->json($result);
                                    } catch (\Stripe\Exception\ApiErrorException $e) {
                                        // Display a very generic error to the user, and maybe send
                                        // yourself an email
                                        $result = ['status' => false, 'message' => $e->getMessage()];
                                        return response()->json($result);
                                    } catch (\Exception $e) {
                                        // Something else happened, completely unrelated to Stripe
                                        $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                                        return response()->json($result);
                                    }
                                }
                                $lineItem = [
                                    'price_data' => [
                                        'currency' => $currency,
                                        'unit_amount' => $offerAmount, // Amount in cents
                                        'product_data' => [
                                            'name' => $offer_item->product_name,
                                        ],
                                    ],
                                    'quantity' => $offer_item->qty,
                                    'tax_rates' => [$stripe_tax_rate->tax_id]
                                ];
                                $lineItems[] = $lineItem;
                            } else {
                                $lineItem = [
                                    'price_data' => [
                                        'currency' => $currency,
                                        'unit_amount' => $offerAmount, // Amount in cents
                                        'product_data' => [
                                            'name' => $offer_item->product_name
                                        ],
                                    ],
                                    'quantity' => $offer_item->qty
                                ];
                                $lineItems[] = $lineItem;
                            }
                        }
                        if ($total_amount > 9000) {
                            if (array_key_exists(1, $payment_method_types)) {
                                unset($payment_method_types[1]);
                            }
                        }
                        if ($loginUser->customer_id) {
                            try {
                                $session = stripeSession::create([
                                    'customer' => $loginUser->customer_id,
                                    'payment_method_types' => $payment_method_types,
                                    'line_items' => $lineItems,
                                    'mode' => 'payment',
                                    // 'automatic_tax' => [
                                    //     'enabled' => true,
                                    // ],
                                    'payment_intent_data' => [
                                        // 'application_fee_amount' => 500, // Application fee in cents
                                        'on_behalf_of' => $dealerinfo->stripe_account_id,
                                        'transfer_data' => [
                                            'destination' => $dealerinfo->stripe_account_id, // Replace with the actual connected account ID
                                        ],
                                    ],
                                    'success_url' => route('customer.purchase.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                                    'cancel_url' => route('customer.purchase.checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
                                ]);
                            } catch (\Stripe\Exception\CardException $e) {
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\RateLimitException $e) {
                                // Too many requests made to the API too quickly
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\InvalidRequestException $e) {
                                // Invalid parameters were supplied to Stripe's API
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\AuthenticationException $e) {
                                // Authentication with Stripe's API failed
                                // (maybe you changed API keys recently)
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\ApiConnectionException $e) {
                                // Network communication with Stripe failed
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\ApiErrorException $e) {
                                // Display a very generic error to the user, and maybe send
                                // yourself an email
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (Exception $e) {
                                // Something else happened, completely unrelated to Stripe
                                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                                return response()->json($result);
                            }
                        } else {
                            // create new subscription through checkout page
                            try {
                                $session = stripeSession::create([
                                    'payment_method_types' => $payment_method_types,
                                    'line_items' => $lineItems,
                                    'customer_email' => $loginUser->email,
                                    'mode' => 'payment', // subscription - setup - payment
                                    'payment_intent_data' => [
                                        // 'application_fee_amount' => 500, // Application fee in cents
                                        'on_behalf_of' => $dealerinfo->stripe_account_id,
                                        'transfer_data' => [
                                            'destination' =>  $dealerinfo->stripe_account_id, // Replace with the actual connected account ID
                                        ],
                                    ],
                                    'success_url' => route('customer.purchase.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                                    'cancel_url' => route('customer.purchase.checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
                                ]);
                            } catch (\Stripe\Exception\CardException $e) {
                                // Since it's a decline, \Stripe\Exception\CardException will be caught
                                // echo 'Status is:' . $e->getHttpStatus() . '\n';
                                // echo 'Type is:' . $e->getError()->type . '\n';
                                // echo 'Code is:' . $e->getError()->code . '\n';
                                // // param is '' in this case
                                // echo 'Param is:' . $e->getError()->param . '\n';
                                // echo 'Message is:' . $e->getError()->message . '\n';
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\RateLimitException $e) {
                                // Too many requests made to the API too quickly
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\InvalidRequestException $e) {
                                // Invalid parameters were supplied to Stripe's API
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\AuthenticationException $e) {
                                // Authentication with Stripe's API failed
                                // (maybe you changed API keys recently)
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\ApiConnectionException $e) {
                                // Network communication with Stripe failed
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (\Stripe\Exception\ApiErrorException $e) {
                                // Display a very generic error to the user, and maybe send
                                // yourself an email
                                $result = ['status' => false, 'message' => $e->getMessage()];
                                return response()->json($result);
                            } catch (Exception $e) {
                                // Something else happened, completely unrelated to Stripe
                                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                                return response()->json($result);
                            }
                        }
                        if (!empty($session->url)) {
                            $offercheckout->session_id = $session->id;
                            $offercheckout->customer_id = $loginUser->id;
                            $offercheckout->dealer_id = $dealerinfo->id;
                            $offercheckout->request = json_encode($request->all());
                            $offercheckout->offer_purchase_enquiry_id = $offerPurchaseEnquiry->id;
                            $offercheckout->status = 0;
                            $offercheckout->created_at = Carbon::now();
                            if ($offercheckout->save()) {
                                $result = ['status' => true, 'redirect_url' => $session->url];
                            }
                        } else {
                            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                        }
                    } else {
                        if (isset($checkOrder)) {
                            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                        } else {
                            $result = ['status' => false, 'message' => trans('translation.This offer is valid for 12 hours')];
                        }
                    }
                } else {
                    $result = ['status' => false, 'message' => trans('translation.This offer is valid for 12 hours')];
                }
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        }
        return response()->json($result);
    }
    public function checkoutSuccess(Request $main_request)
    {

        $session_id = $main_request->session_id;
        $offercheckout = OfferCheckoutDetail::where('session_id', $session_id)->first();
        if ($offercheckout) {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::retrieve($session_id);
            $loginUser = User::find($offercheckout->customer_id);
            $loginUser->customer_id = $session->customer;
            $loginUser->save();
            $offercheckout->status = 3;
            $offercheckout->response = json_encode($session->toArray());
            $offercheckout->save();

            $request = json_decode($offercheckout->request);
            $id = (isset($request->offerid) && $request->offerid != "") ? $request->offerid : 0;
            $status_from = (isset($request->offerstatus) && $request->offerstatus != "") ? $request->offerstatus : "";
            $currentlang = App::getLocale();

            $payment_method = (isset($request->payment_method) && $request->payment_method != "") ? $request->payment_method : "";

            if (isset($payment_method) && !empty($payment_method)) {

                $statusOP = OfferPurchaseEnquiry::$status;

                $condition = [];
                $condition_2 = [];
                $condition[] = ['id', '=', $id];
                if ($loginUser->role_type == 2) {
                    $condition[] = ['dealer_id', '=', $loginUser->id];
                    $condition_2[] = ['dealer_id', '=', $loginUser->id];
                }
                if ($loginUser->role_type == 3) {
                    $condition[] = ['customer_id', '=', $loginUser->id];
                    $condition_2[] = ['customer_id', '=', $loginUser->id];
                }

                $offerPurchaseEnquiry = OfferPurchaseEnquiry::where($condition)
                    ->where('created_at', '>', Carbon::parse('-12 hours'))
                    ->first();

                if (isset($offerPurchaseEnquiry->id)) {
                    $checkOrder = Order::query()
                        ->where('enquiry_id', $offerPurchaseEnquiry->customer_enquiry_id)
                        ->where('offer_id', $offerPurchaseEnquiry->id)
                        ->first();
                    // pr($checkOrder);die;

                    $dealerinfo = OfferPurchaseEnquiry::query()
                        ->select('dealer.*')
                        ->join('users as dealer', 'dealer.id', '=', 'offer_purchase_enquiry.dealer_id')
                        ->where('offer_purchase_enquiry.id', $id)
                        ->where('offer_purchase_enquiry.customer_id', $loginUser->id)
                        ->where('offer_purchase_enquiry.status', '1')
                        ->first();

                    if (!isset($checkOrder) && isset($offerPurchaseEnquiry->id) && $status_from && isset($dealerinfo) && !empty($dealerinfo->id)) {

                        $offerPurchaseEnquiry->status = $status_from;

                        if ($offerPurchaseEnquiry->save()) {
                            $condition_2[] = ['status', '=', 1];
                            $condition_2[] = ['customer_enquiry_id', '=', $offerPurchaseEnquiry->customer_enquiry_id];
                            $offerPurchaseEnquiresRemaining = OfferPurchaseEnquiry::where($condition_2)->get();

                            foreach ($offerPurchaseEnquiresRemaining as $offerPurchaseEnquiryRemaining) {
                                $offerPurchaseEnquiryRemaining->status = 3;
                                if ($offerPurchaseEnquiryRemaining->save()) {
                                    $userSchema = User::find($offerPurchaseEnquiryRemaining->dealer_id);
                                    $dataNotify = [
                                        'name' => $userSchema->company_name,
                                        'title' => trans('translation.offer_declined_title'),
                                        'type' => 'purchaseEnquiry',
                                        'status' => 'offerDeclined',
                                        'body' => trans('translation.offer_declined_body', ['name' => trans('translation.Customer')]),
                                        'senderId' => $loginUser->id,
                                        'url' => route('dealer.dealerEnquiryView', $offerPurchaseEnquiryRemaining->customer_enquiry_id),
                                        'dealer_id' => $userSchema->id,
                                        'id' => $offerPurchaseEnquiryRemaining->customer_enquiry_id
                                    ];
                                    Notification::send($userSchema, new PurchaseEnquiryNotification($dataNotify));
                                }
                            }

                            OfferDetails::query()
                                ->where('offer_id', $offerPurchaseEnquiry->id)
                                ->where('offer_status', '1')
                                ->update(['offer_status' => 2]);

                            OfferDetails::query()
                                ->join('offer_purchase_enquiry', 'offer_purchase_enquiry.id', '=', "offer_details.offer_id")
                                ->where('offer_purchase_enquiry.customer_enquiry_id', '=', $offerPurchaseEnquiry->customer_enquiry_id)
                                ->where('offer_purchase_enquiry.status', '=', 3)
                                ->where('offer_details.offer_status', 1)
                                ->update(['offer_details.offer_status' => 3]);

                            $purchaseEnquiry = PurchaseEnquiry::where("id", "=", $offerPurchaseEnquiry->customer_enquiry_id)->first();
                            if (isset($purchaseEnquiry->id)) {
                                $purchaseEnquiry->status = 2;
                                $purchaseEnquiry->save();
                            }

                            $totalAmount = OfferDetails::query()
                                ->select(
                                    'offer_purchase_enquiry.customer_enquiry_id',
                                    DB::raw("sum(offer_details.offer_amount * purchase_enquiry_products.qty) as amonut"),
                                    'offer_details.vat_rate'
                                )
                                ->join('offer_purchase_enquiry', 'offer_purchase_enquiry.id', '=', 'offer_details.offer_id')
                                ->join('purchase_enquiry_products', function ($query) {
                                    $query->on('purchase_enquiry_products.customer_enquiry_id', '=', 'offer_purchase_enquiry.customer_enquiry_id');
                                    $query->on('purchase_enquiry_products.product_id', '=', 'offer_details.product_id');
                                })
                                ->where('offer_details.offer_id', $offerPurchaseEnquiry->id)
                                ->where('offer_details.offer_status', 2)
                                ->groupBy('offer_purchase_enquiry.customer_enquiry_id')
                                ->first()
                                ->toArray();

                            // $site_charge_rate=config('common.site_charge_rate');
                            $site_commission = 0;
                            $dealero = User::find($offerPurchaseEnquiry->dealer_id);
                            // $rate_arr = config('common.commision_rate');
                            $rate_arr = PlanType::get();
                            if ($dealero->is_distributor == 1) {
                                $rate_arr = $rate_arr->where('type', 'DISTRIBUTER')->first();
                            } else {
                                $rate_arr = $rate_arr->where('type', 'DEALER')->first();
                            }

                            //dealer lavels
                            $dealer_levels = [
                                0 => 'silver_level',
                                1 => 'gold_level',
                                2 => 'platinum_level',
                                3 => 'diamond_level',
                            ];

                            $dealer_current_level = ($dealero->status_level >= 0) ? $dealero->status_level : 0;
                            $dealer_level = $dealer_levels[$dealer_current_level] ?? 0;
                            $site_charge_rate = $rate_arr->where('plan_type', 'BASIC')->first()->$dealer_level ?? 0;
                            if ($dealero->package_id) {
                                $package = Packages::find($dealero->package_id);
                                if ($package->plan_type == 1) {
                                    $site_charge_rate = $rate_arr->where('plan_type', 'BASIC')->first()->$dealer_level ?? 0;
                                } else if ($package->plan_type == 2) {
                                    $site_charge_rate = $rate_arr->where('plan_type', 'PREMIUM')->first()->$dealer_level ?? 0;
                                } else {
                                    $site_charge_rate = $rate_arr->where('plan_type', 'SUPREME')->first()->$dealer_level ?? 0;
                                }
                            }


                            if (isset($dealero) && !empty($dealero)) {
                                if (!empty($totalAmount['vat_rate'])) {
                                    $total_vat_amount = ($totalAmount['amonut'] * $totalAmount['vat_rate']) / 100;
                                    $totalAmount['amonut'] = round($total_vat_amount + $totalAmount['amonut'], 2);
                                    $totalAmount['amonut'] = number_format((float)$totalAmount['amonut'], 2, '.', '');
                                }
                                $totaloffer = isset($totalAmount) && !empty($totalAmount) ? round($totalAmount['amonut'], 2) : '0.00';
                                $site_commission = ($totaloffer * $site_charge_rate) / 100;
                                $resultd = updateDelaerStatus($dealero, $totaloffer, 'add');
                                if (isset($resultd) && !empty($resultd)) {
                                    $dealero->turnover = $resultd['turnover'];
                                    if ($dealero->status_level != $resultd['status']) {
                                        $this->mailer->sendDealerStatusLevelUpdateEmail($dealero, $resultd['status']);
                                    }
                                    $dealero->status_level = $resultd['status'];
                                }
                                $dealero->save();
                            }
                            // update subscription units
                            $commision_invoice_id = $subscription_id = '';
                            if ($dealero->subscription_id && $dealero->is_active_subscription == 1) {
                                $subscription_id = $dealero->subscription_id;
                                $sub_log = SubscriptionLog::where('user_id', $dealero->id)->orderBy('id', 'DESC')->first();
                                if (!empty($sub_log)) {
                                    // $commision_invoice_id = $sub_log->invoice_id;
                                }
                                try {
                                    $subscription = \Stripe\Subscription::retrieve($dealero->subscription_id);
                                    // $commision_invoice_id = $subscription->latest_invoice;
                                    $quantity = $subscription->quantity;
                                    $qty = round($site_commission, 2) / config('common.commision_unit');

                                    $subscription->quantity = $quantity + $qty;
                                    $subscription->prorate = false;
                                    $subscription->save();
                                } catch (Exception $e) {
                                    Log::critical("Can not update commision qty on subscription " . $dealero->subscription_id);
                                }
                            }
                            // Create order
                            $order = new Order;
                            $order->customer_id = $offerPurchaseEnquiry->customer_id;
                            $order->dealer_id = $offerPurchaseEnquiry->dealer_id;
                            $order->currency = getDealerCurrencyType($dealero);
                            $order->enquiry_id = $offerPurchaseEnquiry->customer_enquiry_id;
                            $order->offer_id = $offerPurchaseEnquiry->id;
                            $order->status = 1;
                            $order->amount = isset($totalAmount) && !empty($totalAmount) ? round($totalAmount['amonut'], 2) : '0.00';
                            $order->payment_method = $payment_method;
                            $order->site_commission = $site_commission;
                            $order->subscription_id = $subscription_id;
                            $order->shipping_company = null;
                            $order->tracking_number = null;
                            $order->cancel_proof = null;
                            $order->invoice_number = rand(10000, 99999) . time();
                            $order->commision_invoice_id = $commision_invoice_id;


                            if ($order->save()) {
                                $order->invoice_number = 'OC' . ($order->id + 1000);
                                $order->save();

                                // store payment detail into log
                                $orderPaymentLog = new OrderPaymentLog;
                                $orderPaymentLog->order_id = $order->id;
                                $orderPaymentLog->offer_purchase_enquiry_id = $offerPurchaseEnquiry->id;
                                $orderPaymentLog->customer_id = $offerPurchaseEnquiry->customer_id;
                                $orderPaymentLog->dealer_payout = isset($totalAmount) && !empty($totalAmount) ? round($totalAmount['amonut'], 2) : '0.00';
                                $orderPaymentLog->site_fees = $site_commission;
                                $orderPaymentLog->checkout_id = $offercheckout->id;
                                $orderPaymentLog->created_at = Carbon::now();
                                $orderPaymentLog->save();
                                // end payment details in log
                            }
                            // Send confirmation email
                            $public_path = 'public/order_documents';
                            $absoulute_path = 'app/' . $public_path;
                            $pdf_store_folder = 'offer' . $offerPurchaseEnquiry->id;
                            $public_path_ori = 'storage/order_documents';

                            if (!file_exists(storage_path($absoulute_path))) {
                                Storage::makeDirectory($public_path);
                            }

                            $documents = [];

                            if (!file_exists(storage_path($absoulute_path . '/' . $pdf_store_folder))) {
                                Storage::makeDirectory($public_path . '/' . $pdf_store_folder);
                            }

                            $document_content = Document::find('3');
                            if ($currentlang == "de") {
                                if (isset($document_content->german_description) && !empty($document_content->german_description)) {
                                    $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->german_description);
                                    $terms_pdf = PDF::loadHtml($content);
                                } else {
                                    $terms_pdf = PDF::loadView('purchaseEnquiry.documents.termsandcondition', compact('dealerinfo'));
                                }
                            } else {
                                if (isset($document_content->description) && !empty($document_content->description)) {
                                    $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->description);
                                    $terms_pdf = PDF::loadHtml($content);
                                } else {
                                    $terms_pdf = PDF::loadView('purchaseEnquiry.documents.termsandcondition', compact('dealerinfo'));
                                }
                            }


                            $terms_and_condition = trans('translation.Terms and Condition');
                            $terms_pdf_filename = $terms_and_condition . '.pdf';
                            $terms_pdf_path = storage_path($absoulute_path . '/' . $pdf_store_folder . '/' . $terms_pdf_filename);
                            $terms_pdf->save($terms_pdf_path);
                            if (file_exists($terms_pdf_path)) {
                                $documents['terms']['path'] = $terms_pdf_path;
                                $documents['terms']['filename'] = $terms_pdf_filename;
                            }

                            $document_content = Document::find('4');
                            if ($currentlang == "de") {
                                if (isset($document_content->german_description) && !empty($document_content->german_description)) {
                                    $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->german_description);
                                    $withdrawal_pdf = PDF::loadHtml($content);
                                } else {
                                    $withdrawal_pdf = PDF::loadView('purchaseEnquiry.documents.withdrawaldeclaration', compact('dealerinfo'));
                                }
                            } else {
                                if (isset($document_content->description) && !empty($document_content->description)) {
                                    $content = str_replace(array('{dealer_company_name}', '{dealer_street}', '{dealer_house_number}', '{dealer_zipcode}', '{dealer_city}', '{dealer_email}', '{dealer_phone}'), array($dealerinfo->company_name, $dealerinfo->street, $dealerinfo->house_number, $dealerinfo->zipcode, $dealerinfo->city, $dealerinfo->email, $dealerinfo->phone), $document_content->description);
                                    $withdrawal_pdf = PDF::loadHtml($content);
                                } else {
                                    $withdrawal_pdf = PDF::loadView('purchaseEnquiry.documents.withdrawaldeclaration', compact('dealerinfo'));
                                }
                            }

                            $withdrawal_pdf_filename = 'Withdrawal Declaration.pdf';
                            $withdrawal_pdf_path = storage_path($absoulute_path . '/' . $pdf_store_folder . '/' . $withdrawal_pdf_filename);
                            $withdrawal_pdf->save($withdrawal_pdf_path);
                            if (file_exists($withdrawal_pdf_path)) {
                                $documents['withdrawal']['path'] = $withdrawal_pdf_path;
                                $documents['withdrawal']['filename'] = $withdrawal_pdf_filename;
                            }

                            // Invoice generate
                            $orders = Order::query()
                                ->select(
                                    'product.id',
                                    'orders.id as order_id',
                                    'orders.dealer_id',
                                    'orders.customer_id',
                                    'product.product_name',
                                    'orders.invoice_number',
                                    'orders.created_at',
                                    'purchase_enquiry_products.qty',
                                    'offer_details.offer_amount',
                                    'orders.amount',
                                    'orders.currency',
                                    'brand.brand_name'
                                )
                                ->join('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
                                ->join('product', 'product.id', '=', 'offer_details.product_id')
                                ->join('brand', 'brand.id', '=', 'product.brand_id')
                                ->join('purchase_enquiry_products', function ($q) {
                                    $q->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id');
                                    $q->on('purchase_enquiry_products.product_id', '=', 'product.id');
                                })
                                ->where('orders.id', $order->id)
                                ->where(function ($qry) use ($loginUser) {
                                    if ($loginUser->role_type == 2) {
                                        $qry->where('orders.dealer_id', $loginUser->id);
                                    }
                                    if ($loginUser->role_type == 3) {
                                        $qry->where('orders.customer_id', $loginUser->id);
                                    }
                                })
                                ->groupby('product.id')
                                ->get();

                            $dealer = User::find($orders[0]->dealer_id);
                            $customer = User::find($orders[0]->customer_id);
                            $taxpercentage = 19.00;

                            // Store Invoice
                            $invoice_public_path = 'public/invoice';
                            $invoice_absoulute_path = 'app/' . $invoice_public_path;

                            if (!file_exists(storage_path($invoice_absoulute_path))) {
                                Storage::makeDirectory($invoice_public_path);
                            }

                            header('Content-Type: text/html; charset=utf-8');
                            $invoice_pdf = PDF::loadView('orders.invoice.invoice', compact('orders', 'dealer', 'customer', 'taxpercentage'));

                            $invoice_pdf_filename = trans('translation.Invoice') . '-' . $order->invoice_number . '.pdf';
                            $invoice_pdf_path = storage_path($invoice_absoulute_path . '/' . $invoice_pdf_filename);
                            $invoice_pdf->save($invoice_pdf_path);
                            if (file_exists($invoice_pdf_path)) {
                                $documents['invoice']['path'] = $invoice_pdf_path;
                                $documents['invoice']['filename'] = $invoice_pdf_filename;
                            }

                            $products = Product::query()
                                ->select(
                                    DB::raw('CONCAT(brand.brand_name, " ", product.product_name) as product_name')
                                )
                                ->join('brand', 'brand.id', '=', 'product.brand_id')
                                ->whereIn('product.id', explode(', ', $offerPurchaseEnquiry->product_id))
                                ->groupBy('product.id')
                                ->get()
                                ->pluck('product_name')
                                ->toArray();

                            $userSchema = User::find($offerPurchaseEnquiry->dealer_id);
                            $dataNotify = [
                                'name' => $userSchema->company_name,
                                'title' => trans('translation.order_created_title'),
                                'type' => 'order',
                                'mailGretting' => trans('translation.congratulation'),
                                'mailSubject' => trans('translation.orderCreateDealer_email_subject', ['product' => implode(',', $products)]),
                                'mailBody' => trans('translation.orderCreateDealer_email_body', ['product' => implode(',', $products), 'url' => '<a href="' . route('dealer.sales') . '?oid=' . $order->id . '">' . route('dealer.sales') . '</a>']),
                                'status' => 'created',
                                'body' => trans('translation.order_created_body', ['name' => trans('translation.Customer')]),
                                'senderId' => $loginUser->id,
                                'url' => route('dealer.sales'),
                                'dealer_id' => $userSchema->id,
                                'id' => $order->id
                            ];
                            Notification::send($userSchema, new OrderNotification($dataNotify));

                            if (isset($documents) && !empty($documents) && count($documents) == 3) {
                                $dataNotify = [
                                    'name' => $loginUser->first_name . ' ' . $loginUser->last_name,
                                    'title' => trans('translation.order_created_title'),
                                    'type' => 'order',
                                    'mailGretting' => trans('translation.Hello'),
                                    'mailSubject' => trans('translation.orderCreateCustomer_email_subject', ['product' => implode(',', $products)]),
                                    'mailBody' => trans('translation.orderCreateCustomer_email_body', ['product' => implode(',', $products), 'url' => '<a href="' . route('customer.purchases') . '?oid=' . $order->id . '">' . route('customer.purchases') . '</a>']),
                                    'documents' => $documents,
                                    'status' => 'createdInvoices',
                                    'body' => trans('translation.order_created_body', ['name' => trans('translation.Dealer')]),
                                    'senderId' => $userSchema->id,
                                    'url' => route('customer.purchases'),
                                    'dealer_id' => $userSchema->id,
                                    'id' => $order->id
                                ];
                                Notification::send($loginUser, new OrderNotification($dataNotify));

                                $dataNotify = [
                                    'name' => $userSchema->company_name,
                                    'title' => trans('translation.order_payment_completed_title'),
                                    'type' => 'order',
                                    'mailGretting' => trans('translation.Hello'),
                                    'mailSubject' => trans('translation.orderCompletedPaymentDealer_email_subject', ['product' => implode(',', $products)]),
                                    'mailBody' => trans('translation.orderCompletedPaymentDealer_email_body', ['product' => implode(',', $products)]),
                                    'status' => 'createdInvoices',
                                    'body' => trans('translation.order_payment_completed_body', ['name' => $loginUser->first_name . ' ' . $loginUser->last_name]),
                                    'senderId' => $loginUser->id,
                                    'url' => route('dealer.sales'),
                                    'dealer_id' => $userSchema->id,
                                    'id' => $order->id
                                ];
                                Notification::send($userSchema, new OrderNotification($dataNotify));
                            }

                            $status_name = (isset($statusOP[$offerPurchaseEnquiry->status])) ? $statusOP[$offerPurchaseEnquiry->status] : trans('translation.Status Update');
                            $result = ['status' => true, 'message' => trans('translation.Offers') . ' ' . $status_name . ' ' . trans('translation.successfully')];
                        } else {
                            $result = ['status' => false, 'message' => 'Error in saving data'];
                        }
                    } else {
                        if (isset($checkOrder)) {
                            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                        } else {
                            $result = ['status' => false, 'message' => trans('translation.This offer is valid for 12 hours')];
                        }
                    }
                } else {
                    $result = ['status' => false, 'message' => trans('translation.This offer is valid for 12 hours')];
                }
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        }
        // return with flash
        if (!empty($result['status']) && $result['status'] == false) {
            $message = $result['message'];
            Session::flash('error', $message);
        } elseif (!empty($result['status']) && $result['status'] == true) {
            $message = $result['message'];
            Session::flash('succuss', $message);
            return redirect()->route('customer.purchases');
        } else {
            $message = trans('translation.Something went wrong');
            Session::flash('error', $message);
        }
        return redirect()->route('customer.enquiry');
    }
    public function checkoutCancel(Request $request)
    {
        $offercheckout = OfferCheckoutDetail::where('session_id', $request->session_id)->first();
        if ($offercheckout) {
            $offercheckout->status = 4;
            $offercheckout->save();
        }
        $error_message = trans('translation.Something went wrong');
        Session::flash('error', $error_message);
        return redirect()->route('customer.enquiry');
    }
}
