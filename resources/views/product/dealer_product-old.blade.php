
@extends('layouts.master')

@section('title')
     @lang('translation.Products')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />

    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Products')
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-4">
                        <h4 class="card-title">@lang('translation.All_Products')</h4>
                    </div>
                    <div class="product-filter-form-container mb-5">
                        <form id="filter-product-form" method="post" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="brand_id" class="control-label">@lang('translation.Filter By Brand')</label>
                                        <select name="brand_id[]" id="brand_id"
                                            class="form-control select2 select2-multiple" placeholder="@lang('translation.Select Brand')"
                                            multiple>
                                            @if (isset($allBrands) && !empty($allBrands) && count($allBrands) > 0)
                                                @foreach ($allBrands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback error-brand_id"
                                            role="alert"><strong></strong></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="producttype_id" class="control-label">
                                            @lang('translation.Filter By Type')</label>
                                        <select name="producttype_id[]" id="producttype_id"
                                            class="form-control select2 select2-multiple" placeholder="@lang('translation.Select Type')"
                                            multiple>
                                            @if (isset($allProductType) && !empty($allProductType) && count($allProductType) > 0)
                                                @foreach ($allProductType as $type)
                                                    <option value="{{ $type->id }}">{{ $type->type_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback error-producttype_id"
                                            role="alert"><strong></strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productcategory_id" class="control-label">
                                        @lang('translation.Filter By Category')</label>
                                        <select name="productcategory_id[]" id="productcategory_id"
                                            class="form-control select2 select2-multiple" placeholder="@lang('translation.Select_Category')"
                                            multiple>
                                            @if (isset($allProductCategory) && !empty($allProductCategory) && count($allProductCategory) > 0)
                                                @foreach ($allProductCategory as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->category_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback error-productcategory_id"
                                            role="alert"><strong></strong></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_id" class="control-label">
                                        @lang('translation.Filter By Product')</label>
                                        <select name="product_id[]" id="product_id"
                                            class="form-control select2 select2-multiple" placeholder="@lang('translation.Select Product')"
                                            multiple>
                                            @if (isset($allProducts) && !empty($allProducts) && count($allProducts) > 0)
                                                @foreach ($allProducts as $product)
                                                    <option value="{{ $product->id }}">
                                                        {{ $product->product_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback error-product_id"
                                            role="alert"><strong></strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productconnection_id" class="control-label">
                                            @lang('translation.Filter By Connections')</label>
                                        <select name="productconnection_id[]" id="productconnection_id"
                                            class="form-control select2 select2-multiple" placeholder="@lang('translation.Select Connections')"
                                            multiple>
                                            @if (isset($allProductConnections) && !empty($allProductConnections) && count($allProductConnections) > 0)
                                                @foreach ($allProductConnections as $connection)
                                                    <option value="{{ $connection->id }}">
                                                        {{ $connection->connection_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback error-productconnection_id"
                                            role="alert"><strong></strong></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productexecution_id" class="control-label">
                                            @lang('translation.Filter By Execution')</label>
                                        <select name="productexecution_id[]" id="productexecution_id"
                                            class="form-control select2 select2-multiple" placeholder="@lang('translation.Select Execution')"
                                            multiple>
                                            @if (isset($allProductExecutions) && !empty($allProductExecutions) && count($allProductExecutions) > 0)
                                                @foreach ($allProductExecutions as $execution)
                                                    <option value="{{ $execution->id }}">
                                                        {{ $execution->execution_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback error-productexecution_id"
                                            role="alert"><strong></strong></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="productattributes_id" class="control-label">
                                            @lang('translation.Filter By Attribute')</label>
                                        <select name="productattributes_id[]" id="productattributes_id"
                                            class="form-control select2 select2-multiple" placeholder="@lang('translation.Select Attribute')"
                                            multiple>
                                            @if (isset($attributes) && !empty($attributes) && count($attributes) > 0)
                                                @foreach ($attributes as $attribute => $attributes_name)
                                                    <option value="{{ $attribute }}">
                                                        {{ $attributes_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value=""></option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback error-productattributes_id"
                                            role="alert"><strong></strong></span>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>
                            <div class="mb-3">
                                <a href="javascript:void(0)"
                                    class="btn btn-primary waves-effect waves-light reset-filter d-block">
                                    @lang('translation.Reset_filter')</a>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="product_list_table"
                            class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>@lang('translation.Brand')</th>
                                    <th>@lang('translation.Type')</th>
                                    <th>@lang('translation.Category')</th>
                                    <th>@lang('translation.Product')</th>
                                    <th>@lang('translation.Retail')</th>
                                    <th>@lang('translation.URL')</th>
                                    <th>@lang('translation.Actions')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

    <!-- Page Models -->
    <div id="add-product-attribute-modal" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">
                        @lang('translation.Product Attribute Details')</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="main-box d-none">
                        <div class="row main-row-product-attributes sub-box">
                            <div class="col-lg-3 connection-col product-attr-col" data-displaycol="true">
                                <div class="mb-3">
                                    <label for="product_connection" class="control-label">@lang('translation.CONNECTIONS')</label>
                                    <select class="form-control select2 product_connection"
                                        name="product_attribute[0][connection]" id="product_connection"
                                        placeholder="@lang('translation.Select connection')">
                                        <option></option>
                                    </select>
                                    <span class="invalid-feedback" id="product_connectionError"
                                        data-ajax-feedback="product_connection" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-3 execution-col product-attr-col" data-displaycol="true">
                                <div class="mb-3">
                                    <label for="product_execution" class="control-label">@lang('translation.
                                        EXECUTION')</label>
                                    <select class="form-control select2 product_execution"
                                        name="product_attribute[0][execution]" id="product_execution"
                                        placeholder="@lang('translation.Select Execution')">
                                        <option></option>
                                    </select>
                                    <span class="invalid-feedback" id="product_executionError"
                                        data-ajax-feedback="product_execution" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-5 attribute-col product-attr-col">
                                <div class="mb-3">
                                    <label for="product_attribute" class="control-label">@lang('translation.Attributes')</label>
                                    <select class="form-control select2 product_attribute"
                                        name="product_attribute[0][attribute]" id="product_attribute"
                                        placeholder="@lang('translation.Select Attribute')">
                                    </select>
                                    <span class="invalid-feedback" id="product_attributeError"
                                        data-ajax-feedback="product_attribute" role="alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <button class="btn btn-danger delete"><i class="bx bx-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <form id="add-product-attribute-form" method="post" class="form-horizontal" action="#">
                        @csrf
                        <div id="add_error_message"></div>
                        <input type="hidden" name="product_id" id="attribute_product_id">

                        <div class="multiple-product-attributes">

                        </div>
                        <div class="notice-empty-row alert alert-warning d-block" role="alert">
                            <input type="hidden" name="empty_attribute" value="true" class="empty_attribute"
                                id="empty_attribute">
                            <i class="mdi mdi-alert-outline me-2"></i>
                            @lang('translation.Please add new row for add attributes.')
                        </div>
                        <div class="col-md-12 modal-footer justify-content-between">
                            <div class="left-buttons">
                                <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-row"
                                    id="add"><i class="bx bx-plus label-icon"></i>@lang('translation.Add Row')</button>
                            </div>
                            <div class="right-buttons">
                                <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                    aria-label="Close">@lang('translation.Close')</button>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    @lang('translation.Save_changes')</button>
                            </div>
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

    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('dealer.getDealerProduct') }}";
        var detailUrl = "{{ route('dealer.getDealerProductAttributes') }}";
        var addUrl = "{{ route('dealer.addDealerProductAttributes') }}";
        var filterOptionsUrl = "{{ route('dealer.getFilterOptions') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealer_product.js') }}"></script>
@endsection
