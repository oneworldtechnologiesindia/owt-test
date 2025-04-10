@extends('layouts.master')

@section('title')
    @lang('translation.ads')
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
            @lang('translation.ads')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All_Ads')</h4>
                        <div>
                            <a type="button" class="btn btn-info waves-effect btn-label waves-light preview"
                                href="{{ route('ad.preview') }}"><i class="bx bxs-show label-icon"></i>
                                @lang('translation.Preview')</a>
                            <button type="button" class="btn btn-primary waves-effect btn-label waves-light add-new"><i
                                    class="bx bx-plus label-icon"></i> @lang('translation.Add_New')</button>
                        </div>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('translation.Title')</th>
                                    <th>@lang('translation.Size')</th>
                                    <th>@lang('translation.Ad_publish')</th>
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
                    <h5 class="modal-title" id="myLargeModalLabel"><span class="modal-lable-class">@lang('translation.Add')</span>
                        @lang('translation.ads')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-form" method="post" class="form-horizontal" action="#">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">
                        <div class="mb-3">
                            <label for="size" class="form-label">@lang('translation.Size')</label>
                            <select id="size" type="text"
                                class="form-select size @error('size') is-invalid @enderror" name="size">
                                <option value="">@lang('translation.Select_size')</option>
                                @foreach ($adBannerSizes as $key => $bannerSize)
                                    <option value="{{ $key }}" {{ old('size') == $bannerSize ? 'selected' : '' }}>
                                        {{ $bannerSize }}</option>
                                @endforeach
                            </select>

                            <span class="invalid-feedback" id="sizeError" data-ajax-feedback="size" role="alert"></span>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="control-label">@lang('translation.Ad_title')</label>
                            <input id="title" type="text" class="form-control" name="title"
                                placeholder="@lang('translation.Enter_Ad_title')">
                            <span class="invalid-feedback" id="titleError" data-ajax-feedback="title" role="alert"></span>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="control-label">@lang('translation.Ad_url')</label>
                            <input id="url" type="text" class="form-control" name="url"
                                placeholder="@lang('translation.Enter_Ad_url')">
                            <span class="invalid-feedback" id="urlError" data-ajax-feedback="url" role="alert"></span>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="control-label">@lang('translation.Ad_publish')</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input status" name="status" id="status_1"
                                        value="1" checked>
                                    <label class="form-check-label" for="status_1">@lang('translation.Yes')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input status" name="status" id="status_2"
                                        value="2">
                                    <label class="form-check-label" for="status_2">@lang('translation.No')</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="control-label">@lang('translation.Ad_Image')</label>
                            <input id="image" type="file" class="form-control imageUpload" name="image"
                                id="imageUploadLabel">
                            <input type="hidden" name="hidden_image" class="hidden_image">
                            <span class="invalid-feedback" id="imageError" data-ajax-feedback="image"
                                role="alert"></span>
                        </div>
                        <div class="mb-3 ">
                            <div class="gallery">
                                <img id="image-preview" src="{{ asset('assets/images/default-image.png') }}"
                                    alt="Preview" class="mt-2 img-size" width="200" height="100">
                            </div>
                        </div>
                        <div class="col-md-12 modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"
                                aria-label="Close">@lang('translation.Close')</button>
                            <button type="submit"
                                class="btn btn-success waves-effect waves-light">@lang('translation.Save_changes')</button>
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
        var apiUrl = "{{ route('ad.list') }}";
        var detailUrl = "{{ route('ad.detail') }}";
        var deleteUrl = "{{ route('ad.delete') }}";
        var addUrl = "{{ route('ad.addupdate') }}";
        let defaultimg = "{{ asset('assets/images/default-image.png') }}";
        let basepath = "{{ asset('storage/ad_image') }}/";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('ad.js') }}"></script>
@endsection
