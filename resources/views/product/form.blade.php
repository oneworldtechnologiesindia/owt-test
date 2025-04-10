@extends('layouts.master')

@section('title')
     @lang('translation.Product')
@endsection

@section('css')
    <!-- Select2 Css -->
    <link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
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
            @if ($model->id)
                @lang('translation.Edit Product')
            @else
                @lang('translation.Add_New')
            @endif
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">
                            @if ($model->id)
                                @lang('translation.Edit Product') : {{ $model->product_name }}
                            @else
                                @lang('translation.Add New Product')
                            @endif
                        </h4>
                    </div>
                    <div class="form-container">
                        <form id="add_product_form" method="post" class="form form-horizontal" action="#"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $model->id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="brand_id" class="form-label">@lang('translation.Brand')</label>
                                        <select name="brand_id" id="brand_id" class="form-control">
                                            <option value="">@lang('translation.Select Brand')</option>
                                            @foreach ($brandData as $bkey => $blist)
                                                @php
                                                    $bselected = $bkey == $model->brand_id ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $bkey }}" {{ $bselected }}>
                                                    {{ $blist }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="brand_idError" data-ajax-feedback="brand_id"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type_id" class="form-label">@lang('translation.Product Type')</label>
                                        <select name="type_id" id="type_id" class="form-control">
                                            <option value="">@lang('translation.Select Product Type')</option>
                                            @foreach ($productType as $pkey => $plist)
                                                @php
                                                    $tselected = $pkey == $model->type_id ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $pkey }}" {{ $tselected }}>
                                                    {{ $plist }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="type_idError" data-ajax-feedback="type_id"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">@lang('translation.Product Category')</label>
                                        <select name="category_id" id="category_id" class="form-control">
                                            <option value="">@lang('translation.Select Product Category')</option>
                                            @foreach ($productCategory as $ckey => $clist)
                                                @php
                                                    $cselected = $ckey == $model->category_id ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $ckey }}" {{ $cselected }}>
                                                    {{ $clist }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="category_idError"
                                            data-ajax-feedback="category_id" role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_name" class="form-label">@lang('translation.Product Name')</label>
                                        <input id="product_name" type="text" class="form-control product_name"
                                            name="product_name" placeholder="@lang('translation.Product Name')"
                                            value="{{ $model->product_name }}">
                                        <span class="invalid-feedback" id="product_nameError"
                                            data-ajax-feedback="product_name" role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="connections" class="form-label">@lang('translation.CONNECTIONS')</label>
                                        <select name="connections[]" id="connections"
                                            class="form-control select2 select2-multiple" multiple>
                                            @foreach ($productConnections as $ckey => $clist)
                                                @php
                                                    $cselected = in_array($ckey, explode(', ', $model->connections)) ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $ckey }}" {{ $cselected }}>
                                                    {{ $clist }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="connectionsError"
                                            data-ajax-feedback="connections" role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="execution" class="form-label">@lang('translation.EXECUTION')</label>
                                        <select name="execution[]" id="execution"
                                            class="form-control select2 select2-multiple" multiple>
                                            @foreach ($productExecution as $ekey => $elist)
                                                @php
                                                    $eselected = in_array($ekey, explode(', ', $model->execution)) ? 'selected' : '';
                                                @endphp
                                                <option value="{{ $ekey }}" {{ $eselected }}>
                                                    {{ $elist }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="executionError" data-ajax-feedback="execution"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="retail" class="form-label">@lang('translation.Retail')</label>
                                        <input id="retail" type="text" class="form-control retail" name="retail"
                                            placeholder="@lang('translation.Retail')" value="{{ round($model->retail, 2) }}">
                                        <span class="invalid-feedback" id="retailError" data-ajax-feedback="retail"
                                            role="alert"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="url" class="form-label">@lang('translation.URL')</label>
                                        <input id="url" type="text" class="form-control url" name="url"
                                            placeholder="@lang('translation.URL')" value="{{ $model->url }}">
                                        <span class="invalid-feedback" id="urlError" data-ajax-feedback="url"
                                            role="alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="remark" class="form-label">@lang('translation.Remark')</label>
                                <input id="remark" type="text" class="form-control remark" name="remark"
                                    placeholder="@lang('translation.Remark')" value="{{ $model->remark }}">
                                <span class="invalid-feedback" id="remarkError" data-ajax-feedback="remark"
                                    role="alert"></span>
                            </div>
                            <div class="col-md-12 form-footer text-end mt-3">
                                <a href="{{ route('product') }}" class="btn btn-default waves-effect">@lang('translation.Cancel')</a>
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    @if ($model->id)
                                        @lang('translation.Update')
                                    @else
                                        @lang('translation.Submit')
                                    @endif
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
    <script>
        var apiUrl = "{{ route('product.list') }}";
        var indexUrl = "{{ route('product') }}";
        var detailUrl = "{{ route('product.detail') }}";
        var deleteUrl = "{{ route('product.delete') }}";
        var addUrl = "{{ route('product.addupdate') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('product.js') }}"></script>
@endsection
