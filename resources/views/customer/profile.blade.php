@extends('layouts.master')

@section('title')
    @lang('translation.Profile')
@endsection

@section('css')
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">
@endsection


@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('customer.home') }}">@lang('translation.Dashboard')</a>
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
                    <h4 class="card-title mb-4">@lang('translation.Personal_Details')</h4>
                    <form id="profile_form" method="post" class="form-horizontal"
                        action="{{ route('customer.updateProfile') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="salutation" class="form-label w-100">@lang('translation.Salutation') </label>
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio"
                                            class="btn-check salutation @error('salutation') is-invalid @enderror"
                                            name="salutation" value="herr" id="salutation_herr" autocomplete="off"
                                            {{ old('salutation') == 'herr' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-dark" for="salutation_herr">
                                            @lang('translation.Herr')</label>

                                        <input type="radio"
                                            class="btn-check salutation @error('salutation') is-invalid @enderror"
                                            name="salutation" value="frau" id="salutation_frau" autocomplete="off"
                                            {{ old('salutation') == 'frau' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-dark" for="salutation_frau">
                                            @lang('translation.Frau')</label>

                                        <input type="radio"
                                            class="btn-check salutation @error('salutation') is-invalid @enderror"
                                            name="salutation" value="divers" id="salutation_divers" autocomplete="off"
                                            {{ old('salutation') == 'divers' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-dark" for="salutation_divers">
                                            @lang('translation.Divers')</label>

                                        <input type="radio"
                                            class="btn-check salutation @error('salutation') is-invalid @enderror"
                                            name="salutation" value="firma" id="salutation_firma" autocomplete="off"
                                            {{ old('salutation') == 'firma' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-dark" for="salutation_firma">
                                            @lang('translation.Firma')</label>
                                    </div>
                                    <span class="invalid-feedback d-block" id="salutationError"
                                        data-ajax-feedback="salutation" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id" class="form-label">@lang('translation.Kundennummer')</label>
                                    <input id="id" type="text" class="form-control id" name="id"
                                        placeholder="@lang('translation.Your id')" readonly>
                                    <span class="invalid-feedback" id="idError" data-ajax-feedback="id"
                                        role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row customer_field_add">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_company_name" class="form-label">
                                        @lang('translation.Company_Name')</label>
                                    <input id="customer_company_name" type="text"
                                        class="form-control customer_company_name @error('customer_company_name') is-invalid @enderror"
                                        name="customer_company_name" value="{{ old('customer_company_name') }}"
                                        placeholder="@lang('translation.Enter_Company_Name')">
                                    <span class="invalid-feedback" id="customer_company_nameError"
                                        data-ajax-feedback="customer_company_name" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_vat_number" class="form-label">@lang('translation.VAT_Number')</label>
                                    <input id="customer_vat_number" type="text"
                                        class="form-control customer_vat_number @error('customer_vat_number') is-invalid @enderror"
                                        name="customer_vat_number" value="{{ old('customer_vat_number') }}"
                                        placeholder="@lang('translation.Enter_VAT_Number')">
                                    <span class="invalid-feedback" id="customer_vat_numberError"
                                        data-ajax-feedback="customer_vat_number" role="alert"></span>
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
                            {{-- @if (empty($loginUser->country))
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
                                            <span class="invalid-feedback" id="countryError" data-ajax-feedback="country"
                                                role="alert"></span>
                                        </div>
                                    </div>
                                @endif
                            @else
                                    <input type="hidden" name="country" value="{{ $loginUser->country }}"/>
                            @endif --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="control-label">@lang('translation.Birth_Date')</label>
                                    <div class="input-group" id="birth_date_container">
                                        <input type="text" id="birth_date" name="birth_date" class="form-control"
                                            placeholder="@lang('translation.Enter_Birth_Date')" data-date-format="dd.mm.yyyy"
                                            data-date-container='#birth_date_container' data-provide="datepicker"
                                            data-date-autoclose="true">
                                        <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>

                                        <span class="invalid-feedback" id="birth_dateError"
                                            data-ajax-feedback="birth_date" role="alert"></span>
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
                        <div class="col-md-12 form-footer text-end mt-3">
                            <a href="{{ route('customer.home') }}"
                                class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                @lang('translation.Update')
                            </button>
                        </div>
                    </form>
                    <div class="mt-4" id="country-info">
                        <div class="alert alert-success d-block mb-0" role="alert">
                            <h5 class="m-0 text-capitalize text-primary">@lang('translation.Country'):
                                {{ $loginUser->country }}
                            </h5>
                        </div>
                    </div>
                    <div class="mt-4" id="delete-account">
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
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <!-- Confirm Delete Account Modal -->
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
                    <form method="POST" id="confirm-delete-account-form-customer"
                        action="{{ route('customer.deleteAccount') }}">
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
    </div>
@endsection

@section('script')
    <!-- Inputmask js -->
    <script src="{{ URL::asset('/assets/libs/inputmask/inputmask.min.js') }}"></script>
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript">
        var updateProfileUrl = "{{ route('customer.updateProfile') }}";
        var profileDetailUrl = "{{ route('customer.profileDetail') }}";
        var please_type_delete_to_confirm = "{{ trans('translation.please_type_delete_to_confirm') }}";
        var loginRoute = "{{ route('login') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('profile_all.js') }}"></script>
@endsection
