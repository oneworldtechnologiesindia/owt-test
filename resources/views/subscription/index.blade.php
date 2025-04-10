@extends('layouts.master')

@section('title')
     @lang('translation.Profile')
@endsection

@section('css')
    <!-- Timepicker Css -->
    <link href="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"
        type="text/css">

    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .stripe-button-el{
            display: none !important;
        }
        .card-body{
            height: 423px;
        }
    </style>
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Subscription')
        @endslot
    @endcomponent

    <div class="page-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="text-center mb-5">
                        <h4>@lang('translation.choose_your_pricing_plan')</h4>
                        {{-- <p class="text-muted">To achieve this, it would be necessary to have uniform grammar, pronunciation and more common words If several languages coalesce</p> --}}
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach ($packages as $package)
                    <div class="col-xl-6 col-md-6">
                        <div class="card plan-box">
                            <div class="card-body p-4">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h5>{{$package->name}}</h5>
                                    </div>
                                    <div class="flex-shrink-0 ms-3">
                                        <i class="bx bx-walk h1 text-primary"></i>
                                    </div>
                                </div>
                                <div class="py-4">
                                    <h2>
                                        <sup><small>{{($package->plan_currency==1)?'€':'$'}}</small></sup>
                                        {{$package->price}}/ <span class="font-size-13">{{$package->is_yearly==1?trans('translation.per_year'):trans('translation.per_month')}}</span></h2>
                                </div>
                                <div class="text-center plan-btn">
                                    @if($loginUser->package_id==$package->id && $loginUser->is_active_subscription==1)
                                        <button class=" btn btn-primary btn-sm waves-effect waves-light" disabled="disabled">@lang("translation.Subscribed")</button>
                                    @elseif($loginUser->package_id < $package->id && $loginUser->is_active_subscription==1)
                                        <button class=" subscribe_btn btn btn-primary btn-sm waves-effect waves-light" data-package="{{$package->id}}">@lang("translation.Upgrade Now")</button>
                                    @elseif($loginUser->package_id > $package->id && $loginUser->is_active_subscription==1)
                                        <button class=" subscribe_btn btn btn-primary btn-sm waves-effect waves-light" data-package="{{$package->id}}">@lang("translation.Downgrade Now")</button>
                                    @else
                                        <button class=" subscribe_btn btn btn-primary btn-sm waves-effect waves-light" data-package="{{$package->id}}">@lang("translation.Subscribe Now")</button>
                                    @endif
                                </div>
                                @if($package->plan_type == 1)
                                    <div class="plan-features mt-5">
                                        <p><i class="bx bx-checkbox-square text-primary me-2"></i> Preisangebote senden (anonym)</p>
                                        <p><i class="bx bx-checkbox-square text-primary me-2"></i> Hörterminverwaltung</p>
                                        <p><i class="bx bx-checkbox-square text-primary me-2"></i> Kalenderfunktion</p>
                                        <p><i class="bx bx-checkbox-square text-primary me-2"></i> Portfolioverwaltung</p>
                                        <p><i class="bx bx-checkbox-square text-primary me-2"></i> Kontakt-Management</p>
                                    </div>
                                @elseif($package->plan_type == 2)
                                    <div class="plan-features mt-5">
                                        <p><i class="bx bx-checkbox-square text-primary me-2"></i> Alle Basic-Funktionen</p>
                                        <p><i class="bx bx-checkbox-square text-primary me-2"></i> Video-Beratung</p>
                                        <p><i class="bx bx-checkbox-square text-primary me-2"></i> Marktanalyse-Tool</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
    {{-- <form method="post" id="stripe_form" class="myForms" action="{{ route('dealer.subscription.subscribe') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" class='package_id' name="package_id" value="1">
        <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
              data-key="pk_test_Fy9z2s6WVpEpxJ7Lw6f5jXd4"
              data-email="sukunj@gmail.com"
              data-description="HiFi Quest"></script>
    </form> --}}
    <form method="post" id="stripe_form" class="myForms" action="{{ route('dealer.subscription.subscribe') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" class='package_id' name="package_id" value="">
    </form>
@endsection

@section('script')
    <!-- Timepicker js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Inputmask js -->
    <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
@endsection

@section('script-bottom')
    @php
    $js_array=[
        "lang"=>[
            "title_of_confirm_subscription"=>trans('translation.title_of_subscription_confirm'),
            "description_of_confirm_subscription"=>trans('translation.description_of_confirm_subscription'),
            "checkout"=>trans('translation.checkout')
        ],
        "URL"=>[
            'processSubscription'=>route('dealer.subscription.subscribe')
        ]
    ];
    @endphp
    <script>
        subscription_var=@json($js_array);
    </script>
    <script src="{{ addPageJsLink('subscription.js') }}"></script>
@endsection
