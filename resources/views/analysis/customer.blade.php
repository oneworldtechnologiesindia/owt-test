@extends('layouts.master')

@section('title')
    @lang('translation.Customer_Analysis_Charts')
@endsection

@section('css')
    <!-- Sweet Alert-->
    <link href="{{ URL::asset('/assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('dealer.home') }}">@lang('translation.Dashboard')</a>
        @endslot
        @slot('title')
            @lang('translation.Customer_Analysis')
        @endslot
    @endcomponent

    <div class="row customer-analysis">
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.Product_Wise_Sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills product-wise-chart-summary">
                                <div class="nav-items mx-1">
                                    <select class="form-control form-select" name="product_customer_filter" id="product_customer_filter">
                                        <option value="dealer_customer" selected>@lang('translation.Your_Customer')</option>
                                        <option value="all_customer">@lang('translation.All_Customer')</option>
                                    </select>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="product-wise-chart-id"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.Product_Type_Wise_Sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills product-type-wise-chart-summary">
                                <div class="nav-items mx-1">
                                    <select class="form-control form-select" name="product_type_customer_filter" id="product_type_customer_filter">
                                        <option value="dealer_customer" selected>@lang('translation.Your_Customer')</option>
                                        <option value="all_customer">@lang('translation.All_Customer')</option>
                                    </select>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="product-type-wise-chart-id"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.Brand_Wise_Sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills brand-wise-chart-summary">
                                <div class="nav-items mx-1">
                                    <select class="form-control form-select" name="brand_customer_filter" id="brand_customer_filter">
                                        <option value="dealer_customer" selected>@lang('translation.Your_Customer')</option>
                                        <option value="all_customer">@lang('translation.All_Customer')</option>
                                    </select>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="brand-wise-chart-id"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.Category_Wise_Sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills category-wise-chart-summary">
                                <div class="nav-items mx-1">
                                    <select class="form-control form-select" name="category_customer_filter" id="category_customer_filter">
                                        <option value="dealer_customer" selected>@lang('translation.Your_Customer')</option>
                                        <option value="all_customer">@lang('translation.All_Customer')</option>
                                    </select>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="category-wise-chart-id"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.City_Wise_Sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills city-wise-chart-summary">
                                <div class="nav-items mx-1">
                                    <select class="form-control form-select" name="city_customer_filter" id="city_customer_filter">
                                        <option value="dealer_customer" selected>@lang('translation.Your_Customer')</option>
                                        <option value="all_customer">@lang('translation.All_Customer')</option>
                                    </select>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="city-wise-chart-id"></div>
                </div>
            </div>
        </div> <!-- end col -->
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.Zip_Code_Wise_Sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills zipcode-wise-chart-summary">
                                <div class="nav-items mx-1">
                                    <select class="form-control form-select" name="zipcode_customer_filter" id="zipcode_customer_filter">
                                        <option value="dealer_customer" selected>@lang('translation.Your_Customer')</option>
                                        <option value="all_customer">@lang('translation.All_Customer')</option>
                                    </select>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="zipcode-wise-chart-id"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.Age_Wise_Sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills age-wise-chart-summary">
                                <div class="nav-items mx-1">
                                    <select class="form-control form-select" name="age_customer_filter" id="age_customer_filter">
                                        <option value="dealer_customer" selected>@lang('translation.Your_Customer')</option>
                                        <option value="all_customer">@lang('translation.All_Customer')</option>
                                    </select>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>

                        </div>
                    </div>
                    <div id="age-wise-chart-id"></div>
                </div>
            </div>
        </div> <!-- end col -->
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="position: relative;">
                    <div class="d-sm-flex flex-wrap">
                        <h4 class="card-title mb-4">@lang('translation.Gender_Wise_Sales')</h4>
                        <div class="ms-auto">
                            <ul class="nav nav-pills gender-wise-chart-summary">
                                <div class="nav-items mx-1">
                                    <select class="form-control form-select" name="gender_customer_filter" id="gender_customer_filter">
                                        <option value="dealer_customer" selected>@lang('translation.Your_Customer')</option>
                                        <option value="all_customer">@lang('translation.All_Customer')</option>
                                    </select>
                                </div>
                                <li class="nav-item">
                                    <a class="nav-link" data-chart="month" href="#">@lang('translation.Month')</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" data-chart="year" href="#">@lang('translation.Year')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="gender-wise-chart-id"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Apexcharts js -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        var getAgeWiseData = "{{ route('dealer.customer-analysis.age-wise-chart') }}";
        var getGenderWiseData = "{{ route('dealer.customer-analysis.gender-wise-chart') }}";
        var getCityWiseData = "{{ route('dealer.customer-analysis.city-wise-chart') }}";
        var getZipcodeWiseData = "{{ route('dealer.customer-analysis.zipcode-wise-chart') }}";
        var getBrandWiseData = "{{ route('dealer.customer-analysis.brand-wise-chart') }}";
        var getCategoryWiseData = "{{ route('dealer.customer-analysis.category-wise-chart') }}";
        var getProducTypeWiseData = "{{ route('dealer.customer-analysis.product-type-wise-chart') }}";
        var getProducWiseData = "{{ route('dealer.customer-analysis.product-wise-chart') }}";
    </script>
@endsection

@section('script-bottom')
    <script src="{{ addPageJsLink('customer-analysis.js') }}"></script>
@endsection
