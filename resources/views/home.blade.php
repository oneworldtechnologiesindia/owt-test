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
                                <h5 class="text-primary">@lang('translation.Welcome_Back')!</h5>
                                <p>{{ config('app.name') }} @lang('translation.Dashboard')</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="{{ asset('assets/images/profile-img.png') }}" alt="{{ config('app.name') }}" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img src="{{ isset($loginUser->avatar) ? asset($loginUser->avatar) : asset('/assets/images/avatar.png') }}"
                                    alt="{{ $loginUser->first_name }} {{ $loginUser->last_name }}"
                                    class="img-thumbnail rounded-circle">
                            </div>
                            <h5 class="font-size-15">
                                {{ Str::ucfirst($loginUser->first_name) . ' ' . Str::ucfirst($loginUser->last_name) }}
                            </h5>
                            <p class="text-muted mb-0">{{ $roles[$loginUser->role_type] }}</p>
                        </div>

                        <div class="col-sm-6 align-self-end">
                            <div class="pt-4">
                                <div class="mt-4 text-end">
                                    <a href="{{ route('profile') }}"
                                        class="btn btn-primary waves-effect waves-light btn-sm">@lang('translation.View_Profile')
                                        <i class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
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
                            <h3>$<span id="yearly-earning-amount">00</span></h3>
                            <p class="text-muted"><span class="text-success me-2"><span
                                        id="yearly-earning-percentage">00</span>% <i class="mdi mdi-arrow-up"></i>
                                </span> @lang('translation.From_previous_period')</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="mt-4 mt-sm-0" style="position: relative;">
                                <div id="yearly-earning-percentage-chart" data-colors='["--bs-success"]' class="apex-charts"
                                    data-title="@lang('translation.Yearly_Turnover')"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">@lang('translation.Dealers')</p>
                                    <h4 class="mb-0" id="number-of-dealers">00
                                    </h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center">
                                    <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                        <span class="avatar-title">
                                            <i class="bx bx-group font-size-24"></i>
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
                                    <p class="text-muted fw-medium">@lang('translation.Brands')</p>
                                    <h4 class="mb-0" id="number-of-brands">00
                                    </h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center ">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-store font-size-24"></i>
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
                                    <p class="text-muted fw-medium">@lang('translation.Products')</p>
                                    <h4 class="mb-0" id="number-of-products">00
                                    </h4>
                                </div>

                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx bx-package font-size-24"></i>
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
                        data-colors='["--bs-primary", "--bs-warning", "--bs-success"]' dir="ltr"
                        data-names="['@lang("translation.Purchase_Enquiries")', '@lang("translation.Sales")', '@lang("translation.Appointments")']"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Apexcharts js -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        var dashboardDataInfoUrl = "{{ route('home.dashboardDataInfo') }}";
        var summaryChartFilterUrl = "{{ route('home.summaryChartFilter') }}";
        var purchaseEnquiries = "@lang('translation.Purchase_Enquiries')";
        var sales = "@lang('translation.Sales')";
        var appointments = "@lang('translation.Appointments')";

    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('admin_dashboard.js') }}"></script>
@endsection
