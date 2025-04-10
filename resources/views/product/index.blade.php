@extends('layouts.master')

@section('title')
     @lang('translation.Products')
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
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Product')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All_Products')</h4>
                        <div class="button-multiple">
                            <button type="button"
                                class="btn btn-primary waves-effect btn-label waves-light me-3 import_product"><i
                                    class="bx bx-import label-icon"></i> @lang('translation.Import Product')</button>
                            <a href="{{ route('product.create') }}"
                                class="btn btn-primary waves-effect btn-label waves-light"><i
                                    class="bx bx-plus label-icon"></i>
                               @lang('translation.Add_New')</a>
                        </div>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('translation.Brand')</th>
                                    <th>@lang('translation.Type')</th>
                                    <th>@lang('translation.Category')</th>
                                    <th>@lang('translation.Product')</th>
                                    <th>@lang('translation.Retail')</th>
                                    <th>@lang('translation.URL')</th>
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
    <div id="import-product-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Add')</span> @lang('translation.Product')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="import-product-form" method="post" class="form-horizontal" action="#">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        <div class="mb-3">
                            <label for="product_csv" class="control-label">@lang('translation.CSV File') : <a download=""
                                    href="{{ asset('/assets/sample-file/product_template_xlsx.xlsx') }}">@lang('translation.Template')</a></label>
                            <input id="product_csv" type="file" class="form-control" name="product_csv">
                            <span class="invalid-feedback" id="product_csvError" data-ajax-feedback="product_csv"
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
        var apiUrl = "{{ route('product.list') }}";
        var importUrl = "{{ route('product.import') }}";
        var editUrl = "{{ route('product.edit') }}";
        var viewUrl = "{{ route('product.view') }}";
        var detailUrl = "{{ route('product.detail') }}";
        var deleteUrl = "{{ route('product.delete') }}";
        var addUrl = "{{ route('product.addupdate') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('product.js') }}"></script>
@endsection
