@extends('layouts.master')

@section('title')
     @lang('translation.Purchase Enquiry')
@endsection

@section('css')
    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">

    <!-- bootstrap touchspin Css -->
    <link href="{{ URL::asset('/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet"
        type="text/css">
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('customer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('li_2')
            <a href="{{ route('customer.enquiry') }}">@lang('translation.Purchase Enquiry')</a>
        @endslot
        @slot('title')
            @lang('translation.Create New')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">
                            @lang('translation.Search For Products')
                        </h4>
                    </div>
                    <div class="form-container">
                        <form id="purchase_enquiry_form" method="post" class="form" action="#"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $model->id }}">
                            <div class="product-filter-container">
                                <div class="row">
                                    @if (isset($allBrands) && !empty($allBrands) && count($allBrands) > 0)
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="brand_id" class="form-label">@lang('translation.Brand')</label>
                                                <select name="brand_id[]" id="brand_id"
                                                    class="form-control select2 select2-multiple" placeholder="@lang('translation.Filter Product By Brand')" multiple>
                                                    @foreach ($allBrands as $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($allProductType) && !empty($allProductType) && count($allProductType) > 0)
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="producttype_id" class="form-label">@lang('translation.Type')</label>
                                                <select name="producttype_id[]" id="producttype_id"
                                                    class="form-control select2 select2-multiple" placeholder="@lang('translation.Filter Product By Type')" multiple>
                                                    @foreach ($allProductType as $type)
                                                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    @if (isset($allProductCategory) && !empty($allProductCategory) && count($allProductCategory) > 0)
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="productcategory_id" class="form-label">
                                                @lang('translation.Category')</label>
                                                <select name="productcategory_id[]" id="productcategory_id"
                                                    class="form-control select2 select2-multiple" placeholder="@lang('translation.Filter Product By Category')" multiple>
                                                    @foreach ($allProductCategory as $category)
                                                        <option value="{{ $category->id }}">{{ $category->category_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="select-product-container mb-4">
                                            <div class="mb-3">
                                                <label for="product_id" class="form-label">@lang('translation.Product')</label>
                                                <select name="product_id[]" id="product_id" class="form-control select2 m-b-10" placeholder="@lang('translation.Select Product')">
                                                    <option></option>
                                                </select>
                                                <span class="invalid-feedback" id="product_idError"
                                                    data-ajax-feedback="product_id" role="alert"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light reset-filter d-block">@lang('translation.Reset_filter')</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="select-product-container mb-4">
                                        <div class="list-selected-products-container mt-5">
                                            <div class="card-header-divider mb-4">
                                                <h4 class="pb-2">@lang('translation.List of purchase enquiry products')</h4>
                                            </div>
                                            <div class="list-selected-products position-relative">
                                                <div class="notice-empty alert alert-warning d-block" role="alert">
                                                    <i class="mdi mdi-alert-outline me-2"></i>
                                                    @lang('translation.No any selected product')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="enquiry_description" class="form-label">
                                        @lang('translation.Purchase Note')</label>
                                        <textarea id="enquiry_description" class="form-control enquiry_description" name="enquiry_description"
                                            placeholder="@lang('translation.Enter Purchase Note')" rows="5"></textarea>
                                        <span class="invalid-feedback" id="enquiry_descriptionError"
                                            data-ajax-feedback="enquiry_description" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 form-footer text-end mt-3">
                                <a href="{{ route('customer.enquiry') }}" class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    @lang('translation.Submit')
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
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <!-- bootstrap touchspin js -->
    <script src="{{ URL::asset('/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('customer.enquiry.list') }}";
        var indexUrl = "{{ route('customer.enquiry') }}";
        var deleteUrl = "{{ route('customer.enquiry.delete') }}";
        var addUrl = "{{ route('customer.enquiry.addupdate') }}";
        var getProduct = "{{ route('customer.enquiry.getProduct') }}";
        var getProductInfo = "{{ route('customer.enquiry.getProductInfo') }}";
        var filterOptionsUrl = "{{ route('customer.enquiry.getFilterOptions') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ @asset('assets/libs/slimscroll/jquery.slimscroll.js') }}"></script>
    <script src="{{ addPageJsLink('purchase-enquiry.js') }}"></script>
@endsection
