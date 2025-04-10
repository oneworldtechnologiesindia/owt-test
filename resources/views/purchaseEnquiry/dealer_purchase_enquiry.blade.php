@extends('layouts.master')

@section('title')
     @lang('translation.Purchase Enquiry')
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
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Purchase Enquiry')
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <h4 class="card-title">@lang('translation.Purchase Enquiry')</h4>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>@lang('translation.Customer')</th>
                                    <th>@lang('translation.Product')</th>
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
        </div> <!-- end col -->
    </div>
@endsection

@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('dealer.getDealerEnquiry') }}";
        var detailUrl = "{{ route('dealer.getDetailEnquiry') }}";
        var sendOfferUrl = "{{ route('dealer.sendOffer') }}";
        var getOfferListUrl = "{{ route('dealer.getOfferList') }}";
        var getofferDetailUrl = "{{ route('dealer.getofferDetail') }}";
        var getEnquiryProductListUrl = "{{ route('dealer.getEnquiryProductList') }}";
        var delaerDeleteUrl = "{{ route('dealer.enquiry.delaerDelete') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealer_purchase_enquiry.js') }}"></script>
@endsection
