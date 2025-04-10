<?php

namespace App\Http\Controllers;

use App\User;
use DateTime;
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use App\Models\ProductCategory;
use App\Models\PurchaseEnquiry;
use App\Models\ProductExecution;
use App\Models\AppointmentDealer;
use App\Models\ProductAttributes;
use App\Models\ProductConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AppointmentNotification;

use App\Traits\ZoomMeetingTrait;

class AppointmentDealerController extends Controller
{
    use ZoomMeetingTrait;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function appointmentIndex(Request $request)
    {
        $loginUser = Auth::user();
        if (isset($request->id) && !empty($request->id)) {
            Session::put('notificatioShow', $request->id);
            Session::save();
            if($loginUser->role_type == 2) {
                $urlr = route('dealer.appointment') . '?notify=1';
            } elseif($loginUser->role_type == 3) {
                $urlr = route('customer.appointment') . '?notify=1';
            }
            return redirect($urlr);
        } else {
            if (isset($request->notify) && !empty($request->notify) && session()->has('notificatioShow')) {
                $appo_id = Session::get('notificatioShow');
                Session::forget('notificatioShow');
            } else {
                $appo_id = '';
                Session::forget('notificatioShow');
            }
            if ($loginUser->role_type == '2') {
                return view('appointment.dealerindex', compact('loginUser', 'appo_id'));
            } elseif ($loginUser->role_type == '1') {
                $dealers = User::where('role_type', 2)->get();
                $countries = User::$countries;
                return view('appointment.adminindex', compact('loginUser', 'dealers', 'countries', 'appo_id'));
            } elseif ($loginUser->role_type == '3') {
                return view('appointment.customerindex', compact('appo_id'));
            } else {
                return abort('404');
            }
        }
    }

    public function getProducts(Request $request)
    {
        $getProduct = [];

        if ((isset($request->brand_id) && !empty($request->brand_id)) || (isset($request->producttype_id) && !empty($request->producttype_id)) || (isset($request->productcategory_id) && !empty($request->productcategory_id)) || (isset($request->enquiry_type) && !empty($request->enquiry_type))) {
            $getProduct = Product::query()
                ->join('brand', 'brand.id', '=', 'product.brand_id')
                ->select(
                    'product.id',
                    'product.brand_id',
                    DB::raw('CONCAT(product_name, " (", brand.brand_name,")") as text')
                );
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
            $all_products = $getProduct->get()->toArray();
        } else {
            $all_products = [];
        }

        if (isset($all_products) && !empty($all_products)) {
            return response()->json(['status' => '200', 'data' => $all_products], 200);
        } else {
            return response()->json(['status' => '400', 'message' => 'No any product found.'], 200);
        }

        return response()->json(['status' => false, 'message' => 'Product not found.']);
    }

    public function calendarIndex(Request $request)
    {
        $loginUser = Auth::user();
        if ($loginUser->role_type == '2') {
            $event_categories = CalendarEvent::$category;
            return view('calendar.dealerindex', compact('event_categories'));
        } elseif ($loginUser->role_type == '3') {
            return abort('404');
        } else {
            return abort('404');
        }
    }

