@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Recover_Password')
@endsection

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
                                            <h5 class="text-primary"> @lang('translation.Reset_Password')</h5>
                                            <p>@lang('translation.Re_Password_with') {{ config('app.name') }}.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ URL::asset('/assets/images/profile-img.png') }}"
                                            alt="{{ config('app.name') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div>
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <a href="{{ route('index') }}">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ URL::asset('/assets/images/logo.svg') }}"
                                                    alt="{{ config('app.name') }}" class="" height="30">
                                            </span>
                                        </a>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <form class="form-horizontal" method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <div class="mb-3">
                                            <label for="useremail" class="form-label">@lang('translation.Email')</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="useremail" name="email" placeholder="@lang('translation.Enter_Email')"
                                                value="{{ $email ?? old('email') }}" id="email">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="userpassword">@lang('translation.Password')</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                id="userpassword" placeholder="@lang('translation.Enter_Password')">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="userpassword">@lang('translation.Confirm_Password')</label>
                                            <input id="password-confirm" type="password" name="password_confirmation"
                                                class="form-control" placeholder="@lang('translation.Enter_Confirm_Password')">
                                        </div>

                                        <div class="text-end">
                                            <button class="btn btn-primary w-md waves-effect waves-light"
                                                type="submit">@lang('translation.Reset')</button>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>@lang('translation.Remember_It') <a href="{{ url('login') }}" class="fw-medium text-primary">
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
