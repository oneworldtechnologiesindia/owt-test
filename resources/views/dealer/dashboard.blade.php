@extends('layouts.master')

@section('title')
     @lang('translation.Dashboard')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.Dashboard')
        @endslot
        @slot('title')
            @lang('translation.Dashboard')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">@lang('translation.Welcome_Back') !</h5>

                                <a href="{{ route('dealer.profile') }}"
                                    class="btn btn-primary waves-effect waves-light btn-sm">
                                    @lang('translation.View_Profile')
                                    <i class="mdi mdi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="{{ URL::asset('/assets/images/profile-img.png') }}" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0 pb-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="avatar-md profile-user-wid">
                                @php
                                    if (isset($loginUser) && !empty($loginUser) && $loginUser->role_type == 2 && !empty($loginUser->company_logo) && file_exists(public_path() . '/storage/company_logo/' . $loginUser->company_logo)) {
                                        $image_path = asset('/storage/company_logo/' . $loginUser->company_logo);
                                    } else {
                                        $image_path = asset('/assets/images/avatar.png');
                                    }
                                @endphp
                                <img src="{{ isset($image_path) ? $image_path : asset('/assets/images/avatar.png') }}"
                                    alt="" class="img-thumbnail rounded-    ">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row align-items-baseline">
                        <div class="col-sm-5">
                            <h5 class="font-size-15">
                                {{ Str::ucfirst($loginUser->first_name) . ' ' . Str::ucfirst($loginUser->last_name) }}</h5>
                            <p class="text-muted mb-0">{{ $roles[$loginUser->role_type] }}</p>
                            <div class="mt-1">
                                <div class="services-rating pt-3">
                                    <div class="rating-star">
                                        <input type="hidden" class="rating" data-filled="mdi mdi-star text-primary" data-empty="mdi mdi-star-outline text-muted" data-fractions="2" value="{{ $feedBackData['average_rating'] }}" disabled />

                                        <a href="{{ route('dealer.feedback') }}"><p class="text-muted mb-0"> {{ $feedBackData['total_feedback'] }} @lang('translation.Feedbacks')</p></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-7">
                                    &nbsp;
                                </div>
                                <div class="col-5">
                                    @if (isset($stauslevels) && !empty($stauslevels) && isset($stauslevels[$loginUser->status_level]))
                                        <div
                                            class="left-userinfo mini-stats-wid text-center {{ strtolower(str_replace(' ', '', $stauslevels[$loginUser->status_level])) }}-level-user">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary m-auto">
                                                <span class="avatar-title">
                                                    @switch($loginUser->status_level)
                                                        @case(2)
                                                            <i class="bx bxs-award font-size-24"></i>
                                                        @break

                                                        @case(3)
                                                            <i class="bx bxs-crown font-size-24"></i>
                                                        @break

                                                        @default
                                                            <i class="bx bxs-dollar-circle font-size-24"></i>
                                                    @endswitch
                                                </span>
                                            </div>
                                            <p class="m-0 pt-1">
                                                {{ $stauslevels[$loginUser->status_level] }}
                                                @lang('translation.Level')</p>
                                        </div>
                                    @else
                                        <div class="left-userinfo mini-stats-wid text-center silver-level-user">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary m-auto">
                                                <span class="avatar-title">
                                                    <i class="bx bxs-dollar-circle font-size-24"></i>
                                                </span>
                                            </div>
                                            <p class="m-0 pt-1">@lang('translation.Silver Level')</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Monthly Earning')</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="text-muted">@lang('translation.This month')</p>
                            <h3><span id="monthly-earning-amount">00</span></h3>
                            <p class="text-muted"><span class="text-success me-2"><span
                                        id="monthly-earning-percentage">00</span>% <i class="mdi mdi-arrow-up"></i>
                                </span> @lang('translation.From_previous_period')</p>
                            <div class="mt-4">
                                <a href="{{ route('dealer.sales') }}" class="btn btn-primary waves-effect waves-light btn-sm">@lang('translation.View More') <i class="mdi mdi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mt-4 mt-sm-0" style="position: relative;">
                                <div id="monthly-earning-percentage-chart" data-colors='["--bs-success"]'
                                    class="apex-charts"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">@lang('translation.Yearly_Turnover')</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="text-muted">@lang('translation.This_year')</p>
                            <h3><span id="yearly-earning-amount">00</span>
                            </h3>
                            <p class="text-muted"><span class="text-success me-2"><span
                                        id="yearly-earning-percentage">00</span>% <i class="mdi mdi-arrow-up"></i>
                                </span>@lang('translation.From_previous_period')</p>

                            <div class="mt-4">
                                <a href="{{ route('dealer.sales') }}" class="btn btn-primary waves-effect waves-light btn-sm">@lang('translation.View More') <i class="mdi mdi-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mt-4 mt-sm-0" style="position: relative;">
                                <div id="yearly-earning-percentage-chart" data-colors='["--bs-success"]'
                                    class="apex-charts"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (isset($stauslevels) &&
                !empty($stauslevels) &&
                isset($stauslevels[$loginUser->status_level]) &&
                (int) $loginUser->status_level + 1 <= 3)
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">@lang('translation.Status Level'): {{ $stauslevels[$loginUser->status_level] }}</h4>
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="text-muted">@lang('translation.Required amount')</p>
                                <h3><span id="required-level-amount">00</span>
                                </h3>
                                <p class="text-muted text-lowercase"><span class="text-success me-2"><span
                                            id="left-level-percentage">00</span>%
                                    </span> @lang('translation.left for') {{ $stauslevels[(int) $loginUser->status_level + 1] }}</p>
                            </div>
                            <div class="col-sm-6">
                                <div class="mt-4 mt-sm-0" style="position: relative;">
                                    <div id="completed-level-percentage-chart" data-colors='["--bs-success"]'
                                        class="apex-charts"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">@lang('translation.Purchase_Enquiries')</p>
                                    <h4 class="mb-0" id="purchase-enquiry-monthly">00
                                    </h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-copy-alt font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">@lang('translation.Sales')</p>
                                    <h4 class="mb-0" id="sales-monthly">00
                                    </h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center ">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-archive-in font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">@lang('translation.Appointments')</p>
                                    <h4 class="mb-0" id="appointments-monthly">00
                                    </h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-purchase-tag-alt font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.top_5_sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills top-sales-switch">
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="d-sm-flex top-sales-data">
                        <div class="col-md-4">
                            <p class="text-muted fw-medium">@lang('translation.brand_wise')</p>
                            <div class="row">
                                <div class="col-sm-12" id="brand-sales-value">
                                    <table class="table brand-sales-table table-bordered table-striped">
                                        <thead>
                                            <th>@lang('translation.Name')</th>
                                            <th>@lang('translation.Sales')</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="2" id="no-record-brand"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted fw-medium">@lang('translation.product_wise')</p>
                            <div class="row">
                                <div class="col-sm-12" id="product-sales-value">
                                    <table class="table product-sales-table table-bordered table-striped">
                                        <thead>
                                            <th>@lang('translation.Name')</th>
                                            <th>@lang('translation.Sales')</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="2" id="no-record-product"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted fw-medium">@lang('translation.product_type_wise')</p>
                            <div class="row">
                                <div class="col-sm-12" id="product-type-sales-value">
                                    <table class="table product-type-sales-table table-bordered table-striped">
                                        <thead>
                                            <th>@lang('translation.Name')</th>
                                            <th>@lang('translation.Sales')</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.Summary')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills chart-summary-switch">
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="week" href="#">@lang('translation.Week')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div id="summary-chart" class="apex-charts"
                        data-colors='["--bs-primary", "--bs-warning", "--bs-success"]' dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Apexcharts js -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Bootstrap rating js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-rating/bootstrap-rating.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/pages/rating-init.js') }}"></script>

    <script>
        var dashboardDataInfoUrl = "{{ route('dealer.home.dashboardDataInfo') }}";
        var summaryChartFilterUrl = "{{ route('dealer.home.summaryChartFilter') }}";
        var topSalesChartFilterUrl = "{{ route('dealer.home.topSalesChartFilter') }}";
        var monthlyEarning = "@lang('translation.Monthly Earning')";
        var yearlyTurnover = "@lang('translation.Yearly_Turnover')";
        var purchaseEnquiries = "@lang('translation.Purchase_Enquiries')";
        var sales = "@lang('translation.Sales')";
        var appointments = "@lang('translation.Appointments')";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealer_dashboard.js') }}"></script>
@endsection
