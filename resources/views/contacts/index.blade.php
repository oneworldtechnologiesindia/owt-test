@extends('layouts.master')

@section('title')
    @lang('translation.Brands')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Contacts')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All_Contact')</h4>
                        <div class="button-multiple">
                            <button type="button" class="btn btn-primary waves-effect btn-label waves-light me-3 import_contacts">
                                <i class="bx bx-import label-icon"></i>
                                @lang('translation.import_contacts')
                            </button>
                            <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new" >
                                <i class="bx bx-plus label-icon"></i>
                                @lang('translation.Add_New')
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('translation.Name')</th>
                                    <th>@lang('translation.Surname')</th>
                                    <th>@lang('translation.Contact_Type')</th>
                                    <th>@lang('translation.salutation')</th>
                                    <th>@lang('translation.company_name')</th>
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
    {{-- Create Modal --}}
    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Add')</span>
                        @lang('translation.Contact')</h5>
                        <button data-bs-dismiss="modal" id="modalClose" style="display: none;"></button>
                    <button type="button" class="btn-close closeContactmodal"  aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-form" method="post" class="form-horizontal" action="#">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        <div class="row">
                            @if (isset($contact_types) && !empty($contact_types))
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_type" class="form-label">@lang('translation.Contact_type')</label>
                                        <select id="contact_type" type="text"class="form-select contact_type" name="contact_type">
                                            <option value="">@lang('translation.Select_contact_type')</option>
                                            @foreach ($contact_types as $key => $contacts)
                                                <option value="{{ $key }}">
                                                    {{ $contacts }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="contact_typeError" data-ajax-feedback="contact_type" role="alert"></span>
                                    </div>
                                </div>
                            @endif
                            @if (isset($salutations) && !empty($salutations))
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="salutation" class="form-label">@lang('translation.salutation')</label>
                                        <select id="salutation" type="text"class="form-select salutation" name="salutation">
                                            <option value="">@lang('translation.Select_salutation')</option>
                                            @foreach ($salutations as $key =>$salutations_data)
                                                <option value="{{ $key }}">
                                                    {{ $salutations_data }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="salutationError" data-ajax-feedback="salutation" role="alert"></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company" class="form-label">@lang('translation.company_name')</label>
                                    <input id="company" type="text" class="form-control company" name="company" placeholder="@lang('translation.Enter_company_name')">
                                    <span class="invalid-feedback" id="companyError" data-ajax-feedback="company" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">@lang('translation.Email')</label>
                                    <input id="email" type="text" class="form-control email" name="email" placeholder="@lang('translation.Enter_Email')">
                                    <span class="invalid-feedback" id="emailError" data-ajax-feedback="email" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">@lang('translation.Name')</label>
                                    <input id="name" type="text" class="form-control name" name="name" placeholder="@lang('translation.Enter_Name')">
                                    <span class="invalid-feedback" id="nameError" data-ajax-feedback="name" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="surname" class="form-label">@lang('translation.Surname')</label>
                                    <input id="surname" type="text" class="form-control surname" name="surname" placeholder="@lang('translation.Enter_Surname')">
                                    <span class="invalid-feedback" id="surnameError" data-ajax-feedback="surname" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="street" class="form-label">@lang('translation.Street')</label>
                                    <input id="street" type="text" class="form-control street" name="street" placeholder="@lang('translation.Enter_Street')">
                                    <span class="invalid-feedback" id="streetError" data-ajax-feedback="street" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="street_nr" class="form-label">@lang('translation.nr_street')</label>
                                    <input id="street_nr" type="text" class="form-control street_nr" name="street_nr" placeholder="@lang('translation.Enter_nr_street')">
                                    <span class="invalid-feedback" id="street_nrError" data-ajax-feedback="street_nr" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="zipcode" class="form-label">@lang('translation.Zipcode')</label>
                                    <input id="zipcode" type="text" class="form-control zipcode" name="zipcode" placeholder="@lang('translation.Enter_Zip_Code')">
                                    <span class="invalid-feedback" id="zipcodeError" data-ajax-feedback="zipcode" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telephone" class="form-label">@lang('translation.telephone')</label>
                                    <input id="telephone" type="text" class="form-control telephone" name="telephone" placeholder="@lang('translation.Enter_telephone')">
                                    <span class="invalid-feedback" id="telephoneError" data-ajax-feedback="telephone" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">@lang('translation.City')</label>
                                    <input id="city" type="text" class="form-control city" name="city" placeholder="@lang('translation.Enter_City')">
                                    <span class="invalid-feedback" id="cityError" data-ajax-feedback="city" role="alert"></span>
                                </div>
                            </div>
                            @if (isset($countries) && !empty($countries))
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="country" class="form-label">@lang('translation.Country')</label>
                                        <select id="country" type="text" class="form-select country" name="country">
                                            <option value="">@lang('translation.Select_country')</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country }}">
                                                    {{ $country }}</option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="countryError" data-ajax-feedback="country" role="alert"></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="note" class="form-label">@lang('translation.Note')</label>
                                    <textarea id="note" class="form-control note" name="note" placeholder="@lang('translation.Enter Note')"></textarea>
                                    <span class="invalid-feedback" id="noteError" data-ajax-feedback="note" role="alert"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 modal-footer">
                            <button type="button" class="btn btn-default waves-effect closeContactmodal"
                            aria-label="Close">@lang('translation.Close')</button>
                            <button type="submit"
                                class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- View Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span>@lang('translation.Contact_information')</span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-centered mb-0" id="information">
                                <tbody>
                                    <tr>
                                        <th width="30%">@lang('translation.Contact_type')</th>
                                        <td><span class="contact_type"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.salutation')</th>
                                        <td><span class="salutation"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.company_name')</th>
                                        <td><span class="company"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.Email')</th>
                                        <td><span class="email"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.Name')</th>
                                        <td><span class="name"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.Surname')</th>
                                        <td><span class="surname"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.Street')</th>
                                        <td><span class="street"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.nr_street')</th>
                                        <td><span class="street_nr"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.Zipcode')</th>
                                        <td><span class="zipcode"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.telephone_number')</th>
                                        <td><span class="telephone"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.City')</th>
                                        <td><span class="city"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.Country')</th>
                                        <td><span class="country"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.Note')</th>
                                        <td><span class="note"></span></td>
                                    </tr>
                                    <tr>
                                        <th width="30%">@lang('translation.Created_At')</th>
                                        <td><span class="created_at"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal" aria-label="Close">@lang('translation.Close')</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Export Modal --}}
    <div id="import-contact-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Add')</span> @lang('translation.Contacts')</h5>
                    <a class="btn btn-primary mx-xl-3" href="{{ asset('/assets/sample-file/contact_template_csv.csv') }}">@lang('translation.Download_sample_file')</a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="import-contact-form" method="post" class="form-horizontal" action="#">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        <div class="mb-3">
                            <label for="contact_csv" class="control-label">@lang('translation.CSV File') : </label>
                            <input id="contact_csv" type="file" class="form-control" name="contact_csv">
                            <span class="invalid-feedback" id="contact_csvError" data-ajax-feedback="contact_csv"
                                role="alert"></span>
                        </div>
                        <div class="col-md-12 modal-footer">
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
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('dealer.contacts.list') }}";
        var detailUrl = "{{ route('dealer.contacts.detail') }}";
        var deleteUrl = "{{ route('dealer.contacts.delete') }}";
        var addUrl = "{{ route('dealer.contacts.addupdate') }}";
        var importUrl = "{{ route('dealer.contacts.import.csv') }}";
        var viewModalId = "#viewModal";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('contacts.js') }}"></script>
@endsection
