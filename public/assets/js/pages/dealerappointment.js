$(document).ready(function () {
    /* Data table for the brand listing */

    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: false,

        ajax: {
            url: apiUrl,
            type: 'GET',
            headers: {
                'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            data: function (d) {
                d.search = $('input[type="search"]').val();
                if ($('#country').length > 0)
                    d['country'] = $('#country').val();

                if ($('#dealer').length > 0)
                    d['dealer'] = $('#dealer').val();

                if ($('#start_date').length > 0)
                    d['start_date'] = $('#start_date').val();

                if ($('#end_date').length > 0)
                    d['end_date'] = $('#end_date').val();
            },
        },
        columnDefs: [{
            type: 'extract-date',
            targets: [2]
        }],
        order: [6, 'desc'],
        columns: [{
                data: 'customer_name',
                name: 'customer_name',
                width: '20%'
            },
            {
                data: 'products',
                name: 'product.product_name',
                render: function (data, type, full, meta) {
                    var contactId = full['id'];
                    var products = full['products'];

                    if (contactId) {
                        var actions = '';
                        var i = 1;
                        products.forEach(product => {
                            if(product[0] && product[0] != undefined) {
                                actions += '<div class="productdetail-btn-gorup"><button class="custom-plus-btn text-dark" data-bs-toggle="collapse" data-bs-target="#collapse_' + product[0].id + i + '_' + meta.row + '" aria-expanded="false" aria-controls="collapse_' + product[0].id + i + '_' + meta.row + '"><i class="bx bx-plus-circle bx-sm"></i> ' + product[0].product_name + '</button></div><div class="collapse" id="collapse_' + product[0].id + i + '_' + meta.row + '"><div class="card border shadow-none card-body text-muted my-2 bg-light border-dark enquiry-productdetail-box"><table cellpadding="5" class="table table-striped addition-info-table mb-0" cellspacing="0" border="0" style="width:100%"><tbody><tr><td>' + brand + ':</td><td>' + product[0].brand_name + '</td></tr><tr><td>' + typep + ':</td><td>' + product[0].type_name + '</td></tr><tr><td>' + category + ':</td><td>' + product[0].category_name + '</td></tr></tbody></table></div></div>';
                                i++;
                            }
                        });

                        return actions;
                    }

                    return '';
                },
            },
            {
                data: 'appo_date',
                name: 'appo_date',
                width: '10%',
                render: function (data, type, row, meta) {
                    return '<span>' + data + '</span>';
                }
            },
            {
                data: 'appo_time',
                name: 'time',
                width: '10%'
            },
            {
                data: 'status',
                name: 'status',
                width: '10%'
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var elementId = full['id'];

                    if (elementId) {
                        var actions = '<a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light pe-2 view-appointment" title=' + view + '><i class="mdi mdi-eye-outline mdi-18px"></i></a>'

                        return actions;
                    }

                    return '';
                },
                width: '5%'
            },
            {
                data: 'updated_at',
                name: 'updated_at',
            }
        ],
        columnDefs: [
            { targets: 6, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) {}
    });

    if (notifyAppoId != '') {
        setTimeout(function () {
            $('#listTable').find('.view-appointment[data-id="' + notifyAppoId + '"]').trigger('click');
        }, 1000)
    }

    $(document).on('click', '.custom-plus-btn', function (e) {
        e.preventDefault();
        $('.collapse').collapse('hide');
        $(this).toggleClass('detailopen')
        if ($(this).hasClass('detailopen')) {
            $(this).find('i.bx-plus-circle').removeClass('bx-plus-circle').addClass('bx-minus-circle')
        } else {
            $(this).find('i.bx-minus-circle').removeClass('bx-minus-circle').addClass('bx-plus-circle');
        }
    })

    $(document).on('shown.bs.collapse', '.collapse', function (e) {
        $(this).prev().addClass('testDemo');
        $(this).prev().toggleClass('detailopen')
        if ($(this).prev().hasClass('detailopen')) {
            $(this).prev().find('i.bx-plus-circle').removeClass('bx-plus-circle').addClass('bx-minus-circle')
        } else {
            $(this).prev().find('i.bx-minus-circle').removeClass('bx-minus-circle').addClass('bx-plus-circle');
        }
    })

    $(document).on('hidden.bs.collapse', '.collapse', function (e) {
        $(this).prev().addClass('testDemo');
        $(this).prev().toggleClass('detailopen')
        if ($(this).prev().hasClass('detailopen')) {
            $(this).prev().find('i.bx-plus-circle').removeClass('bx-plus-circle').addClass('bx-minus-circle')
        } else {
            $(this).prev().find('i.bx-minus-circle').removeClass('bx-minus-circle').addClass('bx-plus-circle');
        }
    })


    if ($('#appo_time').length > 0) {
        $('#appo_time').timepicker({
            showMeridian: false,
            minuteStep: 30,
            defaultTime: null,
            icons: {
                up: 'mdi mdi-chevron-up',
                down: 'mdi mdi-chevron-down'
            },
            appendWidgetTo: "#appo_time_container",
        }).on('change', function (e) {
            onchangeform($(this));
        });
    }

    /* Open view appointment popup */
    $(document).on('click', '.view-appointment', function (e) {
        e.preventDefault();
        var tableRowData = listTable.row($(this).parents('tr')).data();
        $('#view-modal').modal('show');
        $('#view-modal .task_id').val(tableRowData.id);
        jQuery.ajax({
            url: getCustomerListUrl + '?appointment_id=' + tableRowData.id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#view-modal .appointment_id').val(result.allData.appointment_id);
                    $('#view-modal .appo_date').html(result.allData.appo_date_actual);
                    $('#view-modal .appo_time').html(result.allData.appo_time_actual);
                    $('#view-modal .title').html(result.allData.title);
                    $('#view-modal .note').html(result.allData.note);
                    $('#view-modal .status_name').html(result.allData.status_name);
                    $('#view-modal .appo_type_name').html(result.allData.appo_type_name);

                    $('#view-modal .zoom_met_join_url_link').hide();
                    $('#view-modal #zoom_met_join_url_view').attr("href","#");
                    if(result.allData.zoom_met_join_url){
                        $('#view-modal .zoom_met_join_url_link').show();
                        $('#view-modal #zoom_met_join_url_view').html(result.allData.zoom_met_join_url);
                        $('#view-modal #zoom_met_join_url_view').attr("href",result.allData.zoom_met_join_url);
                    }

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

                    $('#view-modal .shop_time').html(result.allData.shop_time);
                    if ((result.allData.status == 1) && result.allData.is_cancel_app) {
                        $('.status_cancel').show();
                    } else {
                        $('.status_cancel').hide();
                    }

                    if (result.allData.status == 1) {
                        $('.reschedule_appointment').show();
                    } else {
                        $('.reschedule_appointment').hide();
                    }

                    if (result.allData.status == 1) {
                        $('.status_confirmed').show();
                    } else {
                        $('.status_confirmed').hide();
                    }
                    if (result.allData.status == 2 || result.allData.status == 7) {
                        $('.status_complete').show();
                    } else {
                        $('.status_complete').hide();
                    }

                    if (result.allData.reschedule_appo_date && result.allData.reschedule_appo_time) {
                        $('.reschedule_appo_row').show();
                        $('.reschedule_appointment').hide();
                        $('#view-modal .reschedule_appo_date').html(result.allData.reschedule_appo_date);
                        $('#view-modal .reschedule_appo_time').html(result.allData.reschedule_appo_time);
                    } else {
                        $('.reschedule_appo_row').hide();
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
                    getNotification();
                }
            }
        });
    });

    $('.reschedule_appointment').on('click', function (e) {
        e.preventDefault();
        var appointmentId = $('#view-modal #appointment_id').val();
        $('#view-modal').modal('hide');
        $('#reschedule-modal').modal('show');
        $('#add_form')[0].reset();
        $('#add_form .is-invalid').removeClass('is-invalid');
        $('#reschedule-modal #appointment_id').val(appointmentId);
    })

    /* appointment status */
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
                $('#listTable').DataTable().ajax.reload();
            }
        });
    }

    /* form submit */
    $('#add_form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        var dataString = new FormData($('#add_form')[0]);
        $.ajax({
            url: rescheduleAppointmentUrl,
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
                    $('#reschedule-modal').modal('hide');
                    $('#listTable').DataTable().ajax.reload();
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                    if (result.message != appointment_already_taken_please_choose_another_time) {
                        $('#reschedule-modal').modal('hide');
                    }
                } else {
                    first_input = "";
                    $('.invalid-feedback strong').html("");
                    $('#add_form .is-invalid').removeClass('is-invalid');
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#add_form .error-' + key).html('<strong>' + result.error[key] + '</strong>');
                        $('#add_form #' + key).addClass('is-invalid');
                    });
                    $('#add_form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage('error', something_went_wrong);
            }
        });
    });

    $('form#filter-appointment-form').on('change', function () {
        getFilterOptions()
        $('#listTable').DataTable().ajax.reload();
    })

    if ($('#filter-appointment-form select').length > 0) {
        intializeFilterSelect();
    }

    function intializeFilterSelect() {
        if ($('#filter-appointment-form select').length > 0) {
            $('#filter-appointment-form select').each(function () {
                $(this).select2({
                    placeholder: $(this).attr('placeholder')
                });
            });
        }
    }

    function getFilterOptions() {
        let formData = new FormData($('form#filter-appointment-form')[0]);
        let dealerId = $('#dealer').val();
        $.ajax({
            url: countryDealerFilterUrl,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.data) {
                    $('#dealer').find('option').remove();
                    $('#dealer').append($("<option></option>"));
                    $.each(result.data, function (key, value) {
                        $('#dealer').append($("<option></option>").attr("value", value.id).text(value.text));
                    });
                    if (dealerId) {
                        $('#dealer').val(dealerId)
                    }
                    intializeFilterSelect();
                } else {
                    if (result.message && result.status != 1) {
                        showMessage("error", result.message);
                    } else {
                        showMessage("error", something_went_wrong);
                    }
                }
            },
            error: function (error) {
                showMessage("error", something_went_wrong);
            }
        });
    }

    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        $('form#filter-appointment-form')[0].reset();
        $('#listTable').DataTable().ajax.reload();
        getFilterOptions();
        intializeFilterSelect();
    })

});
