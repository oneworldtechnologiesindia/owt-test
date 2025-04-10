@extends('layouts.master')

@section('title')
     @lang('translation.Product')
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
            @lang('translation.View')
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group-card-header d-flex align-items-center justify-content-between mb-4">
                        <h4 class="card-title">
                            @lang('translation.View Product') : {{ $model->product_name }}
                        </h4>
                    </div>
                    <div class="view-container table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th width="20%">@lang('translation.Brand'):</th>
                                    <td width="30%">{{ $model->brand_name }}</td>

                                    <th width="20%">@lang('translation.Product Type'):</th>
                                    <td width="30%">{{ $model->type_name }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.Product Category'):</th>
                                    <td>{{ $model->category_name }}</td>

                                    <th>@lang('translation.Product Name'):</th>
                                    <td>{{ $model->product_name }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.CONNECTIONS'):</th>
                                    <td>
                                        {{ $model->connectionsView($model->connections) }}
                                    </td>

                                    <th>@lang('translation.EXECUTION'):</th>
                                    <td>
                                        {{ $model->executionView($model->execution) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.URL'):</th>
                                    <td>{{ $model->url }}</td>

                                    <th>@lang('translation.Retail'):</th>
                                    <td>{{ $model->retail }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.Remark'):</th>
                                    <td>{!! $model->remark !!}</td>

                                    <th></th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>@lang('translation.Created_At'):</th>
                                    <td>{{ $model->created_at->format('d.m.Y H:i') }}</td>

                                    <th>@lang('translation.Updated At'):</th>
                                    <td>{{ $model->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">
                                        <a href="{{ Route('product') }}"
                                            class="btn btn-warning waves-effect waves-light m-r-10">
                                            @lang('translation.Back To List')</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection

@section('script')
    <script>
        var apiUrl = "{{ route('product.list') }}";
        var indexUrl = "{{ route('product') }}";
        var detailUrl = "{{ route('product.detail') }}";
        var deleteUrl = "{{ route('product.delete') }}";
        var addUrl = "{{ route('product.addupdate') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink(' .js') }}"></script>
@endsection
