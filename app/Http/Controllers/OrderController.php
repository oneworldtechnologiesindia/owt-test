<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\DealerBrand;
use Illuminate\Http\Request;
use App\Helpers\MailerFactory;
use App\Models\ProductCategory;
use App\Models\PurchaseEnquiry;
use App\Models\ProductExecution;
use App\Models\ProductConnection;
use App\Models\OrderRating;
use Illuminate\Support\Facades\DB;
use App\Models\OfferPurchaseEnquiry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
    protected $mailer;
    public function __construct(MailerFactory $mailer)
    {
        $this->middleware(['auth']);
        $this->mailer = $mailer;
    }

    public function salesIndex(Request $request)
    {
        $loginUser = Auth::user();
        if (isset($request->oid) && !empty($request->oid)) {
            Session::put('orderShow', $request->oid);
            Session::save();
            if ($loginUser->role_type == 2) {
                $urlr = route('dealer.sales') . '?notify=1';
            } elseif ($loginUser->role_type == 1) {
                $urlr = route('sales') . '?notify=1';
            }
            return redirect($urlr);
        } else {
            if (isset($request->notify) && !empty($request->notify) && session()->has('orderShow')) {
                $order_id = Session::get('orderShow');
                Session::forget('orderShow');
            } else {
                $order_id = '';
                Session::forget('orderShow');
            }
        }
        notificationMarkAsRead('order', $order_id, 'created');
        notificationMarkAsRead('order', $order_id, 'rated');

        if ($loginUser->role_type == 2) {
            return view('orders.dealersales', compact('loginUser', 'order_id'));
        } elseif ($loginUser->role_type == '1') {
            $dealers = User::where('role_type', 2)->get();
            $countries = User::$countries;
            return view('orders.sales', compact('loginUser', 'dealers', 'countries'));
        } else {
            return abort('404');
        }
    }

    public function purchaseIndex(Request $request)
    {
        if (isset($request->oid) && !empty($request->oid)) {
            Session::put('orderShow', $request->oid);
            Session::save();
            $urlr = route('customer.purchases') . '?notify=1';
            return redirect($urlr);
        } else {
            if (isset($request->notify) && !empty($request->notify) && session()->has('orderShow')) {
                $order_id = Session::get('orderShow');
                Session::forget('orderShow');
            } else {
                $order_id = '';
                Session::forget('orderShow');
            }
        }
        $loginUser = Auth::user();
        if ($loginUser->role_type == 3) {
            notificationMarkAsRead('order', $order_id, 'created');
            return view('orders.purchase', compact('order_id'));
        } else {
            return abort('404');
        }
    }

    public function salesGet(Request $request)
    {
        $loginUser = Auth::user();
        $statusOrder = Order::$status;
        $data = Order::query()
            ->select(
                'orders.*',
                'order_rating.average',
                DB::raw('CONCAT(customer.first_name," ",customer.last_name) as customer_name'),
                DB::raw("GROUP_CONCAT(DISTINCT product.id) as pro_id"),
                DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name"),
                'offer_details.vat_amount'
            )
            ->join('users as dealer', 'orders.dealer_id', '=', 'dealer.id')
            ->leftJoin('order_rating', 'order_rating.order_id', '=', 'orders.id')
            ->join('users as customer', 'orders.customer_id', '=', 'customer.id')
            ->join('offer_purchase_enquiry', 'orders.offer_id', '=', 'offer_purchase_enquiry.id')
            ->join('offer_details', 'offer_purchase_enquiry.id', '=', 'offer_details.offer_id')
            ->join('product', 'offer_details.product_id', '=', 'product.id');

        if ($loginUser->role_type != 1) {
            $data->where('orders.dealer_id', $loginUser->id);
        }

        if (isset($request->country) && !empty($request->country)) {
            $country = $request->country;
            $data->where(function ($q) use ($country) {
                $q->orWhere('dealer.country', $country);
                $q->orWhere('customer.country', $country);
            });
        }

        if (isset($request->dealer) && !empty($request->dealer)) {
            $data->where('orders.dealer_id', $request->dealer);
        }

        if (isset($request->start_date) && !empty($request->start_date)) {
            $data->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime($request->start_date)));
        }

        if (isset($request->end_date) && !empty($request->end_date)) {
            $data->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime($request->end_date)));
        }

        $data = $data->groupBy('orders.id');

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('products', function ($row) use ($loginUser) {
                $pro_ids = explode(',', $row->pro_id);
                $productData = [];
                foreach ($pro_ids as $pro_id) {
                    $productQuery = Product::query()
                        ->join('product_category', 'product_category.id', '=', 'product.category_id')
                        ->join('product_type', 'product_type.id', '=', 'product.type_id')
                        ->join('brand', 'brand.id', '=', 'product.brand_id')
                        ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'product.id');

                    if ($loginUser->role_type == 2) {
                        $productQuery->join('dealer_brand', function ($join) use ($loginUser) {
                            $join->on('brand.id', '=', 'dealer_brand.brand_id')
                                ->whereNull('dealer_brand.deleted_at')
                                ->where('dealer_brand.dealer_id', $loginUser->id);
                        });
                    }

                    $productQuery->where('product.id', $pro_id)
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
                        );

                    $productData[] = $productQuery->get();
                }
                return (isset($productData) && $productData) ? $productData : "";
            })
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->editColumn('status_html', function ($row) use ($statusOrder) {
                $badge_class = 'bg-primary';
                if ($row->status == 1) {
                    $badge_class = "bg-info";
                } elseif ($row->status == 2) {
                    $badge_class = "bg-success";
                } elseif ($row->status == 3) {
                    $badge_class = "bg-danger";
                }

                return '<h5><span class="badge text-capitalize ' . $badge_class . '">' . ((isset($statusOrder[$row->status])) ? $statusOrder[$row->status] : "") . '</span></h5>';
            })
            ->editColumn('amount', function ($row) {
                if (isset($row->amount) && !empty($row->amount)) {
                    $vat_amount = $row->vat_amount ?? 0;
                    return formatCurrencyOutput($row->amount + $vat_amount, $row->currency, true, 'before');
                } else {
                    return '-';
                }
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function purchaseGet(Request $request)
    {
        $loginUser = Auth::user();
        $statusOrder = Order::$status;
        $data = Order::query()
            ->select(
                'orders.*',
                DB::raw('dealer.company_name as dealer_name'),
                DB::raw("GROUP_CONCAT(DISTINCT product.id) as pro_id"),
                DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name"),
                'offer_details.vat_amount'
            )
            ->join('users as dealer', 'orders.dealer_id', '=', 'dealer.id')
            ->join('users as customer', 'orders.customer_id', '=', 'customer.id')
            ->join('offer_purchase_enquiry', 'orders.offer_id', '=', 'offer_purchase_enquiry.id')
            ->join('offer_details', 'offer_purchase_enquiry.id', '=', 'offer_details.offer_id')
            ->join('product', 'offer_details.product_id', '=', 'product.id')
            ->where('orders.customer_id', $loginUser->id)
            ->groupBy('orders.id');

        return datatables()::of($data)
            ->addIndexColumn()
            ->editColumn('products', function ($row) use ($loginUser) {
                $pro_ids = explode(',', $row->pro_id);
                $productData = [];
                foreach ($pro_ids as $pro_id) {
                    $productQuery = Product::query()
                        ->join('product_category', 'product_category.id', '=', 'product.category_id')
                        ->join('product_type', 'product_type.id', '=', 'product.type_id')
                        ->join('brand', 'brand.id', '=', 'product.brand_id')
                        ->leftJoin('product_attributes', 'product_attributes.product_id', '=', 'product.id');

                    if ($loginUser->role_type == 2) {
                        $productQuery->join('dealer_brand', function ($join) use ($loginUser) {
                            $join->on('brand.id', '=', 'dealer_brand.brand_id')
                                ->whereNull('dealer_brand.deleted_at')
                                ->where('dealer_brand.dealer_id', $loginUser->id);
                        });
                    }

                    $productQuery->where('product.id', $pro_id)
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
                        );

                    $productData[] = $productQuery->get();
                }
                return (isset($productData) && $productData) ? $productData : "";
            })
            ->editColumn('created_at', function ($row) {
                return getDateFormateView($row->created_at);
            })
            ->editColumn('amount', function ($row) {
                $vat_amount = $row->vat_amount ?? 0;
                return formatCurrencyOutput($row->amount + $vat_amount, $row->currency, true, 'before');
            })
            ->editColumn('status_html', function ($row) use ($statusOrder) {
                $badge_class = 'bg-primary';
                if ($row->status == 1) {
                    $badge_class = "bg-info";
                } elseif ($row->status == 2) {
                    $badge_class = "bg-success";
                } elseif ($row->status == 3) {
                    $badge_class = "bg-danger";
                }

                return '<h5><span class="badge text-capitalize ' . $badge_class . '">' . ((isset($statusOrder[$row->status])) ? $statusOrder[$row->status] : "") . '</span></h5>';
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search')) && $request->get('search')) {
                    $search = $request->get('search');
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function getDetailOrder(Request $request)
    {
        $loginUser = Auth::user();
        $orderid = isset($request->id) && !empty($request->id) ? $request->id : '';
        if (!empty($orderid) && $request->ajax()) {
            $query = Order::query();
            if ($loginUser->role_type == 2 || $loginUser->role_type == 1) {
                $query->select(
                    'customer.*',
                    'orders.status',
                    'orders.shipping_company',
                    'orders.tracking_number',
                    'orders.cancel_proof',
                    'orders.payment_method',
                    DB::raw('CONCAT(customer.first_name," ",customer.last_name) as customer_name')
                );
                $query->join('users as customer', 'customer.id', '=', 'orders.customer_id');
            } elseif ($loginUser->role_type == 3) {
                $query->select(
                    'dealer.*',
                    'orders.status',
                    'orders.shipping_company',
                    'orders.tracking_number',
                    'orders.cancel_proof',
                    'orders.payment_method',
                    'orders.id as orderid',
                    DB::raw('dealer.company_name as dealer_name')
                );
                $query->join('users as dealer', 'dealer.id', '=', 'orders.dealer_id');
            }

            $data = $query->where('orders.id', $orderid)->first();

            $data->cancel_proof = $data->getCancelDocumentUrl($data->cancel_proof);

            $payment_methods = OfferPurchaseEnquiry::$payment_method;
            if (isset($data->payment_method) && !empty($data->payment_method)) {
                $data->payment_method = $payment_methods[$data->payment_method];
            }

            $data->transaction_rating = $data->delivery_rating = $data->communication_rating = 0;

            $orderRatingDetails = OrderRating::query()
                ->where('order_id', $orderid)
                ->first();

            if (!empty($orderRatingDetails)) {
                $data->transaction_rating = $orderRatingDetails->transaction_rating;
                $data->delivery_rating = $orderRatingDetails->delivery_rating;
                $data->communication_rating = $orderRatingDetails->communication_rating;
            }

            if (isset($data->id)) {
                if ($loginUser->role_type == 2) {
                    notificationMarkAsRead('order', $orderid, 'created');
                } elseif ($loginUser->role_type == 3) {
                    notificationMarkAsRead('order', $orderid, 'paymentConfirmed');
                }
                $result = ['status' => true, 'message' => '', 'data' => $data];
            } else {
                $result = ['status' => false, 'message' => '', 'data' => []];
            }
        }
        return response()->json($result);
        exit();
    }

    public function getOrderProductList(Request $request)
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
        $order_id = ($request->order_id) ? $request->order_id : 0;

        $qry = order::query()
            ->where('orders.id', $order_id)
            ->join('purchase_enquiry', 'purchase_enquiry.id', '=', 'orders.enquiry_id')
            ->join('purchase_enquiry_products', 'purchase_enquiry.id', '=', 'purchase_enquiry_products.customer_enquiry_id')
            ->select(
                'product.*',
                DB::raw('CONCAT(product.product_name, " (", brand.brand_name,")") as product_brand'),
                'brand.brand_name',
                'orders.amount as total',
                'orders.currency as currency',
                'purchase_enquiry_products.customer_enquiry_id',
                'purchase_enquiry_products.qty',
                'purchase_enquiry_products.connection_ids',
                'purchase_enquiry_products.execution_ids',
                'purchase_enquiry_products.attribute_ids',
                'offer_details.offer_amount as price',
                'offer_details.vat_amount'
            );

        if ($loginUser->role_type == 2 || $loginUser->role_type == 1) {
            $qry->join('dealer_purchase_enquiry', 'dealer_purchase_enquiry.customer_enquiry_id', '=', 'purchase_enquiry_products.customer_enquiry_id');
            if ($loginUser->role_type != 1) {
                $qry->join('product', function ($query) use ($brandIds) {
                    $query->on('product.id', '=', 'purchase_enquiry_products.product_id');
                    $query->on(DB::raw("FIND_IN_SET(product.id,dealer_purchase_enquiry.for_product_id)"), ">", DB::raw("'0'"));
                    $query->whereIn('product.brand_id', $brandIds);
                });
                $qry->where('dealer_purchase_enquiry.dealer_id', $loginUser->id);
            } else {
                $qry->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id');
            }
        } else if ($loginUser->role_type == 3) {
            $qry->join('product', 'product.id', '=', 'purchase_enquiry_products.product_id');
            $qry->where('purchase_enquiry.customer_id', $loginUser->id);
        }

        $qry->join('offer_details', function ($q) {
            $q->on('orders.offer_id', '=', 'offer_details.offer_id');
            $q->on('product.id', '=', 'offer_details.product_id');
        })
            ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
            ->groupBy('product.id');

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
            ->editColumn('price', function ($row) {
                if (isset($row->price) && !empty($row->price)) {
                    return formatCurrencyOutput($row->price, $row->currency, true, 'before');
                } else {
                    return '-';
                }
            })
            ->editColumn('total', function ($row) {
                if (isset($row->total) && !empty($row->total)) {
                    $vat_amount = $row->vat_amount ?? 0;
                    return formatCurrencyOutput($row->total + $vat_amount, $row->currency, true, 'before');
                } else {
                    return '-';
                }
            })
            ->escapeColumns([])
            ->make(true);
        die();
    }

    public function confirmOrderPayment(Request $request)
    {
        $order = Order::find($request->id);
        if (isset($order) && !empty($order) && $order->status == 0 && $order->status != 1) {
            $order->status = 1;
            if ($order->save()) {
                $loginUser = Auth::user();
                $userSchema = User::find($order->customer_id);
                $products = OfferPurchaseEnquiry::query()
                    ->select(
                        DB::raw('CONCAT(brand.brand_name, " ", product.product_name) as product_name')
                    )
                    ->join('offer_details', 'offer_details.offer_id', 'offer_purchase_enquiry.id')
                    ->join('product', 'product.id', 'offer_details.product_id')
                    ->join('brand', 'brand.id', '=', 'product.brand_id')
                    ->where('offer_purchase_enquiry.id', $order->offer_id)
                    ->groupBy('product.id')
                    ->get()->pluck('product_name')->toArray();

                $dataNotify = [
                    'name' => $userSchema->first_name . ' ' . $userSchema->last_name,
                    'mailGretting' => trans('translation.Hello'),
                    'mailSubject' => trans('translation.orderConfirmPayment_email_subject', ['product' => implode(',', $products)]),
                    'mailBody' => trans('translation.orderConfirmPayment_email_body', ['product' => implode(',', $products)]),
                    'title' => trans('translation.order_payment_method_title'),
                    'type' => 'order',
                    'status' => 'paymentConfirmed',
                    'body' => trans('translation.order_payment_method_body', ['name' => trans('translation.Dealer')]),
                    'senderId' => $loginUser->id,
                    'url' => route('customer.purchases'),
                    'dealer_id' => $userSchema->id,
                    'id' => $order->id
                ];
                Notification::send($userSchema, new OrderNotification($dataNotify));

                $result = ['status' => true, 'message' => trans('translation.Order status change successfully')];
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }

    public function orderShipping(Request $request)
    {
        $rules = array(
            'shipping_company' => 'required',
            'tracking_number' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $result = ['status' => false, 'error' => $validator->errors()];
        } else {
            $order = Order::find($request->id);
            if (isset($order) && !empty($order) && $order->status == 1 && $order->status != 2) {
                $order->shipping_company = $request->shipping_company;
                $order->tracking_number = $request->tracking_number;
                $order->status = 2;

                if ($order->save()) {
                    $loginUser = Auth::user();
                    $userSchema = User::find($order->customer_id);
                    $products = Product::query()
                        ->select(
                            DB::raw('CONCAT(brand.brand_name, " ", product.product_name) as product_name')
                        )
                        ->join('brand', 'brand.id', '=', 'product.brand_id')
                        ->whereIn('product.id', explode(', ', optional($order->offerPurchaseEnquiry)->product_id))
                        ->groupBy('product.id')
                        ->get()
                        ->pluck('product_name')
                        ->toArray();

                    $dataNotify = [
                        'name' => $userSchema->first_name . ' ' . $userSchema->last_name,
                        'title' => trans('translation.order_shipped_title'),
                        'type' => 'order',
                        'mailGretting' => trans('translation.Hello'),
                        'mailSubject' => trans('translation.orderShippedCustomer_email_subject', ['product' => implode(',', $products)]),
                        'mailBody' => trans('translation.orderShippedCustomer_email_body', ['product' => implode(',', $products), 'url' => '<a href="' . route('customer.purchases') . '?oid=' . $order->id . '">' . route('customer.purchases') . '</a>']),
                        'status' => 'created',
                        'body' => trans('translation.order_shipped_body', ['name' => trans('translation.Dealer')]),
                        'senderId' => $loginUser->id,
                        'url' => route('customer.purchases'),
                        'dealer_id' => $loginUser->id,
                        'id' => $order->id
                    ];
                    Notification::send($userSchema, new OrderNotification($dataNotify));
                    $result = ['status' => true, 'message' => trans('translation.Order status change successfully')];
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                }
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        }
        return response()->json($result);
    }

    public function orderCanceled(Request $request)
    {
        $rules = array(
            'cancel_proof' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $result = ['status' => false, 'error' => $validator->errors()];
        } else {
            $order = Order::find($request->corderid);
            if (isset($order) && !empty($order) && $order->status != 3) {
                if ($order->cancel_proof != '') {
                    $oldfileExists = storage_path('app/public/cancel_proof/') . $order->cancel_proof;
                    if (file_exists($oldfileExists)) {
                        unlink($oldfileExists);
                    }
                }
                $filename = $request->cancel_proof->hashName();
                $request->cancel_proof->storeAs('cancel_proof', $filename, 'public');
                $order->cancel_proof = $filename;
                $order->status = 3;

                if ($order->save()) {

                    $dealero = User::find($order->dealer_id);
                    if (isset($dealero) && !empty($dealero)) {
                        $resultd = updateDelaerStatus($dealero, $order->amount, 'cancel');
                        if (isset($resultd) && !empty($resultd)) {
                            $dealero->turnover = $resultd['turnover'];
                            if ($dealero->status_level != $resultd['status']) {
                                $this->mailer->sendDealerStatusLevelUpdateEmail($dealero, $resultd['status']);
                            }
                            $dealero->status_level = $resultd['status'];
                        }
                        $dealero->save();
                    }
                    $result = ['status' => true, 'message' => trans('translation.Order status change successfully')];
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
                }
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        }
        return response()->json($result);
    }

    public function getInvoice(Request $request)
    {
        $loginUser = Auth::user();
        if (isset($loginUser) && !empty($loginUser) && isset($request->id) && !empty($request->id)) {
            $orders = Order::query()
                ->select(
                    'product.id',
                    'orders.id as order_id',
                    'orders.dealer_id',
                    'orders.currency',
                    'orders.customer_id',
                    'product.product_name',
                    'orders.invoice_number',
                    'orders.created_at',
                    'purchase_enquiry_products.qty',
                    'offer_details.offer_amount',
                    'orders.amount',
                    'brand.brand_name',
                    'offer_details.vat_amount',
                    'offer_details.vat_rate'
                )
                ->join('offer_details', 'offer_details.offer_id', '=', 'orders.offer_id')
                ->join('product', 'product.id', '=', 'offer_details.product_id')
                ->join('brand', 'brand.id', '=', 'product.brand_id')
                ->join('purchase_enquiry_products', function ($q) {
                    $q->on('purchase_enquiry_products.customer_enquiry_id', '=', 'orders.enquiry_id');
                    $q->on('purchase_enquiry_products.product_id', '=', 'product.id');
                })
                ->where('orders.id', $request->id)
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
            $loggedInUserCountry = $loginUser->country;
            $taxpercentage = 19.00;
            if (isset($orders) && !empty($orders) && count($orders) > 0 && isset($dealer) && !empty($dealer) && isset($customer) && !empty($customer)) {
                header('Content-Type: text/html; charset=utf-8');
                $pdf = PDF::loadView('orders.invoice.invoice', compact('orders', 'dealer', 'customer', 'taxpercentage', 'loggedInUserCountry'));
                $pdf_filename = 'Invoice.pdf';
            } else {
                return abort('404');
            }
        } else {
            return abort('404');
        }
        return $pdf->stream($pdf_filename);
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

    public function addRatingUrl(Request $request)
    {
        if (!empty($request->get('purchase_id'))) {
            $data = array();

            $orderRatingDetails = OrderRating::query()
                ->where('order_id', $request->get('purchase_id'))
                ->first();

            if (empty($orderRatingDetails)) {
                $orderRatingDetails = new OrderRating;
                $orderRatingDetails->created_at = date('Y-m-d H:i:s');
            }

            $orderRatingDetails->order_id = $request->get('purchase_id');

            $rating_value = '';
            $rating_type = '';
            if (!empty($request->get('communication_rating')) && $orderRatingDetails->communication_rating == 0) {
                $orderRatingDetails->communication_rating = $request->get('communication_rating');
                $rating_value = $request->get('communication_rating');
                $rating_type = 'communication';
            }

            if (!empty($request->get('transaction_rating')) && $orderRatingDetails->transaction_rating == 0) {
                $orderRatingDetails->transaction_rating = $request->get('transaction_rating');
                $rating_value = $request->get('transaction_rating');
                $rating_type = 'transaction';
            }

            if (!empty($request->get('delivery_rating')) && $orderRatingDetails->delivery_rating == 0) {
                $orderRatingDetails->delivery_rating = $request->get('delivery_rating');
                $rating_value = $request->get('delivery_rating');
                $rating_type = 'delivery';
            }

            $orderRatingDetails->average = ($orderRatingDetails->communication_rating + $orderRatingDetails->transaction_rating + $orderRatingDetails->delivery_rating) / 3;
            $orderRatingDetails->updated_at = date('Y-m-d H:i:s');

            $orderRatingDetails->save();


            $userSchema = User::find($orderRatingDetails->order->dealer_id);
            $loginUser = Auth::user();

            // Send notification to dealer for rating
            $dataNotify = [
                'name' => $userSchema->first_name . ' ' . $userSchema->last_name,
                'mailGretting' => trans('translation.Hello'),
                'mailSubject' => trans('translation.orderRatedDealer_email_subject'),
                'mailBody' => trans('translation.orderRatedDealer_email_body', [
                    'rating_type' => $rating_type,
                    'rating_value' => $rating_value,
                ]),
                'title' => trans('translation.order_rated_title'),
                'type' => 'order',
                'status' => 'rated',
                'body' => trans('translation.order_rated_body', [
                    'rating_type' => $rating_type,
                    'rating_value' => $rating_value,
                ]),
                'senderId' => $loginUser->id,
                'url' => route('dealer.sales'),
                'dealer_id' => $userSchema->id,
                'id' => $orderRatingDetails->order_id,
                'ratings' => [
                    'communication' => $orderRatingDetails->communication_rating,
                    'transaction' => $orderRatingDetails->transaction_rating,
                    'delivery' => $orderRatingDetails->delivery_rating,
                    'average' => $orderRatingDetails->average,
                ],
            ];

            Notification::send($userSchema, new OrderNotification($dataNotify));

            $data['communication_rating'] = $orderRatingDetails->communication_rating;
            $data['transaction_rating'] = $orderRatingDetails->transaction_rating;
            $data['delivery_rating'] = $orderRatingDetails->delivery_rating;

            $result = ['status' => true, 'message' => trans('translation.Review saved successfully'), 'data' => $data];

            return response()->json($result);
        }
    }
}
