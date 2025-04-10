@extends('layouts.master')

@section('title')
    @lang('translation.Customer')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">

    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Customer')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All Customer')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                                class="bx bx-plus label-icon"></i>@lang('translation.Add_New')</button>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('translation.Name')</th>
                                    <th>@lang('translation.Phone')</th>
                                    <th>@lang('translation.Email')</th>
                                    <th>@lang('translation.Status')</th>
                                    <th>@lang('translation.Created_At')</th>
                                    <th>@lang('translation.Actions')</th>
                                    <th>@lang('translation.Updated At')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <!-- Page Models -->
    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Add')</span> @lang('translation.Customer')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-form" method="post" class="form-horizontal" action="{{ route('customer.addupdate') }}">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        <div class="mb-3">
                            <label for="salutation" class="form-label w-100">@lang('translation.Salutation')</label>
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check salutation @error('salutation') is-invalid @enderror"
                                    name="salutation" value="herr" id="salutation_herr" autocomplete="off"
                                    {{ old('salutation') == 'herr' ? 'checked' : '' }}>
                                <label class="btn btn-outline-dark" for="salutation_herr">
                                @lang('translation.Herr')</label>

                                <input type="radio" class="btn-check salutation @error('salutation') is-invalid @enderror"
                                    name="salutation" value="frau" id="salutation_frau" autocomplete="off"
                                    {{ old('salutation') == 'frau' ? 'checked' : '' }}>
                                <label class="btn btn-outline-dark" for="salutation_frau">@lang('translation.Frau')</label>

                                <input type="radio" class="btn-check salutation @error('salutation') is-invalid @enderror"
                                    name="salutation" value="divers" id="salutation_divers" autocomplete="off"
                                    {{ old('salutation') == 'divers' ? 'checked' : '' }}>
                                <label class="btn btn-outline-dark" for="salutation_divers">
                                @lang('translation.Divers')</label>

                                <input type="radio" class="btn-check salutation @error('salutation') is-invalid @enderror"
                                    name="salutation" value="firma" id="salutation_firma" autocomplete="off"
                                    {{ old('salutation') == 'firma' ? 'checked' : '' }}>
                                <label class="btn btn-outline-dark" for="salutation_firma">
                                @lang('translation.Firma')</label>
                            </div>
                            <span class="invalid-feedback d-block" id="salutationError" data-ajax-feedback="salutation"
                                role="alert"></span>
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
                                        name="first_name" value="{{ old('first_name') }}"
                                        placeholder="@lang('translation.Enter_First_Name')">
                                    <span class="invalid-feedback" id="first_nameError" data-ajax-feedback="first_name"
                                        role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">@lang('translation.Last_Name')</label>
                                    <input id="last_name" type="text" class="form-control last_name" name="last_name"
                                        value="{{ old('last_name') }}" placeholder="@lang('translation.Enter_Last_Name')">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birth_date" class="control-label">@lang('translation.Birth_Date')</label>
                                    <div class="input-group" id="birth_date_container">
                                        <input type="text" id="birth_date" name="birth_date" class="form-control"
                                            value="{{ old('birth_date') }}" placeholder="@lang('translation.Enter_Birth_Date')"
                                            data-date-format="dd.mm.yyyy" data-date-container='#birth_date_container'
                                            data-provide="datepicker" data-date-autoclose="true">
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
                                        value="{{ old('email') }}" placeholder="@lang('translation.Enter_Email')">
                                    <span class="invalid-feedback" id="emailError" data-ajax-feedback="email"
                                        role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">@lang('translation.Phone')</label>
                                    <input id="phone" type="text" class="form-control phone" name="phone"
                                        value="{{ old('phone') }}" placeholder="@lang('translation.Enter_Phone_Number')">
                                    <span class="invalid-feedback" id="phoneError" data-ajax-feedback="phone"
                                        role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row password_div">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">@lang('translation.Password')</label>
                                    <input id="password" type="password" class="form-control" name="password"
                                        placeholder="@lang('translation.Enter_Password')">
                                    <span class="invalid-feedback" id="passwordError" data-ajax-feedback="password"
                                        role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">@lang('translation.Confirm_Password')</label>
                                    <input id="password_confirmation" type="password" class="form-control"
                                        name="password_confirmation" placeholder="@lang('translation.Enter_Confirm_Password')">
                                    <span class="invalid-feedback" id="password_confirmationError"
                                        data-ajax-feedback="password_confirmation" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                aria-label="Close">@lang('translation.Close')</button>
                            <button type="submit" class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('customer.list') }}";
        var detailUrl = "{{ route('customer.detail') }}";
        var deleteUrl = "{{ route('customer.delete') }}";
        var updateStatusUrl = "{{ url('/admin/customer/updatefield') }}";
        var addUrl = $('#add-form').attr('action');
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('customer.js') }}"></script>
@endsection
