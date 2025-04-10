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
            <div class="form-container">
                <form id="purchase_enquiry_form" method="post" class="form" action="#" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $model->id }}">
                    <div class="row">
                        <div class="col-lg-3">
                            @component('components.customerProductFilter',['allBrands'=>$allBrands, 'allProductType' => $allProductType, 'allProductCategory' => $allProductCategory])
                            @endcomponent
                        </div>
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('translation.List of purchase enquiry products')</h4>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="select-product-container mb-4">
                                                <div class="list-selected-products-container mt-3">
                                                    <div class="list-selected-products position-relative">
                                                        <div class="notice-empty alert alert-warning d-block"
                                                            role="alert">
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
                                                    placeholder="@lang('translation.Enter Purchase Note')" rows="4"></textarea>
                                                <span class="invalid-feedback" id="enquiry_descriptionError"
                                                    data-ajax-feedback="enquiry_description" role="alert"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 form-footer text-end mt-3">
                                        <a href="{{ route('customer.enquiry') }}"
                                            class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                                        <button type="submit" class="btn btn-success waves-effect waves-light">
                                            @lang('translation.Submit')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- end row -->
                    </div>
                </form>
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
