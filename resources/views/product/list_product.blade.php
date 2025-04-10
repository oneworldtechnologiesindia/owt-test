@extends('layouts.master')

@section('title')
     @lang('translation.Products')
@endsection

@section('css')
    <!-- Datatable Css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('home') }}">@lang('translation.Dashboard')</a>
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
                    <div class="table-responsive" data-simplebar>
                        <table id="product_list_table" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>@lang('translation.Brand')</th>
                                    <th>@lang('translation.Type')</th>
                                    <th>@lang('translation.Category')</th>
                                    <th>@lang('translation.Product')</th>
                                    <th>@lang('translation.Retail')</th>
                                    <th>@lang('translation.URL')</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>

@endsection

@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script>
        var apiUrl = "{{ route('product.list') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('customer_product.js') }}"></script>
@endsection