    public function eventAddupdate(Request $request)
    {
        $loginUser = Auth::User();
        if ($request->ajax() && $loginUser->role_type == 2) {
            if (isset($request->eventType) && !empty($request->eventType)) {
                $rules = array();
            } else {
                $rules = array(
                    'title' => 'required',
                    'event_date' => 'required',
                    'event_time' => 'required',
                    'category' => 'required'
                );
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $succssmsg = trans('translation.Event added successfully.');
                if ($request->event_id) {
                    $model = CalendarEvent::where('id', $request->event_id)->first();
                    if ($model) {
                        $event = $model;
                        $succssmsg = trans('translation.Event updated successfully.');
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
                        return response()->json($result);
                    }
                } else {
                    $event = new CalendarEvent;
                    $event->created_at = Carbon::now();
                }

                $event->user_id = $loginUser->id;
                if (!isset($request->eventdate)) {
                    $event->title = ($request->title) ? $request->title : NULL;
                }
                $event->description = ($request->description) ? $request->description : NULL;
                $event->datetime = (isset($request->eventdate) && !empty($request->eventdate)) ? Carbon::parse($request->eventdate . ' ' . date('H:i:s', strtotime($event->datetime))) : (($request->event_date && $request->event_time) ? Carbon::parse($request->event_date . ' ' . $request->event_time) : NULL);
                $event->category = ($request->category) ? $request->category : NULL;
                $event->updated_at = Carbon::now();

                if ($event->save()) {
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

    public function getAppointment(Request $request)
    {
        $loginUser = Auth::user();
        $statusAppo = AppointmentDealer::$status;

        $data = AppointmentDealer::query()
            ->join("product", DB::raw("FIND_IN_SET(product.id,appointment_dealer.product_id)"), ">", DB::raw("'0'"));

        if ($loginUser->role_type == 2 || $loginUser->role_type == 1) {
            $data->select(
                'appointment_dealer.*',
                DB::raw('CONCAT(users.first_name," ",users.last_name) as customer_name'),
                DB::raw("GROUP_CONCAT(DISTINCT product.id) as pro_id"),
                DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name")
            );
        } else {
            $data->select(
                'appointment_dealer.*',
                DB::raw('users.company_name as dealer_name'),
                DB::raw("GROUP_CONCAT(DISTINCT product.id) as pro_id"),
                DB::raw("GROUP_CONCAT(DISTINCT product.product_name SEPARATOR ', ') as product_name")
            );
        }

        if ($loginUser->role_type == 2 || $loginUser->role_type == 1) {
            $data->join('users', 'users.id', 'appointment_dealer.customer_id');
            $data->join('users as dealer', 'dealer.id', 'appointment_dealer.dealer_id');
        } else {
            $data->join('users', 'users.id', 'appointment_dealer.dealer_id');
        }
        if ($loginUser->role_type != 1) {
            if ($loginUser->role_type == 2) {
                $data->where('appointment_dealer.dealer_id', $loginUser->id);
            } else {
                $data->where('appointment_dealer.customer_id', $loginUser->id);
            }
        }

        if (isset($request->country) && !empty($request->country)) {
            $country = $request->country;
            $data->where(function ($q) use ($country) {
                $q->orWhere('dealer.country', $country);
                $q->orWhere('users.country', $country);
            });
        }

        if (isset($request->dealer) && !empty($request->dealer)) {
            $data->where('appointment_dealer.dealer_id', $request->dealer);
        }

        if (isset($request->start_date) && !empty($request->start_date)) {
            $startdate = date('Y-m-d', strtotime($request->start_date));
            $data->where(function ($query) use ($startdate) {
                $query->orwhere(function ($q) use ($startdate) {
                    $q->whereDate('appointment_dealer.appo_date', '>=', \Carbon\Carbon::parse($startdate))
                        ->whereIn('appointment_dealer.status', [1, 2]);
                });
                $query->orwhere(function ($q) use ($startdate) {
                    $q->whereDate('appointment_dealer.reschedule_appo_date', '>=', \Carbon\Carbon::parse($startdate))
                        ->whereIn('appointment_dealer.status', [6, 7]);
                });
            });
        }

        if (isset($request->end_date) && !empty($request->end_date)) {
            $enddate = date('Y-m-d', strtotime($request->end_date));
            $data->where(function ($query) use ($enddate) {
                $query->orwhere(function ($q) use ($enddate) {
                    $q->whereDate('appointment_dealer.appo_date', '<=', \Carbon\Carbon::parse($enddate))
                        ->whereIn('appointment_dealer.status', [1, 2]);
                });
                $query->orwhere(function ($q) use ($enddate) {
                    $q->whereDate('appointment_dealer.reschedule_appo_date', '<=', \Carbon\Carbon::parse($enddate))
                        ->whereIn('appointment_dealer.status', [6, 7]);
                });
            });
        }

        $data->groupBy('appointment_dealer.id')->orderBy('appo_date', 'DESC');

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
            ->editColumn('appo_date', function ($row) {
                if (!empty($row->reschedule_appo_date) && ($row->status == 7 || $row->status == 6)) {
                    return getDateFormateView($row->reschedule_appo_date);
                } else {
                    return getDateFormateView($row->appo_date);
                }
            })
            ->editColumn('appo_time', function ($row) {
                if (!empty($row->reschedule_appo_time) && ($row->status == 7 || $row->status == 6)) {
                    return $row->reschedule_appo_time;
                } else {
                    return $row->appo_time;
                }
            })
            ->editColumn('status', function ($row) use ($statusAppo) {
                $badge_class = 'bg-primary';
                $appoiment_status = 'Pending';
                if ($row->status == 1) {
                    $badge_class = "bg-primary";
                    $appoiment_status = 'Pending';
                } elseif ($row->status == 2 || $row->status == 7) {
                    $badge_class = "bg-info";
                    $appoiment_status = 'Confirmed';
                } elseif ($row->status == 3) {
                    $badge_class = "bg-success";
                    $appoiment_status = 'Completed';
                } elseif ($row->status == 6) {
                    $badge_class = "bg-warning";
                    $appoiment_status = 'Completed';
                } else {
                    $badge_class = "bg-danger";
                    $appoiment_status = 'Cancled';
                }

                $date_time_today = Carbon::now()->format('Y-m-d H:i:s');
                if (!empty($row->reschedule_appo_date) && ($row->status == 6 || $row->status == 7)) {
                    $appo_date = date("Y-m-d", strtotime($row->reschedule_appo_date));
                    $appo_time = date("H:i:s", strtotime($row->reschedule_appo_time));
                    $date_time = date("Y-m-d H:i:s", strtotime($appo_date . " " . $appo_time));
                } else {
                    $appo_date = date("Y-m-d", strtotime($row->appo_date));
                    $appo_time = date("H:i:s", strtotime($row->appo_time));
                    $date_time = date("Y-m-d H:i:s", strtotime($appo_date . " " . $appo_time));
                }


                if ($date_time <= $date_time_today && ($row->status == 1 || $row->status == 6)) {
                    $status_appo = 8;
                    $badge_class = "bg-danger";
                } else {
                    $status_appo = $row->status;
                }

                return '<h5><span class="badge ' . $badge_class . '">' . ((isset($statusAppo[$status_appo])) ? $statusAppo[$status_appo] : "") . '</span></h5>';
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

    public function rescheduleAppointment(Request $request)
    {
        if ($request->ajax() || (isset($request->appointment_id) && !empty($request->appointment_id))) {
            $appointment = AppointmentDealer::find($request->appointment_id);

            $date_time_today = Carbon::now()->format('Y-m-d H:i:s');
            $appo_date = date("Y-m-d", strtotime($appointment->appo_date));
            $appo_time = date("H:i:s", strtotime($appointment->appo_time));
            $date_time = date("Y-m-d H:i:s", strtotime($appo_date . " " . $appo_time));

            if (isset($appointment) && !empty($appointment) && $appointment->status == 1 && $date_time >= $date_time_today) {
                if (empty($appointment->reschedule_appo_date) && empty($appointment->reschedule_appo_time)) {
                    $loginUser = Auth::User();
                    $rules = array(
                        'appointment_id' => 'required',
                        'appo_date' => 'required',
                        'appo_time' => 'required',
                    );

                    $messsages['appo_date.required'] = trans('validation.custom.appo_date.required');
                    $messsages['appo_time.required'] = trans('validation.custom.appo_time.required');

                    $validator = Validator::make($request->all(), $rules, $messsages);
                    if ($validator->fails()) {
                        $result = ['status' => false, 'error' => $validator->errors()];
                    } else {
                        $appo_date = ($request->appo_date) ? date('Y-m-d', strtotime($request->appo_date)) : date('Y-m-d');
                        $appo_time = ($request->appo_time) ? date('H:i', strtotime($request->appo_time)) : date('H:i');

                        $now_date = new DateTime();
                        $due_date = $appo_date . " " . $appo_time;
                        $due_date = new DateTime($due_date);

                        if ($now_date < $due_date) {
                            $appo_time_main = strtotime($appo_time);
                            $startTime = date("H:i", strtotime('-59 minutes', $appo_time_main));
                            $endTime = date("H:i", strtotime('+59 minutes', $appo_time_main));

                            $condition = [];
                            $condition[] = ['dealer_id', '=', $loginUser->id];

                            $findAppointment = AppointmentDealer::where($condition)
                                ->where(function ($query) use ($startTime, $endTime, $appo_date) {
                                    $query->orwhere(function ($q) use ($startTime, $endTime, $appo_date) {
                                        $q->whereTime('appo_time', '>=', \Carbon\Carbon::parse($startTime))
                                            ->whereTime('appo_time', '<=', \Carbon\Carbon::parse($endTime))
                                            ->where('appo_date', '=', $appo_date)
                                            ->whereIn('status', [1, 2]);
                                    });
                                    $query->orwhere(function ($q) use ($startTime, $endTime, $appo_date) {
                                        $q->whereTime('reschedule_appo_time', '>=', \Carbon\Carbon::parse($startTime))
                                            ->whereTime('reschedule_appo_time', '<=', \Carbon\Carbon::parse($endTime))
                                            ->where('reschedule_appo_date', '=', $appo_date)
                                            ->whereIn('status', [6, 7]);
                                    });
                                })
                                ->first();

                            if (empty($findAppointment)) {
                                $appointment->status = 6;
                                $appointment->reschedule_appo_date = $appo_date;
                                $appointment->reschedule_appo_time = \Carbon\Carbon::parse($appo_time);
                                $appointment->updated_at = Carbon::now();

                                $products = Product::query()
                                    ->select(
                                        DB::raw('CONCAT(brand.brand_name, " ", product.product_name) as product_name')
                                    )
                                    ->join('brand', 'brand.id', '=', 'product.brand_id')
                                    ->whereIn('product.id', explode(', ', $appointment->product_id))
                                    ->groupBy('product.id')
                                    ->get()
                                    ->pluck('product_name')
                                    ->toArray();

                                if ($appointment->save()) {
                                    $userSchema = User::find($appointment->customer_id);

                                    $meetingData = "";
                                    $mailType = "Hörtermin";
                                    if($appointment->appo_type==1){
                                        $mailType = "Videoberatungs";
                                        $zoom_meeting_json = ($appointment->zoom_meeting_json)? json_decode($appointment->zoom_meeting_json,true):"";
                                        $zoom_meeting_id = (isset($zoom_meeting_json['data']['id']) && $zoom_meeting_json['data']['id'])? $zoom_meeting_json['data']['id']:"";
                                        $postZoomMeeting=[];
                                        $postZoomMeeting['topic'] = $loginUser->first_name." ".$loginUser->last_name." is inviting you to a scheduled Zoom meeting.";
                                        $postZoomMeeting['start_time'] = date('Y-m-d H:i:s',strtotime($appo_date." ".$appo_time));
                                        $postZoomMeeting['duration'] = 30;//The meeting's scheduled duration, in minutes
                                        $postZoomMeeting['agenda'] = ($request->note)? $request->note:"";
                                        if($zoom_meeting_id){
                                            $meetingData = $this->updateZoomMeeting($zoom_meeting_id,$postZoomMeeting);
                                        }else{
                                            $meetingData = $this->createZoomMeeting($postZoomMeeting);
                                        }
                                    }
                                    if(isset($meetingData['data']['id']) && $meetingData['data']['id']){
                                        $appointment->zoom_met_join_url = $meetingData['data']['join_url'];
                                        $appointment->zoom_meeting_json = json_encode($meetingData);
                                        $appointment->save();
                                    }

                                    $mailBody  = "";
                                    $mailBody .= trans('translation.apporeschedule_email_body', ['date' => date('d.m.Y', strtotime($appo_date)), 'time' => date('H:m', strtotime($appo_time)), 'url' => '<a href="' . route('customer.appointment') . '?oid=' . $appointment->id . '">' . route('customer.appointment') . '</a>', 'mailtype' => $mailType]);

                                    if($appointment->zoom_met_join_url){
                                        $join_now  = '<a href="' .$appointment->zoom_met_join_url. '">' . $appointment->zoom_met_join_url . '</a>';
                                        $mailBody .= "<br /><br />";
                                        $mailBody .= trans('translation.appocreate_email_zoom_met_join_url', ['zoom_met_join_url' =>$join_now]);
                                    }

                                    $dataNotify = [
                                        'name' => $userSchema->first_name . ' ' . $userSchema->last_name,
                                        'mailGretting' => trans('translation.Hello'),
                                        'mailSubject' => trans('translation.apporeschedule_email_subject', ['product' => (($products) ? implode(",", $products) : '')]),
                                        'mailBody' => $mailBody,
                                        'title' => trans('translation.appoinment_reschedule_title'),
                                        'type' => 'appointment',
                                        'status' => 'reschedule',
                                        'body' => trans('translation.appoinment_reschedule_body', ['name' => trans('translation.Dealer')]),
                                        'senderId' => $loginUser->id,
                                        'url' => route('customer.appointment'),
                                        'dealer_id' => $userSchema->id,
                                        'id' => $appointment->id
                                    ];
                                    Notification::send($userSchema, new AppointmentNotification($dataNotify));
                                    $result = ['status' => true, 'message' => trans('translation.Appointment Reschedule successfully.'), 'data' => []];
                                } else {
                                    $result = ['status' => false, 'message' => trans('translation.Something went wrong please try again'), 'data' => []];
                                }
                            } else {
                                $result = ['status' => false, 'message' => trans('translation.Appointment already taken please choose another time'), 'data' => []];
                            }
                        } else {
                            $result = ['status' => false, 'message' => trans('translation.Please choose a valid date and time'), 'data' => []];
                        }
                    }
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Appointment already rescheduled'), 'data' => []];
                }
            } else {
                $result = ['status' => false, 'message' => trans('translation.You can not reschedule appointment.')];
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
        }
        return response()->json($result);
    }

    public function create()
    {
        $productBrand = Product::query()
            ->join('brand', 'brand.id', '=', 'product.brand_id')
            ->select('product.brand_id', 'product.id', DB::raw('CONCAT(product_name, " (", brand.brand_name,")") as product_name'))
            ->get()
            ->toArray();

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

        $model = new AppointmentDealer;

        /*$postZoomMeeting=[];
        $postZoomMeeting['topic'] = "Request for Appointment Meeting to amcodr new";
        $postZoomMeeting['start_time'] = date('Y-m-d H:i:s',strtotime("2023-05-22 16:17:35"));
        $postZoomMeeting['duration'] = 30;//The meeting's scheduled duration, in minutes
        $postZoomMeeting['agenda'] = "demo";
        $postZoomMeeting['contact_name'] = "chirag patel";
        $postZoomMeeting['contact_email'] = "chiragpatel5081@gmail.com";
        $postZoomMeeting['invitees'] = ["radheshyam.amcodr@gmail.com"];
        $postZoomMeeting['schedule_for'] = "radheshyam.amcodr@gmail.com";
        //$meetingData = $this->createZoomMeeting($postZoomMeeting);
        $meetingData = $this->updateZoomMeeting(74717541046,$postZoomMeeting);
        $meetingData = $this->getZoomMeeting(74717541046);
        pr($meetingData);*/

        $loginUser = Auth::user();
        if ($loginUser->role_type == '3') {
            return view('customer.appointment_form', compact('model', 'productBrand', 'allBrands', 'allProductType', 'allProductCategory', 'allProductConnections', 'allProductExecutions'));
        } else {
            abort('404');
        }
    }
    public function addupdate(Request $request)
    {
        if ($request->ajax()) {
            $loginUser = Auth::User();
            $rules = array(
                'sproduct_id' => 'required',
                'dealer_id' => 'required',
                'appo_date' => 'required',
                'appo_time' => 'required',
                'note' => 'nullable|string|max:1000'
            );
            $messsages = array();

            $validator = Validator::make($request->all(), $rules, $messsages);
            if ($validator->fails()) {
                $result = ['status' => false, 'error' => $validator->errors()];
            } else {
                $appo_date = date('Y-m-d');
                if($request->appo_date){
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $request->appo_date)->format('Y-m-d');
                    $appo_date = date('Y-m-d', strtotime($formattedDate));
                }
                $appo_time = ($request->appo_time) ? date('H:i', strtotime($request->appo_time)) : date('H:i');

                $now_date = new DateTime();
                $due_date = $appo_date . " " . $appo_time;
                $due_date = new DateTime($due_date);

                if ($now_date < $due_date) {
                    $appo_time_main = strtotime($appo_time);
                    $startTime = date("H:i", strtotime('-59 minutes', $appo_time_main));
                    $endTime = date("H:i", strtotime('+59 minutes', $appo_time_main));

                    $condition = [];
                    $condition[] = ['dealer_id', '=', $request->dealer_id];
                    $condition[] = [DB::raw('DATE(appo_date)'), '=', $appo_date];

                    $findAppointment = AppointmentDealer::where($condition)
                        ->where(function ($query) use ($startTime, $endTime, $appo_date) {
                            $query->orwhere(function ($q) use ($startTime, $endTime, $appo_date) {
                                $q->whereTime('appo_time', '>=', \Carbon\Carbon::parse($startTime))
                                    ->whereTime('appo_time', '<=', \Carbon\Carbon::parse($endTime))
                                    ->where('appo_date', '=', $appo_date)
                                    ->whereIn('status', [1, 2]);
                            });
                            $query->orwhere(function ($q) use ($startTime, $endTime, $appo_date) {
                                $q->whereTime('reschedule_appo_time', '>=', \Carbon\Carbon::parse($startTime))
                                    ->whereTime('reschedule_appo_time', '<=', \Carbon\Carbon::parse($endTime))
                                    ->where('reschedule_appo_date', '=', $appo_date)
                                    ->whereIn('status', [6, 7]);
                            });
                        })
                        ->first();

                    if (empty($findAppointment)) {

                        $products = Product::query()
                            ->select(
                                DB::raw('CONCAT(brand.brand_name, " ", product.product_name) as product_name')
                            )
                            ->join('brand', 'brand.id', '=', 'product.brand_id')
                            ->whereIn('product.id', $request->sproduct_id)
                            ->groupBy('product.id')
                            ->get()
                            ->pluck('product_name')
                            ->toArray();

                        $appointment = new AppointmentDealer;
                        $appointment->customer_id = $loginUser->id;
                        $appointment->dealer_id = $request->dealer_id;
                        $appointment->brand_id = ($request->brand_id) ? implode(",", $request->brand_id) : NULL;
                        $appointment->product_id = ($request->sproduct_id) ? implode(",", $request->sproduct_id) : NULL;
                        $appointment->title = (isset($request->title) && !empty($request->title)) ? $request->title : null;
                        $appointment->note = nl2br($request->note);
                        $appointment->appo_type = ($request->appo_type)? true:false;
                        $appointment->appo_date = $appo_date;
                        $appointment->appo_time = \Carbon\Carbon::parse($appo_time);
                        $appointment->updated_at = Carbon::now();

                        if ($appointment->save()) {
                            $userSchema = User::find($appointment->dealer_id);
                            $meetingData = "";
                            $mailType = "Hörtermin";
                            $mailSubjectType = "Hörtermin";

                            if($appointment->appo_type==1){
                                $mailSubjectType = "Videoberatung";
                                $mailType = "Videoberatungs";
                                $postZoomMeeting=[];
                                $postZoomMeeting['topic'] = $loginUser->first_name." ".$loginUser->last_name." is inviting you to a scheduled Zoom meeting.";
                                $postZoomMeeting['start_time'] = date('Y-m-d H:i:s',strtotime($appo_date." ".$appo_time));
                                $postZoomMeeting['duration'] = 30;//The meeting's scheduled duration, in minutes
                                $postZoomMeeting['agenda'] = ($request->note)? $request->note:"";
                                $meetingData = $this->createZoomMeeting($postZoomMeeting);
                            }
                            if(isset($meetingData['data']['id']) && $meetingData['data']['id']){
                                $appointment->zoom_met_join_url = $meetingData['data']['join_url'];
                                $appointment->zoom_meeting_json = json_encode($meetingData);
                                $appointment->save();
                            }

                            $mailBody  = "";
                            $mailBody .= trans('translation.appocreate_email_body', ['date' => date('d.m.Y', strtotime($appo_date)), 'time' => date('H:m', strtotime($appo_time)), 'url' => '<a href="' . route('dealer.appointment') . '?oid=' . $appointment->id . '">' . route('dealer.appointment') . '</a>', 'mailtype' => $mailType]);

                            if($appointment->zoom_met_join_url){
                                $join_now  = '<a href="' .$appointment->zoom_met_join_url. '">' . $appointment->zoom_met_join_url . '</a>';
                                $mailBody .= "<br /><br />";
                                $mailBody .= trans('translation.appocreate_email_zoom_met_join_url', ['zoom_met_join_url' =>$join_now]);
                            }

                            $dataNotify = [
                                'name' => $userSchema->company_name,
                                'mailGretting' => trans('translation.Hello'),
                                'mailSubject' => trans('translation.appocreate_email_subject', ['product' => (($products) ? implode(",", $products) : ''), 'mailtype' => $mailSubjectType]),
                                'mailBody' => $mailBody,
                                'title' => trans('translation.appoinment_created_title'),
                                'type' => 'appointment',
                                'status' => 'created',
                                'body' => trans('translation.appoinment_created_body', ['name' => trans('translation.Customer')]),
                                'senderId' => $loginUser->id,
                                'url' => route('dealer.appointment'),
                                'dealer_id' => $userSchema->id,
                                'id' => $appointment->id,
                            ];
                            Notification::send($userSchema, new AppointmentNotification($dataNotify));
                            $result = ['status' => true, 'message' => trans('translation.Appointment saving successfully.'), 'data' => []];
                        } else {
                            $result = ['status' => false, 'message' => trans('translation.Something went wrong please try again'), 'data' => []];
                        }
                    } else {
                        $result = ['status' => false, 'message' => trans('translation.Appointment already taken please choose another time'), 'data' => []];
                    }
                } else {
                    $result = ['status' => false, 'message' => trans('translation.Please choose a valid date and time'), 'data' => []];
                }
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Invalid request'), 'data' => []];
        }
        return response()->json($result);
    }
    public function getCustomerList(Request $request)
    {
        $appointment_id = (isset($request->appointment_id) && $request->appointment_id != "") ? $request->appointment_id : "";
        $allData = $this->appointmentDealerData($appointment_id);
        if (isset($appointment_id) && !empty($appointment_id)) {
            $events = CalendarEvent::where('id', $appointment_id)->get();
        } else {
            $events = CalendarEvent::all();
        }
        if (!empty($allData)) {
            if ($appointment_id) {
                switch ($allData[0]['status']) {
                    case 2:
                        $status = 'confirmed';
                        break;

                    case 3:
                        $status = 'completed';
                        break;

                    case 4:
                        $status = 'cancel';
                        break;

                    case 5:
                        $status = 'cancelDelaer';
                        break;

                    case 6:
                        $status = 'reschedule';
                        break;

                    case 7:
                        $status = 'rescheduleConfirmed';
                        break;

                    case 8:
                        $status = 'expired';
                        break;

                    default:
                        $status = 'created';
                        break;
                }
                notificationMarkAsRead('appointment', $appointment_id, $status);
                $result = ['status' => true, 'allData' => $allData[0], 'events' => $events];
            } else {
                $result = ['status' => true, 'allData' => $allData, 'events' => $events];
            }
        } else {
            $result = ['status' => false, 'allData' => [], 'events' => $events];
        }
        return response()->json($result);
    }
    public function appointmentDealerData($appointment_id = "")
    {
        $loginUser = Auth::User();
        $statusAppo = AppointmentDealer::$status;
        $appoType = AppointmentDealer::$appoType;

        $condition = [];
        $condition[] = ['appointment_dealer.deleted_at', '=', NULL];
        if ($loginUser->role_type == 2) {
            $condition[] = ['appointment_dealer.dealer_id', '=', $loginUser->id];
        }
        if ($loginUser->role_type == 3) {
            $condition[] = ['appointment_dealer.customer_id', '=', $loginUser->id];
        }
        if (isset($appointment_id) && $appointment_id) {
            $condition[] = ['appointment_dealer.id', '=', $appointment_id];
        }
        $data = AppointmentDealer::where($condition)
            ->leftjoin("product", DB::raw("FIND_IN_SET(product.id,appointment_dealer.product_id)"), ">", DB::raw("'0'"))
            ->join('brand', 'brand.id', '=', 'appointment_dealer.brand_id')
            ->join('users as dealer', 'appointment_dealer.dealer_id', '=', 'dealer.id')
            ->join('users as customer', 'appointment_dealer.customer_id', '=', 'customer.id')
            ->select(
                'appointment_dealer.*',
                DB::raw("GROUP_CONCAT(product.product_name SEPARATOR ', ') as product_name"),
                DB::raw("GROUP_CONCAT(DISTINCT brand.brand_name SEPARATOR ', ') as brand_name"),
                DB::raw('dealer.company_name as dealer_name'),
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
            )
            ->groupBy("appointment_dealer.id")
            ->get();
        $allData = [];
        if ($data->toArray()) {
            foreach ($data as $key => $row) {

                $now_date_time = new DateTime();
                $now_date = date("Y-m-d");
                $now_date_time_check = date('Y-m-d H:i');

                $appo_date = date("Y-m-d", strtotime($row->appo_date));
                $appo_time = date("H:i", strtotime($row->appo_time));
                $date_time = date("Y-m-d H:i", strtotime($appo_date . " " . $appo_time));

                $reschedule_appo_date = (isset($row->reschedule_appo_date) && !empty($row->reschedule_appo_date)) ? date("Y-m-d", strtotime($row->reschedule_appo_date)) : '';

                $reschedule_appo_time = (isset($row->reschedule_appo_time) && !empty($row->reschedule_appo_time)) ? date("H:i", strtotime($row->reschedule_appo_time)) : '';

                $reschedule_date_time = ((isset($row->reschedule_appo_date) && !empty($row->reschedule_appo_date)) && isset($row->reschedule_appo_time) && !empty($row->reschedule_appo_time)) ? date("Y-m-d H:i", strtotime($reschedule_appo_date . " " . $reschedule_appo_time)) : '';

                $endTime = date("H:i", strtotime('-30 minutes', strtotime($appo_time)));
                $cancel_last_time = date("Y-m-d H:i", strtotime($appo_date . " " . $endTime));
                $cancel_last_time = new DateTime($cancel_last_time);

                $title_small = (strlen($row->customer_name) >= 10) ? substr($row->customer_name, 0, 10) . '...' : $row->customer_name;

                $tmp = [];

                if ((isset($reschedule_date_time) && !empty($reschedule_date_time) && $now_date_time_check > $reschedule_date_time)) {
                    $tmp["appo_expired"] = true;
                    $tmp['status_name'] = (isset($statusAppo[$row->status])) ? $statusAppo[8] : "";
                } else {
                    if (isset($reschedule_date_time) && empty($reschedule_date_time) && (isset($date_time) && !empty($date_time) && $now_date_time_check > $date_time)) {
                        $tmp["appo_expired"] = true;
                        $tmp['status_name'] = (isset($statusAppo[$row->status])) ? $statusAppo[8] : "";
                    } else {
                        $tmp["appo_expired"] = false;
                        $tmp['status_name'] = (isset($statusAppo[$row->status])) ? $statusAppo[$row->status] : "";
                    }
                }

                $tmp['appo_type_name'] = ($row->appo_type==1) ? trans('translation.AppoTypeZoomMeeting') : trans('translation.AppoTypeAppointment');
                $tmp['zoom_met_join_url'] = (isset($row->zoom_met_join_url)) ? $row->zoom_met_join_url : "";

                $tmp["appointment_id"] = $row->id;
                $tmp["title"] = (isset($row->title) && !empty($row->title) ? $row->title : '');
                $tmp["title_small"] = $title_small;
                $tmp["note"] = $row->note;
                $tmp["appo_date"] = date("d.m.Y", strtotime($appo_date));
                $tmp["appo_time"] = $appo_time;
                $tmp["date_time"] = $date_time;

                $tmp["reschedule_appo_date"] = (isset($reschedule_appo_date) && !empty($reschedule_appo_date)) ? date("d.m.Y", strtotime($reschedule_appo_date)) : '';
                $tmp["reschedule_appo_time"] = (isset($reschedule_appo_time) && !empty($reschedule_appo_time)) ? $reschedule_appo_time : '';
                $tmp["reschedule_date_time"] = (isset($reschedule_date_time) && !empty($reschedule_date_time)) ? $reschedule_date_time : '';

                if (isset($reschedule_appo_date) && !empty($reschedule_appo_date) && ($row->status == 6 || $row->status == 7)) {
                    $tmp["appo_date_actual"] = date("d.m.Y", strtotime($reschedule_appo_date));
                    $tmp["appo_time_actual"] = $reschedule_appo_time;
                    $tmp["date_time_actual"] = $reschedule_date_time;
                } else {
                    $tmp["appo_date_actual"] = date("d.m.Y", strtotime($appo_date));
                    $tmp["appo_time_actual"] = $appo_time;
                    $tmp["date_time_actual"] = $date_time;
                }


                $tmp["status"] = $row->status;
                $tmp["rating"] = $row->rating;

                $tmp['is_cancel_app'] = ($now_date_time < $cancel_last_time) ? true : false;

                $tmp["product_name"] = $row->product_name;
                $tmp["brand_name"] = $row->brand_name;

                $tmp["dealer_name"] = $row->dealer_name;
                $tmp["dealer_email"] = $row->dealer_email;
                $tmp["dealer_phone"] = $row->dealer_phone;
                $tmp["dealer_shop_address"] = $row->dealer_shop_address;
                $tmp["company_name"] = $row->company_name;
                $tmp["shop_start_time"] = ($row->shop_start_time) ? date('H:i', strtotime($row->shop_start_time)) : "";
                $tmp["shop_end_time"] = ($row->shop_end_time) ? date('H:i', strtotime($row->shop_end_time)) : "";
                $tmp["shop_time"] = ($tmp["shop_start_time"] && $tmp["shop_end_time"]) ? $tmp["shop_start_time"] . "-" . $tmp["shop_end_time"] : "";

                $tmp["customer_name"] = $row->customer_name;
                $tmp["customer_email"] = $row->customer_email;
                $tmp["customer_phone"] = $row->customer_phone;
                $tmp["customer_shop_address"] = $row->customer_shop_address;
                $tmp["login_user_role"] = $loginUser->role_type;

                if ($row->status == 1) {
                    $tmp['bg_type'] = "bg-primary";
                } elseif ($row->status == 2 || $row->status == 7) {
                    $tmp['bg_type'] = "bg-info";
                } elseif ($row->status == 3) {
                    $tmp['bg_type'] = "bg-success";
                } elseif ($row->status == 6) {
                    $tmp['bg_type'] = "bg-warning";
                } else {
                    $tmp['bg_type'] = "bg-danger";
                }
                $allData[] = $tmp;
            }
        }
        return $allData;
    }
    public function updateStatus(Request $request)
    {
        $appointment_id = (isset($request->appointment_id) && $request->appointment_id != "") ? $request->appointment_id : 0;
        $status_from = (isset($request->status) && $request->status != "") ? $request->status : "";

        $loginUser = Auth::User();
        $statusAppo = AppointmentDealer::$status;

        $condition = [];
        $condition[] = ['id', '=', $appointment_id];
        if ($loginUser->role_type == 2) {
            $condition[] = ['dealer_id', '=', $loginUser->id];
        }
        if ($loginUser->role_type == 3) {
            $condition[] = ['customer_id', '=', $loginUser->id];
        }
        $condition = [];
        $condition[] = ['id', '=', $appointment_id];
        $findAppointment = AppointmentDealer::where($condition)->first();

        if (isset($findAppointment->id) && $status_from) {
            $date_time_today = Carbon::now()->format('Y-m-d H:i:s');
            if (!empty($findAppointment->reschedule_appo_date) && ($findAppointment->status == 6 || $findAppointment->status == 7)) {
                $appo_date = date("Y-m-d", strtotime($findAppointment->reschedule_appo_date));
                $appo_time = date("H:i:s", strtotime($findAppointment->reschedule_appo_time));
                $date_time = date("Y-m-d H:i:s", strtotime($appo_date . " " . $appo_time));
            } else {
                $appo_date = date("Y-m-d", strtotime($findAppointment->appo_date));
                $appo_time = date("H:i:s", strtotime($findAppointment->appo_time));
                $date_time = date("Y-m-d H:i:s", strtotime($appo_date . " " . $appo_time));
            }

            if ($date_time >= $date_time_today && (($status_from == 2 && $findAppointment->status == 1) ||
                ($status_from == 3 && ($findAppointment->status == 2 || $findAppointment->status == 7)) ||
                ($status_from == 4 && ($findAppointment->status == 1 || $findAppointment->status == 6 || $findAppointment->status == 2)) ||
                ($status_from == 5 && $findAppointment->status == 1) ||
                ($status_from == 6 && $findAppointment->status == 1) ||
                ($status_from == 7 && $findAppointment->status == 6))) {
                $findAppointment->status = $status_from;

                $products = Product::query()
                    ->select(
                        DB::raw('CONCAT(brand.brand_name, " ", product.product_name) as product_name')
                    )
                    ->join('brand', 'brand.id', '=', 'product.brand_id')
                    ->whereIn('product.id', explode(', ', $findAppointment->product_id))
                    ->groupBy('product.id')
                    ->get()
                    ->pluck('product_name')
                    ->toArray();

                if ($findAppointment->save()) {
                    if ($findAppointment->appo_type == 1) {
                        $mailsubjectType = "Videoberatung";
                        $mailbodytype = "Videoberatungs-Termin";
                    } else {
                        $mailsubjectType = "Hörtermin";
                        $mailbodytype = "Hörtermin";
                    }
                    switch ($status_from) {
                        case 2:
                            $title = trans('translation.appoinment_confirmed_title');
                            $status = 'confirmed';
                            $mailGretting = trans('translation.Hello');
                            $mailSubject = trans('translation.appoconfirm_email_subject', ['product' => (($products) ? implode(",", $products) : ''), 'url' => '<a href="' . route('customer.appointment') . '?oid=' . $findAppointment->id . '">' . route('customer.appointment') . '</a>', 'mailtype' => $mailsubjectType]);
                            $mailBody = trans('translation.appoconfirm_email_body', ['date' => date('d.m.Y', strtotime($appo_date)), 'time' => date('H:m', strtotime($appo_time)),'url' => '<a href="' . route('customer.appointment') . '?id=' . $findAppointment->id . '">' . route('customer.appointment') . '</a>', 'mailtype' => $mailbodytype]);
                            $body = trans('translation.appoinment_confirmed_body', ['name' => trans('translation.Dealer')]);
                            break;

                        case 3:
                            $title = trans('translation.appoinment_completed_title');
                            $status = 'completed';
                            $body = trans('translation.appoinment_completed_body', ['name' => trans('translation.Dealer')]);
                            break;

                        case 4:
                            $title = trans('translation.appoinment_cancel_title');
                            $status = 'cancel';
                            $mailGretting = trans('translation.Hello');
                            $mailSubject = trans('translation.appoCustomerCancel_email_subject', ['product' => (($products) ? implode(",", $products) : ''), 'mailtype' => $mailsubjectType]);
                            $mailBody = trans('translation.appoCustomerCancel_email_body', ['mailtype' => $mailbodytype]);
                            $body = trans('translation.appoinment_cancel_body', ['name' => trans('translation.Customer')]);
                            break;

                        case 5:
                            $title = trans('translation.appoinment_cancel_title');
                            $status = 'cancelDelaer';
                            $body = trans('translation.appoinment_cancel_body', ['name' => trans('translation.Dealer')]);
                            break;

                        case 6:
                            $title = trans('translation.appoinment_reschedule_title');
                            $status = 'reschedule';
                            $body = trans('translation.appoinment_reschedule_body', ['name' => trans('translation.Dealer')]);
                            break;

                        case 7:
                            $title = trans('translation.appoinment_confirmedreschedule_title');
                            $status = 'rescheduleConfirmed';
                            $mailGretting = trans('translation.Hello');
                            $mailSubject = trans('translation.appoRescheduleAccept_email_subject', ['product' => (($products) ? implode(",", $products) : '')]);
                            $mailBody = trans('translation.appoRescheduleAccept_email_body', ['date' => date('d.m.Y', strtotime($appo_date)), 'time' => date('H:m', strtotime($appo_time))]);
                            $body = trans('translation.appoinment_confirmedreschedule_body', ['name' => trans('translation.Customer')]);
                            break;

                        case 8:
                            $title = trans('translation.appoinment_expired_title');
                            $status = 'expired';
                            $body = trans('translation.appoinment_created_body', ['name' => trans('translation.Customer')]);
                            break;

                        default:
                            $title = trans('translation.appoinment_created_title');
                            $status = 'created';
                            $body = trans('translation.appoinment_expired_body', ['name' => trans('translation.Customer')]);
                            break;
                    }
                    if ($loginUser->role_type == 3) {
                        $userSchema = User::find($findAppointment->dealer_id);
                        $name = $userSchema->company_name;
                    } else {
                        $userSchema = User::find($findAppointment->customer_id);
                        $name = $userSchema->first_name . ' ' . $userSchema->last_name;
                    }
                    $dataNotify = [
                        'name' => $name,
                        'title' => $title,
                        'mailGretting' => (isset($mailGretting) && !empty($mailGretting)) ? $mailGretting : '',
                        'mailSubject' => (isset($mailSubject) && !empty($mailSubject)) ? $mailSubject : '',
                        'mailBody' => (isset($mailBody) && !empty($mailBody)) ? $mailBody : '',
                        'type' => 'appointment',
                        'status' => $status,
                        'body' => $body,
                        'senderId' => $loginUser->id,
                        'url' => route('customer.appointment'),
                        'dealer_id' => $userSchema->id,
                        'id' => $findAppointment->id
                    ];
                    Notification::send($userSchema, new AppointmentNotification($dataNotify));
                    $status_name = (isset($statusAppo[$findAppointment->status])) ? $statusAppo[$findAppointment->status] : "Status Update";
                    $result = ['status' => true, 'message' => 'Appointment ' . $status_name . ' successfully.'];
                } else {
                    $result = ['status' => false, 'message' => 'Error in saving data'];
                }
            } else {
                $status_name = (isset($statusAppo[$status_from])) ? $statusAppo[$status_from] : "Status Update";
                $result = ['status' => false, 'message' => 'You can not ' . strtolower($status_name) . ' appointment.'];
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request'];
        }
        return response()->json($result);
    }
    public function updateRating(Request $request)
    {
        $appointment_id = (isset($request->appointment_id) && $request->appointment_id != "") ? $request->appointment_id : 0;
        $rating = (isset($request->rating) && $request->rating) ? $request->rating : 0;

        $loginUser = Auth::User();

        $condition = [];
        $condition[] = ['id', '=', $appointment_id];
        if ($loginUser->role_type == 2) {
            $condition[] = ['dealer_id', '=', $loginUser->id];
        }
        if ($loginUser->role_type == 3) {
            $condition[] = ['customer_id', '=', $loginUser->id];
        }
        $findAppointment = AppointmentDealer::where($condition)->first();
        if (isset($findAppointment->id)) {
            $findAppointment->rating = $rating;
            if ($findAppointment->save()) {
                $result = ['status' => true, 'message' => 'Thank you for your valuable feedback.'];
            } else {
                $result = ['status' => false, 'message' => 'Error in saving data'];
            }
        } else {
            $result = ['status' => false, 'message' => 'Invalid request'];
        }
        return response()->json($result);
    }

    public function eventDelete(Request $request)
    {
        if (isset($request->id) && !empty($request->id)) {
            $event = CalendarEvent::where('id', $request->id);
            if ($event->delete()) {
                $result = ['status' => true, 'message' => trans('translation.Delete successfully')];
            } else {
                $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
            }
        } else {
            $result = ['status' => false, 'message' => trans('translation.Something went wrong')];
        }
        return response()->json($result);
    }

    public function getTimePickerData(Request $request){
        $get_time_slot_gap = config('services.timeslots.defaulgap');
        if (isset($request->selectedDate) && !empty($request->selectedDate)) {
            $date = Carbon::createFromFormat('d/m/Y', $request->selectedDate);
            $selecteddate = $date->format('Y-m-d');
        } else {
            $selecteddate = date('Y-m-d');
        }

        $slots = $this->getTimeSlot($get_time_slot_gap, $selecteddate);

        if (isset($slots) && !empty($slots)) {
            return response()->json(['status' => '200', 'message' => 'Get available timeslot', 'timeslot' => $slots], 200);
        } else {
            return response()->json(['status' => '400', 'message' => 'Not found available timeslot', 'timeslot' => $slots], 200);
        }
        $interval = $get_time_slot_gap;
    }
    public function getTimeSlot($interval, $selecteddate = '', $appointments_edit = '')
    {
        $booked_timeslot = [];
        $appointments = AppointmentDealer::query()
            ->where('appo_date', date("Y-m-d", strtotime($selecteddate)))
            ->when($appointments_edit != '', function($query) use ($appointments_edit){
                $query->where('id', '!=', $appointments_edit);
            })
            ->get();
        foreach ($appointments as $appointment) {
            $startDatetime = new DateTime($appointment->appo_date . $appointment->appo_time);
            $startTime = $startDatetime->format('Y-m-d H:i');

            $booked_timeslot[] = date('Y-m-d H:i', strtotime($startTime));
            $booked_timeslot[] = date('Y-m-d H:i', strtotime('+' . $interval . ' minutes', strtotime($startTime)));
        }

        if (isset($selecteddate) && !empty($selecteddate)) {
            $startDatetime = new DateTime($selecteddate . ' 09:00');
            $endDatetime = new DateTime($selecteddate . ' 18:00');
        } else {
            $startDatetime = new DateTime(date('Y-m-d') . ' 09:00');
            $endDatetime = new DateTime(date('Y-m-d') . ' 18:00');
        }

        $startTime = $startDatetime->format('Y-m-d H:i');
        $endTime = $endDatetime->format('Y-m-d H:i');


        // if (date('Y-m-d H:i') > $startTime) {
        //     $startTime = date('Y-m-d H:i', $this->roundToNearestInterval(strtotime(date('H:i'))));
        // }
        $timeslot = [];
        while (strtotime($startTime) <= strtotime($endTime)) {
            $start = $startTime;
            $startTime = date('Y-m-d H:i', strtotime('+' . $interval . ' minutes', strtotime($startTime)));
            if (strtotime($startTime) <= strtotime($endTime)) {
                // if(!in_array($start, $booked_timeslot)){
                    $timeslot[] = date('H:i', strtotime($start));
                // }
            }
        }
        return $timeslot;
    }

    public function roundToNearestInterval($timestamp)
    {
        list($m, $d, $y, $h, $i, $s) = explode(' ', date('m d Y H i s', $timestamp));
        if ($s != 0) $s = 0;

        if ($i < 15) {
            $i = 15;
        } else if ($i < 30) {
            $i = 30;
        } else if ($i < 45) {
            $i = 45;
        } else if ($i < 60) {
            $i = 0;
            $h++;
        }

        return mktime($h, $i, $s, $m, $d, $y);
    }

}
