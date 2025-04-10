@extends('layouts.master')

@section('title')
    @lang('translation.Sale')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">
    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Sale')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All Sales')</h4>
                    </div>
                    @if (isset($loginUser) && isset($loginUser->role_type) && !empty($loginUser->role_type) && $loginUser->role_type == 1)
                        <div class="sales-filter-form-container mb-5">
                            <form id="filter-sales-form" method="post" class="form-horizontal"
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
                                    <th>Id</th>
                                    <th>@lang('translation.Order')</th>
                                    <th>@lang('translation.Customer')</th>
                                    <th>@lang('translation.Product')</th>
                                    <th>@lang('translation.Rating')</th>
                                    <th>@lang('translation.Amount')</th>
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
        </div> <!-- end col -->
    </div>

    <!-- Page Models -->
    <div id="view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.View')</span>
                        @lang('translation.Order')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="orderid" value="0" id="orderid" class="orderid">
                    <input type="hidden" name="orderstatus" value="0" id="orderstatus" class="orderstatus">
                    <div class="tabs-order-details">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#product-information"
                                    role="tab">
                                    <span class="d-block d-sm-none"><i class="bx bx-shopping-bag bx-sm"></i></span>
                                    <span class="d-none d-sm-block">@lang('translation.Products')</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#customer-information" role="tab">
                                    <span class="d-block d-sm-none"><i class="bx bx-user bx-sm"></i></span>
                                    <span class="d-none d-sm-block">@lang('translation.Customer Profile')</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="product-information" role="tabpanel">
                                <div class="product-order-info table-responsive" data-simplebar>
                                    <table id="orderProductListTable"
                                        class="table align-middle table-hover table-nowrap w-100 dataTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>@lang('translation.Product')</th>
                                                <th>@lang('translation.Connection')</th>
                                                <th>@lang('translation.Execution')</th>
                                                <th>@lang('translation.Attribute')</th>
                                                <th>@lang('translation.Qty')</th>
                                                <th>@lang('translation.Price')</th>
                                                <th>@lang('translation.Updated At')</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th colspan="2">@lang('translation.Total Amount'):<span class="text-muted d-block">
                                                        @lang('translation.inkl_Mwst & Lieferung')</span></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="tab-pane mt-5" id="order-information" role="tabpanel">
                                    <h4 class="text-dark mb-4">@lang('translation.Order Information')</h4>
                                    <div class="table-responsive" data-simplebar>
                                        <table class="table table-striped w-100">
                                            <tbody>
                                                <tr>
                                                    <th width="20%">@lang('translation.Payment Method'):</th>
                                                    <td width="30%">
                                                        <p class="payment_method"></p>
                                                    </td>
                                                    <th width="20%"></th>
                                                    <td width="30%"></td>
                                                </tr>
                                                <tr class="shipping-info">
                                                    <th width="20%">@lang('translation.Shipping Company'):</th>
                                                    <td width="30%">
                                                        <p class="shipping_company"></p>
                                                    </td>
                                                    <th width="20%">@lang('translation.Tracking Numbers'):</th>
                                                    <td width="30%">
                                                        <p class="tracking_number"></p>
                                                    </td>
                                                </tr>
                                                <tr class="cancel-proof-div">
                                                    <th width="20%">@lang('translation.Cancel proof'):</th>
                                                    <td width="30%">
                                                        <p class="cancel-proof"><a href="javascript:void(0)"
                                                                target="_blank">@lang('translation.Cancel Document')</a></p>
                                                    </td>
                                                    <th width="20%"></th>
                                                    <td width="30%"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="rating_section" style="display:none;">
                                        <form method="POST" id="add-rating">
                                            <input type="hidden" name="purchase_id" class="purchase_id">
                                            <h4 class="text-dark mb-4">@lang('translation.Rating')</h4>
                                            <div class="row">
                                                <div class="col-xl-4 col-md-4 col-sm-6">
                                                    <h5 class="font-size-15">@lang('translation.Communication')</h5>
                                                    <div class="rating-star">
                                                        <input type="hidden" id="communication_rating"
                                                            data-filled="mdi mdi-star text-primary"
                                                            data-empty="mdi mdi-star-outline text-muted"
                                                            name="communication_rating" data-fractions="2"
                                                            data-input="communication" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-md-4 col-sm-6">
                                                    <h5 class="font-size-15">@lang('translation.Transaction')</h5>
                                                    <div class="rating-star">
                                                        <input type="hidden" id="transaction_rating"
                                                            data-filled="mdi mdi-star text-primary"
                                                            data-empty="mdi mdi-star-outline text-muted"
                                                            name="transaction_rating" data-fractions="2"
                                                            data-input="transaction" />
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-md-4 col-sm-6">
                                                    <h5 class="font-size-15">@lang('translation.Delivery')</h5>
                                                    <div class="rating-star">
                                                        <input type="hidden" id="delivery_rating"
                                                            data-filled="mdi mdi-star text-primary"
                                                            data-empty="mdi mdi-star-outline text-muted"
                                                            name="delivery_rating" data-fractions="2"
                                                            data-input="delivery" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="customer-information" role="tabpanel">
                                <div class="table-responsive" data-simplebar>
                                    <table class="table table-striped w-100">
                                        <tbody>
                                            <tr>
                                                <th width="20%">@lang('translation.Name'):</th>
                                                <td width="30%">
                                                    <p class="name"></p>
                                                </td>
                                                <th width="20%">@lang('translation.Phone'):</th>
                                                <td width="30%">
                                                    <p class="phone"></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th width="20%">@lang('translation.Email'):</th>
                                                <td width="30%">
                                                    <p class="email"></p>
                                                </td>
                                                <th width="20%">@lang('translation.Street'):</th>
                                                <td width="30%">
                                                    <p class="street p-box"></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th width="20%">@lang('translation.House_Number'):</th>
                                                <td width="30%">
                                                    <p class="house_number"></p>
                                                </td>
                                                <th width="20%">@lang('translation.Zipcode'):</th>
                                                <td width="30%">
                                                    <p class="zipcode p-box"></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th width="20%">@lang('translation.City'):</th>
                                                <td width="30%">
                                                    <p class="city"></p>
                                                </td>
                                                <th width="20%">@lang('translation.Country'):</th>
                                                <td width="30%">
                                                    <p class="country p-box"></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                            aria-label="Close">@lang('translation.Close')</button>
                        @if (isset($loginUser) && isset($loginUser->role_type) && !empty($loginUser->role_type) && $loginUser->role_type != 1)
                            <button type="button" class="btn btn-primary waves-effect waves-light payment-confirm">
                                @lang('translation.Confirm payment')</button>
                            <button type="button"
                                class="btn btn-primary waves-effect waves-light order-shipping">@lang('translation.Shipping')</button>
                            <button type="button" class="btn btn-danger waves-effect waves-light order-cancel">
                                @lang('translation.Cancel Order')</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="order-shipping-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Order shipping')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="post" class="form order-shipping-form" id="order-shipping-form">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="sorderid" value="0" id="sorderid" class="sorderid">
                        <input type="hidden" name="sorderstatus" value="0" id="sorderstatus"
                            class="sorderstatus">
                        <div class="mb-3">
                            <label for="shipping_company" class="control-label">@lang('translation.Shipping Company')</label>
                            <input id="shipping_company" type="text" class="form-control" name="shipping_company"
                                placeholder="@lang('translation.Enter shipping company')">
                            <span class="invalid-feedback" id="shipping_companyError"
                                data-ajax-feedback="shipping_company" role="alert"></span>
                        </div>
                        <div class="mb-3">
                            <label for="tracking_number" class="control-label">@lang('translation.Tracking number')</label>
                            <input id="tracking_number" type="text" class="form-control" name="tracking_number"
                                placeholder="@lang('translation.Enter tracking number')">
                            <span class="invalid-feedback" id="tracking_numberError" data-ajax-feedback="tracking_number"
                                role="alert"></span>
                        </div>
                    </div>
                    <div class="col-md-12 modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                            aria-label="Close">@lang('translation.Close')</button>
                        <button type="submit" class="btn btn-success waves-effect accept-offer">
                            @lang('translation.Ready for shipping')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="order-cancel-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Order cancel')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="post" class="form order-cancel-form" id="order-cancel-form">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="corderid" value="0" id="corderid" class="corderid">
                        <input type="hidden" name="corderstatus" value="0" id="corderstatus"
                            class="corderstatus">
                        <div class="mb-3">
                            <label for="cancel_proof" class="form-label">@lang('translation.Order cancel proof')</label>
                            <input class="form-control cancel_proof" type="file" id="cancel_proof"
                                name="cancel_proof">
                            <span class="invalid-feedback" id="cancel_proofError" data-ajax-feedback="cancel_proof"
                                role="alert"></span>
                        </div>
                    </div>
                    <div class="col-md-12 modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                            aria-label="Close">@lang('translation.Close')</button>
                        <button type="submit"
                            class="btn btn-danger waves-effect cancel-order">@lang('translation.Cancel')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>

    <!-- Bootstrap rating js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-rating/bootstrap-rating.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/pages/rating-init.js') }}"></script>

    <script>
        var apiUrl = "{{ route('sales.list') }}";
        var deleteUrl = "{{ route('sales.delete') }}";
        var addUrl = "{{ route('sales.addupdate') }}";
        var getOrderProductListUrl = "{{ route('getOrderProductList') }}";
        var detailUrl = "{{ route('getDetailOrder') }}";
        var confirmOrderPaymentUrl = "{{ route('sales.confirmOrderPayment') }}";
        var orderShippingUrl = "{{ route('sales.orderShipping') }}";
        var orderCanceledUrl = "{{ route('sales.orderCanceled') }}";
        var countryDealerFilterUrl = "{{ route('countryDealerFilter') }}";
        var getInvoiceUrl = "{{ route('sales.getInvoice') }}";
        var notifyOrderId = "{{ isset($order_id) && !empty($order_id) ? $order_id : '' }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('sales.js') }}"></script>
@endsection
