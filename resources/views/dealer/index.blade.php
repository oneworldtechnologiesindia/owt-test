@extends('layouts.master')

@section('title')
     @lang('translation.Dealer')
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
            @lang('translation.Dealer')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All_Dealers')</h4>
                        <a href="{{ route('dealers.create') }}"
                            class="btn btn-primary waves-effect btn-label waves-light"><i class="bx bx-plus label-icon"></i>
                            @lang('translation.Add_New')</a>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                     <th>@lang('translation.Company')</th>
                                    <th>@lang('translation.Phone')</th>
                                    <th>@lang('translation.Email')</th>
                                    <th>@lang('translation.Distributor')</th>
                                    <th>@lang('translation.Status')</th>
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
@endsection



@section('script')
    <!-- Datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        var apiUrl = "{{ route('dealer.list') }}";
        var detailUrl = "{{ route('dealer.detail') }}";
        var editUrl = "{{ route('dealers.edit') }}";
        var deleteUrl = "{{ route('dealer.delete') }}";
        var updateStatusUrl = "{{ url('admin/dealer/updatefield') }}";
        var addUrl = $('#add-form').attr('action');
        var updateDistributorStatusUrl = "{{ route('dealer.updateDistributorStatus') }}";

    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('dealer.js') }}"></script>
@endsection
