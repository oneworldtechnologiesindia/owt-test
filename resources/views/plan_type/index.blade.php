@extends('layouts.master')

@section('title')
    @lang('translation.Plan Types')
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
            @lang('translation.Plan Types')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">@lang('translation.All Plan Types')</h4>
                    </div>
                    <div class="table-responsive" data-simplebar>
                        <table id="listTable" class="table align-middle table-hover table-nowrap w-100 dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('translation.Plan Type')</th>
                                    <th>@lang('translation.Silver Level')</th>
                                    <th>@lang('translation.Gold Level')</th>
                                    <th>@lang('translation.Platinum Level')</th>
                                    <th>@lang('translation.Diamond Level')</th>
                                    <th>@lang('translation.Type')</th>
                                    <th>@lang('translation.Created_At')</th>
                                    <th>@lang('translation.Updated At')</th>
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
                        @lang('translation.Plan Type')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-form" method="post" class="form-horizontal" action="#">
                        @csrf
                        <input type="hidden" name="id" value="0" id="edit-id">

                        <!-- Plan Type -->
                        <div class="mb-3">
                            <label for="plan_type" class="control-label">@lang('translation.Plan Type')</label>
                            <input id="plan_type" type="text" class="form-control" name="plan_type"
                                placeholder="@lang('translation.Plan Type')" disabled>
                            <span class="invalid-feedback" id="planTypeError" data-ajax-feedback="plan_type"
                                role="alert"></span>
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="control-label">@lang('translation.Type')</label>
                            <input id="type" type="text" class="form-control" name="type"
                                placeholder="@lang('translation.Type')" disabled>
                            <span class="invalid-feedback" id="typeError" data-ajax-feedback="type" role="alert"></span>
                        </div>

                        <!-- Silver Level -->
                        <div class="mb-3">
                            <label for="silver_level" class="control-label">@lang('translation.Silver Level')</label>
                            <input id="silver_level" type="number" class="form-control" name="silver_level"
                                placeholder="@lang('translation.Silver Level')" step="any">
                        </div>

                        <!-- Gold Level -->
                        <div class="mb-3">
                            <label for="gold_level" class="control-label">@lang('translation.Gold Level')</label>
                            <input id="gold_level" type="number" class="form-control" name="gold_level"
                                placeholder="@lang('translation.Gold Level')" step="any">
                        </div>

                        <!-- Platinum Level -->
                        <div class="mb-3">
                            <label for="platinum_level" class="control-label">@lang('translation.Platinum Level')</label>
                            <input id="platinum_level" type="number" class="form-control" name="platinum_level"
                                placeholder="@lang('translation.Platinum Level')" step="any">
                        </div>

                        <!-- Diamond Level -->
                        <div class="mb-3">
                            <label for="diamond_level" class="control-label">@lang('translation.Diamond Level')</label>
                            <input id="diamond_level" type="number" class="form-control" name="diamond_level"
                                placeholder="@lang('translation.Diamond Level')" step="any">
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
        var apiUrl = "{{ route('plan_type.list') }}";
        var detailUrl = "{{ route('plan_type.detail') }}";
        var addUrl = "{{ route('plan_type.addupdate') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('plan_type.js') }}"></script>
@endsection
