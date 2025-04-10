@extends('layouts.master-without-nav')

@section('title', 'Verify Password')

@section('body')

    <body>
    @endsection

    @section('content')
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary"> @lang('translation.Verify_Password')</h5>
                                            <p>@lang('translation.Verify_Password_with') {{ config('app.name') }}.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ URL::asset('/assets/images/profile-img.png') }}" alt=""
                                            class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div>
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <a href="{{ route('index') }}">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ URL::asset('/assets/images/logo.svg') }}" alt=""
                                                    class="" height="30">
                                            </span>
                                        </a>
                                    </div>
                                </div>

                                @if (session('resent'))
                                    <div class="alert alert-success" role="alert">
                                        @lang('translation.A_fresh_verification_link_has_been_sent_to_your_email_address')
                                    </div>
                                @endif
                                @lang('translation.Before_proceeding_please_check_your_email_for_a_verification_link')
                                @lang('translation.If_you_did_not_receive_the_email'),
                                <div class="p-2">
                                    <form class="form-horizontal" method="POST"
                                        action="{{ route('verification.resend') }}">
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        @csrf

                                        <div class="text-end">
                                            <button class="btn btn-primary w-md waves-effect waves-light"
                                                type="submit">@lang('translation.click_here_to_request_another')</button>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>@lang('translation.Remember_It') <a href="{{ route('login') }}" class="fw-medium text-primary">
                                    @lang('translation.Sign_In_here')</a>
                            </p>
                            <p>©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> {{ config('app.name') }}. @lang('translation.Crafted_with') <i
                                    class="mdi mdi-heart text-danger"></i> @lang('translation.by') <a href="https://amcodr.com/"
                                    target="_blank">Amcodr IT Solutions</a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection
