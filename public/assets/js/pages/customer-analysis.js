$(document).ready(function () {
    getAgeWiseDataFun("year","dealer_customer");
    $('.age-wise-chart-summary li').on('click', function (e) {
        e.preventDefault();
        let customer_filter = $('#age_customer_filter').val();
        $('.age-wise-chart-summary .nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');

        if (chartFilter.length > 0) {
            getAgeWiseDataFun(chartFilter,customer_filter);
        } else {
            getAgeWiseDataFun("year",customer_filter);
        }
    })
    $('#age_customer_filter').on('change',function(){
        let chartFilterData = $('.age-wise-chart-summary li').find('.active').data('chart');
        getAgeWiseDataFun(chartFilterData,$(this).val());
    })

    getGenderWiseDataFun("year","dealer_customer");
    $('.gender-wise-chart-summary li').on('click', function (e) {
        e.preventDefault();
        let customer_filter = $('#gender_customer_filter').val();
        $('.gender-wise-chart-summary .nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');

        if (chartFilter.length > 0) {
            getGenderWiseDataFun(chartFilter,customer_filter);
        } else {
            getGenderWiseDataFun("year",customer_filter);
        }
    })
    $('#gender_customer_filter').on('change',function(){
        let chartFilterData = $('.gender-wise-chart-summary li').find('.active').data('chart');
        getGenderWiseDataFun(chartFilterData,$(this).val());
    })

    getCityWiseDataFun("year","dealer_customer");
    $('.city-wise-chart-summary li').on('click', function (e) {
        e.preventDefault();
        let customer_filter = $('#city_customer_filter').val();
        $('.city-wise-chart-summary .nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');

        if (chartFilter.length > 0) {
            getCityWiseDataFun(chartFilter,customer_filter);
        } else {
            getCityWiseDataFun("year",customer_filter);
        }
    })
    $('#city_customer_filter').on('change',function(){
        let chartFilterData = $('.city-wise-chart-summary li').find('.active').data('chart');
        getCityWiseDataFun(chartFilterData,$(this).val());
    })

    getZipcodeWiseDataFun("year","dealer_customer");
    $('.zipcode-wise-chart-summary li').on('click', function (e) {
        e.preventDefault();
        let customer_filter = $('#zipcode_customer_filter').val();
        $('.zipcode-wise-chart-summary .nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');

        if (chartFilter.length > 0) {
            getZipcodeWiseDataFun(chartFilter,customer_filter);
        } else {
            getZipcodeWiseDataFun("year",customer_filter);
        }
    })
    $('#zipcode_customer_filter').on('change',function(){
        let chartFilterData = $('.zipcode-wise-chart-summary li').find('.active').data('chart');
        getZipcodeWiseDataFun(chartFilterData,$(this).val());
    })

    getBrandWiseDataFun("year","dealer_customer");
    $('.brand-wise-chart-summary li').on('click', function (e) {
        e.preventDefault();
        let customer_filter = $('#brand_customer_filter').val();
        $('.brand-wise-chart-summary .nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');

        if (chartFilter.length > 0) {
            getBrandWiseDataFun(chartFilter,customer_filter);
        } else {
            getBrandWiseDataFun("year",customer_filter);
        }
    })
    $('#brand_customer_filter').on('change',function(){
        let chartFilterData = $('.brand-wise-chart-summary li').find('.active').data('chart');
        getBrandWiseDataFun(chartFilterData,$(this).val());
    })

    getCategoryWiseDataFun("year","dealer_customer");
    $('.category-wise-chart-summary li').on('click', function (e) {
        e.preventDefault();
        let customer_filter = $('#category_customer_filter').val();
        $('.category-wise-chart-summary .nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');

        if (chartFilter.length > 0) {
            getCategoryWiseDataFun(chartFilter,customer_filter);
        } else {
            getCategoryWiseDataFun("year",customer_filter);
        }
    })
    $('#category_customer_filter').on('change',function(){
        let chartFilterData = $('.category-wise-chart-summary li').find('.active').data('chart');
        getCategoryWiseDataFun(chartFilterData,$(this).val());
    })

    getProducTypeWiseDataFun("year","dealer_customer");
    $('.product-type-wise-chart-summary li').on('click', function (e) {
        e.preventDefault();
        let customer_filter = $('#product_type_customer_filter').val();
        $('.product-type-wise-chart-summary .nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');

        if (chartFilter.length > 0) {
            getProducTypeWiseDataFun(chartFilter,customer_filter);
        } else {
            getProducTypeWiseDataFun("year",customer_filter);
        }
    })
    $('#product_type_customer_filter').on('change',function(){
        let chartFilterData = $('.product-type-wise-chart-summary li').find('.active').data('chart');
        getProducTypeWiseDataFun(chartFilterData,$(this).val());
    })

    getProducWiseDataFun("year","dealer_customer");
    $('.product-wise-chart-summary li').on('click', function (e) {
        e.preventDefault();
        let customer_filter = $('#product_customer_filter').val();
        $('.product-wise-chart-summary .nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');

        if (chartFilter.length > 0) {
            getProducWiseDataFun(chartFilter,customer_filter);
        } else {
            getProducWiseDataFun("year",customer_filter);
        }
    })
    $('#product_customer_filter').on('change',function(){
        let chartFilterData = $('.product-wise-chart-summary li').find('.active').data('chart');
        getProducWiseDataFun(chartFilterData,$(this).val());
    })
});

// ***************************** Ajax Routes **********************************************
function getAgeWiseDataFun(filterTime,customerFilter) {
    $.ajax({
        url: getAgeWiseData,
        type: 'GET',
        data:{
            filter_time : filterTime,
            filter_customer : customerFilter
        },
        success: function (response) {
            if (response.status) {
                CustomerAgeChart(response.age_wise.age_keys,response.age_wise.sales);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    })
}

function getGenderWiseDataFun(filterTime,customerFilter) {
    $.ajax({
        url: getGenderWiseData,
        type: 'GET',
        data:{
            filter_time : filterTime,
            filter_customer : customerFilter
        },
        success: function (response) {
            if (response.status) {
                CustomerGenderChart(response.gender_wise.gender_keys,response.gender_wise.sales);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    })
}

function getCityWiseDataFun(filterTime,customerFilter) {
    $.ajax({
        url: getCityWiseData,
        type: 'GET',
        data:{
            filter_time : filterTime,
            filter_customer : customerFilter
        },
        success: function (response) {
            if (response.status) {
                CustomerCityChart(response.city_wise.city,response.city_wise.sales);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    })
}

function getZipcodeWiseDataFun(filterTime,customerFilter) {
    $.ajax({
        url: getZipcodeWiseData,
        type: 'GET',
        data:{
            filter_time : filterTime,
            filter_customer : customerFilter
        },
        success: function (response) {
            if (response.status) {
                CustomerZipCodeChart(response.zip_code_wise.zipcode,response.zip_code_wise.sales);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    })
}

function getBrandWiseDataFun(filterTime,customerFilter) {
    $.ajax({
        url: getBrandWiseData,
        type: 'GET',
        data:{
            filter_time : filterTime,
            filter_customer : customerFilter
        },
        success: function (response) {
            if (response.status) {
                CustomerBrandChart(response.brand_wise.brand_name,response.brand_wise.sales);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    })
}

function getCategoryWiseDataFun(filterTime,customerFilter) {
    $.ajax({
        url: getCategoryWiseData,
        type: 'GET',
        data:{
            filter_time : filterTime,
            filter_customer : customerFilter
        },
        success: function (response) {
            if (response.status) {
                CustomerCategoryChart(response.category_wise.category_name,response.category_wise.sales);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    })
}

function getProducTypeWiseDataFun(filterTime,customerFilter) {
    $.ajax({
        url: getProducTypeWiseData,
        type: 'GET',
        data:{
            filter_time : filterTime,
            filter_customer : customerFilter
        },
        success: function (response) {
            if (response.status) {
                CustomerProductTypeChart(response.product_type_wise.type_name,response.product_type_wise.sales);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    })
}

function getProducWiseDataFun(filterTime,customerFilter) {
    $.ajax({
        url: getProducWiseData,
        type: 'GET',
        data:{
            filter_time : filterTime,
            filter_customer : customerFilter
        },
        success: function (response) {
            if (response.status) {
                CustomerProducChart(response.product_wise.product_name,response.product_wise.sales);
            }
        },
        error: function (error) {
            showMessage("error", something_went_wrong);
        }
    })
}

// ******************************************* Chart Functions **************************************************

var ageChartEi = document.getElementById('age-wise-chart-id');
var ageBarChartLine = "";
function CustomerAgeChart(age_kyes,data) {
    var optionsBarChart = {
        series: [{
            name: 'Sales',
            data: data
        }],
        chart: {
            type: 'bar',
            width: "100%",
            height: 360
        },
        theme: {
            monochrome: {
                enabled: true,
                color: '#31316A',
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '25%',
                borderRadius: 5,
                radiusOnLastStackedBar: true,
                colors: {
                    backgroundBarColors: ['#F2F4F6', '#F2F4F6', '#F2F4F6', '#F2F4F6'],
                    backgroundBarRadius: 5,
                },
            }
        },
        labels: [1, 2, 3, 4, 5, 6, 7],
        xaxis: {
            categories: age_kyes,
            crosshairs: {
                width: 1
            },
        },
        tooltip: {
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter',
            },
            y: {
                formatter: function (val) {
                    return val
                }
            }
        },
    };

    if(ageBarChartLine){
        ageBarChartLine.destroy();
    }

    if (ageChartEi) {
        ageBarChartLine = new ApexCharts(ageChartEi, optionsBarChart);
        ageBarChartLine.render();
    }
}

var genderCharEi = document.getElementById('gender-wise-chart-id');
var genderChartLine = "";
function CustomerGenderChart(gender_array,data) {
    // Line chart
    var optionsLineChart = {
        series: [{
            name: 'Sales',
            data: data
        }],
        labels: gender_array,
        chart: {
            type: 'area',
            width: "100%",
            height: 360
        },
        theme: {
            monochrome: {
                enabled: true,
                color: '#31316A',
            }
        },
        tooltip: {
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter',
            },
        },
    };

    if(genderChartLine){
        genderChartLine.destroy();
    }

    if (genderCharEi) {
        genderChartLine = new ApexCharts(genderCharEi, optionsLineChart);
        genderChartLine.render();
    }

}

var cityChartEi = document.getElementById('city-wise-chart-id');
var cityChartLine = "";
function CustomerCityChart(city_name,data) {
    var optionsLineChart = {
        series: [{
            name: 'Sales',
            data: data
        }],
        labels: city_name,
        chart: {
            type: 'area',
            width: "100%",
            height: 360
        },
        theme: {
            monochrome: {
                enabled: true,
                color: '#31316A',
            }
        },
        tooltip: {
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter',
            },
        },
    };

    if (cityChartLine) {
        cityChartLine.destroy();
    }
    if (cityChartEi) {
        cityChartLine = new ApexCharts(cityChartEi, optionsLineChart);
        cityChartLine.render();
    }
}

var zipChartEi = document.getElementById('zipcode-wise-chart-id');
var zipChartLine = "";
function CustomerZipCodeChart(zip_codes,data) {
    var optionsBarChart = {
        series: [{
            name: 'Sales',
            data: data
        }],
        chart: {
            type: 'bar',
            width: "100%",
            height: 360
        },
        theme: {
            monochrome: {
                enabled: true,
                color: '#31316A',
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '25%',
                borderRadius: 5,
                radiusOnLastStackedBar: true,
                colors: {
                    backgroundBarColors: ['#F2F4F6', '#F2F4F6', '#F2F4F6', '#F2F4F6'],
                    backgroundBarRadius: 5,
                },
            }
        },
        labels: [1, 2, 3, 4, 5, 6, 7],
        xaxis: {
            categories: zip_codes,
            crosshairs: {
                width: 1
            },
        },
        tooltip: {
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter',
            },
            y: {
                formatter: function (val) {
                    return val
                }
            }
        },
    };

    if(zipChartLine){
        zipChartLine .destroy();
    }

    if (zipChartEi) {
        zipChartLine = new ApexCharts(zipChartEi, optionsBarChart);
        zipChartLine.render();
    }
}

var BrandChartEi = document.getElementById('brand-wise-chart-id');
var brandChartLine = "";
function CustomerBrandChart(brand_name,data) {
    var optionsBarChart = {
        series: [{
            name: 'Sales',
            data: data
        }],
        chart: {
            type: 'bar',
            width: "100%",
            height: 360
        },
        theme: {
            monochrome: {
                enabled: true,
                color: '#31316A',
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '25%',
                borderRadius: 5,
                radiusOnLastStackedBar: true,
                colors: {
                    backgroundBarColors: ['#F2F4F6', '#F2F4F6', '#F2F4F6', '#F2F4F6'],
                    backgroundBarRadius: 5,
                },
            }
        },
        labels: [1, 2, 3, 4, 5, 6, 7],
        xaxis: {
            categories: brand_name,
            crosshairs: {
                width: 1
            },
        },
        tooltip: {
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter',
            },
            y: {
                formatter: function (val) {
                    return val
                }
            }
        },
    };

    if(brandChartLine){
        brandChartLine.destroy();
    }

    if (BrandChartEi) {
        brandChartLine = new ApexCharts(BrandChartEi, optionsBarChart);
        brandChartLine.render();
    }
}

var categoryChartEi = document.getElementById('category-wise-chart-id');
var categoryChartLine = "";
function CustomerCategoryChart(category_name,data) {
    var optionsLineChart = {
        series: [{
            name: 'Sales',
            data: data
        }],
        labels: category_name,
        chart: {
            type: 'area',
            width: "100%",
            height: 360
        },
        theme: {
            monochrome: {
                enabled: true,
                color: '#31316A',
            }
        },
        tooltip: {
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter',
            },
        },
    };

    if(categoryChartLine){
        categoryChartLine .destroy();
    }

    if (categoryChartEi) {
        categoryChartLine = new ApexCharts(categoryChartEi, optionsLineChart);
        categoryChartLine.render();
    }
}

var productTypeChartEl = document.getElementById('product-type-wise-chart-id');
var productTypeChartLine = "";
function CustomerProductTypeChart(type_name,data) {
    var optionsLineChart = {
        series: [{
            name: 'Sales',
            data: data
        }],
        labels: type_name,
        chart: {
            type: 'area',
            width: "100%",
            height: 360
        },
        theme: {
            monochrome: {
                enabled: true,
                color: '#31316A',
            }
        },
        tooltip: {
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter',
            },
        },
    };

    if(productTypeChartLine){
        productTypeChartLine.destroy();
    }

    if (productTypeChartEl) {
        productTypeChartLine = new ApexCharts(productTypeChartEl, optionsLineChart);
        productTypeChartLine.render();
    }
}

var productChartEi = document.getElementById('product-wise-chart-id');
var productChartLine = "";
function CustomerProducChart(product_name,data) {
    var optionsBarChart = {
        series: [{
            name: 'Sales',
            data: data
        }],
        chart: {
            type: 'bar',
            width: "100%",
            height: 360
        },
        theme: {
            monochrome: {
                enabled: true,
                color: '#31316A',
            }
        },
        plotOptions: {
            bar: {
                columnWidth: '25%',
                borderRadius: 5,
                radiusOnLastStackedBar: true,
                colors: {
                    backgroundBarColors: ['#F2F4F6', '#F2F4F6', '#F2F4F6', '#F2F4F6'],
                    backgroundBarRadius: 5,
                },
            }
        },
        labels: [1, 2, 3, 4, 5, 6, 7],
        xaxis: {
            categories: product_name,
            crosshairs: {
                width: 1
            },
        },
        tooltip: {
            fillSeriesColor: false,
            onDatasetHover: {
                highlightDataSeries: false,
            },
            theme: 'light',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter',
            },
            y: {
                formatter: function (val) {
                    return val
                }
            }
        },
    };

    if(productChartLine){
        productChartLine.destroy();
    }

    if (productChartEi) {
        productChartLine = new ApexCharts(productChartEi, optionsBarChart);
        productChartLine.render();
    }
}

