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
                    <div class="purchase-enquiry-filter-form-container mb-5">
                        <form id="filter-purchase-enquiry-form" method="post" class="form-horizontal"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                @if (isset($countries) && !empty($countries))
                                    <div
                                        class="{{ isset($dealers) && !empty($dealers) && count($dealers) > 0 ? 'col-md-6' : 'col-md-12' }}">
                                        <div class="mb-3">
                                            <label for="country" class="control-label">@lang('translation.Country')</label>
                                            <select id="country" type="text" class="form-select country" name="country"
                                                placeholder="@lang('translation.Select_country')">
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
                                <div class="input-daterange input-group" id="filterdaterange" data-date-format="dd.mm.yyyy"
                                    data-date-autoclose="true" data-provide="datepicker"
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
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('getDealerEnquiry') }}";
        var detailUrl = "{{ route('getDetailEnquiry') }}";
        var sendOfferUrl = "{{ route('sendOffer') }}";
        var getOfferListUrl = "{{ route('getOfferList') }}";
        var getofferDetailUrl = "{{ route('getofferDetail') }}";
        var getEnquiryProductListUrl = "{{ route('getEnquiryProductList') }}";
        var delaerDeleteUrl = "{{ route('enquiry.delaerDelete') }}";
        var countryDealerFilterUrl = "{{ route('countryDealerFilter') }}";

    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('admin_purchase_enquiry.js') }}"></script>
@endsection
