@extends('layouts.master')

@section('title')
    @lang('translation.Appointment')
@endsection

@section('css')
    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Datepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css">
    <!-- Timepicker Css -->
    <link href="{{ URL::asset('assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet"
        type="text/css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('customer.home') }}">
                @lang('translation.Dashboard')</a>
        @endslot
        @slot('li_2')
            <a href="{{ route('customer.appointment') }}">@lang('translation.Appointment')</a>
        @endslot
        @slot('title')
            @if ($model->id)
                @lang('translation.Edit') : {{ $model->title }}
            @else
                @lang('translation.New')
            @endif
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <form id="add_form" method="post" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">@lang('translation.Search For Products')</h4>
                                    <div class="filter-option-container">
                                        @if (isset($allBrands) && !empty($allBrands) && count($allBrands) > 0)
                                            <div>
                                                <div class="mb-3">
                                                    <label for="brand_id" class="form-label">@lang('translation.Brand')</label>
                                                    <select name="brand_id[]" id="brand_id"
                                                        class="form-control select2 select2-multiple"
                                                        placeholder="@lang('translation.Filter Product By Brand')" data-allow-clear="false" multiple>
                                                        @foreach ($allBrands as $brand)
                                                            <option value="{{ $brand->id }}">
                                                                {{ $brand->brand_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="invalid-feedback error-brand_id" role="alert"></span>
                                                </div>
                                            </div>
                                        @endif
                                        @if (isset($allProductType) && !empty($allProductType) && count($allProductType) > 0)
                                            <div>
                                                <div class="mb-3">
                                                    <label for="producttype_id" class="form-label">
                                                        @lang('translation.Product Type')</label>
                                                    <select name="producttype_id[]" id="producttype_id"
                                                        class="form-control select2 select2-multiple"
                                                        placeholder="@lang('translation.Filter Product By Type')" data-allow-clear="false" multiple>
                                                        @foreach ($allProductType as $type)
                                                            <option value="{{ $type->id }}">
                                                                {{ $type->type_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        @if (isset($allProductCategory) && !empty($allProductCategory) && count($allProductCategory) > 0)
                                            <div>
                                                <div class="mb-3">
                                                    <label for="productcategory_id" class="form-label">
                                                        @lang('translation.Product Category')</label>
                                                    <select name="productcategory_id[]" id="productcategory_id"
                                                        class="form-control select2 select2-multiple"
                                                        placeholder="@lang('translation.Filter Product By Category')" data-allow-clear="false" multiple>
                                                        @foreach ($allProductCategory as $category)
                                                            <option value="{{ $category->id }}">
                                                                {{ $category->category_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="mb-3">
                                                <label for="product_id" class="form-label">@lang('translation.Product')</label>
                                                <select name="product_id" id="product_id"
                                                    class="form-control select2 m-b-10" data-allow-clear="true"
                                                    placeholder="@lang('translation.Select Product')">
                                                    <option></option>
                                                </select>
                                                <span class="invalid-feedback error-product_id" role="alert"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <button type="reset"
                                                class="btn btn-primary reset-filter waves-effect reset-filter w-100">@lang('translation.Reset_filter')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">@lang('translation.Selected products')</h4>
                                    <div class="selected-products-container mb-3 ">
                                        <div class="card-header-divider mb-3">
                                            <h5 class="pb-2"></h5>
                                        </div>
                                        <div class="notice-empty alert alert-warning d-block" role="alert">
                                            <i class="mdi mdi-alert-outline me-2"></i>
                                            @lang('translation.No any selected product')
                                        </div>
                                        <div class="selected-products-list d-none"></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input appo_type" name="appo_type"
                                                id="appo_type_1" value="0" checked>
                                            <label class="form-check-label" for="appo_type_1">@lang('translation.AppoTypeAppointment')</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input appo_type" name="appo_type"
                                                id="appo_type_2" value="1">
                                            <label class="form-check-label" for="appo_type_2">@lang('translation.AppoTypeZoomMeeting')</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="appt_custom_box col-md-7 row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="appo_date" class="control-label">@lang('translation.Date')</label>
                                                    <input type="hidden" name="appo_date" id="appo_date">
                                                    <div class="input-group" id="appo_date_container">
                                                        <span class="invalid-feedback error-appo_date"
                                                            role="alert"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="appo_date"
                                                        class="control-label">@lang('translation.Available_Time_Slots')</label>
                                                    <input type="hidden" name="appo_time" id="appo_time">
                                                    <div class="input-group" id="appo_time_container">
                                                        <span class="invalid-feedback error-appo_date"
                                                            role="alert"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mb-3 dealer_container">
                                                <label for="dealer_id" class="control-label">@lang('translation.Dealer')</label>
                                                <select name="dealer_id" id="dealer_id"
                                                    class="form-control dealer_id select2 select2-multiple"
                                                    placeholder="@lang('translation.Select_dealer')" data-allow-clear="true">
                                                    <option value=""></option>
                                                </select>
                                                <span class="invalid-feedback error-dealer_id" role="alert"></span>
                                            </div>
                                            <div>
                                                <label for="note" class="control-label">@lang('translation.Note')</label>
                                                <textarea id="note" class="form-control note" name="note" placeholder="@lang('translation.Enter Note')" rows="5"></textarea>
                                                <span class="invalid-feedback error-note" role="alert"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 form-footer text-end mt-3">
                                        <a href="{{ route('customer.home') }}"
                                            class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                                        <button type="reset"
                                            class="btn btn-primary reset-filter waves-effect reset-form">
                                            @lang('translation.Reset_filter')</button>
                                        <button type="submit" class="btn btn-success waves-effect waves-light">
                                            @if ($model->id)
                                                @lang('translation.Update')
                                            @else
                                                @lang('translation.Submit')
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- end col -->
    </div>
@endsection

@section('script')
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <!-- Datepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- Timepicker Css -->
    <script src="{{ URL::asset('assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script>
        var getDealerOfBrandUrl = "{{ route('customer.getDealerOfBrand') }}";
        var addAppointmentUrl = "{{ route('customer.appointment.addupdate') }}";
        var indexUrl = "{{ route('customer.appointment') }}";
        var getProductsUrl = "{{ route('customer.appointment.products') }}";
        var getProduct = "{{ route('customer.enquiry.getProduct') }}";
        var filterOptionsUrl = "{{ route('customer.enquiry.getFilterOptions') }}";
        var select_product = "@lang('translation.Select Product')";
        var getTimePickerUrl = "{{ route('customer.appointment.getTimePickerData') }}"
    </script>
@endsection

@section('script-bottom')
    <script src="{{ @asset('assets/libs/slimscroll/jquery.slimscroll.js') }}"></script>
    <script src="{{ addPageJsLink('customer_appointment.js') }}"></script>
@endsection
