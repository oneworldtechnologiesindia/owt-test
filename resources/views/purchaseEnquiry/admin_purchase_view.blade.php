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
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('li_2')
            <a href="{{ route('dealer-enquiry') }}">@lang('translation.Enquiry')</a>
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
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#customer-information" role="tab">
                                    <span class="d-block d-sm-none"><i class="bx bx-detail bx-sm"></i></span>
                                    <span class="d-none d-sm-block">@lang('translation.Details')</span>
                                </a>
                            </li>
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
                            <div class="tab-pane" id="customer-information" role="tabpanel">
                                @if (isset($data) && !empty($data))
                                    <div class="table-responsive" data-simplebar>
                                        <table class="table table-striped w-100">
                                            <tbody>
                                                @if ((isset($data->enquiry_description) && !empty($data->enquiry_description)) ||
                                                    (isset($data->created_at) && !empty($data->created_at)))
                                                    <tr>
                                                        @if (isset($data->enquiry_description) && !empty($data->enquiry_description))
                                                            <th width="20%">@lang('translation.Purchase Note'):</th>
                                                            <td width="30%">
                                                                <p class="enquiry_description p-box">
                                                                    {{ $data->enquiry_description }}</p>
                                                            </td>
                                                        @endif
                                                        @if (isset($data->created_at) && !empty($data->created_at))
                                                            <th width="20%">@lang('translation.Date'):</th>
                                                            <td width="30%">
                                                                <p class="date">
                                                                    {{ date('d.m.Y H:i', strtotime($data->created_at)) }}
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
                            <div class="tab-pane" id="offer-information" role="tabpanel">
                                <div class="table-responsive" data-simplebar>
                                    <table id="offerListTable"
                                        class="table align-middle table-hover table-nowrap w-100 dataTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th></th>
                                                <th>@lang('translation.Company')</th>
                                                <th>@lang('translation.Phone')</th>
                                                <th>@lang('translation.Total Amount')</th>
                                                <th>@lang('translation.Validity')</th>
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

    <!-- Page Models -->
    <div id="offer-view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Offer Details')</h5>
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
                                    <th colspan="2">@lang('translation.Total Amount'):<span class="text-muted d-block">@lang('translation.inkl_Mwst & Lieferung')</span></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="delivery-date-time">
                        <span><b>@lang('translation.Delivery_time'):</b></span>
                        <span class="delivery-date-time"></span>
                    </div>
                </div>
                <div class="col-md-12 modal-footer">
                    <button type="button" class="btn btn-success waves-effect" data-bs-dismiss="modal"
                        aria-label="Close">@lang('translation.Close')</button>
                </div>
            </div>
        </div>
    </div>

    @if (isset($payment_method) && !empty($payment_method))
        <div id="confirm-offer-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Accept offer')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="#" method="post" class="form accept-offer" id="accept-offer">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="offerid" value="0" class="offerid" id="offerid">
                            <input type="hidden" name="offerstatus" value="0" class="offerstatus"
                                id="offerstatus">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">@lang('translation.Payment Method')</label>
                                <select name="payment_method" id="payment_method" class="form-select">
                                    <option value="">@lang('translation.Choose payment method')</option>
                                    @foreach ($payment_method as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="payment_methodError"
                                    data-ajax-feedback="payment_method" role="alert"></span>
                            </div>
                            <div class="mb-2">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" id="dsgvo_terms" name="dsgvo_terms">
                                    <label class="form-check-label" for="dsgvo_terms">@lang('translation.I_Agree') <a
                                            href="{{ route('enquiry.getEnquiryDocuments') }}" target="_blank"
                                            class="enquiry-documents" data-type="terms">
                                            @lang('translation.Terms & Coditions')</a>.</label>
                                    <span class="invalid-feedback" id="dsgvo_termsError" data-ajax-feedback="dsgvo_terms"
                                        role="alert"></span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" id="withdrawal_declaration"
                                        name="withdrawal_declaration">
                                    <label class="form-check-label" for="withdrawal_declaration">
                                        @lang('translation.I_Agree') <a
                                            href="{{ route('enquiry.getEnquiryDocuments') }}" target="_blank"
                                            class="enquiry-documents" data-type="withdrawal">
                                            @lang('translation.Withdrawal Declaration')</a>.</label>
                                    <span class="invalid-feedback" id="withdrawal_declarationError"
                                        data-ajax-feedback="withdrawal_declaration" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                aria-label="Close">@lang('translation.Close')</button>
                            <button type="submit" class="btn btn-success waves-effect accept-offer">@lang('translation.Confirm')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('getDealerEnquiry') }}";
        var detailUrl = "{{ route('getDetailEnquiry') }}";
        var sendOfferUrl = "{{ route('sendOffer') }}";
        var getOfferListUrl = "{{ route('getOfferList') }}";
        var getofferDetailUrl = "{{ route('getofferDetail') }}";
        var getEnquiryProductListUrl = "{{ route('getEnquiryProductList') }}";
        var getOfferProductListUrl = "{{ route('getOfferProductList') }}";
        var updateOfferStatusUrl = "{{ route('updateOfferStatus') }}";
        var mypurchasesUrl = "{{ route('purchases') }}";
        var are_you_sure_want_to_accepted_this_offers = "@lang('translation.Are you sure want to Accepted This Offers')";
        var are_you_sure_want_to_rejected_this_offers = "@lang('translation.Are you sure want to Rejected This Offers')";
        var notifyOfferId = "{{ isset($offer_id) && !empty($offer_id) ? $offer_id : '' }}";
        var getCheckoutSessionUrl="{{ route('getCheckoutSession') }}";
        var getDealerInfoUrl = "{{ route('enquiry.getDealerInfo') }}";


    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('customer_purchase_enquiry.js') }}"></script>
@endsection
