@extends('layouts.master')

@section('title')
    @lang('translation.Calendar')
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!--Main css-->
    <link href="{{ URL::asset('/assets/css/main.css') }}" rel="stylesheet" type="text/css" />
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
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Appointment_Details')</span></h5>
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
@endsection

@section('script')
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Bootstrap rating js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-rating/bootstrap-rating.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/pages/rating-init.js') }}"></script>
    
    <script>
        var getCustomerListUrl = "{{ route('appointment.getCustomerList') }}";
        var updateStatusUrl = "{{ route('appointment.updateStatus') }}";
        var main_calendar = "";
        var calendarRightButton = "today myCustomButton";
        var apiUrl = "{{ route('getDealerEnquiry') }}";
        var detailUrl = "{{ route('getDetailEnquiry') }}";
        var createUrl = "{{ route('appointment.create') }}";

    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('customer_dashboard.js') }}"></script>
    <!-- plugin js -->
    <script src="{{ URL::asset('/assets/js/main.js') }}"></script>
    <script src="{{ addPageJsLink('calendars-full.init.js') }}"></script>
@endsection
