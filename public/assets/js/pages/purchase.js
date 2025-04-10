$(document).ready(function () {
    /* Data table for the brand listing */

    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
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
            targets: [5]
        }],
        order: [8, 'desc'],
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
            data: 'dealer_name',
            name: 'dealer_name',
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
                        if (product[0] && product[0] != undefined) {
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
            name: 'status',
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
                        actions += ' <a href="' + getInvoiceUrl + '?id=' + elementId + '" target="_blank" data-id="' + elementId + '" class="waves-effect waves-light pe-2 text-info get-inovice" title="' + invoice + '"><i class="bx bx-receipt bx-sm"></i></a>';
                    }

                    actions += '</div>';

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
            { targets: 8, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) { }
    });

    if (notifyOrderId != '') {
        setTimeout(function () {
            $('#listTable').find('.view-row[data-id="' + notifyOrderId + '"]').trigger('click');
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


    $('body').on('click', '.view-row', function (event) {
        var id = $(this).attr('data-id');
        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#view-modal').modal('show');

                    if (result.data.status == 1 || result.data.status == 2) {
                        $('.rating_section').show();
                    } else {
                        $('.rating_section').hide();
                    }

                    if (result.data.communication_rating > 0) {
                        $('#communication_rating').prop("disabled", true);
                        $('#communication_rating').rating('rate', result.data.communication_rating);
                    } else {
                        $('#communication_rating').prop("disabled", false);
                        $('#communication_rating').rating('rate', 0);
                    }

                    if (result.data.transaction_rating > 0) {
                        $('#transaction_rating').prop("disabled", true);
                        $('#transaction_rating').rating('rate', result.data.transaction_rating);
                    } else {
                        $('#transaction_rating').prop("disabled", false);
                        $('#transaction_rating').rating('rate', 0);
                    }

                    if (result.data.delivery_rating > 0) {
                        $('#delivery_rating').prop("disabled", true);
                        $('#delivery_rating').rating('rate', result.data.delivery_rating);
                    } else {
                        $('#delivery_rating').prop("disabled", false);
                        $('#delivery_rating').rating('rate', 0);
                    }

                    // order id
                    $('.purchase_id').val(result.data.orderid);

                    // Customer Information
                    $('#view-modal').find('.name').html(result.data.dealer_name);
                    $('#view-modal').find('.phone').html(result.data.phone);
                    $('#view-modal').find('.email').html(result.data.email);
                    $('#view-modal').find('.street').html(result.data.street);
                    $('#view-modal').find('.house_number').html(result.data.house_number);
                    $('#view-modal').find('.zipcode').html(result.data.zipcode);
                    $('#view-modal').find('.city').html(result.data.city);
                    $('#view-modal').find('.country').html(result.data.country);
                    $('#view-modal').find('.payment_method').html(result.data.payment_method);

                    if (result.data.status == 2) {
                        $('.shipping-info').show();
                        $('#view-modal').find('.shipping_company').html(result.data.shipping_company);
                        $('#view-modal').find('.tracking_number').html(result.data.tracking_number);
                    } else {
                        $('.shipping-info').hide();
                        $('#view-modal').find('.shipping_company').html('');
                        $('#view-modal').find('.tracking_number').html('');
                    }

                    $('#view-modal').find('.cancel-proof a').attr("href", "javascript:void(0)");
                    if (result.data.status == 3) {
                        $('.shipping-info').hide();
                        $('#view-modal').find('.shipping_company').html('');
                        $('#view-modal').find('.tracking_number').html('');
                        $('.cancel-proof-div').show();
                        $('#view-modal').find('.cancel-proof a').attr("href", result.data.cancel_proof);
                    } else {
                        $('.cancel-proof-div').hide();
                        $('#view-modal').find('.cancel-proof a').attr("href", "javascript:void(0)");
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
                { targets: 7, visible: false },
            ],
            "drawCallback": function (settings) { },
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

    /* form submit */
    $('body').on('change', '#communication_rating,#transaction_rating,#delivery_rating', function (event) {
        var val = $(this).val();
        var input_name = $(this).attr('data-input');

        var dataString = new FormData($('#add-rating')[0]);

        // Helper function to generate star HTML with half-star support
        function getStarRatingHTML(rating) {
            let fullStars = Math.floor(rating);
            let halfStar = rating % 1 >= 0.5 ? 1 : 0;
            let emptyStars = 5 - fullStars - halfStar;
            let starsHTML = '';

            for (let i = 0; i < fullStars; i++) {
                starsHTML += '<svg class="star full-star" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#FFD700" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.568L24 9.423l-6 5.847L19.335 24 12 20.201 4.665 24 6 15.27 0 9.423l8.332-1.268z"/></svg>';
            }

            if (halfStar) {
                starsHTML += '<svg class="star half-star" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#FFD700" viewBox="0 0 24 24"><defs><linearGradient id="halfGrad"><stop offset="50%" stop-color="#FFD700"/><stop offset="50%" stop-color="#e0e0e0"/></linearGradient></defs><path d="M12 .587l3.668 7.568L24 9.423l-6 5.847L19.335 24 12 20.201 4.665 24 6 15.27 0 9.423l8.332-1.268z" fill="url(#halfGrad)"/></svg>';
            }

            for (let i = 0; i < emptyStars; i++) {
                starsHTML += '<svg class="star empty-star" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#e0e0e0" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.568L24 9.423l-6 5.847L19.335 24 12 20.201 4.665 24 6 15.27 0 9.423l8.332-1.268z"/></svg>';
            }

            return starsHTML;
        }

        // Updated SweetAlert with proper UI and half-star support
        Swal.fire({
            title: 'Rate this order',
            html: `
                <div class="rating-display">
                    ${getStarRatingHTML(val)}
                    <p class="mt-2">You selected ${val} star${val !== 1 ? 's' : ''}</p>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Rate',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            width: '400px', // Adjust width as needed
            customClass: {
                popup: 'custom-sweetalert-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: addRatingUrl,
                    type: 'POST',
                    data: dataString,
                    processData: false,
                    contentType: false,
                    success: function (result) {
                        if (result.data.communication_rating > 0) {
                            $('#communication_rating').rating('rate', result.data.communication_rating);
                            $('#communication_rating').prop("disabled", true);
                        }

                        if (result.data.transaction_rating > 0) {
                            $('#transaction_rating').rating('rate', result.data.transaction_rating);
                            $('#transaction_rating').prop("disabled", true);
                        }

                        if (result.data.delivery_rating > 0) {
                            $('#delivery_rating').rating('rate', result.data.delivery_rating);
                            $('#delivery_rating').prop("disabled", true);
                        }
                    },
                    error: function (error) {
                        console.log('Error');
                    }
                });
            }
        });
    });
});
