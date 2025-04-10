$(document).ready(function () {
    /* Data table for the brand listing */

    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: false,
        order: [6, 'desc'],
        ajax: {
            url: apiUrl,
            type: 'GET',
            headers: {
                'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            data: function (d) {
                d.search = $('input[type="search"]').val()
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
        columnDefs: [
            { type: 'extract-date', targets: [6] },
            { targets: 9, defaultContent: "-", visible: false }
        ],
        columns: [{
                data: 'id',
                name: 'id',
                visible: false
            },
            {
                data: 'invoice_number',
                name: 'invoice_number',
                render: function (data) {
                    return '#' + data;
                },
                visible: false
            },
            {
                data: 'customer_name',
                name: 'customer_name',
                width: '10%'
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
                data: 'rating',
                name: 'rating',
                render: function (data, type, full, meta) {
                    if(full['average'] > 0)
                        return '<input type="hidden" data-filled="mdi mdi-star text-primary" data-empty="mdi mdi-star-outline text-muted" data-fractions="2" class="rating" value='+full['average']+' disabled/>';
                    else
                        return '';
                }
            },
            {
                data: 'amount',
                name: 'amount',
                width: '10%'
            },
            {
                data: 'created_at',
                name: 'created_at',
                width: '10%',
                render: function (data, type, row, meta) {
                    return '<span>' + data + '</span>';
                }
            },
            {
                data: 'status_html',
                name: 'status_html',
                width: '15%'
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var elementId = full['id'];
                    var elementStatus = full['status'];

                    if (elementId) {
                        var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';

                        actions += '<a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light pe-2 view-row" title=' + view + '><i class="mdi mdi-eye-outline mdi-18px"></i></a>';

                        if (elementStatus != 3) {
                            actions += ' <a href="' + getInvoiceUrl + '?id=' + elementId + '" target="_blank" data-id="' + elementId + '" class="waves-effect waves-light pe-2 text-info get-inovice" title="'+ invoice +'"><i class="bx bx-receipt bx-sm"></i></a>';
                        }

                        actions += '</div>';

                        return actions;
                    }

                    return '';
                },
                width: '5%'
            },
            {
                name: 'updated_at',
                data: 'updated_at',
            }
        ],
        "drawCallback": function (settings) {
            $('.rating').rating();
        }
    });


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

    if (notifyOrderId != '') {
        setTimeout(function () {
            $('#listTable').find('.view-row[data-id="' + notifyOrderId + '"]').trigger('click');
        }, 1000)
    }

    $('body').on('click', '.view-row', function (event) {
        $('#orderid').val('0');
        $('#orderstatus').val('0');
        var id = $(this).attr('data-id');
        $('#orderid').val(id);
        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#view-modal').modal('show');

                    // Customer Information
                    $('#view-modal').find('.name').html(result.data.customer_name);
                    $('#view-modal').find('.phone').html(result.data.phone);
                    $('#view-modal').find('.email').html(result.data.email);
                    $('#view-modal').find('.street').html(result.data.street);
                    $('#view-modal').find('.house_number').html(result.data.house_number);
                    $('#view-modal').find('.zipcode').html(result.data.zipcode);
                    $('#view-modal').find('.city').html(result.data.city);
                    $('#view-modal').find('.country').html(result.data.country);
                    $('#view-modal').find('.payment_method').html(result.data.payment_method);

                    $('#orderstatus').val(result.data.status);
                    if (result.data.status == 2) {
                        $('.shipping-info').show();
                        $('#view-modal').find('.shipping_company').html(result.data.shipping_company);
                        $('#view-modal').find('.tracking_number').html(result.data.tracking_number);
                    } else {
                        $('.shipping-info').hide();
                        $('#view-modal').find('.shipping_company').html('');
                        $('#view-modal').find('.tracking_number').html('');
                    }


                    if(result.data.status == 1 || result.data.status == 2){
                        $('.rating_section').show();
                    }else{
                        $('.rating_section').hide();
                    }

                    if(result.data.communication_rating > 0){
                        $('#communication_rating').prop("disabled",true);
                        $('#communication_rating').rating('rate', result.data.communication_rating);
                    }else{
                        $('#communication_rating').prop("disabled",false);
                        $('#communication_rating').rating('rate', 0);
                        $('#communication_rating').rating();
                    }

                    if(result.data.transaction_rating > 0){
                        $('#transaction_rating').prop("disabled",true);
                        $('#transaction_rating').rating('rate', result.data.transaction_rating);
                    }else{
                        $('#transaction_rating').prop("disabled",false);
                        $('#transaction_rating').rating('rate', 0);
                        $('#transaction_rating').rating();
                    }

                    if(result.data.delivery_rating > 0){
                        $('#delivery_rating').prop("disabled",true);
                        $('#delivery_rating').rating('rate', result.data.delivery_rating);
                    }else{
                        $('#delivery_rating').prop("disabled",false);
                        $('#delivery_rating').rating('rate', 0);
                        $('#delivery_rating').rating();
                    }


                    $('#communication_rating').prop("disabled",true);
                    $('#transaction_rating').prop("disabled",true);
                    $('#delivery_rating').prop("disabled",true);

                    $('#view-modal').find('.cancel-proof a').attr("href", "javascript:void(0)");
                    if (result.data.status == 3) {
                        $('.shipping-info').hide();
                        $('.order-cancel').hide();
                        $('#view-modal').find('.shipping_company').html('');
                        $('#view-modal').find('.tracking_number').html('');
                        $('.cancel-proof-div').show();
                        $('#view-modal').find('.cancel-proof a').attr("href", result.data.cancel_proof);
                    } else {
                        $('.order-cancel').show();
                        $('.cancel-proof-div').hide();
                        $('#view-modal').find('.cancel-proof a').attr("href", "javascript:void(0)");
                    }

                    if (result.data.status == 0) {
                        $('.payment-confirm').show();
                    } else {
                        $('.payment-confirm').hide();
                    }

                    if (result.data.status == 1) {
                        $('.order-shipping').show();
                    } else {
                        $('.order-shipping').hide();
                    }
                    getNotification();
                    viewOrderProductListTable(id);
                }
            }
        });
    });

    var orderProductListTable = "";

    function viewOrderProductListTable(id) {
        if (orderProductListTable) {
            orderProductListTable.destroy();
        }
        orderProductListTable = $('#orderProductListTable').DataTable({
            language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: false,
            order: [7, 'desc'],
            ajax: {
                url: getOrderProductListUrl,
                type: 'GET',
                headers: {
                    'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                },
                data: function (d) {
                    d.search = $('input[type="search"]').val(),
                        d.order_id = id
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'product_brand',
                    name: 'product_name'
                },
                {
                    data: 'connections',
                    name: 'connections'
                },
                {
                    data: 'executions',
                    name: 'executions'
                },
                {
                    data: 'attributes',
                    name: 'attributes'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                }
            ],
            columnDefs: [
                { targets: 7, defaultContent: "-", visible: false },
            ],
            "drawCallback": function (settings) {},
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(),
                    dataAll = api.rows().data(),
                    total = 0;

                $.each(dataAll, function (key, value) {
                    total = value.total;
                })

                // Update footer
                $(api.column(6).footer()).html(total);
            }
        });
    }

    $('.payment-confirm').on('click', function (e) {
        let id = $('#orderid').val();
        e.preventDefault();
        Swal.fire({
            title: are_you_sure,
            text: you_want_to_confirm_payment,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_confirm_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: confirmOrderPaymentUrl + '?id=' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        $('#view-modal').modal('hide');
                        if (result.status) {
                            showMessage("success", result.message);
                        } else {
                            showMessage("error", result.message);
                        }
                        $('#listTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    })

    $('.order-shipping').on('click', function (e) {
        e.preventDefault();
        $('#view-modal').modal('hide');
        $('#order-shipping-modal').modal('show');
        $('#order-shipping-form')[0].reset();
        $('#sorderid').val($('#orderid').val());
        $('#sorderstatus').val($('#orderstatus').val());
    })

    $('#order-shipping-form').submit(function (event) {
        event.preventDefault();
        var id = $('#sorderid').val();
        var curruntStatus = $('#sorderstatus').val();
        var shippingCompany = $('#shipping_company').val();
        var trackingNumber = $('#tracking_number').val();

        Swal.fire({
            title: are_you_sure,
            text: are_you_sure_want_to_ship_this_order,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_ship_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: orderShippingUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                        curruntStatus: curruntStatus,
                        shipping_company: shippingCompany,
                        tracking_number: trackingNumber,
                    },
                    beforeSend: function () {
                        $(this).prop('disabled', true);
                    },
                    success: function (result) {
                        $(this).prop('disabled', false);
                        if (result.status) {
                            $('#order-shipping-modal').modal('hide');
                            showMessage("success", result.message);
                        } else if (!result.status && result.message) {
                            showMessage("error", result.message);
                            $('#order-shipping-modal').modal('hide');
                        } else {
                            first_input = "";
                            $('.invalid-feedback strong').html("");
                            $.each(result.error, function (key) {
                                if (first_input == "") first_input = key;
                                $('#order-shipping-form #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                                $('#order-shipping-form #' + key).addClass('is-invalid');
                            });
                            $('#order-shipping-form').find("#" + first_input).focus();
                        }
                        $('#listTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    $('.order-cancel').on('click', function (e) {
        e.preventDefault();
        $('#view-modal').modal('hide');
        $('#order-cancel-modal').modal('show');
        $('#order-cancel-form')[0].reset();
        $('#corderid').val($('#orderid').val());
        $('#corderstatus').val($('#orderstatus').val());
    })

    $('#order-cancel-form').submit(function (event) {
        event.preventDefault();
        var id = $('#corderid').val();
        var curruntStatus = $('#corderstatus').val();
        var cancelProof = $('#cancel_proof').val();
        var dataString = new FormData($('#order-cancel-form')[0]);

        Swal.fire({
            title: are_you_sure,
            text: are_you_sure_want_to_cancel_this_order,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_cancel_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: orderCanceledUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: dataString,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $(this).prop('disabled', true);
                    },
                    success: function (result) {
                        $(this).prop('disabled', false);
                        if (result.status) {
                            $('#order-cancel-modal').modal('hide');
                            showMessage("success", result.message);
                        } else if (!result.status && result.message) {
                            showMessage("error", result.message);
                            $('#order-cancel-modal').modal('hide');
                        } else {
                            first_input = "";
                            $('.invalid-feedback strong').html("");
                            $.each(result.error, function (key) {
                                if (first_input == "") first_input = key;
                                $('#order-cancel-form #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                                $('#order-cancel-form #' + key).addClass('is-invalid');
                            });
                            $('#order-cancel-form').find("#" + first_input).focus();
                        }
                        $('#listTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    /* form submit */
    $('#addt-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);

        var dataString = new FormData($('#add-form')[0]);

        $.ajax({
            url: addUrl,
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
                    $this[0].reset();
                    $('#edit-id').val(0);
                    showMessage("success", result.message);

                    $('#listTable').DataTable().ajax.reload();

                    setTimeout(function () {
                        $('#add-modal').modal('hide');
                    }, 300);
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#add-form #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                        $('#add-form #' + key).addClass('is-invalid');
                    });
                    $('#add-form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage('error', something_went_wrong);
            }
        });
    });

    $('form#filter-sales-form').on('change', function () {
        getFilterOptions();
        $('#listTable').DataTable().ajax.reload();
    })

    if ($('#filter-sales-form select').length > 0) {
        intializeFilterSelect();
    }

    function intializeFilterSelect() {
        if ($('#filter-sales-form select').length > 0) {
            $('#filter-sales-form select').each(function () {
                $(this).select2({
                    placeholder: $(this).attr('placeholder')
                });
            });
        }
    }

    function getFilterOptions() {
        let formData = new FormData($('form#filter-sales-form')[0]);
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
        $('form#filter-sales-form')[0].reset();
        $('#listTable').DataTable().ajax.reload();
        getFilterOptions();
        intializeFilterSelect();
    })
});
