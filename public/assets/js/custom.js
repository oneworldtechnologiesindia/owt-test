function showMessage(e = "success", a = "") {
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: false,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "3000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        closeEasing: "linear",
        showMethod: "show",
        hideMethod: "hide",
        closeMethod: "hide"
    }, "success" != e && "Success" != e || toastr.success(a), "warning" != e && "Warning" != e || toastr.warning(a), "info" != e && "Info" != e || toastr.info(a), "error" != e && "Error" != e || toastr.error(a)
}

function pagereload() {
    var interval = setInterval(function () {
        location.reload(true);
        clearInterval(interval);
    }, 1500);
}
$(document).ready(function () {
    var intervalAlert = setInterval(function () {
        $('.alert').hide();
        clearInterval(intervalAlert);
    }, 6000);
    $("input").attr("autocomplete", "off");
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    setInterval(function () {
        getNotification();
    }, 15 * 1000);

    getNotification();
});

if ($.fn.datepicker) {
    $.fn.datepicker.dates.de = {
        days: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
        daysShort: ["Son", "Mon", "Die", "Mit", "Don", "Fre", "Sam"],
        daysMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
        months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
        monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        today: "Heute",
        monthsTitle: "Monate",
        clear: "Löschen",
        weekStart: 1
    }
}

if ($('input[data-provide="datepicker"], div[data-provide="datepicker"] input').length > 0) {
    $('input[data-provide="datepicker"], div[data-provide="datepicker"] input').datepicker({
        language: localLang
    });
}

if (jQuery.fn.dataTableExt) {
    jQuery.extend(jQuery.fn.dataTableExt.oSort, {
        "extract-date-pre": function (value) {
            var date = $(value, 'span')[0].innerHTML;
            date = date.split('.');
            return Date.parse(date[2] + '/' + date[1] + '/' + date[0])
        },
        "extract-date-asc": function (a, b) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        "extract-date-desc": function (a, b) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
    });
}

$('body').on('keyup', 'form input, form textarea', function (event) {
    onchangeform($(this));
});

if ($('input[data-provide="datepicker"]').length > 0) {
    $('input[data-provide="datepicker"]').datepicker().on('change', function (ev) {
        onchangeform($(this));
    });
}

function getNotification() {
    if ($('#notification-hifi').length > 0) {
        $.ajax({
            url: getNotificationUrl,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#sidebar-menu').find('.badge').addClass('d-none');
                $('.notification-listing').html('');
                $('#notification-hifi').find('.notification-main-icon.bx-tada').removeClass(
                    'bx-tada');
                $('#notification-hifi').find('.notification-count').text('0');
                var html = '';
                if (response.status && response.data.length > 0) {
                    if (!$('#notification-hifi').find('.notification-main-icon').hasClass('bx-tada')) {
                        $('#notification-hifi').find('.notification-main-icon').addClass(
                            'bx-tada');
                    }
                    $('#notification-hifi').find('.notification-count').text(response.data.length);
                    $.each(response.data, function (key, value) {
                        html +=
                            '<a href="' + value.url + '" class="text-reset notification-item">' +
                            '<div class="d-flex">' +
                            '<div class="avatar-xs me-3">' +
                            '<span class="avatar-title bg-primary rounded-circle font-size-16">' +
                            '<i class="' + value.icon + '"></i>' +
                            '</span>' +
                            '</div>' +
                            '<div class="flex-grow-1">' +
                            '<h6 class="mb-1">' + value.title + '</h6>' +
                            '<div class="font-size-12 text-muted">' +
                            '<p class="mb-1">' + value.body + '</p>' +
                            '<p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>' +
                            value.time + '</span></p>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</a>';

                        if (value.type == 'appointment') {
                            if ($('.appointment-badge').hasClass('d-none')) {
                                $('.appointment-badge').removeClass('d-none')
                            }
                        }

                        if (value.type == 'purchaseEnquiry') {
                            if ($('.purchase-enquiry-badge').hasClass('d-none')) {
                                $('.purchase-enquiry-badge').removeClass('d-none')
                            }
                        }

                        if (value.type == 'order') {
                            if ($('.order-badge').hasClass('d-none')) {
                                $('.order-badge').removeClass('d-none')
                            }
                        }

                        if (value.type == 'calendar') {
                            if ($('.calendar-badge').hasClass('d-none')) {
                                $('.calendar-badge').removeClass('d-none')
                            }
                        }
                    })
                } else {
                    html += '<div class="notice-empty alert alert-warning d-block mb-2 me-3 ms-3" role="alert" style="display: none;"><i class="mdi mdi-alert-outline me-2"></i>' + No_new_notifications + '</div>';
                }
                $('.notification-listing').html(html);

            },
            error: function (error) {
                showMessage("error", something_went_wrong);
            }
        });
    } else {
        $('#sidebar-menu').find('.badge').remove();
    }
}

function onchangeform(element) {
    if ($(element).val().length > 0) {
        $(element).closest('.mb-3').find('.invalid-feedback').html('');
        if ($(element).hasClass('is-invalid'))
            $(element).removeClass('is-invalid');

        if ($(element).parents('.input-group').hasClass('is-invalid'))
            $(element).parents('.input-group').removeClass('is-invalid');
    }
}

$('body').on("change", "form input[type='file'], form input[type='checkbox'], form select, form input[type='radio']", function (event) {
    onchangeform($(this));
});

