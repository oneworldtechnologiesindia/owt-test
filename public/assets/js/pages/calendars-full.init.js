/*
Template Name: Skote - Admin & Dashboard Template
Author: Themesbrand
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Apex Chart init js
*/

! function ($) {
    "use strict";

    var CalendarPage = function () {};

    CalendarPage.prototype.init = function () {
            /* initialize the calendar */
            var calendarEl = document.getElementById('calendar');
            var addEvent = $("#event-modal"),
                modalTitle = $("#modal-title");

            var calendar = new FullCalendar.Calendar(calendarEl, {
                editable: false,
                locale: localLang,
                droppable: true,
                selectable: true,
                eventDisplay: 'block',
                themeSystem: 'bootstrap',
                buttonText: {
                    today: todayLang,
                },
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: calendarRightButton
                },
                customButtons: {
                    myCustomButton: {
                        text: addAppointmentLang,
                        click: function () {
                            window.location = createUrl;
                        }
                    },
                    myEventButton: {
                        text: addNewEventLang,
                        click: function () {
                            addEvent.modal('show');
                            $('#eventadd_form')[0].reset();
                            $('.invalid-feedback strong').text('');
                            $('#eventadd_form .is-invalid').removeClass('is-invalid');
                            $('#btn-delete-event').hide();
                            $("#event-title").val();
                            $('#event-category').val();
                            modalTitle.text(add_event);
                            $('#btn-save-event').text(save);
                            $("#event_id").val('');
                        }
                    }
                },
                eventClick: function (info) {
                    let eventData = info.event
                    let eventType = info.event.extendedProps.eventType
                    if (eventData.id) {
                        if (eventType == 'appointment') {
                            $('#view-modal .task_id').val(eventData.id);
                        }
                        if (eventType == 'event') {
                            $("#event_id").val(eventData.id);
                            $('#eventadd_form')[0].reset();
                            $('.invalid-feedback strong').text('');
                            $('#eventadd_form .is-invalid').removeClass('is-invalid');
                            $('#btn-delete-event').show();
                        }
                        jQuery.ajax({
                            url: getCustomerListUrl + '?appointment_id=' + eventData.id,
                            type: 'GET',
                            dataType: 'json',
                            success: function (result) {
                                if (result.status || result.events.length > 0) {
                                    if (result.events.length > 0 && eventType == 'event') {
                                        $("#title").val(result.events[0].title);
                                        $("#description").val(result.events[0].description);
                                        $("#event_date").datepicker("setDate", new Date(result.events[0].datetime));
                                        $("#event_time").timepicker('setTime', new Date(result.events[0].datetime));
                                        $('#category').val(result.events[0].category);
                                        modalTitle.text(edit_event);
                                        $('#btn-save-event').text(update);
                                        addEvent.modal('show');
                                    } else {
                                        $('#view-modal .appointment_id').val(result.allData.appointment_id);
                                        $('#view-modal .appo_date').html(result.allData.appo_date_actual);
                                        $('#view-modal .appo_time').html(result.allData.appo_time_actual);
                                        $('#view-modal .title').html(result.allData.title);
                                        $('#view-modal .note').html(result.allData.note);
                                        $('#view-modal .status_name').html(result.allData.status_name);

                                        $('#view-modal .product_name').html(result.allData.product_name);
                                        $('#view-modal .brand_name').html(result.allData.brand_name);

                                        $('#view-modal .dealer_name').html(result.allData.dealer_name);
                                        $('#view-modal .dealer_email').html(result.allData.dealer_email);
                                        $('#view-modal .dealer_phone').html(result.allData.dealer_phone);
                                        $('#view-modal .company_name').html(result.allData.company_name);
                                        $('#view-modal .dealer_shop_address').html(result.allData.dealer_shop_address);

                                        $('#view-modal .customer_email').html(result.allData.customer_email);
                                        $('#view-modal .customer_name').html(result.allData.customer_name);
                                        $('#view-modal .customer_phone').html(result.allData.customer_phone);
                                        $('#view-modal .customer_shop_address').html(result.allData.customer_shop_address);

                                        if (result.allData.reschedule_appo_date && result.allData.reschedule_appo_time) {
                                            $('.reschedule_appo_row').show();
                                            $('.reschedule_appointment').hide();
                                            $('#view-modal .reschedule_appo_date').html(result.allData.reschedule_appo_date);
                                            $('#view-modal .reschedule_appo_time').html(result.allData.reschedule_appo_time);
                                        } else {
                                            $('.reschedule_appo_row').hide();
                                        }

                                        $('#view-modal .shop_time').html(result.allData.shop_time);
                                        if ((result.allData.status == 1 || result.allData.status == 6) && result.allData.is_cancel_app) {
                                            $('.status_cancel').show();
                                        } else {
                                            $('.status_cancel').hide();
                                            $('.reschedule_appointment').hide();
                                        }

                                        if (result.allData.status == 6) {
                                            $('.reschedule_appointment_confirm').show();
                                        } else {
                                            $('.reschedule_appointment_confirm').hide();
                                        }

                                        if (result.allData.status == 1) {
                                            $('.status_confirmed').show();
                                        } else {
                                            $('.status_confirmed').hide();
                                        }
                                        if (result.allData.status == 2) {
                                            $('.status_complete').show();
                                        } else {
                                            $('.status_complete').hide();
                                        }

                                        if (result.allData.appo_expired) {
                                            $('.status_complete').hide();
                                            $('.reschedule_appointment_confirm').hide();
                                            $('.reschedule_appointment').hide();
                                            $('.status_cancel').hide();
                                            $('.status_confirmed').hide();
                                        }

                                        $('#stars li.selected').removeClass('selected');
                                        if (result.allData.status == 3) {
                                            // if (result.allData.rating) {
                                            //     var onStar = parseInt(result.allData.rating);
                                            //     var stars = $('#stars li').parent().children('li.star');
                                            //     for (var i = 0; i < onStar; i++) {
                                            //         $(stars[i]).addClass('selected');
                                            //     }
                                            // }
                                            if(result.allData.rating > 0){
                                                $('.rating').rating('rate', result.allData.rating);
                                                $('.rating').prop("disabled",true);
                                            }else{
                                                $('.rating').rating('rate', 0);
                                                $('.rating').prop("disabled",false);
                                            }
                                            var total_rating=parseFloat(result.allData.rating);

                                            $('.rating-star .badge.bg-info').text(total_rating.toFixed(1));
                                            $('.rating_stars_div').show();
                                        } else {
                                            $('.rating_stars_div').hide();
                                        }
                                        $('#view-modal').modal('show');
                                    }
                                }
                            }
                        });


                    }
                },
                events: function (fetchInfo, successCallback, failureCallback) { //include the parameters fullCalendar supplies to you!
                    var eventsDisplay = [];
                    jQuery.ajax({
                        url: getCustomerListUrl,
                        type: 'GET',
                        dataType: 'json',
                        success: function (result) {
                            if (result.status) {
                                $.each(result.allData, function (key, row) {
                                    if (row.status == 2 || row.status == 3 || row.status == 7) {
                                        var datetimeStart = '';
                                        if (row.reschedule_date_time != '' && row.status == 7) {
                                            datetimeStart = row.reschedule_date_time;
                                        } else {
                                            datetimeStart = row.date_time;
                                        }
                                        eventsDisplay.push({
                                            id: row.appointment_id,
                                            title: row.title_small,
                                            start: datetimeStart,
                                            className: row.bg_type,
                                            editable: false,
                                            eventType: 'appointment'
                                        });
                                    }
                                });
                            }

                            if (result.events.length > 0) {
                                $.each(result.events, function (key, row) {
                                    eventsDisplay.push({
                                        id: row.id,
                                        title: (row.title.length > 10) ? row.title.slice(0, 10 - 1) + ' ...' : row.title,
                                        start: row.datetime,
                                        className: 'bg-' + row.category,
                                        editable: true,
                                        eventType: 'event',
                                        eventdatetime: new Date(row.datetime),
                                        category: row.category,
                                        description: row.description
                                    });
                                });
                            }
                            successCallback(eventsDisplay); //you have to pass the list of events to fullCalendar!
                        }
                    });
                },
                eventDrop: function (info) {
                    let eventData = info.event
                    var newDate = new Date(eventData.start);
                    if (newDate > Date.now()) {
                        var date = new Date(eventData.start),
                            mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                            day = ("0" + date.getDate()).slice(-2),
                            newdate = [date.getFullYear(), mnth, day].join("-");
                        Swal.fire({
                            title: are_you_sure,
                            text: you_want_to_change_this,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: yes_change_it,
                            cancelButtonText: no_cancel,
                            confirmButtonClass: 'btn btn-success mt-2',
                            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
                            buttonsStyling: false,
                        }).then(function (result) {
                            if (result.value) {
                                $.ajax({
                                    url: eventAddupdateUrl,
                                    type: 'POST',
                                    data: {
                                        event_id: eventData.id,
                                        title: eventData.title,
                                        description: eventData.extendedProps.description,
                                        eventType: 'event',
                                        eventdate: newdate,
                                        category: eventData.extendedProps.category,
                                    },
                                    dataType: 'json',
                                    success: function (result) {
                                        if (result.status) {
                                            showMessage("success", result.message);
                                        } else {
                                            showMessage("error", result.message);
                                        }
                                    }
                                });
                            }
                        });
                    } else {
                        info.revert();
                    }
                },
                eventTimeFormat: { // like '01:30 pm'
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }
            });
            calendar.render();
            main_calendar = calendar;
        },
        //init
        $.CalendarPage = new CalendarPage, $.CalendarPage.Constructor = CalendarPage
}(window.jQuery),

//initializing
function ($) {
    "use strict";
    $.CalendarPage.init()
}(window.jQuery);
