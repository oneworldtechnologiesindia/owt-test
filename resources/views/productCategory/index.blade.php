@extends('layouts.master')

@section('title')
     @lang('translation.Product Categories')
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
        @slot('li_2')
            <a href="{{ route('product') }}">@lang('translation.Product')</a>
        @endslot
        @slot('title')
            @lang('translation.Category')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title"> @lang('translation.All Categories')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                                class="bx bx-plus label-icon"></i> @lang('translation.Add_New')</button>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('translation.Name')</th>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Add')</span> @lang('translation.Category')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-form" method="post" class="form-horizontal" action="#">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        <div class="mb-3">
                            <label for="category_name" class="control-label">@lang('translation.Category')</label>
                            <input id="category_name" type="text" class="form-control" name="category_name"
                                placeholder="@lang('translation.Enter Category')">
                            <span class="invalid-feedback" id="category_nameError" data-ajax-feedback="category_name"
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
        var apiUrl = "{{ route('product-category.list') }}";
        var detailUrl = "{{ route('product-category.detail') }}";
        var deleteUrl = "{{ route('product-category.delete') }}";
        var addUrl = "{{ route('product-category.addupdate') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('productCategory.js') }}"></script>
@endsection