function changeDatatableLang() {
    if (typeof localLang !== 'undefined') {
        if (localLang == 'de') {
            return {
                "sEmptyTable": "Keine Daten in der Tabelle vorhanden",
                "sInfo": "_START_ bis _END_ von _TOTAL_ Einträgen",
                "sInfoEmpty": "0 bis 0 von 0 Einträgen",
                "sInfoFiltered": "(gefiltert von _MAX_ Einträgen)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ Einträge anzeigen",
                "sLoadingRecords": "Wird geladen...",
                "sProcessing": "Bitte warten...",
                "sSearch": "Suchen",
                "sZeroRecords": "Keine Einträge vorhanden.",
                "oPaginate": {
                    "sFirst": "Erste",
                    "sPrevious": "Zurück",
                    "sNext": "Nächste",
                    "sLast": "Letzte"
                },
                "oAria": {
                    "sSortAscending": ": aktivieren, um Spalte aufsteigend zu sortieren",
                    "sSortDescending": ": aktivieren, um Spalte absteigend zu sortieren"
                }
            };
        } else {
            return {
                "sEmptyTable": "No data available in table",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                "sInfoEmpty": "Showing 0 to 0 of 0 entries",
                "sInfoFiltered": "(filtered from _MAX_ total entries)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Show _MENU_ entries",
                "sLoadingRecords": "Loading...",
                "sProcessing": "Processing...",
                "sSearch": "Search:",
                "sZeroRecords": "No matching records found",
                "oPaginate": {
                    "sFirst": "First",
                    "sLast": "Last",
                    "sNext": "Next",
                    "sPrevious": "Previous"
                },
                "oAria": {
                    "sSortAscending": ": activate to sort column ascending",
                    "sSortDescending": ": activate to sort column descending"
                }
            };
        }
    } else {
        return {
            "sEmptyTable": "No data available in table",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
            "sInfoFiltered": "(filtered from _MAX_ total entries)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Show _MENU_ entries",
            "sLoadingRecords": "Loading...",
            "sProcessing": "Processing...",
            "sSearch": "Search:",
            "sZeroRecords": "No matching records found",
            "oPaginate": {
                "sFirst": "First",
                "sLast": "Last",
                "sNext": "Next",
                "sPrevious": "Previous"
            },
            "oAria": {
                "sSortAscending": ": activate to sort column ascending",
                "sSortDescending": ": activate to sort column descending"
            }
        };
    }
}

$(document).ready(function(e){
    if(window.currency_type=="eur"){
        $('.changenumber_w_o_d').number( true, 2 ,',', '.','€');
        $('.autoNumeric').each(function() {
            var autoNumericInstance = new AutoNumeric(this, {
                 currencySymbol: '€',
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 2,
                unformatOnSubmit: true,
                minimumValue: 0
            });

            $(this).data('autoNumericInstance', autoNumericInstance);
        });
    }
    else{
        $('.changenumber_w_o_d').number( true, 2 ,'.', ',','$');
        // $('.autoNumeric').autoNumeric('init', {
        //     currencySymbol: '$',
        //     digitGroupSeparator: ',',
        //     decimalCharacter: '.',
        //     decimalPlaces: 2
        // });
        $('.autoNumeric').each(function() {
            var autoNumericInstance = new AutoNumeric(this, {
                currencySymbol: '$',
                digitGroupSeparator: ',',
                decimalCharacter: '.',
                decimalPlaces: 2,
                unformatOnSubmit: true,
                minimumValue: 0
            });
            $(this).data('autoNumericInstance', autoNumericInstance);
        });
    }
});
function reInitAutoNumberic(){
    // $('.autonumeric').autoNumeric('destroy');
    // $('.autonumeric').autoNumeric('init');
    if(window.currency_type=="eur"){
        $('.autoNumeric').each(function() {
            $(this).removeData('autoNumericInstance');
            $(this).removeData('autoNumeric');
            var autoNumericInstance = new AutoNumeric(this, {
                 currencySymbol: '€',
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 2,
                unformatOnSubmit: true,
                minimumValue: 0
            });

            $(this).data('autoNumericInstance', autoNumericInstance);
        });
    }
    else{
        $('.autoNumeric').each(function() {
            $(this).removeData('autoNumericInstance');
            $(this).removeData('autoNumeric');
            var autoNumericInstance = new AutoNumeric(this, {
                currencySymbol: '$',
                digitGroupSeparator: ',',
                decimalCharacter: '.',
                decimalPlaces: 2,
                unformatOnSubmit: true,
                minimumValue: 0
            });
            $(this).data('autoNumericInstance', autoNumericInstance);
        });
    }
}

function reInitNumberic(){
    $('.changenumber_w_o_d').each(function(i){
        var self = $(this);
        try{
            self.removeData('numFormat');
        }catch(err){
            console.log("Not an autonumeric field: " + self.attr("name"));
        }
    });
    if(window.currency_type=="eur"){
        $('.changenumber_w_o_d').number( true, 2 ,',', '.','€');
    }
    else{
        $('.changenumber_w_o_d').number( true, 2 ,'.', ',','$');
    }
}


function custom_currency_format(){
    $('.custom_currency_format').each(function(e){
        if($(this).attr('data-currency-type')=="eur"){
            $(this).number( true, 2 ,',', '.','€');
        }
        else{
            // $(this).number( true, 2 ,',', '.','$');
            $(this).number( true, 2 ,'.', ',','$');
        }
    });
}

$.fn.data_attributes = function () {
    var data = {};
    [].forEach.call(this.get(0).attributes, function (attr) {
        if (/^data-/.test(attr.name)) {
            var camelCaseName = attr.name.substr(5).replace(/-(.)/g, function ($0, $1) {
                return $1.toUpperCase();
            });
            data[camelCaseName] = attr.value;
        }
    });
    return data;
}
