@extends('layouts.master')

@section('title')
    @lang('translation.Preview_ads')
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('customer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('li_2')
            <a href="{{ route('ad') }}">
                @lang('translation.ads')</a>
        @endslot
        @slot('title')
            @lang('translation.Preview_ads')
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
                                <p>{{ config('app.name') }}@lang('translation.Preview_ads')</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="avatar-md profile-user-wid mb-4">
                                <img src="{{ isset($loginUser->avatar) ? asset($loginUser->avatar) : asset('/assets/images/avatar.png') }}"
                                    alt="" class="img-thumbnail rounded-circle">
                            </div>
                            <h5 class="font-size-15">
                                {{ Str::ucfirst($loginUser->first_name) . ' ' . Str::ucfirst($loginUser->last_name) }}
                            </h5>
                            <p class="text-muted mb-0">{{ $roles[$loginUser->role_type] }}</p>
                        </div>

                        <div class="col-sm-6 align-self-end">
                            <div class="pt-4">
                                <div class="mt-4 text-end">
                                    <a href="{{ route('customer.profile') }}"
                                        class="btn btn-primary waves-effect waves-light btn-sm">
                                        @lang('translation.View_Profile')
                                        <i class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
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
            <div class="row" id="size-1-banner">
            </div>
        </div>
        <div class="col-xl-4" id="size-3-banner">
        </div>
        <div class="col-xl-8">
            <div class="row">
                <div class="col-md-3" id="size-2-banner">
                </div>
                <div class="col-md-9" id="size-4-banner">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var dashboardDataInfoUrl = "{{ route('ad.preview.fetch') }}";
        let basepath = "{{ asset('storage/ad_image') }}/";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('customer_dashboard.js') }}"></script>
@endsection
