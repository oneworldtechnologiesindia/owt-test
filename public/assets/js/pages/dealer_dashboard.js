/*
Template Name: Skote - Admin & Dashboard Template
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Dashboard Init Js File
*/

// get colors array from the string
function getChartColorsArray(chartId) {
    if (document.getElementById(chartId) !== null) {
        var colors = document.getElementById(chartId).getAttribute("data-colors");
        if (colors) {
            colors = JSON.parse(colors);
            return colors.map(function (value) {
                if(value){
                    var newValue = value.replaceAll(" ", "");
                    if (newValue.indexOf(",") === -1) {
                        var color = getComputedStyle(document.documentElement).getPropertyValue(newValue).replaceAll(" ", "");
                        if (color) return color;
                        else return newValue;
                    } else {
                        var val = value.split(',');
                        if (val.length == 2) {
                            var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]).replaceAll(" ", "");
                            rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                            return rgbaColor;
                        } else {
                            return newValue;
                        }
                    }
                }
            });
        }
    }
}

function earningRadialChart(elmentid, value = 0, title = monthlyEarning) {
    // Radial chart
    if ($('#' + elmentid).length > 0) {
        var radialbarColors = getChartColorsArray(elmentid);
        if (radialbarColors) {
            var options = {
                chart: {
                    height: 200,
                    type: 'radialBar',
                    offsetY: -10
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        dataLabels: {
                            name: {
                                fontSize: '13px',
                                color: undefined,
                                offsetY: 60
                            },
                            value: {
                                offsetY: 22,
                                fontSize: '16px',
                                color: undefined,
                                formatter: function (val) {
                                    return val + "%";
                                }
                            }
                        }
                    }
                },
                colors: radialbarColors,
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        shadeIntensity: 0.15,
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 65, 91]
                    },
                },
                stroke: {
                    dashArray: 4,
                },
                series: [value],
                labels: [title],

            }

            var chart = new ApexCharts(
                document.querySelector("#" + elmentid),
                options
            );


            chart.render();
        }
    }
}

// stacked column chart
var linechartBasicColors = getChartColorsArray('summary-chart');
if (linechartBasicColors) {
    var options = {
        chart: {
            height: 360,
            type: 'bar',
            stacked: true,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: true
            }
        },

        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '15%',
                endingShape: 'rounded'
            },
        },

        dataLabels: {
            enabled: false
        },
        series: [{
            name: purchaseEnquiries,
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        }, {
            name: sales,
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

        }, {
            name: appointments,
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        }],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        colors: linechartBasicColors,
        legend: {
            position: 'bottom',
        },
        fill: {
            opacity: 1
        },
    }

    var summaryChart = new ApexCharts(
        document.querySelector("#summary-chart"),
        options
    );

    summaryChart.render();
}

$(document).ready(function () {
    summaryChartFilter();
    dashboardDataInfo();
    topSalesChartFilter();
    $('.chart-summary-switch li').on('click', function (e) {
        e.preventDefault();
        $('.nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');
        if (chartFilter.length > 0) {
            summaryChartFilter(chartFilter);
        } else {
            summaryChartFilter();
        }
    })

    $('.top-sales-switch li').on('click', function (e) {
        e.preventDefault();
        $('.nav-link.active').removeClass('active');
        $(this).find('.nav-link').addClass('active');
        let chartFilter = $(this).find('.nav-link').data('chart');
        if (chartFilter.length > 0) {
            topSalesChartFilter(chartFilter);
        } else {
            topSalesChartFilter();
        }
    })
})

function summaryChartFilter(type = 'year') {
    $.ajax({
        url: summaryChartFilterUrl,
        type: 'post',
        data: {
            type: type
        },
        success: function (response) {
            if (response.status) {
                summaryChart.updateSeries([{
                    name: response.names.Purchase_Enquiries,
                    data: response.data.purchaseenquiries.split(',')
                }, {
                    name: response.names.Sales,
                    data: response.data.sales.split(',')

                }, {
                    name: response.names.Appointment,
                    data: response.data.appointments.split(',')
                }]);

                summaryChart.updateOptions({
                    xaxis: {
                        categories: response.data.categories,
                    },
                });
            } else {
                showMessage("error", something_went_wrong);
            }
        },
        error: function (error) {
            // console.log(error);
            showMessage("error", something_went_wrong);
        }
    })
}

