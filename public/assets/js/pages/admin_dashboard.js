$(document).ready(function () {

    dashboardDataInfo();
    summaryChartFilter();

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
});

// get colors array from the string
function getChartColorsArray(chartId) {
    if (document.getElementById(chartId) !== null) {
        var colors = document.getElementById(chartId).getAttribute("data-colors");
        if (colors) {
            colors = JSON.parse(colors);
            return colors.map(function (value) {
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
            });
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

function earningRadialChart(elmentid, value = 0, title = 'Monthly Earning') {
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

function dashboardDataInfo() {
    $.ajax({
        url: dashboardDataInfoUrl,
        type: 'post',
        success: function (response) {
            if (response.status) {
                $.each(response.data, function (key, value) {
                    if (key == 'yearly-earning-percentage') {
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
                        earningRadialChart(key + '-chart', Math.abs(value), $('#' + key + '-chart').data('title'));
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