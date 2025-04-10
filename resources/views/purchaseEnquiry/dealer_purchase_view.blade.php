@extends('layouts.master')

@section('title')
    @lang('translation.Enquiry')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('li_2')
            <a href="{{ route('dealer.dealer-enquiry') }}">@lang('translation.Enquiry')</a>
        @endslot
        @slot('title')
            @lang('translation.View')
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <input type="hidden" name="customer_enquiry_id" value="{{ $id }}" id="customer_enquiry_id">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.Enquiry Details')</h4>
                        @if (date('Y-m-d H:i:s') < date('Y-m-d H:i:s', strtotime($data->created_at . ' + 24 hours')) &&
                                isset($data->status) &&
                                !empty($data->status) &&
                                $data->status == 1 &&
                                $checkoffer < 1)
                            <button type="button"
                                class="btn btn-primary waves-effect btn-label waves-light offer-create-btn"
                                data-bs-toggle="modal" data-bs-target="#enquiry-view-modal"><i
                                    class="bx bx-plus label-icon"></i>
                                @lang('translation.Create offer')</button>
                        @endif
                    </div>
                    <div class="tabs-enquiry-details">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#product-information" role="tab">
                                    <span class="d-block d-sm-none"><i class="bx bx-shopping-bag bx-sm"></i></span>
                                    <span class="d-none d-sm-block">@lang('translation.Products')</span>
                                </a>
                            </li>
                            @if (isset($loginUser) && !empty($loginUser) && $loginUser->role_type == 1)
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#customer-information" role="tab">
                                        <span class="d-block d-sm-none"><i class="bx bx-user bx-sm"></i></span>
                                        <span class="d-none d-sm-block">@lang('translation.Customer Profile')</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#offer-information" role="tab">
                                    <span class="d-block d-sm-none"><i class="bx bxs-offer bx-sm"></i></span>
                                    <span class="d-none d-sm-block">@lang('translation.Offers')</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="product-information" role="tabpanel">
                                <div class="product-enquiry-info table-responsive" data-simplebar>
                                    <table id="enrquiryProductListTable"
                                        class="table align-middle table-hover table-nowrap w-100 dataTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>@lang('translation.Product')</th>
                                                <th>@lang('translation.Qty')</th>
                                                <th>@lang('translation.Brand')</th>
                                                <th>@lang('translation.Connections')</th>
                                                <th>@lang('translation.Executions')</th>
                                                <th>@lang('translation.Attributes')</th>
                                                <th>@lang('translation.Updated At')</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            @if (isset($loginUser) && !empty($loginUser) && $loginUser->role_type == 1)
                                <div class="tab-pane" id="customer-information" role="tabpanel">
                                    @if (isset($data) && !empty($data))
                                        <div class="table-responsive" data-simplebar>
                                            <table class="table table-striped w-100">
                                                <tbody>
                                                    @if (
                                                        (isset($data->customer_name) && !empty($data->customer_name)) ||
                                                            (isset($data->customer_phone) && !empty($data->customer_phone)))
                                                        <tr>
                                                            @if (isset($data->customer_name) && !empty($data->customer_name))
                                                                <th width="20%">@lang('translation.Name'):</th>
                                                                <td width="30%">
                                                                    <p class="name">{{ $data->customer_name }}</p>
                                                                </td>
                                                            @endif
                                                            @if (isset($data->customer_phone) && !empty($data->customer_phone))
                                                                <th width="20%">@lang('translation.Phone'):</th>
                                                                <td width="30%">
                                                                    <p class="phone">{{ $data->customer_phone }}</p>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                    @if (
                                                        (isset($data->customer_email) && !empty($data->customer_email)) ||
                                                            (isset($data->customer_street) && !empty($data->customer_street)))
                                                        <tr>
                                                            @if (isset($data->customer_email) && !empty($data->customer_email))
                                                                <th width="20%">@lang('translation.Email'):</th>
                                                                <td width="30%">
                                                                    <p class="email">{{ $data->customer_email }}</p>
                                                                </td>
                                                            @endif
                                                            @if (isset($data->customer_street) && !empty($data->customer_street))
                                                                <th width="20%">@lang('translation.Street'):</th>
                                                                <td width="30%">
                                                                    <p class="shop_address p-box">
                                                                        {{ $data->customer_street }}
                                                                    </p>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                    @if (
                                                        (isset($data->customer_house_number) && !empty($data->customer_house_number)) ||
                                                            (isset($data->customer_zipcode) && !empty($data->customer_zipcode)))
                                                        <tr>
                                                            @if (isset($data->customer_house_number) && !empty($data->customer_house_number))
                                                                <th width="20%">@lang('translation.House_Number'):</th>
                                                                <td width="30%">
                                                                    <p class="email">{{ $data->customer_house_number }}
                                                                    </p>
                                                                </td>
                                                            @endif
                                                            @if (isset($data->customer_zipcode) && !empty($data->customer_zipcode))
                                                                <th width="20%">@lang('translation.Zipcode'):</th>
                                                                <td width="30%">
                                                                    <p class="shop_address p-box">
                                                                        {{ $data->customer_zipcode }}
                                                                    </p>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                    @if (
                                                        (isset($data->customer_city) && !empty($data->customer_city)) ||
                                                            (isset($data->customer_country) && !empty($data->customer_country)))
                                                        <tr>
                                                            @if (isset($data->customer_city) && !empty($data->customer_city))
                                                                <th width="20%">@lang('translation.City'):</th>
                                                                <td width="30%">
                                                                    <p class="email">{{ $data->customer_city }}</p>
                                                                </td>
                                                            @endif
                                                            @if (isset($data->customer_country) && !empty($data->customer_country))
                                                                <th width="20%">@lang('translation.Country'):</th>
                                                                <td width="30%">
                                                                    <p class="shop_address p-box">
                                                                        {{ $data->customer_country }}
                                                                    </p>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                    @if (
                                                        (isset($data->enquiry_description) && !empty($data->enquiry_description)) ||
                                                            (isset($data->created_at) && !empty($data->created_at)))
                                                        <tr>
                                                            @if (isset($data->enquiry_description) && !empty($data->enquiry_description))
                                                                <th width="20%">@lang('translation.Description'):</th>
                                                                <td width="30%">
                                                                    <p class="enquiry_description p-box">
                                                                        {{ $data->enquiry_description }}</p>
                                                                </td>
                                                            @endif
                                                            @if (isset($data->created_at) && !empty($data->created_at))
                                                                <th width="20%">@lang('translation.Date'):</th>
                                                                <td width="30%">
                                                                    <p class="date">
                                                                        {{ date('d/m/Y', strtotime($data->created_at)) }}
                                                                    </p>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-warning d-block" role="alert">
                                            <i class="mdi mdi-alert-outline me-2"></i>
                                            @lang('translation.Customer information not found')!
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="tab-pane" id="offer-information" role="tabpanel">
                                <div class="table-responsive" data-simplebar>
                                    <table id="offerListTable"
                                        class="table align-middle table-hover table-nowrap w-100 dataTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th></th>
                                                <th>@lang('translation.Description')</th>
                                                <th>@lang('translation.Date')</th>
                                                <th>@lang('translation.Status')</th>
                                                <th>@lang('translation.Actions')</th>
                                                <th>@lang('translation.Updated At')</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
    @php
    $ac_data_field='';
    // $ac_data_field='data-a-sign="$"  data-a-dec="." data-a-sep=","';
    @endphp
    {{-- @php $ac_data_field='data-a-sign="€ " data-a-dec="," data-a-sep="."';@endphp --}}

    @if (date('Y-m-d H:i:s') < date('Y-m-d H:i:s', strtotime($data->created_at . ' + 24 hours')) &&
            isset($data->status) &&
            !empty($data->status) &&
            $data->status == 1 &&
            $checkoffer < 1)
        <!-- Page Models -->
        <div id="enquiry-view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">
                                @lang('translation.Create offer')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="offer-form" method="post" class="form-horizontal" action="{{ route('dealer.sendOffer') }}">
                            @csrf
                            <input type="hidden" name="customer_enquiry_id" value="{{ $id }}"
                                id="customer_enquiry_id_offer">
                            <div id="add_error_message"></div>
                            <div class="table-responsive" data-simplebar>
                                <table id="enquiryProductTable"
                                    class="table align-middle table-hover table-nowrap w-100 dataTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="35%">@lang('translation.Product')</th>
                                            <th width="15%">@lang('translation.Qty')</th>
                                            <th width="50%">@lang('translation.Offer Amount')</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            @php
                            $is_exclude_vat_from_offer=config('common.is_exclude_vat_from_offer');
                            $german_country=config('common.german_country');
                            $customer_country=$loginUser->country;

                            if(in_array($customer_country, $german_country)){
                                $ac_data_field='data-a-sign="€ " data-a-dec="," data-a-sep="."';
                            }
                            @endphp

                            @if($is_exclude_vat_from_offer)

                                <div class="my-3 row">
                                    <label for="total_offer_ammount" class="col-md-6 col-form-label text-md-end">
                                        @lang('translation.Total Amount')</label>
                                    <div class="col-md-6">
                                        <input class="form-control changenumber_w_o_d" type="text" name="total_offer_ammount"
                                            id="total_offer_ammount" <?php echo $ac_data_field;?> readonly>
                                    </div>
                                </div>
                                <div class="my-3 row">
                                    <label for="total_vat_amount" class="col-md-6 col-form-label text-md-end">
                                        @lang('translation.Total Mwst & Lieferung') <br/>  @lang('translation.VAT') ({{$loginUser->vat}}%) </label>
                                    <div class="col-md-6">
                                        <input class="form-control changenumber_w_o_d" type="text" name="total_vat_amount"
                                            id="total_vat_amount" <?php echo $ac_data_field;?> readonly>
                                        <span class="invalid-feedback" id="total_vat_amountError" data-ajax-feedback="total_vat_amount" role="alert"></span>
                                    </div>
                                </div>
                                <div class="my-3 row">
                                    <label for="final_offer_amount" class="col-md-6 col-form-label text-md-end">
                                        @lang('translation.Final Amount')</label>
                                    <div class="col-md-6">
                                        <input class="form-control changenumber_w_o_d" type="text" name="final_offer_amount"
                                            id="final_offer_amount" <?php echo $ac_data_field;?> readonly>
                                        <span class="text-muted pt-1 d-block">@lang('translation.inkl_Mwst & Lieferung')</span>
                                    </div>
                                </div>
                            @else
                                <div class="my-3 row">
                                    <label for="total_offer_ammount" class="col-md-6 col-form-label text-md-end">
                                        @lang('translation.Total Amount')</label>
                                    <div class="col-md-6">
                                        <input class="form-control changenumber_w_o_d" type="text" name="total_offer_ammount"
                                            id="total_offer_ammount" <?php echo $ac_data_field;?> readonly>
                                        <span class="text-muted pt-1 d-block">@lang('translation.inkl_Mwst & Lieferung')</span>
                                    </div>
                                </div>
                            @endif
                            <div class="my-3 row">
                                <label for="total_offer_ammount" class="col-md-6 col-form-label">@lang('translation.Delivery_time')</label>

                                <div class="col-md-3">
                                    <input class="form-control delivery_time" type="number" min="0" name="delivery_time" id="delivery_time">
                                    <span class="invalid-feedback" id="delivery_timeError" data-ajax-feedback="delivery_time" role="alert"></span>
                                </div>
                                <div class="col-md-3">
                                    <select id="del-days-week" type="text" class="form-control form-select country delivery_time_type" name="delivery_time_type">
                                        <option value="1">@lang('translation.Days')</option>
                                        <option value="2">@lang('translation.Weeks')</option>
                                    </select>
                                    <span class="invalid-feedback" id="delivery_time_typeError" data-ajax-feedback="delivery_time_type" role="alert"></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="offer_description" class="form-label">@lang('translation.Description')</label>
                                <textarea name="offer_description" id="offer_description" class="form-control offer_description" placeholder=""
                                    rows="4"></textarea>
                                <span class="invalid-feedback" id="offer_descriptionError"
                                    data-ajax-feedback="offer_description" role="alert"></span>
                            </div>
                            <div class="col-md-12 modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                    aria-label="Close">@lang('translation.Close')</button>
                                <button type="submit"
                                    class="btn btn-success waves-effect waves-light">@lang('translation.Send')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Page Models -->
    <div id="offer-view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Offer Details')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" data-simplebar>
                        <table id="offerViewTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>@lang('translation.Product')</th>
                                    <th>@lang('translation.Qty')</th>
                                    <th>@lang('translation.Brand')</th>
                                    <th>@lang('translation.Amount')</th>
                                    <th>@lang('translation.Updated At')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th colspan="2">@lang('translation.Total Amount'):<span
                                            class="text-muted d-block">@lang('translation.inkl_Mwst & Lieferung')</span></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="delivery-date-time">
                        <span><b>@lang('translation.Delivery_time'):</b></span>
                        <span class="delivery-date-time"></span>
                    </div>
                    {{-- <div class="mt-4 row d-flex align-items-center">
                        <p class="col-md-6 text-md-end">Total amount</p>
                        <div class="col-md-6">
                            <p class="form-control" id="total_offer_ammount_view">
                                <span class="text-muted pt-1 d-block">inkl. Mwst & Lieferung</span>
                        </div>
                    </div> --}}
                </div>
                <div class="col-md-12 modal-footer">
                    <button type="button" class="btn btn-success waves-effect" data-bs-dismiss="modal"
                        aria-label="Close">@lang('translation.Close')</button>
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
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('dealer.getDealerEnquiry') }}";
        var detailUrl = "{{ route('dealer.getDetailEnquiry') }}";
        var sendOfferUrl = "{{ route('dealer.sendOffer') }}";
        var getOfferListUrl = "{{ route('dealer.getOfferList') }}";
        var getofferDetailUrl = "{{ route('dealer.getofferDetail') }}";
        var getEnquiryProductListUrl = "{{ route('dealer.getEnquiryProductList') }}";
        var getOfferProductListUrl = "{{ route('dealer.getOfferProductList') }}";
        var vat_per={{!empty($loginUser->vat)?$loginUser->vat:0}};
        var is_exclude_vat_from_offer={{config('common.is_exclude_vat_from_offer')}};
        var ac_data_field='<?php echo $ac_data_field;?>';

    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealer_purchase_enquiry.js') }}"></script>
@endsection
