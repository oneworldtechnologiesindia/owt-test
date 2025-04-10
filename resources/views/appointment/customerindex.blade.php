@extends('layouts.master')

@section('title')
    @lang('translation.Appointment')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('customer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Appointment')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All_Appointments')</h4>
                        <a href="{{ route('customer.appointment.create') }}"
                            class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                                class="bx bx-plus label-icon"></i> @lang('translation.Add_New')</a>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('translation.Dealer')</th>
                                    <th>@lang('translation.Product')</th>
                                    <th>@lang('translation.Date')</th>
                                    <th>@lang('translation.Time')</th>
                                    <th>@lang('translation.Status')</th>
                                    <th>@lang('translation.Actions')</th>
                                    <th>@lang('translation.Updated At')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <!-- Page Models -->
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
                                        <th width="20%">@lang('translation.Company'):</th>
                                        <td width="30%">
                                            <p class="company_name"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Email'):</th>
                                        <td width="30%">
                                            <p class="dealer_email"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">@lang('translation.Phone'):</th>
                                        <td width="30%">
                                            <p class="dealer_phone"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Address_Shop'):</th>
                                        <td width="30%">
                                            <p class="dealer_shop_address p-box"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">@lang('translation.Shop_Time'):</th>
                                        <td width="30%">
                                            <p class="shop_time"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Date'):</th>
                                        <td width="30%">
                                            <p class="appo_date"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">@lang('translation.Time'):</th>
                                        <td width="30%">
                                            <p class="appo_time"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Brand'):</th>
                                        <td width="30%">
                                            <p class="brand_name"></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">@lang('translation.Product'):</th>
                                        <td width="30%">
                                            <p class="product_name p-box"></p>
                                        </td>

                                        <th width="20%">@lang('translation.Status'):</th>
                                        <td width="30%">
                                            <p class="status_name"></p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th width="20%">@lang('translation.appo_type'):</th>
                                        <td width="30%">
                                            <p class="appo_type_name"></p>
                                        </td>

                                        <th width="20%"><p class="zoom_met_join_url_link">@lang('translation.zoom_met_join_url'):</p></th>
                                        <td width="30%">
                                            <p class="zoom_met_join_url_link"><a href="#" target="_blank" id="zoom_met_join_url_view"></a> </p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th width="20%">@lang('translation.Note'):</th>
                                        <td width="80%" colspan="3">
                                            <p class="note p-box"></p>
                                        </td>
                                    </tr>
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
                        <button type="submit"
                            class="btn btn-success waves-effect waves-light reschedule_appointment_confirm">@lang('translation.Reschedule_Confirm')</button>
                        <button type="submit"
                            class="btn btn-danger waves-effect waves-light status_cancel">@lang('translation.Cancel')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <!-- Bootstrap rating js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-rating/bootstrap-rating.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/pages/rating-init.js') }}"></script>

    <script>
        var apiUrl = "{{ route('customer.appointment.list') }}";
        var getCustomerListUrl = "{{ route('customer.appointment.getCustomerList') }}";
        var updateStatusUrl = "{{ route('customer.appointment.updateStatus') }}";
        var rescheduleAppointmentUrl = "{{ route('customer.appointment.rescheduleAppointment') }}";
        var updateRatingUrl = "{{ route('customer.appointment.updateRating') }}";
        var createUrl = "{{ route('customer.appointment.create') }}";
        var main_calendar = "";
        var notifyAppoId = "{{ isset($appo_id) && !empty($appo_id) ? $appo_id : '' }}";
        var addRatingUrl = "{{ route('customer.purchases.addRatingUrl') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('customerappointment.js') }}"></script>
@endsection
