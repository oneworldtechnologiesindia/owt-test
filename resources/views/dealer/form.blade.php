@extends('layouts.master')

@section('title')
     @lang('translation.Dealer')
@endsection

@section('css')
    <!-- Timepicker Css -->
    <link href="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"
        type="text/css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('li_2')
            <a href="{{ route('dealers') }}">@lang('translation.Dealer')</a>
        @endslot
        @slot('title')
            @if ($model->id)
                @lang('translation.Edit Dealer')
            @else
                @lang('translation.Add_New')
            @endif
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">
                            @if ($model->id)
                                @lang('translation.Edit Dealer') : {{ $model->first_name }} {{ $model->last_name }}
                            @else
                                @lang('translation.Add New Dealer')
                            @endif
                        </h4>
                    </div>
                    <div class="form-container">
                        <form id="add-form" method="post" class="form-horizontal"
                            action="{{ route('dealer.addupdate') }}">
                            @csrf
                            <input type="hidden" name="id"
                                value="{{ isset($model->id) && !empty($model->id) ? $model->id : '' }}" id="edit-id">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">@lang('translation.Company_Name')</label>
                                <input id="company_name" type="text" class="form-control company_name"
                                    name="company_name"
                                    value="{{ isset($model->company_name) && !empty($model->company_name) ? $model->company_name : '' }}"
                                    placeholder="@lang('translation.Enter_Company_Name')" autofocus>
                                <span class="invalid-feedback" id="company_nameError" data-ajax-feedback="company_name"
                                    role="alert"></span>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">@lang('translation.First_Name')</label>
                                        <input id="first_name" type="text" class="form-control first_name"
                                            name="first_name"
                                            value="{{ isset($model->first_name) && !empty($model->first_name) ? $model->first_name : '' }}"
                                            placeholder="@lang('translation.Enter_First_Name')">
                                        <span class="invalid-feedback" id="first_nameError" data-ajax-feedback="first_name"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">@lang('translation.Last_Name')</label>
                                        <input id="last_name" type="text" class="form-control last_name" name="last_name"
                                            value="{{ isset($model->last_name) && !empty($model->last_name) ? $model->last_name : '' }}"
                                            placeholder="@lang('translation.Enter_Last_Name')">
                                        <span class="invalid-feedback" id="last_nameError" data-ajax-feedback="last_name"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 ">
                                    <div class="mb-3">
                                        <label for="street" class="form-label">@lang('translation.Street')</label>
                                        <input id="street" type="text" class="form-control street" name="street"
                                            value="{{ isset($model->street) && !empty($model->street) ? $model->street : '' }}"
                                            placeholder="@lang('translation.Enter_Street')">
                                        <span class="invalid-feedback" id="streetError" data-ajax-feedback="street"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="house_number" class="form-label">@lang('translation.House_Number')</label>
                                        <input id="house_number" type="text" class="form-control house_number"
                                            name="house_number"
                                            value="{{ isset($model->house_number) && !empty($model->house_number) ? $model->house_number : '' }}"
                                            placeholder="@lang('translation.Enter_House_Number')">
                                        <span class="invalid-feedback" id="house_numberError"
                                            data-ajax-feedback="house_number" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="zipcode" class="form-label">@lang('translation.Zipcode')</label>
                                        <input id="zipcode" type="text" class="form-control zipcode" name="zipcode"
                                            value="{{ isset($model->zipcode) && !empty($model->zipcode) ? $model->zipcode : '' }}"
                                            placeholder="@lang('translation.Enter_Zip_Code')">
                                        <span class="invalid-feedback" id="zipcodeError" data-ajax-feedback="zipcode"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">@lang('translation.City')</label>
                                        <input id="city" type="text" class="form-control city" name="city"
                                            value="{{ isset($model->city) && !empty($model->city) ? $model->city : '' }}"
                                            placeholder="@lang('translation.Enter_City')">
                                        <span class="invalid-feedback" id="cityError" data-ajax-feedback="city"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if (isset($countries) && !empty($countries))
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="country" class="form-label">@lang('translation.Country')</label>
                                            <select id="country" type="text"
                                                class="form-select country @error('country') is-invalid @enderror"
                                                name="country">
                                                <option value="">@lang('translation.Select_country')</option>
                                                @foreach ($countries as $country)
                                                    <option
                                                        value="{{ $country }}" {{ isset($model->country) && !empty($model->country) && $model->country == $country ? 'selected' : '' }}>
                                                        {{ $country }}</option>
                                                @endforeach
                                            </select>
                                            <span class="invalid-feedback" id="countryError" data-ajax-feedback="country"
                                                role="alert"></span>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4 col-4">
                                            <div class="mb-3">
                                                <label for="vat" class="form-label">@lang('translation.VAT')</label>
                                                <div class="input-group">
                                                    <input id="vat" type="text" class="form-control vat" name="vat"
                                                        placeholder="@lang('translation.Enter_VAT')" value="{{ isset($model->vat) && !empty($model->vat) ? $model->vat : '' }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <span class="invalid-feedback" id="vatError" data-ajax-feedback="vat"
                                                    role="alert"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-8">
                                            <div class="mb-3">
                                                <label for="vat_number" class="form-label">@lang('translation.VAT_Number')</label>
                                                <input id="vat_number" type="text" class="form-control vat_number"
                                                    name="vat_number"
                                                    value="{{ isset($model->vat_number) && !empty($model->vat_number) ? $model->vat_number : '' }}"
                                                    placeholder="@lang('translation.Enter_VAT_Number')">
                                                <span class="invalid-feedback" id="vat_numberError"
                                                    data-ajax-feedback="vat_number" role="alert"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shop_start_time" class="form-label">
                                        @lang('translation.Shop_Opening_Time')</label>
                                        <div class="input-group" id="timepicker-input-shop_start_time">
                                            <input id="shop_start_time" type="text"
                                                class="form-control shop_start_time" name="shop_start_time"
                                                value="{{ isset($model->shop_start_time) && !empty($model->shop_start_time) ? $model->shop_start_time : '' }}"
                                                placeholder="@lang('translation.Enter_Shop_Opening_Time')">
                                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                            <span class="invalid-feedback" id="shop_start_timeError"
                                                data-ajax-feedback="shop_start_time" role="alert"></span>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="shop_end_time" class="form-label">
                                        @lang('translation.Shop_Close_Time')</label>
                                        <div class="input-group" id="timepicker-input-shop_end_time">
                                            <input id="shop_end_time" type="text" class="form-control shop_end_time"
                                                name="shop_end_time"
                                                value="{{ isset($model->shop_end_time) && !empty($model->shop_end_time) ? $model->shop_end_time : '' }}"
                                                placeholder="@lang('translation.Enter_Shop_Close_Time')">
                                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                            <span class="invalid-feedback" id="shop_end_timeError"
                                                data-ajax-feedback="shop_end_time" role="alert"></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">@lang('translation.Email')</label>
                                        <input id="email" type="email" class="form-control email" name="email"
                                            value="{{ isset($model->email) && !empty($model->email) ? $model->email : '' }}"
                                            placeholder="@lang('translation.Enter_Email')">
                                        <span class="invalid-feedback" id="emailError" data-ajax-feedback="email"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">@lang('translation.Phone')</label>
                                        <input id="phone" type="text" class="form-control phone" name="phone"
                                            value="{{ isset($model->phone) && !empty($model->phone) ? $model->phone : '' }}"
                                            placeholder="@lang('translation.Enter_Phone_Number')">
                                        <span class="invalid-feedback" id="phoneError" data-ajax-feedback="phone"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            @if (!$model->id)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">@lang('translation.Password')</label>
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password" placeholder="@lang('translation.Enter_Password')">
                                            <span class="invalid-feedback" id="passwordError"
                                                data-ajax-feedback="password" role="alert"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">
                                            @lang('translation.Confirm_Password')</label>
                                            <input id="password_confirmation" type="password"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                name="password_confirmation" placeholder="@lang('translation.Enter_Confirm_Password')">
                                            <span class="invalid-feedback" id="password_confirmationError"
                                                data-ajax-feedback="password_confirmation" role="alert"></span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="bank_name" class="form-label">@lang('translation.Bank_Name')</label>
                                        <input id="bank_name" type="text" class="form-control bank_name"
                                            name="bank_name"
                                            value="{{ isset($model->bank_name) && !empty($model->bank_name) ? $model->bank_name : '' }}"
                                            placeholder="@lang('translation.Enter_Bank_Name')">
                                        <span class="invalid-feedback" id="bank_nameError" data-ajax-feedback="bank_name"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="iban" class="form-label">@lang('translation.IBAN')</label>
                                        <input id="iban" type="text" class="form-control iban" name="iban"
                                            value="{{ isset($model->iban) && !empty($model->iban) ? $model->iban : '' }}"
                                            placeholder="@lang('translation.Enter_IBAN')">
                                        <span class="invalid-feedback" id="ibanError" data-ajax-feedback="iban"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="bic" class="form-label">@lang('translation.BIC')</label>
                                        <input id="bic" type="text" class="form-control bic" name="bic"
                                            value="{{ isset($model->bic) && !empty($model->bic) ? $model->bic : '' }}"
                                            placeholder="@lang('translation.Enter_BIC')">
                                        <span class="invalid-feedback" id="bicError" data-ajax-feedback="bic"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 form-footer text-end mt-3">
                                <a href="{{ route('dealers') }}" class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    @if ($model->id)
                                        @lang('translation.Update')
                                    @else
                                        @lang('translation.Submit')
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection



@section('script')
    <!-- Timepicker js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>

    <script>
        var listUrl = "{{ route('dealers') }}";
        var apiUrl = "{{ route('dealer.list') }}";
        var detailUrl = "{{ route('dealer.detail') }}";
        var deleteUrl = "{{ route('dealer.delete') }}";
        var updateStatusUrl = "{{ url('/dealer/updatefield') }}";
        var addUrl = $('#add-form').attr('action');
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealer.js') }}"></script>
@endsection
