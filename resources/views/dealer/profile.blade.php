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
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Profile')
        @endslot
    @endcomponent
    @php
        $loginUser = Auth::user();
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="profile_form" method="post" class="form-horizontal"
                        action="{{ route('dealer.updateProfile') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-container">
                            <div class="card-header-divider d-flex align-items-center justify-content-between mb-4">
                                <h4 class="card-title">
                                    @lang('translation.Company Details')
                                </h4>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="id" class="form-label">@lang('translation.Merchant ID')</label>
                                        <input id="id" type="text" class="form-control id" name="id"
                                            placeholder="@lang('translation.Your id')" readonly>
                                        <span class="invalid-feedback" id="idError" data-ajax-feedback="id"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">
                                            @lang('translation.Company_Name')</label>
                                        <input id="company_name" type="text" class="form-control company_name"
                                            name="company_name" placeholder="@lang('translation.Enter_Company_Name')" autofocus>
                                        <span class="invalid-feedback" id="company_nameError"
                                            data-ajax-feedback="company_name" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">@lang('translation.First_Name')</label>
                                        <input id="first_name" type="text" class="form-control first_name"
                                            name="first_name" placeholder="@lang('translation.Enter_First_Name')">
                                        <span class="invalid-feedback" id="first_nameError" data-ajax-feedback="first_name"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">@lang('translation.Last_Name')</label>
                                        <input id="last_name" type="text" class="form-control last_name" name="last_name"
                                            placeholder="@lang('translation.Enter_Last_Name')">
                                        <span class="invalid-feedback" id="last_nameError" data-ajax-feedback="last_name"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="street" class="form-label">@lang('translation.Street')</label>
                                        <input id="street" type="text" class="form-control street" name="street"
                                            placeholder="@lang('translation.Enter_Street')">
                                        <span class="invalid-feedback" id="streetError" data-ajax-feedback="street"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="house_number" class="form-label">@lang('translation.House_Number')</label>
                                        <input id="house_number" type="text" class="form-control house_number"
                                            name="house_number" placeholder="@lang('translation.Enter_House_Number')">
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
                                            placeholder="@lang('translation.Enter_Zip_Code')">
                                        <span class="invalid-feedback" id="zipcodeError" data-ajax-feedback="zipcode"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">@lang('translation.City')</label>
                                        <input id="city" type="text" class="form-control city" name="city"
                                            placeholder="@lang('translation.Enter_City')">
                                        <span class="invalid-feedback" id="cityError" data-ajax-feedback="city"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if (empty($loginUser->country))
                                    @if (isset($countries) && !empty($countries))
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="country" class="form-label">@lang('translation.Country')</label>
                                                <select id="country" type="text" class="form-select country"
                                                    name="country">
                                                    <option value="">@lang('translation.Select_country')</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country }}">{{ $country }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="invalid-feedback" id="countryError"
                                                    data-ajax-feedback="country" role="alert"></span>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                {{-- <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vat_number" class="form-label">@lang('translation.VAT_Number')</label>
                                        <input id="vat_number" type="text" class="form-control vat_number"
                                            name="vat_number" placeholder="@lang('translation.Enter_VAT_Number')">
                                        <span class="invalid-feedback" id="vat_numberError"
                                            data-ajax-feedback="vat_number" role="alert"></span>
                                    </div>
                                </div> --}}
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="document_file" class="form-label">
                                            @lang('translation.Business Registration PDF')</label>
                                        <input id="document_file" type="file" class="form-control document_file"
                                            name="document_file" title="@lang('translation.Business Registration PDF')" id="document_file">
                                        <span class="invalid-feedback" id="document_fileError"
                                            data-ajax-feedback="document_file" role="alert"></span>
                                    </div>
                                    <div class="view-pdf">
                                        <p>@lang('translation.Currunt PDF'): <a href="#" target="_blank"
                                                class="btn btn-sm btn-link waves-effect waves-light ms-1">
                                                @lang('translation.Business Registration PDF')</a></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="company_logo" class="form-label">@lang('translation.Company Logo')</label>
                                        <input id="company_logo" type="file" class="form-control company_logo"
                                            name="company_logo" title="@lang('translation.Company Logo')" id="company_logo">
                                        <span class="invalid-feedback" id="company_logoError"
                                            data-ajax-feedback="company_logo" role="alert"></span>
                                    </div>
                                    <div class="view-logo">
                                        <p>@lang('translation.Currunt Logo'): <a href="#" target="_blank"
                                                class="btn btn-sm btn-link waves-effect waves-light ms-1">
                                                @lang('translation.Company Logo')</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">@lang('translation.Gender')</label>
                                        <br>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input gender" name="gender"
                                                id="gender_0" value="0"
                                                {{ @$loginUser->gender == 0 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_0">@lang('translation.Male')</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input gender" name="gender"
                                                id="gender_1" value="1"
                                                {{ @$loginUser->gender == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_1">@lang('translation.Female')</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input  gender" name="gender"
                                                id="gender_2" value="2"
                                                {{ @$loginUser->gender == 2 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_2">@lang('translation.Other')</label>
                                        </div>

                                        <input type="hidden" id="gender" class="form-control gender" />
                                        <span class="invalid-feedback" id="genderError" data-ajax-feedback="gender"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-header-divider d-flex align-items-center justify-content-between my-4">
                                <h4 class="card-title">
                                    @lang('translation.Bank Details')
                                </h4>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="mb-3">
                                        <label for="bank_name" class="form-label">@lang('translation.Bank_Name')</label>
                                        <input id="bank_name" type="text" class="form-control bank_name"
                                            name="bank_name" placeholder="@lang('translation.Enter_Bank_Name')">
                                        <span class="invalid-feedback" id="bank_nameError" data-ajax-feedback="bank_name"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="mb-3">
                                        <label for="iban" class="form-label">@lang('translation.IBAN')</label>
                                        <input id="iban" type="text" class="form-control iban" name="iban"
                                            placeholder="@lang('translation.Enter_IBAN')">
                                        <span class="invalid-feedback" id="ibanError" data-ajax-feedback="iban"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
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
                            {{-- tax details --}}
                            <div class="card-header-divider d-flex align-items-center justify-content-between my-4">
                                <h4 class="card-title">
                                    @lang('translation.Tax Details')
                                </h4>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-2">
                                    <div class="mb-3">
                                        <label for="vat" class="form-label">@lang('translation.VAT')</label>
                                        <div class="input-group">
                                            <input id="vat" type="text" class="form-control vat" name="vat"
                                                placeholder="@lang('translation.Enter_VAT')">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <span class="invalid-feedback" id="vatError" data-ajax-feedback="vat"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6">
                                    <div class="mb-3">
                                        <label for="vat_number" class="form-label">@lang('translation.VAT_Number')</label>
                                        <input id="vat_number" type="text" class="form-control vat_number"
                                            name="vat_number" placeholder="@lang('translation.Enter_VAT_Number')">
                                        <span class="invalid-feedback" id="vat_numberError"
                                            data-ajax-feedback="vat_number" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            {{-- end tax details --}}
                            <div class="col-md-12 form-footer text-end mt-3">
                                <a href="{{ route('dealer.home') }}"
                                    class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    @lang('translation.Update')
                                </button>
                            </div>

                        </div>
                    </form>
                    <div class="mt-4" id="country-info">
                        <div class="alert alert-success d-block mb-0" role="alert">
                            <h5 class="m-0 text-capitalize text-primary">@lang('translation.Country'):
                                {{ $loginUser->country }}
                            </h5>
                        </div>
                    </div>
                    <div class="mt-4" id="contract-info">
                        <div class="alert alert-primary d-block mb-0" role="alert">
                            <h5 class="m-0 text-capitalize text-primary">@lang('translation.Contract Details'):</h5>
                            <div class="contract-dates">
                                <div class="dates-container mt-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="d-flex mb-0 align-items-center startdate"><i
                                                    class="bx bx-calendar bx-sm pe-2"></i>@lang('translation.Start_Date'): <span
                                                    class="ps-2 contractstart"></span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="d-flex mb-0 align-items-center enddate"><i
                                                    class="bx bx-calendar bx-sm pe-2"></i>@lang('translation.End_Date'): <span
                                                    class="ps-2 contractend"></span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="d-flex mb-0 align-items-center canceldate"><i
                                                    class="bx bx-calendar bx-sm pe-2"></i>@lang('translation.Cancel Date'): <span
                                                    class="ps-2 contractcancel"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="button-group">
                                <a href="javascrip:void(0);"
                                    class="btn btn-primary waves-effect btn-label waves-light withdraw-contract mt-3 me-3"><i
                                        class="bx bx-receipt label-icon"></i>
                                    @lang('translation.Withdraw Cancelation')</a>
                                <a href="javascrip:void(0);"
                                    class="btn btn-danger waves-effect btn-label waves-light cancel-contract mt-3"><i
                                        class="bx bx-receipt label-icon"></i>
                                    @lang('translation.Cancel Contract')</a>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4" id="subscription-info">
                        <div class="alert alert-primary d-block mb-0" role="alert">
                            <h5 class="m-0 text-capitalize text-primary">@lang('translation.Current Subscription Plan Details'):</h5>
                            <div class="contract-dates">
                                <div class="dates-container mt-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="d-flex mb-0 align-items-center startdate"><i
                                                    class="bx bx-calendar bx-sm pe-2"></i>@lang('translation.Start_Date'): <span
                                                    class="ps-2 subscription-start"></span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="d-flex mb-0 align-items-center enddate"><i
                                                    class="bx bx-calendar bx-sm pe-2"></i>@lang('translation.End_Date'): <span
                                                    class="ps-2 subscription-end"></span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="d-flex mb-0 align-items-center canceldate"><i
                                                    class="bx bx-calendar bx-sm pe-2"></i>@lang('translation.Cancel Date'): <span
                                                    class="ps-2 subscription-cancel"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="button-group">
                                <a href="{{ route('dealer.subscription') }}"
                                    class="btn btn-primary waves-effect btn-label waves-light change-subscription mt-3 me-3"><i
                                        class="bx bx-receipt label-icon"></i>
                                    @lang('translation.Change Subscription')</a>
                                {{-- <a href="javascrip:void(0);"
                                    class="btn btn-danger waves-effect btn-label waves-light cancel-subscription mt-3"><i
                                        class="bx bx-receipt label-icon"></i>
                                    @lang('translation.Cancel Subscription')</a> --}}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4" id="account-connect-info">
                        <div class="alert alert-warning d-block mb-0" role="alert">
                            <h5 class="m-0 text-capitalize text-primary">@lang('translation.Stripe Account Details'):</h5>

                            <div class="button-group">
                                <a href="{{ route('dealer.stripe.authorize-account') }}"
                                    class="btn btn-primary waves-effect btn-label waves-light connect-stripe-account mt-3 me-3"
                                    style="display:none">
                                    <i class="bx bx-receipt label-icon"></i>
                                    @lang('translation.Connect Stripe Account')
                                </a>
                                <span class="account-connect-status"></span>
                                {{-- @if ($model->stripe_account_status == 4 || $model->stripe_account_status == '')
                                    <a href="{{route('stripe.authorize-account')}}"
                                    class="btn btn-primary waves-effect btn-label waves-light connect-stripe-account mt-3 me-3">
                                        <i class="bx bx-receipt label-icon"></i>
                                        @lang('translation.Connect Stripe Account')
                                    </a>
                                @else
                                    @if ($model->stripe_account_status == 1)
                                    Account Connected
                                    @elseif ($model->stripe_account_status==2)
                                    Pending for activation.
                                    @elseif ($model->stripe_account_status==3)
                                    Account Restricted.
                                    @elseif ($model->stripe_account_status==5)
                                    Account Disabled.
                                    @else
                                    -
                                    @endif
                                @endif --}}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="mt-4" id="delete-account">
                        <div class="alert alert-danger d-block mb-0" role="alert">
                            <h5 class="m-0 text-capitalize text-primary">@lang('translation.Delete Account'):</h5>
                            <div class="button-group">
                                <button type="button"
                                    class="btn btn-danger waves-effect btn-label waves-light delete-account mt-3 me-3"
                                    data-bs-toggle="modal" data-bs-target="#confirm-delete-account-modal">
                                    <i class="bx bx-trash label-icon"></i>
                                    @lang('translation.Delete Account')
                                </button>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div> <!-- end col -->

    {{-- <!-- Confirm Delete Account Modal -->
    <div id="confirm-delete-account-modal" class="modal fade" tabindex="-1" aria-labelledby="confirmDeleteAccountLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteAccountLabel">@lang('translation.Delete Account')</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="confirm-delete-account-form-dealer"
                        action="{{ route('dealer.deleteAccount') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $loginUser->id }}">
                        <div class="mb-3">
                            <label for="delete_account" class="form-label">@lang('translation.Sure_Delete_Account')</label>
                            <input id="delete_account" type="text"
                                class="form-control @error('delete_account') is-invalid @enderror" name="delete_account"
                                autocomplete="off" placeholder='@lang('translation.Type_Delete_Account')' value="{{ old('delete_account') }}">
                            <span class="invalid-feedback" id="delete_accountError" role="alert"></span>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                @lang('translation.Cancel')
                            </button>
                            <button type="submit" class="btn btn-danger">
                                @lang('translation.Delete Account')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@section('script')
    <!-- Timepicker js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Inputmask js -->
    <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>

    <script type="text/javascript">
        var updateProfileUrl = "{{ route('dealer.updateProfile') }}";
        var profileDetailUrl = "{{ route('dealer.profileDetail') }}";
        var contractUpdateUrl = "{{ route('dealer.contractUpdate') }}";
        var please_type_delete_to_confirm = "{{ trans('translation.please_type_delete_to_confirm') }}";
        var loginRoute = "{{ route('login') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('profile_all.js') }}"></script>
@endsection
