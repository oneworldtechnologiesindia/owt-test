@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Login')
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
                                            <h5 class="text-primary">@lang('translation.Welcome_Back') !</h5>
                                            <p>@lang('translation.Sign_in_to_continue_to') {{ config('app.name') }}.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ URL::asset('/assets/images/profile-img.png') }}"
                                            alt="{{ config('app.name') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="auth-logo">
                                    <div class="auth-logo-light logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <a href="{{ route('index') }}">
                                                <span class="avatar-title rounded-circle bg-light">
                                                    <img src="{{ URL::asset('/assets/images/logo-light.svg') }}"
                                                        alt="{{ config('app.name') }}" class="" height="30">
                                                </span>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="auth-logo-dark logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <a href="{{ route('index') }}">
                                                <span class="avatar-title rounded-circle bg-light">
                                                    <img src="{{ URL::asset('/assets/images/logo.svg') }}"
                                                        alt="{{ config('app.name') }}" class="" height="30">
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="username" class="form-label">@lang('translation.Email')</label>
                                            <input name="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" id="username" placeholder="@lang('translation.Enter_Email')"
                                                autocomplete="email" autofocus>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">@lang('translation.Password')</label>
                                            <div
                                                class="input-group auth-pass-inputgroup @error('password') is-invalid @enderror">
                                                <input type="password" name="password"
                                                    class="form-control  @error('password') is-invalid @enderror"
                                                    id="userpassword" placeholder="@lang('translation.Enter_Password')" aria-label="Password"
                                                    aria-describedby="password-addon">
                                                <button class="btn btn-light " type="button" id="password-addon"><i
                                                        class="mdi mdi-eye-outline"></i></button>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                @lang('translation.Remember_me')
                                            </label>
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light"
                                                type="submit">@lang('translation.Log_In')</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="text-muted"><i
                                                        class="mdi mdi-lock me-1"></i> @lang('translation.Forgot_your_password')</a>
                                            @endif

                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">

                            <div>
                                <p>@lang('translation.Dont_have_an_account') <a href="{{ url('register') }}" class="fw-medium text-primary">
                                        @lang('translation.Signup_now') </a> </p>
                                <p>©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> {{ config('app.name') }}. @lang('translation.Crafted_with') <i
                                        class="mdi mdi-heart text-danger"></i>
                                    @lang('translation.by') <a href="https://amcodr.com/" target="_blank">Amcodr IT Solutions</a>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->
    @endsection
