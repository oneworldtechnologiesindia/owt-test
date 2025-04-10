@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Register')
@endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"
        type="text/css">
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">
@endsection
@section('body')

    <body>
    @endsection

    @section('content')

        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-11 col-lg-10 col-xl-8">
                        <div class="card overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">@lang('translation.Free_Register')</h5>
                                            <p>@lang('translation.Get_your_free') {{ config('app.name') }} @lang('translation.account_now').</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end text-end">
                                        <img src="{{ URL::asset('/assets/images/profile-img.png') }}"
                                            alt="{{ config('app.name') }}" class="img-fluid w-75">
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
                                    <form method="POST" class="form-horizontal" action="{{ route('register') }}"
                                        id="registerForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio" class="btn-check role_type" name="role_type"
                                                    value="2" id="role_type_2" autocomplete="off"
                                                    {{ empty(old('role_type')) || old('role_type') == 2 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary"
                                                    for="role_type_2">@lang('translation.Dealer')</label>

                                                <input type="radio" class="btn-check role_type" name="role_type"
                                                    id="role_type_3" autocomplete="off" value="3"
                                                    {{ old('role_type') == 3 ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary"
                                                    for="role_type_3">@lang('translation.Customer')</label>
                                            </div>
                                        </div>
                                        <div class="mb-3 customer_field">
                                            <label for="salutation" class="form-label w-100">@lang('translation.Salutation') </label>
                                            <div class="btn-group" role="group"
                                                aria-label="Basic radio toggle button group">
                                                <input type="radio"
                                                    class="btn-check salutation @error('salutation') is-invalid @enderror"
                                                    name="salutation" value="herr" id="salutation_herr" autocomplete="off"
                                                    {{ old('salutation') == 'herr' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-dark"
                                                    for="salutation_herr">@lang('translation.Herr')</label>

                                                <input type="radio"
                                                    class="btn-check salutation @error('salutation') is-invalid @enderror"
                                                    name="salutation" value="frau" id="salutation_frau" autocomplete="off"
                                                    {{ old('salutation') == 'frau' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-dark"
                                                    for="salutation_frau">@lang('translation.Frau')</label>

                                                <input type="radio"
                                                    class="btn-check salutation @error('salutation') is-invalid @enderror"
                                                    name="salutation" value="divers" id="salutation_divers"
                                                    autocomplete="off"
                                                    {{ old('salutation') == 'divers' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-dark"
                                                    for="salutation_divers">@lang('translation.Divers')</label>

                                                <input type="radio"
                                                    class="btn-check salutation @error('salutation') is-invalid @enderror"
                                                    name="salutation" value="firma" id="salutation_firma"
                                                    autocomplete="off" {{ old('salutation') == 'firma' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-dark"
                                                    for="salutation_firma">@lang('translation.Firma')</label>
                                            </div>
                                            @error('salutation')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="row customer_field_add">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="customer_company_name"
                                                        class="form-label">@lang('translation.Company_Name')</label>
                                                    <input id="customer_company_name" type="text"
                                                        class="form-control customer_company_name @error('customer_company_name') is-invalid @enderror"
                                                        name="customer_company_name"
                                                        value="{{ old('customer_company_name') }}"
                                                        placeholder="@lang('translation.Enter_Company_Name')">

                                                    @error('customer_company_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="customer_vat_number"
                                                        class="form-label">@lang('translation.VAT_Number')</label>
                                                    <input id="customer_vat_number" type="text"
                                                        class="form-control customer_vat_number @error('customer_vat_number') is-invalid @enderror"
                                                        name="customer_vat_number"
                                                        value="{{ old('customer_vat_number') }}"
                                                        placeholder="@lang('translation.Enter_VAT_Number')">

                                                    @error('customer_vat_number')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 dealer_field">
                                            <label for="company_name" class="form-label">@lang('translation.Company_Name')</label>
                                            <input id="company_name" type="text"
                                                class="form-control company_name @error('company_name') is-invalid @enderror"
                                                name="company_name" value="{{ old('company_name') }}"
                                                placeholder="@lang('translation.Enter_Company_Name')" autofocus>

                                            @error('company_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label">@lang('translation.First_Name')</label>
                                                    <input id="first_name" type="text"
                                                        class="form-control first_name @error('first_name') is-invalid @enderror"
                                                        name="first_name" value="{{ old('first_name') }}"
                                                        placeholder="@lang('translation.Enter_First_Name')">

                                                    @error('first_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="last_name" class="form-label">@lang('translation.Last_Name')</label>
                                                    <input id="last_name" type="text"
                                                        class="form-control last_name @error('last_name') is-invalid @enderror"
                                                        name="last_name" value="{{ old('last_name') }}"
                                                        placeholder="@lang('translation.Enter_Last_Name')">

                                                    @error('last_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 ">
                                                <div class="mb-3">
                                                    <label for="street" class="form-label">@lang('translation.Street')</label>
                                                    <input id="street" type="text"
                                                        class="form-control street @error('street') is-invalid @enderror"
                                                        name="street" value="{{ old('street') }}"
                                                        placeholder="@lang('translation.Enter_Street')">

                                                    @error('street')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="house_number"
                                                        class="form-label">@lang('translation.House_Number')</label>
                                                    <input id="house_number" type="text"
                                                        class="form-control house_number @error('house_number') is-invalid @enderror"
                                                        name="house_number" value="{{ old('house_number') }}"
                                                        placeholder="@lang('translation.Enter_House_Number')">

                                                    @error('house_number')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="zipcode" class="form-label">@lang('translation.Zipcode')</label>
                                                    <input id="zipcode" type="text"
                                                        class="form-control zipcode @error('zipcode') is-invalid @enderror"
                                                        name="zipcode" value="{{ old('zipcode') }}"
                                                        placeholder="@lang('translation.Enter_Zip_Code')">

                                                    @error('zipcode')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="city" class="form-label">@lang('translation.City')</label>
                                                    <input id="city" type="text"
                                                        class="form-control city @error('city') is-invalid @enderror"
                                                        name="city" value="{{ old('city') }}"
                                                        placeholder="@lang('translation.Enter_City')">

                                                    @error('city')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if (isset($countries) && !empty($countries))
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="country"
                                                            class="form-label">@lang('translation.Country')</label>
                                                        <select id="country" type="text"
                                                            class="form-select country @error('country') is-invalid @enderror"
                                                            name="country">
                                                            <option value="">@lang('translation.Select_country')</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country }}"
                                                                    {{ old('country') == $country ? 'selected' : '' }}>
                                                                    {{ $country }}</option>
                                                            @endforeach
                                                        </select>

                                                        @error('country')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-6 dealer_field">
                                                <div class="mb-3">
                                                    <div class="row">
                                                        <div class="col-md-5 col-5">
                                                            <div class="mb-3">
                                                                <label for="vat" class="form-label">@lang('translation.VAT')</label>
                                                                <div class="input-group @error('vat') is-invalid @enderror">
                                                                    <input id="vat" type="text" class="form-control vat @error('vat') is-invalid @enderror" name="vat" value="{{ old('vat') }}"
                                                                        placeholder="@lang('translation.Enter_VAT')">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">%</span>
                                                                    </div>
                                                                </div>
                                                                @error('vat')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                            @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-7 col-7">
                                                            <label for="vat_number" class="form-label">@lang('translation.VAT_Number')</label>
                                                            <input id="vat_number" type="text"
                                                                class="form-control vat_number @error('vat_number') is-invalid @enderror"
                                                                name="vat_number" value="{{ old('vat_number') }}"
                                                                placeholder="@lang('translation.Enter_VAT_Number')">

                                                            @error('vat_number')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 customer_field">
                                                <div class="mb-3">
                                                    <label for="birth_date"
                                                        class="control-label">@lang('translation.Birth_Date')</label>
                                                    <div class="input-group" id="birth_date_container">
                                                        <input type="text" id="birth_date" name="birth_date"
                                                            class="form-control @error('birth_date') is-invalid @enderror"
                                                            value="{{ old('birth_date') }}"
                                                            placeholder="@lang('translation.Enter_Birth_Date')" data-date-format="dd.mm.yyyy"
                                                            data-date-container='#birth_date_container'
                                                            data-provide="datepicker" data-date-autoclose="true">
                                                        <span class="input-group-text"><i
                                                                class="mdi mdi-calendar"></i></span>

                                                        @error('birth_date')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row dealer_field">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="shop_start_time"
                                                        class="form-label">@lang('translation.Shop_Opening_Time')</label>
                                                    <div class="input-group" id="timepicker-input-shop_start_time">
                                                        <input id="shop_start_time" type="text"
                                                            class="form-control shop_start_time @error('shop_start_time') is-invalid @enderror"
                                                            name="shop_start_time" value="{{ old('shop_start_time') }}"
                                                            placeholder="@lang('translation.Enter_Shop_Opening_Time')" data-provide="timepicker">
                                                        <span class="input-group-text"><i
                                                                class="mdi mdi-clock-outline"></i></span>
                                                        @error('shop_start_time')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="shop_end_time"
                                                        class="form-label">@lang('translation.Shop_Close_Time')</label>
                                                    <div class="input-group" id="timepicker-input-shop_end_time">
                                                        <input id="shop_end_time" type="text"
                                                            class="form-control shop_end_time @error('shop_end_time') is-invalid @enderror"
                                                            name="shop_end_time" value="{{ old('shop_end_time') }}"
                                                            placeholder="@lang('translation.Enter_Shop_Close_Time')" data-provide="timepicker">
                                                        <span class="input-group-text"><i
                                                                class="mdi mdi-clock-outline"></i></span>
                                                        @error('shop_end_time')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">@lang('translation.Email')</label>
                                                    <input id="email" type="email"
                                                        class="form-control email @error('email') is-invalid @enderror"
                                                        name="email" value="{{ old('email') }}"
                                                        placeholder="@lang('translation.Enter_Email')">

                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">@lang('translation.Phone')</label>
                                                    <input id="phone" type="text"
                                                        class="form-control phone @error('phone') is-invalid @enderror"
                                                        name="phone" value="{{ old('phone') }}"
                                                        placeholder="@lang('translation.Enter_Phone_Number')">

                                                    @error('phone')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">@lang('translation.Password')</label>
                                                    <input id="password" type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        name="password" placeholder="@lang('translation.Enter_Password')">

                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password_confirmation"
                                                        class="form-label">@lang('translation.Confirm_Password')</label>
                                                    <input id="password_confirmation" type="password"
                                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                                        name="password_confirmation" placeholder="@lang('translation.Enter_Confirm_Password')">

                                                    @error('password_confirmation')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="gender" class="form-label">@lang('translation.Gender')</label>
                                                    <br>
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input gender" name="gender" id="gender_0" value="0" checked>
                                                        <label class="form-check-label" for="gender_0">@lang('translation.Male')</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input gender" name="gender" id="gender_1" value="1">
                                                        <label class="form-check-label" for="gender_1">@lang('translation.Female')</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input  gender" name="gender" id="gender_2" value="2">
                                                        <label class="form-check-label" for="gender_2">@lang('translation.Other')</label>
                                                    </div>

                                                    @error('gender')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row dealer_field">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="bank_name" class="form-label">@lang('translation.Bank_Name')</label>
                                                    <input id="bank_name" type="text"
                                                        class="form-control bank_name @error('bank_name') is-invalid @enderror"
                                                        name="bank_name" value="{{ old('bank_name') }}"
                                                        placeholder="@lang('translation.Enter_Bank_Name')">

                                                    @error('bank_name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="iban" class="form-label">@lang('translation.IBAN')</label>
                                                    <input id="iban" type="text"
                                                        class="form-control iban @error('iban') is-invalid @enderror"
                                                        name="iban" value="{{ old('iban') }}"
                                                        placeholder="@lang('translation.Enter_IBAN')">

                                                    @error('iban')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="bic" class="form-label">@lang('translation.BIC')</label>
                                                    <input id="bic" type="text"
                                                        class="form-control bic @error('bic') is-invalid @enderror"
                                                        name="bic" value="{{ old('bic') }}"
                                                        placeholder="@lang('translation.Enter_BIC')">

                                                    @error('bic')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch form-switch-md">
                                                <input class="form-check-input @error('agbs_terms') is-invalid @enderror"
                                                    type="checkbox" id="agbs_terms" name="agbs_terms"
                                                    {{ old('agbs_terms') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="agbs_terms">@lang('translation.I_Agree') <a
                                                        href="javascript:void(0)" data-type="hq-terms-condition"
                                                        class="terms-documents">@lang('translation.AGBs_Terms_Coditions')</a></label>

                                                @error('agbs_terms')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch form-switch-md">
                                                <input class="form-check-input @error('dsgvo_terms') is-invalid @enderror"
                                                    type="checkbox" id="dsgvo_terms" name="dsgvo_terms"
                                                    {{ old('dsgvo_terms') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dsgvo_terms">@lang('translation.I_Agree') <a
                                                        href="javascript:void(0)" data-type="hq-privacy-policy"
                                                        class="terms-documents">@lang('translation.DSGVO_Terms_Coditions')</a></label>
                                                @error('dsgvo_terms')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3 dealer_field">
                                            <div class="form-check form-switch form-switch-md">
                                                <input class="form-check-input @error('sepa_terms') is-invalid @enderror"
                                                    type="checkbox" id="sepa_terms" name="sepa_terms"
                                                    {{ old('sepa_terms') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sepa_terms">@lang('translation.I_Agree')
                                                    @lang('translation.SEPA_Lastschriftmandat')<a href="javascript:void(0)"
                                                        data-bs-toggle="popover" data-bs-trigger="hover"
                                                        title="@lang('translation.SEPA_Lastschriftmandat')" data-bs-html="true"
                                                        data-bs-placement="bottom"
                                                        data-bs-content="<div class='toolttip-content-html'>
                                                            <p>@lang('translation.CPS_Networks_GbR_Mooslängstr_1_82178_Puchheim')</p>
                                                            <p>@lang('translation.Gläubiger_Identifikationsnummer') <strong>@lang('translation.DE44ZZZ00002549501')</strong></p>
                                                            <p>@lang('translation.Mandatsreferenz') „<strong>Customer-ID“</strong></p>
                                                            <p><strong>@lang('translation.SEPA_Lastschriftmandat')</strong></p>
                                                            <p>@lang('translation.Wir_ermächtigen_CPS_Networks_GbR_Zahlungen_von_unserem_Konto_mittels_Lastschrift_einzuziehen_Zugleich_weise_ich_weisen_wir_unser_Kreditinstitut_an_die_von_CPS_Networks_auf_unser_Konto_gezogenen_Lastschriften_einzulösen')</p></div>"
                                                        class="position-relative tooltip-sepa-terms">
                                                        <i
                                                            class="bx bx-info-circle bx-xs info-sepa-terms
                                                        "></i></a></label>
                                                @error('sepa_terms')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mt-4 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light register-button"
                                                type="submit">@lang('translation.Register')</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">

                            <div>
                                <p>@lang('translation.Already_have_an_account') <a href="{{ url('login') }}" class="fw-medium text-primary">
                                        @lang('translation.Login')</a> </p>
                                <p>©
                                    <script>
                                        document.write(new Date().getFullYear())
                                    </script> {{ config('app.name') }}. @lang('translation.Crafted_with') <i
                                        class="mdi mdi-heart text-danger"></i>
                                    @lang('translation.by') <a href="https://amcodr.com/" target="_blank">Amcodr IT
                                        Solutions</a>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Page Models -->
        <div id="documents-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myLargeModalLabel"><span id="modal-title">Title</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="modal-description"></div>
                        <div class="col-md-12 modal-footer mb-0 pb-0">
                            <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                aria-label="Close">@lang('translation.Close')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
    @section('script')
        <!-- Timepicker Css -->
        <script src="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
        <!-- Datepicker Css -->
        <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <script>
            var getSignupDocumentUrl = "{{ route('document.signupGet') }}";
        </script>
        <script src="{{ addPageJsLink('register.js') }}"></script>
    @endsection
