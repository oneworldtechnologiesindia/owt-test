@php
    $login_user = Auth::user();
@endphp
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('index') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('/assets/images/logo.svg') }}" alt="{{ config('app.name') }}" height="15">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="{{ config('app.name') }}" height="50">
                    </span>
                </a>

                <a href="{{ route('index') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('/assets/images/logo-light.svg') }}" alt="{{ config('app.name') }}" height="15">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('/assets/images/logo-light.png') }}" alt="{{ config('app.name') }}" height="50">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    @switch(Session::get('lang'))
                        @case('ru')
                            <img src="{{ URL::asset('/assets/images/flags/russia.jpg') }}" alt="Header Language" height="16">
                        @break

                        @case('it')
                            <img src="{{ URL::asset('/assets/images/flags/italy.jpg') }}" alt="Header Language" height="16">
                        @break

                        @case('de')
                            <img src="{{ URL::asset('/assets/images/flags/germany.jpg') }}" alt="Header Language"
                                height="16">
                        @break

                        @case('es')
                            <img src="{{ URL::asset('/assets/images/flags/spain.jpg') }}" alt="Header Language" height="16">
                        @break

                        @default
                            <img src="{{ URL::asset('/assets/images/flags/us.jpg') }}" alt="Header Language" height="16">
                    @endswitch
                </button>
                <div class="dropdown-menu dropdown-menu-end">

                    <!-- item-->
                    <a href="{{ url('lang/en') }}" class="dropdown-item notify-item language" data-lang="eng">
                        <img src="{{ URL::asset('/assets/images/flags/us.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">English</span>
                    </a>

                    <!-- item-->
                    <a href="{{ url('lang/de') }}" class="dropdown-item notify-item language" data-lang="gr">
                        <img src="{{ URL::asset('/assets/images/flags/germany.jpg') }}" alt="user-image" class="me-1"
                            height="12"> <span class="align-middle">German</span>
                    </a>
                </div>
            </div>

            <div class="dark-light-switch d-flex align-items-center">
                <label for="theme" class="theme m-0">
                    {{-- <span>Light</span> --}}
                    <span class="theme__toggle-wrap">
                        <input type="checkbox" name="theme" id="theme" class="theme__toggle" role="switch"
                            value="dark" x-model="dark">
                        <span class="theme__fill"></span>
                        <span class="theme__icon">
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                            <span class="theme__icon-part"></span>
                        </span>
                    </span>
                    {{-- <span>Dark</span> --}}
                </label>
            </div>
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>
            @if (isset($login_user) && !empty($login_user) && $login_user->role_type != 1)
                <div class="dropdown d-inline-block" id="notification-hifi">
                    <button type="button" class="btn header-item noti-icon waves-effect"
                        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <i class="bx bx-bell bx-tada notification-main-icon"></i>
                        <span class="badge bg-danger rounded-pill notification-count">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                        aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0"> @lang('translation.Notifications') </h6>
                                </div>
                            </div>
                        </div>
                        <div data-simplebar style="max-height: 230px;" class="pb-2">
                            <div class="notification-listing"></div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @php
                        if (isset($login_user) && !empty($login_user) && $login_user->role_type == 2 && !empty($login_user->company_logo) && file_exists(public_path() . '/storage/company_logo/' . $login_user->company_logo)) {
                            $image_path = asset('/storage/company_logo/' . $login_user->company_logo);
                        } else {
                            $image_path = asset('/assets/images/avatar.png');
                        }
                    @endphp
                    <img class="rounded-circle header-profile-user"
                        src="{{ isset($image_path) ? $image_path : asset('/assets/images/avatar.png') }}"
                        alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1">{{ ucfirst($login_user->first_name) }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    @if($login_user->role_type==1)
                    <a class="dropdown-item" href="{{ route('profile') }}"><i
                            class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span>@lang('translation.Profile')</span></a>
                    @elseif($login_user->role_type==2)
                    <a class="dropdown-item" href="{{ route('dealer.profile') }}"><i
                        class="bx bx-user font-size-16 align-middle me-1"></i>
                    <span>@lang('translation.Profile')</span></a>
                    @else
                    <a class="dropdown-item" href="{{ route('customer.profile') }}"><i
                        class="bx bx-user font-size-16 align-middle me-1"></i>
                    <span>@lang('translation.Profile')</span></a>
                    @endif
                    @if($login_user->role_type==2)
                    <a class="dropdown-item" href="{{ route('dealer.subscription') }}"><i
                            class="bx bx-wallet font-size-16 align-middle me-1"></i>
                        <span>@lang('translation.Subscription')</span></a>
                    @endif

                    <a class="dropdown-item d-block change-password-modalbtn" href="javascript:void(0)"><i
                            class="bx bx-wrench font-size-16 align-middle me-1"></i>
                        <span>@lang('translation.Settings')</span></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void();"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                        <span>@lang('translation.Logout')</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<!--  Change-Password example -->
<div class="modal fade change-password" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">@lang('translation.Change_Password')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" id="change-password" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" value="{{ $login_user->id }}" id="data_id">
                    <div class="mb-3">
                        <label for="current_password">@lang('translation.Current_Password')</label>
                        <input id="current-password" type="password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            name="current_password" autocomplete="current_password" placeholder="@lang('translation.Enter_Current_Password')"
                            value="{{ old('current_password') }}">
                        <span class="invalid-feedback" id="current_passwordError"
                            data-ajax-feedback="current_password" role="alert"></span>
                    </div>

                    <div class="mb-3">
                        <label for="newpassword">@lang('translation.New_Password')</label>
                        <input id="spassword" type="password"
                            class="form-control @error('password') is-invalid @enderror" name="password"
                            autocomplete="new_password" placeholder="@lang('translation.Enter_New_Password')">
                        <div class="text-danger invalid-feedback" id="passwordError" data-ajax-feedback="password">
                        </div>
                        <span class="invalid-feedback" id="spasswordError" data-ajax-feedback="password"
                            role="alert"></span>
                    </div>

                    <div class="mb-3">
                        <label for="userpassword">@lang('translation.Confirm_Password')</label>
                        <input id="spassword-confirm" type="password" class="form-control"
                            name="password_confirmation" autocomplete="new_password"
                            placeholder="@lang('translation.Enter_New_Confirm_password')">
                        <span class="invalid-feedback" id="spassword_confirmError"
                            data-ajax-feedback="password_confirmation" role="alert"></span>
                    </div>

                    <div class="mt-3 d-grid">
                        <button class="btn btn-primary waves-effect waves-light UpdatePassword"
                            data-id="{{ $login_user->id }}" type="submit">@lang('translation.Update_Password')</button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
