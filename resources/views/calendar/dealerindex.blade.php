@extends('layouts.master')

@section('title')
    @lang('translation.Calendar')
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!--Main css-->
    <link href="{{ URL::asset('/assets/css/main.css') }}" rel="stylesheet" type="text/css" />
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">
    <!-- Timepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"
        type="text/css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.Dashboard')
        @endslot
        @slot('title')
            @lang('translation.Calendar')
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Appointment_Details')</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row customer_view">
                        <div class="col-md-12">
                            <table class="table table-striped w-100">
                                <tbody>
                                    <tr>
                                        <th width="20%">@lang('translation.Customer'):</th>
                                        <td width="30%">
                                            <p class="customer_name"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Email'):</th>
                                        <td width="30%">
                                            <p class="customer_email"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">@lang('translation.Address_Shop'):</th>
                                        <td width="30%">
                                            <p class="customer_shop_address p-box"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Phone'):</th>
                                        <td width="30%">
                                            <p class="customer_phone"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">@lang('translation.Date'):</th>
                                        <td width="30%">
                                            <p class="appo_date"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Time'):</th>
                                        <td width="30%">
                                            <p class="appo_time"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">@lang('translation.Brand'):</th>
                                        <td width="30%">
                                            <p class="brand_name"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Product'):</th>
                                        <td width="30%">
                                            <p class="product_name p-box"></p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th width="20%">@lang('translation.Note'):</th>
                                        <td width="80%" colspan="3">
                                            <p class="note p-box"></p>
                                        </td>
                                    </tr>

                                    {{-- <tr class="rating_stars_div">
                                        <th width="20%">@lang('translation.Rating'):</th>
                                        <td width="30%">
                                            <div class='rating-stars text-center'>
                                                <ul id='stars'>
                                                    <li class='star' title='Poor' data-value='1'>
                                                        <i class='fa fa-star m-0'></i>
                                                    </li>
                                                    <li class='star' title='Fair' data-value='2'>
                                                        <i class='fa fa-star m-0'></i>
                                                    </li>
                                                    <li class='star' title='Good' data-value='3'>
                                                        <i class='fa fa-star m-0'></i>
                                                    </li>
                                                    <li class='star' title='Excellent' data-value='4'>
                                                        <i class='fa fa-star m-0'></i>
                                                    </li>
                                                    <li class='star' title='WOW!!!' data-value='5'>
                                                        <i class='fa fa-star m-0'></i>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                        <th width="20%">@lang('translation.Status'):</th>
                                        <td width="30%">
                                            <p class="status_name"></p>
                                        </td>
                                    </tr> --}}
                                    <tr class="rating_stars_div">
                                        <th width="20%">@lang('translation.Rating'):</th>
                                        <td width="80%" colspan="3">
                                           <div class="row">
                                                <div class="col-xl-4 col-md-4 col-sm-6">
                                                    <div class="rating-star">
                                                        <input type="hidden" class="rating" data-filled="mdi mdi-star text-primary" data-empty="mdi mdi-star-outline text-muted" name="appointment_rating" data-fractions="2"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 modal-footer">
                        <input type="hidden" name="appointment_id" id="appointment_id" class="appointment_id"
                            value="0">
                        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                            aria-label="Close">@lang('translation.Close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Event MODAL -->
    <div class="modal fade" id="event-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-3 px-4">
                    <h5 class="modal-title" id="modal-title">@lang('translation.Event')</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>

                </div>
                <div class="modal-body">
                    <form id="eventadd_form" method="post" class="form-horizontal" enctype="multipart/form-data">
                        <input type="hidden" name="event_id" id="event_id" value="">
                        <div class="mb-3">
                            <label for="title" class="form-label">@lang('translation.Event_Name')</label>
                            <input class="form-control title" placeholder="@lang('translation.Enter_Event_Name')" type="text"
                                name="title" id="title">
                            <span class="invalid-feedback error-title" role="alert"></span>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="control-label">@lang('translation.Description')</label>
                            <textarea id="description" class="form-control description" name="description" placeholder="@lang('translation.Enter_description')"
                                rows="5"></textarea>
                            <span class="invalid-feedback error-description" role="alert"></span>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="event_date" class="control-label">@lang('translation.Date')</label>
                                    <div class="input-group" id="event_date_container">
                                        <input type="text" id="event_date" name="event_date" class="form-control"
                                            placeholder="@lang('translation.Enter_Date')" data-date-format="dd.mm.yyyy"
                                            data-date-container='#event_date_container' data-provide="datepicker"
                                            data-date-autoclose="true" data-date-start-date="today">

                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                        <span class="invalid-feedback error-event_date" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="event_time" class="control-label">@lang('translation.Time')</label>
                                    <div class="input-group" id="event_time_container">
                                        <input id="event_time" type="text" class="form-control event_time"
                                            name="event_time" placeholder="@lang('translation.Enter_Time')">
                                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                        <span class="invalid-feedback error-event_time" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (isset($event_categories) && !empty($event_categories))
                            <div class="mb-3">
                                <label class="form-label">@lang('translation.Category')</label>
                                <select class="form-control form-select category" name="category" id="category">
                                    <option value=''>@lang('translation.Select_Category')</option>
                                    @foreach ($event_categories as $event_categoryk => $event_categoryv)
                                        <option value="{{ $event_categoryk }}">{{ $event_categoryv }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback error-category" role="alert"></span>
                            </div>
                        @endif
                        <div class="mt-2 col-md-12 modal-footer">
                            <button type="button" class="btn btn-light me-1"
                                data-bs-dismiss="modal">@lang('translation.Close')</button>
                            <button type="button" class="btn btn-danger"
                                id="btn-delete-event">@lang('translation.Delete')</button>
                            <button type="submit" class="btn btn-success"
                                id="btn-save-event">@lang('translation.Save')</button>
                        </div>
                    </form>
                </div>
            </div> <!-- end modal-content-->
        </div> <!-- end modal dialog-->
    </div>
    <!-- end modal-->
@endsection

@section('script')
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Timepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- Bootstrap rating js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-rating/bootstrap-rating.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/pages/rating-init.js') }}"></script>

    <script>
        var getCustomerListUrl = "{{ route('dealer.appointment.getCustomerList') }}";
        var updateStatusUrl = "{{ route('dealer.appointment.updateStatus') }}";
        var main_calendar = "";
        var calendarRightButton = "today myEventButton";
        var apiUrl = "{{ route('dealer.getDealerEnquiry') }}";
        var detailUrl = "{{ route('dealer.getDetailEnquiry') }}";
        var eventDeleteUrl = "{{ route('dealer.calendar.eventDelete') }}";
        var eventAddupdateUrl = "{{ route('dealer.calendar.eventAddupdate') }}"
        var todayLang = "@lang('translation.Today')";
        var addNewEventLang = "@lang('translation.Add_New_Event')";
        var addAppointmentLang = "@lang('translation.Add_Appointment')";

    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealer_calendar.js') }}"></script>
    <!-- plugin js -->
    <script src="{{ URL::asset('/assets/js/main.js') }}"></script>
    <script src="{{ addPageJsLink('calendars-full.init.js') }}"></script>
@endsection
