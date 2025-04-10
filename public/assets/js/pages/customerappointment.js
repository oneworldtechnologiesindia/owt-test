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
                d.search = $('input[type="search"]').val()
            },
        },
        columnDefs: [{
            type: 'extract-date',
            targets: [2]
        }],
        order: [6, 'desc'],
        columns: [{
                data: 'dealer_name',
                name: 'dealer_name',
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
                name: 'updated_at'
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
                    if ((result.allData.status == 1 || result.allData.status == 6 || result.allData.status == 2) && result.allData.is_cancel_app) {
                        $('.status_cancel').show();
                    } else {
                        $('.status_cancel').hide();
                    }

                    if (result.allData.status == 6) {
                        $('.reschedule_appointment_confirm').show();
                    } else {
                        $('.reschedule_appointment_confirm').hide();
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

    /* 1. Visualizing things on Hover - See next part for action on click */
    $('#stars li').on('mouseover', function () {
        var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on
        // Now highlight all the stars that's not after the current hovered star
        $(this).parent().children('li.star').each(function (e) {
            if (e < onStar) {
                $(this).addClass('hover');
            } else {
                $(this).removeClass('hover');
            }
        });

    }).on('mouseout', function () {
        $(this).parent().children('li.star').each(function (e) {
            $(this).removeClass('hover');
        });
    });
    /* 2. Action to perform on click */
    $('#stars li').on('click', function () {
        var onStar = parseInt($(this).data('value'), 10); // The star currently selected
        var stars = $(this).parent().children('li.star');
        for (i = 0; i < stars.length; i++) {
            $(stars[i]).removeClass('selected');
        }
        for (i = 0; i < onStar; i++) {
            $(stars[i]).addClass('selected');
        }
        // JUST RESPONSE (Not needed)
        var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
        setRating(ratingValue)

    });

    function setRating(rating) {
        var appointment_id = $('#view-modal #appointment_id').val();
        $.ajax({
            url: updateRatingUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                appointment_id: appointment_id,
                rating: rating
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
                $('#calendar').fullCalendar('refetchEvents');
            }
        });
    }

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
                setupdateStatus(4);
            }
        });
    });

    $('body').on('click', '.reschedule_appointment_confirm', function (event) {
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
                setupdateStatus(7);
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
    $('body').on('change','.rating',function (event) {
        var val = $(this).val();
        var appointment_id = $('#view-modal #appointment_id').val();
        $.ajax({
            url: updateRatingUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                appointment_id: appointment_id,
                rating: val
            },
            beforeSend: function () {
                $(this).prop('disabled', true);
            },
            success: function (result) {
                $('.rating').prop("disabled",true);
            }
        });
    });
});