function dashboardDataInfo() {
    $.ajax({
        url: dashboardDataInfoUrl,
        type: 'post',
        success: function (response) {
            if (response.status) {
                $.each(response.data, function (key, value) {
                    if (key == 'monthly-earning-percentage') {
                        let monthlyEarningPercentage = value;
                        if (monthlyEarningPercentage > 0) {
                            $('#' + key).parents('p').find('span.text-danger').removeClass('text-danger').addClass('text-success');
                            $('#' + key).parents('p').find('i.mdi-arrow-down').removeClass('mdi-arrow-down').addClass('mdi-arrow-up');
                            $('#' + key + '-chart').attr('data-colors', '["--bs-success"]');
                        } else {
                            $('#' + key).parents('p').find('span.text-success').removeClass('text-success').addClass('text-danger');
                            $('#' + key).parents('p').find('i.mdi-arrow-up').removeClass('mdi-arrow-up').addClass('mdi-arrow-down');
                            $('#' + key + '-chart').attr('data-colors', '["--bs-danger"]');
                        }
                        $('#' + key).html(Math.abs(value))
                        earningRadialChart(key + '-chart', Math.abs(value));
                    } else if (key == 'yearly-earning-percentage') {
                        let yearlyEarningPercentage = value;
                        if (yearlyEarningPercentage > 0) {
                            $('#' + key).parents('p').find('span.text-danger').removeClass('text-danger').addClass('text-success');
                            $('#' + key).parents('p').find('i.mdi-arrow-down').removeClass('mdi-arrow-down').addClass('mdi-arrow-up');
                            $('#' + key + '-chart').attr('data-colors', '["--bs-success"]');
                        } else {
                            $('#' + key).parents('p').find('span.text-success').removeClass('text-success').addClass('text-danger');
                            $('#' + key).parents('p').find('i.mdi-arrow-up').removeClass('mdi-arrow-up').addClass('mdi-arrow-down');
                            $('#' + key + '-chart').attr('data-colors', '["--bs-danger"]');
                        }
                        $('#' + key).html(Math.abs(value))
                        earningRadialChart(key + '-chart', Math.abs(value), yearlyTurnover);
                    } else if (key == 'completed-level-percentage') {
                        earningRadialChart(key + '-chart', Math.abs(value), '');
                    } else {
                        $('#' + key).html(value)
                    }
                });
            } else {
                showMessage("error", something_went_wrong);
            }
        },
        error: function (error) {
            // console.log(error);
            showMessage("error", something_went_wrong);
        }
    })
}

function topSalesChartFilter(type = 'year'){
    $.ajax({
        url: topSalesChartFilterUrl,
        type: 'post',
        data: {
            type: type
        },
        success: function (response) {
            if (response.status == true) {
                $('.product-sales-table tbody').html('');
                $('.brand-sales-table tbody').html('');
                $('.product-type-sales-table tbody').html('');

                // product_array
                if(response.data[0].length > 0){
                    var html="";
                    $.each(response.data[0], function (key, value) {
                        html+="<tr>";
                        let product_array_name = value.product_name;
                        let product_array_qty = value.total_qty;
                        html+="<td>"+product_array_name+"</td>";
                        html+="<td>"+product_array_qty+"</td>";
                        html+="<tr>";
                    })
                    $('.product-sales-table tbody').html(html);
                }else{
                    var html="";
                    html+='<tr>';
                    html+='<td colspan="2" id="no-record-product-type">No Record Found</td>';
                    html+='<tr>';
                    $('.product-sales-table tbody').html(html);
                }
                // brand_array
                if(response.data[1].length > 0){
                    var html="";
                    $('#no-record-brand').html("");
                    $.each(response.data[1], function (key, value) {
                        html+="<tr>";
                        let brand_array_name = value.brand_name;
                        let brand_array_qty = value.total_qty;
                        html+="<td>"+brand_array_name+"</td>";
                        html+="<td>"+brand_array_qty+"</td>";
                        html+="<tr>";
                    })
                    $('.brand-sales-table tbody').html(html);
                }else{
                    var html="";
                    html+='<tr>';
                    html+='<td colspan="2" id="no-record-product-type">No Record Found</td>';
                    html+='<tr>';
                    $('.brand-sales-table tbody').html(html);
                }
                // product_type_array
                if(response.data[2].length > 0){
                    var html="";
                    // $('#no-record-product-type').html("");
                    $.each(response.data[2], function (key, value) {
                        html+="<tr>";
                        let product_type_array_name = value.type_name;
                        let product_type_array_qty = value.total_qty;
                        html+="<td>"+product_type_array_name+"</td>";
                        html+="<td>"+product_type_array_qty+"</td>";
                        html+="<tr>";
                    })
                    $('.product-type-sales-table tbody').html(html);
                }else{
                    var html="";
                    html+='<tr>';
                    html+='<td colspan="2" id="no-record-product-type">No Record Found</td>';
                    html+='<tr>';
                    $('.product-type-sales-table tbody').html(html);
                }
            } else {
                showMessage("error", something_went_wrong);
            }
        },
        error: function (error) {
            // console.log(error);
            showMessage("error", something_went_wrong);
        }
    })
}
