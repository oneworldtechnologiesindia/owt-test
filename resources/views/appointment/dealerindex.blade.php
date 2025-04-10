@extends('layouts.master')

@section('title')
    @lang('translation.Appointment')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">
    <!-- Timepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"
        type="text/css">

    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">
                @lang('translation.Dashboard')</a>
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
                    </div>
                    @if (isset($loginUser) && isset($loginUser->role_type) && !empty($loginUser->role_type) && $loginUser->role_type == 1)
                        <div class="appointment-filter-form-container mb-5">
                            <form id="filter-appointment-form" method="post" class="form-horizontal"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    @if (isset($countries) && !empty($countries))
                                        <div
                                            class="{{ isset($dealers) && !empty($dealers) && count($dealers) > 0 ? 'col-md-6' : 'col-md-12' }}">
                                            <div class="mb-3">
                                                <label for="country" class="control-label">@lang('translation.Country')</label>
                                                <select id="country" type="text" class="form-select country"
                                                    name="country" placeholder="@lang('translation.Select_country')">
                                                    <option value=""></option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country }}">{{ $country }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($dealers) && !empty($dealers) && count($dealers) > 0)
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="dealer" class="control-label">@lang('translation.Dealer')</label>
                                                <select name="dealer" id="dealer" class="form-select"
                                                    placeholder="@lang('translation.Select_dealer')">
                                                    <option value=""></option>
                                                    @foreach ($dealers as $dealer)
                                                        <option value="{{ $dealer->id }}">{{ $dealer->company_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label>@lang('translation.Date_Range')</label>
                                    <div class="input-daterange input-group" id="filterdaterange"
                                        data-date-format="dd.mm.yyyy" data-date-autoclose="true" data-provide="datepicker"
                                        data-date-container='#filterdaterange'>
                                        <input type="text" class="form-control" name="start_date" id="start_date"
                                            placeholder="@lang('translation.Start_Date')" />
                                        <input type="text" class="form-control" name="end_date" id="end_date"
                                            placeholder="@lang('translation.End_Date')" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <a href="javascript:void(0)"
                                        class="btn btn-primary waves-effect waves-light reset-filter d-block text-capitalize">@lang('translation.Reset_filter')</a>
                                </div>
                            </form>
                        </div>
                    @endif
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('translation.Customer')</th>
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

                                   {{--  <tr class="rating_stars_div">
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
                        @if (isset($loginUser) && isset($loginUser->role_type) && !empty($loginUser->role_type) && $loginUser->role_type != 1)
                            <button type="submit"
                                class="btn btn-danger waves-effect waves-light status_cancel">@lang('translation.Cancel')</button>
                            <button type="submit"
                                class="btn btn-secondary waves-effect waves-light reschedule_appointment">@lang('translation.Reschedule')</button>
                            <button type="submit"
                                class="btn btn-primary waves-effect waves-light status_confirmed">@lang('translation.Confirmed')</button>
                            <button type="submit"
                                class="btn btn-success waves-effect waves-light status_complete">@lang('translation.Completed')</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (isset($loginUser) && isset($loginUser->role_type) && !empty($loginUser->role_type) && $loginUser->role_type != 1)
        <div id="reschedule-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myLargeModalLabel"><span
                                class="modal-lable-class">@lang('translation.Appointment_Reschedule')</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="add_form" method="post" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="appointment_id" id="appointment_id" class="appointment_id"
                                value="0">
                            <div class="mb-3">
                                <label for="appo_date" class="control-label">@lang('translation.Date')</label>
                                <div class="input-group" id="appo_date_container">
                                    <input type="text" id="appo_date" name="appo_date" class="form-control"
                                        placeholder="@lang('translation.Enter_Date')" data-date-format="dd.mm.yyyy"
                                        data-date-container='#appo_date_container' data-provide="datepicker"
                                        data-date-autoclose="true" data-date-start-date="today">

                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                    <span class="invalid-feedback error-appo_date" role="alert"></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="appo_time" class="control-label">@lang('translation.Time')</label>
                                <div class="input-group" id="appo_time_container">
                                    <input id="appo_time" type="text" class="form-control appo_time" name="appo_time"
                                        placeholder="@lang('translation.Enter_Time')">
                                    <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                    <span class="invalid-feedback error-appo_time" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-12 modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                    aria-label="Close">@lang('translation.Close')</button>
                                <button type="submit"
                                    class="btn btn-primary waves-effect waves-light">@lang('translation.Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Timepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <!-- Bootstrap rating js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-rating/bootstrap-rating.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/pages/rating-init.js') }}"></script>
    <script>
        var apiUrl = "{{ route('dealer.appointment.list') }}";
        var getCustomerListUrl = "{{ route('dealer.appointment.getCustomerList') }}";
        var updateStatusUrl = "{{ route('dealer.appointment.updateStatus') }}";
        var rescheduleAppointmentUrl = "{{ route('dealer.appointment.rescheduleAppointment') }}";
        var countryDealerFilterUrl = "{{ route('dealer.countryDealerFilter') }}";
        var notifyAppoId = "{{ isset($appo_id) && !empty($appo_id) ? $appo_id : '' }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealerappointment.js') }}"></script>
@endsection
