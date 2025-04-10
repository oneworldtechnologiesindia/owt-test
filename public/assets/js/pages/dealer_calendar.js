$(document).ready(function () {

    if ($('#event_time').length > 0) {
        $('#event_time').timepicker({
            showMeridian: false,
            minuteStep: 30,
            defaultTime: null,
            icons: {
                up: 'mdi mdi-chevron-up',
                down: 'mdi mdi-chevron-down'
            },
            appendWidgetTo: "#event_time_container",
        }).on('change', function (e) {
            onchangeform($(this));
        });
    }

    /* form submit */
    $('#eventadd_form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        var dataString = new FormData($('#eventadd_form')[0]);
        $.ajax({
            url: eventAddupdateUrl,
            type: 'POST',
            data: dataString,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $($this).find('button[type="submit"]').prop('disabled', true);
            },
            success: function (result) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                if (result.status) {
                    showMessage("success", result.message);
                    $('#event-modal').modal('hide');
                    main_calendar.refetchEvents();
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                    $('#event-modal').modal('hide');
                } else {
                    first_input = "";
                    $('.invalid-feedback strong').html("");
                    $('#eventadd_form .is-invalid').removeClass('is-invalid');
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#eventadd_form .error-' + key).html('<strong>' + result.error[key] + '</strong>');
                        $('#eventadd_form #' + key).addClass('is-invalid');
                    });
                    $('#eventadd_form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage('error', something_went_wrong);
            }
        });
    });

    $("#btn-delete-event").on('click', function (e) {
        var id = $('#event_id').val();
        Swal.fire({
            title: are_you_sure,
            text: you_wont_be_able_to_revert_this,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_delete_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: eventDeleteUrl + '?id=' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        $('#event-modal').modal('hide');
                        if (result.status) {
                            showMessage("success", result.message);
                        } else {
                            showMessage("error", result.message);
                        }
                        main_calendar.refetchEvents();
                    }
                });
            }
        });
    });

    $('body').on('click', '.status_cancel', function (event) {
        Swal.fire({
            title: are_you_sure,
            text: you_want_to_cancel_appointment,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_cancel_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $('#view-modal').modal('hide');
                setupdateStatus(5);
            }
        });
    });
    $('body').on('click', '.status_confirmed', function (event) {
        Swal.fire({
            title: are_you_sure,
            text: you_want_to_confirm_appointment,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_confirm_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $('#view-modal').modal('hide');
                setupdateStatus(2);
            }
        });
    });
    $('body').on('click', '.status_complete', function (event) {
        Swal.fire({
            title: are_you_sure,
            text: you_want_to_complete_appointment,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_complete_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $('#view-modal').modal('hide');
                setupdateStatus(3);
            }
        });
    });

    function setupdateStatus(status) {
        var appointment_id = $('#view-modal #appointment_id').val();
        $.ajax({
            url: updateStatusUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                appointment_id: appointment_id,
                status: status
            },
            beforeSend: function () {
                $(this).prop('disabled', true);
            },
            success: function (result) {
                $(this).prop('disabled', false);
                if (result.status) {
                    showMessage("success", result.message);
                } else {
                    showMessage("error", result.message);
                }
                main_calendar.refetchEvents();
            }
        });
    }
});
