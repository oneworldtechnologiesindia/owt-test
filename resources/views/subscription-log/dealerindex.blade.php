@extends('layouts.master')

@section('title')
     @lang('translation.Subscription Invoices')
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
            @lang('translation.Subscription Invoices')
        @endslot
    @endcomponent

    @php
    $loginUser=Auth::user();
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All_Subscription_Payments')</h4>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                     <th>@lang('translation.Subscription Date')</th>
                                    <th>@lang('translation.Start_Date')</th>
                                    <th>@lang('translation.End_Date')</th>
                                    <th>@lang('translation.Amount')</th>
                                    <th>@lang('translation.Tax')</th>
                                    <th>@lang('translation.Total')</th>
                                    <th>@lang('translation.Refund')</th>
                                    <th>@lang('translation.Plan')</th>
                                    <th>@lang('translation.Dealer')</th>
                                    <th>@lang('translation.Created_At')</th>
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
        var apiUrl = "{{ route('dealer.subscription-log.list') }}";
        var login_user_type=parseInt("{{$loginUser->role_type}}");
        var getInvoiceUrl = "{{ route('dealer.subscription-log.getInvoice') }}";
        var getOrderProductListUrl = "{{ route('dealer.getOrderProductList') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('subscription_log.js') }}"></script>
@endsection
