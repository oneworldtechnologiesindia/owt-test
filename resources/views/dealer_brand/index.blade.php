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

    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Brand')
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All_Brands')</h4>
                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                                class="bx bx-plus label-icon"></i> @lang('translation.Add_New')</button>
                    </div>
                    <input type="hidden" name="id" id="id">
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>@lang('translation.Name')</th>
                                    <th>@lang('translation.Created_At')</th>
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
    <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Add')</span> @lang('translation.Brand')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-form" method="post" class="form-horizontal" action="#">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        @if (isset($brandData) && !empty($brandData))
                            <div class="mb-3">
                                <label for="brand_id" class="form-label">@lang('translation.Brand')</label>
                                <select name="brand_id[]" id="brand_id" class="form-control select2 select2-multiple"
                                    multiple>
                                    <option value="">@lang('translation.Select Brand')</option>
                                    @foreach ($brandData as $bkey => $blist)
                                        <option value="{{ $bkey }}">
                                            {{ $blist }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="brand_idError" data-ajax-feedback="brand_id"
                                    role="alert"></span>
                            </div>
                        @endif
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
    <!-- Select2 js -->
    <script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('dealer.dealer-brand.list') }}";
        var productList = "{{ route('dealer.dealer-brand.productList') }}";
        var deleteUrl = "{{ route('dealer.dealer-brand.delete') }}";
        var addUrl = "{{ route('dealer.dealer-brand.addupdate') }}";
        var getBrandListUrl = "{{ route('dealer.dealer-brand.getBrandList') }}";
        var type = "{{ trans('translation.Type') }}";
        var category = "{{ trans('translation.Category') }}";
        var product = "{{ trans('translation.Product') }}";
        var retail = "{{ trans('translation.Retail') }}";
        var url = "{{ trans('translation.URL') }}";
        var Status = "{{ trans('translation.Status') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealer_brand.js') }}"></script>
@endsection
