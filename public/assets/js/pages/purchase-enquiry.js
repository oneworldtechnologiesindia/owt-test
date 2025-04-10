$(document).ready(function () {
    /* Data table for the brand listing */

    if ($('#listTable').length > 0) {
        var listTable = $('#listTable').DataTable({
            language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: false,
            order: [4, 'desc'],
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
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
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
                                if(product[0] && product[0] != undefined){
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
                    data: 'offers',
                    name: 'offers',
                    sortable: false,
                    "width": "40%",
                    "render": function (data, type, row, meta) {
                        let html = '';
                        if (data.length > 0) {

                            html += '<div class="header-table border-bottom border-primary offer-info-enquiry d-flex align-items-center mb-2">';
                            html += '<i class="bx bxs-offer text-primary bx-sm pe-1 opacity-0"></i>';
                            html += '<div class="offer-price pe-3"><p class="m-0">' + amount + '</p></div>';
                            html += '<div class="countdown-offer pe-3"><p class="m-0">' + gultigkeit + '</p></div>';
                            html += '<div></div>';
                            html += '</div>';

                            $.each(data, function (index, value) {
                                html += '<div class="offer-info-enquiry mb-2 d-flex align-items-center">';
                                html += '<i class="bx bxs-offer text-primary bx-sm pe-1"></i>';

                                // console.log(value.amount);
                                html += '<div class="offer-price pe-3"><p class="m-0">' + value.amount + '</p></div>';

                                // timer
                                var startdatetimeObj = row.now_time;
                                var startdatetimeObjTmp = startdatetimeObj.replace(' ', 'T').split(/[^0-9]/);
                                var startdatetime = new Date(Date.UTC(startdatetimeObjTmp[0], startdatetimeObjTmp[1] - 1, startdatetimeObjTmp[2], startdatetimeObjTmp[3], startdatetimeObjTmp[4], startdatetimeObjTmp[5]));

                                var enddatetimeObj = value.valid_upto;
                                var enddatetimeObjTmp = enddatetimeObj.replace(' ', 'T').split(/[^0-9]/);
                                var enddatetime = new Date(Date.UTC(enddatetimeObjTmp[0], enddatetimeObjTmp[1] - 1, enddatetimeObjTmp[2], enddatetimeObjTmp[3], enddatetimeObjTmp[4], enddatetimeObjTmp[5]));

                                var addsecound = 1
                                var x = setInterval(function () {
                                    var startdatetimeObjTmp = startdatetimeObj.replace(' ', 'T').split(/[^0-9]/);
                                    var startdatetime = new Date(Date.UTC(startdatetimeObjTmp[0], startdatetimeObjTmp[1] - 1, startdatetimeObjTmp[2], startdatetimeObjTmp[3], startdatetimeObjTmp[4], startdatetimeObjTmp[5]) + 1);
                                    startdatetime.setSeconds(startdatetime.getSeconds() + addsecound);
                                    var distance = enddatetime - startdatetime;

                                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                    var viewtime = hours + "h " + minutes + "m " + seconds + "s ";
                                    if (document.getElementById("time-" + meta.row + value.id) !== null) {
                                        document.getElementById("time-" + meta.row + value.id).innerHTML = hours + "h " + minutes + "m " + seconds + "s ";

                                        addsecound++;
                                        if (distance < 0) {

                                            clearInterval(x);
                                            document.getElementById("time-" + meta.row + value.id).innerHTML = "0h 0m 0s";
                                        }
                                    }
                                }, 1000);
                                html += '<div class="countdown-offer pe-3"><p id="time-' + meta.row + value.id + '" class="m-0" ></p></div>';
                                html += '<button type="button" class="view_offer_btn btn btn-primary btn-sm btn-rounded waves-effect waves-light offer-view-row-index" data-id="' + value.id + '" data-offerstatus="' + ((row['status_name'] == 'Offer Expired' || row['status_name'] == 'Expired') ? 'expired' : 'valid') + '">' + view_offer + '</button>';
                                html += '</div>';
                            });
                            return html;
                        } else {
                            return '-';
                        }
                    }
                },
                {
                    data: 'status_name',
                    name: 'status',
                    render: function (_, _, full) {
                        var contactId = full['id'];
                        var status = full['status_name'];

                        if (contactId) {
                            var actions = '';

                            let badgeClass = 'bg-primary';
                            if (status == 'Accepted')
                                badgeClass = 'bg-success';

                            if (status == 'Rejected')
                                badgeClass = 'bg-danger';

                            if (status == 'Expired')
                                badgeClass = 'bg-warning';

                            if (status == 'Offer Sent' || status == 'Offer Received')
                                badgeClass = 'bg-info';

                            if (status == 'Offer Expired')
                                badgeClass = 'bg-warning';

                            actions += '<h5><span class="badge ' + badgeClass + '">' + status + '</span></h5>';


                            return actions;
                        }

                        return '';
                    },
                    width: '5%'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    "width": "5%"
                },
                {
                    sortable: false,
                    render: function (_, _, full) {
                        var contactId = full['id'];

                        if (contactId) {
                            var actions = '';
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 view-row" title=' + view + '><i class="mdi mdi-eye-outline mdi-18px"></i></a>';
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title=' + deleteLang + '><i class="bx bx-trash bx-sm"></i></a>';

                            return actions;
                        }

                        return '';
                    },
                    "width": "5%"
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
    }

    var offerViewTableIndex = "";
    $('body').on('click', '.offer-view-row-index', function (event) {
        var offer_id = $(this).attr('data-id');
        $('#offer-view-modal').find('.offer_status').attr('data-id', offer_id);
        $('#accept-offer')[0].reset();
        $('#accept-offer').find('.is-invalid').removeClass('is-invalid');
        $('#accept-offer').find('.invalid-feedback').html('');
        if ($(this).attr('data-offerstatus') == 'expired') {
            $('.offer_status ').hide()
        } else {
            $('.offer_status ').show()
        }
        $('#offer-view-modal').modal('show');
        if (offerViewTableIndex) {
            offerViewTableIndex.destroy();
        }
        offerViewTableIndex = $('#offerViewTableIndex').DataTable({
            language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: false,
            order: [0, 'desc'],
            ajax: {
                url: getofferDetailUrl,
                type: 'GET',
                headers: {
                    'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                },
                data: function (d) {
                    d.search = $('input[type="search"]').val(),
                        d.offer_id = offer_id
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'brand_name',
                    name: 'brand_name'
                },
                {
                    data: 'offer_amount',
                    name: 'offer_amount',
                    render: function (_, _, full) {
                        var html = '<span class="custom_currency_format" data-currency-type="'+full['offer_currency']+'">'+full['offer_amount']+'</span>';
                        return html;
                    }
                },
            ],
            "drawCallback": function (settings) {
                getNotification();
            },
            "initComplete": function( settings, json ) {
                custom_currency_format();
            },
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(),
                    dataAll = api.rows().data(),
                    total = 0;
                var offer_currency='usd';
                var delivery_time='-';
                var delivery_time_type='-';
                $.each(dataAll, function (key, value) {
                    // total += value.qty * value.offer_amount;
                    var final_amount=value.qty * value.offer_amount_without_format;
                    offer_currency=value.offer_currency
                    if(value.delivery_time != null){
                        delivery_time=value.delivery_time
                        delivery_time_type=value.delivery_time_type
                    }

                    if(value.vat_rate){
                        var total_vat_amount=(final_amount.toFixed(2)*value.vat_rate)/100;
                        var final_amount=parseFloat(total_vat_amount)+parseFloat(final_amount);
                    }
                    total += final_amount;
                })

                // Update footer
                if(offer_currency=="eur"){
                    total=total.toString().replace(',','.');
                    total=total.toString().replace('.',',');
                }
                var html = '<span class="custom_currency_format" data-currency-type="'+offer_currency+'">'+total+'</span>';

                $('.delivery-date-time').html(delivery_time+'/'+delivery_time_type);
                $(api.column(4).footer()).html(html);
            }
        });
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

    /* form submit */

    $('#purchase_enquiry_form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);

        var dataString = new FormData($('#purchase_enquiry_form')[0]);
        let existinInputs = [];
        $(".sproduct-options-container select").each(function () {
            existinInputs.push($(this).attr('id'))
        });
        $(".form-group-selected_product input.product_qty").each(function () {
            existinInputs.push($(this).attr('id'))
        });
        dataString.append('extrafields', existinInputs);


        $.get("https://ipinfo.io", function(response) {
            var countryName = getCountryByCode(response.country);
            dataString.append('country', countryName);
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
                    if (result.status) {

                        showMessage("success", result.message);
                        setTimeout(
                            function () {
                                window.location.href = indexUrl;
                            }, 3000);

                    } else if (!result.status && result.message) {

                        showMessage("error", result.message);
                        $($this).find('button[type="submit"]').prop('disabled', false);
                        window.location.href = indexUrl;
                    } else {
                        first_input = "";
                        $($this).find('button[type="submit"]').prop('disabled', false);
                        $('.invalid-feedback strong').html("");
                        $.each(result.error, function (key) {
                            if (key == 'selected_product_ids') {
                                keyid = 'product_id';
                            } else {
                                keyid = key
                            }
                            if (first_input == "") first_input = keyid;
                            $('#purchase_enquiry_form #' + keyid + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                            $('#purchase_enquiry_form #' + keyid).addClass('is-invalid');
                        });
                        $('#purchase_enquiry_form .is-invalid').first().focus();
                    }
                },
                error: function (error) {
                    $($this).find('button[type="submit"]').prop('disabled', false);
                    showMessage('error', something_went_wrong);
                }
            });
        }, "jsonp");
    });

    /* delete brand */
    $('body').on('click', '.delete-row', function (event) {
        var id = $(this).attr('data-id');
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
                    url: deleteUrl + '?id=' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
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
    });


    $("form#purchase_enquiry_form .product-filter-container select").change(function () {
        getFilterOptions();
    });

    function getFilterOptions(type = 'form') {
        let formData = ''
        let brand_id = [];
        let producttype_id = [];
        let productcategory_id = [];
        let product_id = [];

        if (type == 'form') {
            formData = new FormData($('form#purchase_enquiry_form')[0]);
            for (const pair of formData.entries()) {
                if (`${pair[0]}` == 'brand_id[]') {
                    brand_id.push(`${pair[1]}`);
                }
                if (`${pair[0]}` == 'producttype_id[]') {
                    producttype_id.push(`${pair[1]}`);
                }
                if (`${pair[0]}` == 'productcategory_id[]') {
                    productcategory_id.push(`${pair[1]}`);
                }
                if (`${pair[0]}` == 'product_id[]') {
                    product_id.push(`${pair[1]}`);
                }
            }
        }

        $.ajax({
            url: filterOptionsUrl,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#purchase_enquiry_form .product-filter-container select').select2("enable", false)
            },
            success: function (result) {
                $('#purchase_enquiry_form .product-filter-container select').select2("enable")
                if (result.data) {
                    $('#purchase_enquiry_form .product-filter-container select').find('option').remove();
                    $.each(result.data, function (id, value_array) {
                        if (id == 'product_id') {
                            $('#' + id).append('<option></option>');
                        }
                        $('#' + id).select2({
                            placeholder: $('#' + id).attr('placeholder'),
                            allowClear: $('#' + id).attr('data-allow-clear'),
                            data: value_array
                        });
                    })
                    if (brand_id) {
                        $('#brand_id').val(brand_id)
                    }
                    if (producttype_id) {
                        $('#producttype_id').val(producttype_id)
                    }
                    if (productcategory_id) {
                        $('#productcategory_id').val(productcategory_id)
                    }
                    filterSelect2();
                    $('#product_id').select2('enable');
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

    $("#product_id").change(function (e) {
        e.preventDefault();
        let productId = $(this).val(),
            productName = $(this).find("option:selected").text(),
            exists_id = 0,
            html = '',
            dataObject = {
                id: productId
            };

        $.ajax({
            url: getProductInfo,
            type: 'POST',
            data: dataObject,
            beforeSend: function () {
                $('#purchase_enquiry_form').find('button[type="submit"]').prop('disabled', true);
            },
            success: function (response) {
                $('#purchase_enquiry_form').find('button[type="submit"]').prop('disabled', false);
                $('.list-selected-products').find('.notice-empty').removeClass('d-block').css('display', 'none');

                $(".selected_product_ids").each(function () {
                    if ($(this).val() == productId) {
                        exists_id = 1;
                    }
                });

                $("#product_id option").first().prop("selected", true);
                filterSelect2();

                if (exists_id == 0 && productId != undefined && productId != "") {
                    html += '<div class="selected-product-group-container p-3 mb-4 product-' + productId + '">';
                    html += '<div class="form-group-selected_product pb-3">';
                    html += '<div class="sproduct-title w-md-50">';
                    html += '<h5 class="mb-0">' + productName + '</h5>';
                    html += '<input type="hidden" name="selected_product_ids[]" class="selected_product_ids" value="' + productId + '">';
                    html += '</div>';
                    html += '<div class="sproduct-action ps-md-3">';
                    html += '<div class="row">';
                    html += '<label class="col-2 col-form-label">' + qty + ':</label>';
                    html += '<div class="col-8">';
                    html += '<input data-toggle="touchspin" type="text" value="1" class="product_qty" name="product_qty_' + productId + '" id="product_qty_' + productId + '">';
                    html += '<span class="invalid-feedback" id="productenquiry_ids_' + productId + 'Error" data-ajax-feedback="productenquiry_ids_' + productId + '" role="alert"></span>',
                        html += '</div>';
                    html += '<div class="col-2">';
                    html += '<button type="button" class="btn btn-danger btn-circle remove-selected_product-btn"><i class="bx bx-trash"></i> </button>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                    html += '<div class="sproduct-options-container mt-3">';
                    html += '<div class="row">';
                    if (!($.isEmptyObject(response.data.connection))) {
                        html += '<div class="col-md-4">';
                        html += '<div class="mb-3">';
                        html += '<label for="productconnection_ids_' + productId + '" class="form-label">' + product_connection + '</label>';
                        html += '<select name="productconnection_ids_' + productId + '[]" id="productconnection_ids_' + productId + '" class="form-control select2 select2-multiple productconnection_ids" multiple>';
                        $.each(response.data.connection, function (key, value) {
                            html += '<option value="' + value.id + '">' + value.connection_name + '</option>'
                        })
                        html += '</select>'
                        html += '<span class="invalid-feedback" id="productconnection_ids_' + productId + 'Error" data-ajax-feedback="productconnection_ids_' + productId + '" role="alert"></span>',
                            html += '</div>'
                        html += '</div>';
                    }

                    if (!($.isEmptyObject(response.data.execution))) {
                        html += '<div class="col-md-4">';
                        html += '<div class="mb-3">';
                        html += '<label for="productexecution_ids_' + productId + '" class="form-label">' + product_execution + '</label>';
                        html += '<select name="productexecution_ids_' + productId + '[]" id="productexecution_ids_' + productId + '" class="form-control select2 select2-multiple productexecution_ids" multiple>';
                        $.each(response.data.execution, function (key, value) {
                            html += '<option value="' + value.id + '">' + value.execution_name + '</option>'
                        })
                        html += '</select>'
                        html += '<span class="invalid-feedback" id="productexecution_ids_' + productId + 'Error" data-ajax-feedback="productexecution_ids_' + productId + '" role="alert"></span>',
                            html += '</div>'
                        html += '</div>';
                    }
                    if (!($.isEmptyObject(response.data.enquiry))) {
                        html += '<div class="col-md-4">';
                        html += '<div class="mb-3">';
                        html += '<label for="productenquiry_ids_' + productId + '" class="form-label">' + product_attributes + '</label>';
                        html += '<select name="productenquiry_ids_' + productId + '[]" id="productenquiry_ids_' + productId + '" class="form-control select2 select2-multiple productenquiry_ids" multiple>';
                        $.each(response.data.enquiry, function (key, value) {
                            if (key != '3') {
                                html += '<option value="' + key + '">' + value + '</option>'
                            }
                        })
                        html += '</select>'
                        html += '<span class="invalid-feedback" id="productenquiry_ids_' + productId + 'Error" data-ajax-feedback="productenquiry_ids_' + productId + '" role="alert"></span>',
                            html += '</div>'
                        html += '</div>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                    $('.list-selected-products').append(html);
                    $('.list-selected-products').animate({
                        scrollTop: $('.selected-product-group-container:last-child').offset().top
                    }, 1000);

                    $('.product-' + productId + ' .productconnection_ids').select2({
                        placeholder: select_connection
                    });

                    $('.product-' + productId + ' .productexecution_ids').select2({
                        placeholder: select_ausfuhrung
                    });

                    $('.product-' + productId + ' .productenquiry_ids').select2({
                        placeholder: select_attribute
                    });

                    productQty();
                }
            },
            error: function (error) {
                console.log(error)
            }
        });
    })

    function productQty() {

        //Bootstrap-TouchSpin
        var defaultOptions = {
            min: 1,
            max: 1000000000,
            mousewheel: false
        };

        $('[data-toggle="touchspin"]').each(function (idx, obj) {
            var objOptions = $.extend({}, defaultOptions, $(obj).data());
            $(obj).TouchSpin(objOptions);
        });
    }

    if ($('.list-selected-products').length > 0) {
        $('.list-selected-products').slimScroll({
            height: '210px',
            alwaysVisible: true,
            color: '#000',
            size: '5px',
            railVisible: true,
            railColor: '#f3f3f3',
            railOpacity: 1,
        });
    }

    $(document).on('click', '.remove-selected_product-btn', function (e) {
        e.preventDefault();
        $(this).closest('.selected-product-group-container').remove();
        if ($('.selected-product-group-container').length < 1) {
            $('.list-selected-products').find('.notice-empty').addClass('d-block');
        }
    });

    if ($('#purchase_enquiry_form').length > 0) {
        filterSelect2();
    }

    function filterSelect2() {
        if ($('#purchase_enquiry_form .product-filter-container select').length > 0) {
            $('#purchase_enquiry_form .product-filter-container select').each(function () {
                $(this).select2({
                    placeholder: $(this).attr('placeholder')
                });
            });
        }
    }

    $('body').on('click', '.view-row', function (event) {
        var id = $(this).attr('data-id');

        $('#customer_enquiry_id').val(id);
        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                window.location = result.url;
            }
        });
    });

    $('body').on('click', '.offer_status', function (event) {
        event.preventDefault();
        $('#confirm-offer-modal').modal('show');
        $('#offer-view-modal').modal('hide');
        $('#offerid').val($(this).attr('data-id'));
        $('#offerstatus').val($(this).attr('data-code'));
    });

    $('#accept-offer').submit(function (event) {
        event.preventDefault();
        formData = new FormData($('form#accept-offer')[0]);
        var id = $('#offerid').val();
        var status_from = $('#offerstatus').val();
        var payment_method = $('#payment_method').val();
        var msg = "";
        if (status_from == 2) {
            msg = are_you_sure_want_to_accepted_this_offers;
        } else {
            msg = are_you_sure_want_to_rejected_this_offers;
        }
        Swal.fire({
            title: are_you_sure,
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_accept_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                if(payment_method=="2"){
                    $.ajax({
                    url: getCheckoutSessionUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#accept-offer').find('button[type="submit"]').prop('disabled', true);
                    },
                    success: function (result) {
                        $('#accept-offer').find('button[type="submit"]').prop('disabled', false);
                        if (result.status) {
                            if (result.redirect_url) {
                                location.href=result.redirect_url;
                            } else {
                                showMessage("error", something_went_wrong);
                            }
                        } else if (!result.status && result.message) {
                            showMessage("error", result.message);
                            $('#accept-offer').find('button[type="submit"]').prop('disabled', false);
                            $('#confirm-offer-modal').modal('hide');
                        } else {
                            first_input = "";
                            $('.invalid-feedback strong').html("");
                            $.each(result.error, function (key) {
                                if (first_input == "") first_input = key;
                                $('#accept-offer #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                                $('#accept-offer #' + key).addClass('is-invalid');
                            });
                            $('#accept-offer').find("#" + first_input).focus();
                        }
                    }
                });
                }
                else{
                    $.ajax({
                        url: updateOfferStatusUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            $('#accept-offer').find('button[type="submit"]').prop('disabled', true);
                        },
                        success: function (result) {
                            $('#accept-offer').find('button[type="submit"]').prop('disabled', false);
                            if (result.status) {
                                $('#confirm-offer-modal').modal('hide');
                                showMessage("success", result.message);
                                if ($('#listTable').length == 0) {
                                    setTimeout(
                                        function () {
                                            window.location.href = mypurchasesUrl;
                                        }, 3000);
                                }
                            } else if (!result.status && result.message) {
                                showMessage("error", result.message);
                                $('#accept-offer').find('button[type="submit"]').prop('disabled', false);
                                $('#confirm-offer-modal').modal('hide');
                            } else {
                                first_input = "";
                                $('.invalid-feedback strong').html("");
                                $.each(result.error, function (key) {
                                    if (first_input == "") first_input = key;
                                    $('#accept-offer #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                                    $('#accept-offer #' + key).addClass('is-invalid');
                                });
                                $('#accept-offer').find("#" + first_input).focus();
                            }

                            if ($('#listTable').length > 0) {
                                $('#listTable').DataTable().ajax.reload();
                            } else {
                                $('#offerListTable').DataTable().ajax.reload();
                            }
                        }
                    });
                }
            }
        });
    });

    var offerViewTable = "";
    $('body').on('click', '.offer-view-row', function (event) {
        $('#offer-view-modal').find('.offer_status').attr('data-id', '');
        var offer_id = $(this).attr('data-id');
        $('#offer-view-modal').modal('show');
        $('#offer-view-modal').find('.offer_status').attr('data-id', offer_id);
        if (offerViewTable) {
            offerViewTable.destroy();
        }
        offerViewTable = $('#offerViewTable').DataTable({
            language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: false,
            order: [0, 'desc'],
            ajax: {
                url: getofferDetailUrl,
                type: 'GET',
                headers: {
                    'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                },
                data: function (d) {
                    d.search = $('input[type="search"]').val(),
                        d.offer_id = offer_id
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                },
                {
                    data: 'brand_name',
                    name: 'brand_name'
                },
                {
                    data: 'offer_amount',
                    name: 'offer_amount'
                },
            ],
            "drawCallback": function (settings) {}
        });
    });

    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        getFilterOptions('reset');
    })

    $('.enquiry-documents').on('click', function (e) {
        e.preventDefault();
        window.open($(this).attr('href') + '?type=' + $(this).data('type') + '&offerid=' + $('#offerid').val(), "_blank");
    })

    $('body').on('click','.view_offer_btn',function(){
        var val = $(this).attr('data-id');
        $("#payment_method option[value='2']").attr('disabled', false);


        $.ajax({
            url: getDealerRatingUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                offer_id: val,
            },
            success: function (result) {
                $('.rating').rating('rate', result.average_rating);
                $('.rating').prop("disabled",true);

                $('.total_feedback').text(result.total_feedback);
                $('.num_rating').text(result.average_rating);
            }
        });
        $.ajax({
            url: getDealerInfoUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                offer_id: val,
            },
            success: function (result) {
               // if dealer had connected their stripe then option available otherwise disable
                if(result.account_connect_status!=1){
                    $("#payment_method option[value='2']").attr('disabled', true);
                }
                else{
                    $("#payment_method option[value='2']").attr('disabled', false);
                }

            }
        });
    });
});

function getCountryByCode(countryCode) {
    var countryNames = {
        "AF": "Afghanistan",
        "AL": "Albania",
        "DZ": "Algeria",
        "AS": "American Samoa",
        "AD": "Andorra",
        "AO": "Angola",
        "AI": "Anguilla",
        "AQ": "Antarctica",
        "AG": "Antigua and Barbuda",
        "AR": "Argentina",
        "AM": "Armenia",
        "AW": "Aruba",
        "AU": "Australia",
        "AT": "Austria",
        "AZ": "Azerbaijan",
        "BS": "Bahamas",
        "BH": "Bahrain",
        "BD": "Bangladesh",
        "BB": "Barbados",
        "BY": "Belarus",
        "BE": "Belgium",
        "BZ": "Belize",
        "BJ": "Benin",
        "BM": "Bermuda",
        "BT": "Bhutan",
        "BO": "Bolivia",
        "BA": "Bosnia and Herzegowina",
        "BW": "Botswana",
        "BV": "Bouvet Island",
        "BR": "Brazil",
        "IO": "British Indian Ocean Territory",
        "BN": "Brunei Darussalam",
        "BG": "Bulgaria",
        "BF": "Burkina Faso",
        "BI": "Burundi",
        "KH": "Cambodia",
        "CM": "Cameroon",
        "CA": "Canada",
        "CV": "Cape Verde",
        "KY": "Cayman Islands",
        "CF": "Central African Republic",
        "TD": "Chad",
        "CL": "Chile",
        "CN": "China",
        "CX": "Christmas Island",
        "CC": "Cocos (Keeling) Islands",
        "CO": "Colombia",
        "KM": "Comoros",
        "CG": "Congo",
        "CD": "Congo the Democratic Republic of the",
        "CK": "Cook Islands",
        "CR": "Costa Rica",
        "CI": "Cote d'Ivoire",
        "HR": "Croatia (Hrvatska)",
        "CU": "Cuba",
        "CY": "Cyprus",
        "CZ": "Czech Republic",
        "DK": "Denmark",
        "DJ": "Djibouti",
        "DM": "Dominica",
        "DO": "Dominican Republic",
        "TP": "East Timor",
        "EC": "Ecuador",
        "EG": "Egypt",
        "SV": "El Salvador",
        "GQ": "Equatorial Guinea",
        "ER": "Eritrea",
        "EE": "Estonia",
        "ET": "Ethiopia",
        "FK": "Falkland Islands (Malvinas)",
        "FO": "Faroe Islands",
        "FJ": "Fiji",
        "FI": "Finland",
        "FR": "France",
        "GF": "French Guiana",
        "PF": "French Polynesia",
        "TF": "French Southern Territories",
        "GA": "Gabon",
        "GM": "Gambia",
        "GE": "Georgia",
        "DE": "Germany",
        "GH": "Ghana",
        "GI": "Gibraltar",
        "GR": "Greece",
        "GL": "Greenland",
        "GD": "Grenada",
        "GP": "Guadeloupe",
        "GU": "Guam",
        "GT": "Guatemala",
        "GN": "Guinea",
        "GW": "Guinea-Bissau",
        "GY": "Guyana",
        "HT": "Haiti",
        "HM": "Heard and Mc Donald Islands",
        "VA": "Holy See (Vatican City State)",
        "HN": "Honduras",
        "HK": "Hong Kong",
        "HU": "Hungary",
        "IS": "Iceland",
        "IN": "India",
        "ID": "Indonesia",
        "IR": "Iran (Islamic Republic of)",
        "IQ": "Iraq",
        "IE": "Ireland",
        "IL": "Israel",
        "IT": "Italy",
        "JM": "Jamaica",
        "JP": "Japan",
        "JO": "Jordan",
        "KZ": "Kazakhstan",
        "KE": "Kenya",
        "KI": "Kiribati",
        "KP": "Korea Democratic People's Republic of",
        "KR": "Korea Republic of",
        "KW": "Kuwait",
        "KG": "Kyrgyzstan",
        "LA": "Lao People's Democratic Republic",
        "LV": "Latvia",
        "LB": "Lebanon",
        "LS": "Lesotho",
        "LR": "Liberia",
        "LY": "Libyan Arab Jamahiriya",
        "LI": "Liechtenstein",
        "LT": "Lithuania",
        "LU": "Luxembourg",
        "MO": "Macau",
        "MK": "Macedonia The Former Yugoslav Republic of",
        "MG": "Madagascar",
        "MW": "Malawi",
        "MY": "Malaysia",
        "MV": "Maldives",
        "ML": "Mali",
        "MT": "Malta",
        "MH": "Marshall Islands",
        "MQ": "Martinique",
        "MR": "Mauritania",
        "MU": "Mauritius",
        "YT": "Mayotte",
        "MX": "Mexico",
        "FM": "Micronesia Federated States of",
        "MD": "Moldova Republic of",
        "MC": "Monaco",
        "MN": "Mongolia",
        "MS": "Montserrat",
        "MA": "Morocco",
        "MZ": "Mozambique",
        "MM": "Myanmar",
        "NA": "Namibia",
        "NR": "Nauru",
        "NP": "Nepal",
        "NL": "Netherlands",
        "AN": "Netherlands Antilles",
        "NC": "New Caledonia",
        "NZ": "New Zealand",
        "NI": "Nicaragua",
        "NE": "Niger",
        "NG": "Nigeria",
        "NU": "Niue",
        "NF": "Norfolk Island",
        "MP": "Northern Mariana Islands",
        "NO": "Norway",
        "OM": "Oman",
        "PK": "Pakistan",
        "PW": "Palau",
        "PA": "Panama",
        "PG": "Papua New Guinea",
        "PY": "Paraguay",
        "PE": "Peru",
        "PH": "Philippines",
        "PN": "Pitcairn",
        "PL": "Poland",
        "PT": "Portugal",
        "PR": "Puerto Rico",
        "QA": "Qatar",
        "RE": "Reunion",
        "RO": "Romania",
        "RU": "Russian Federation",
        "RW": "Rwanda",
        "KN": "Saint Kitts and Nevis",
        "LC": "Saint Lucia",
        "VC": "Saint Vincent and the Grenadines",
        "WS": "Samoa",
        "SM": "San Marino",
        "ST": "Sao Tome and Principe",
        "SA": "Saudi Arabia",
        "SN": "Senegal",
        "SC": "Seychelles",
        "SL": "Sierra Leone",
        "SG": "Singapore",
        "SK": "Slovakia Slovak Republic",
        "SI": "Slovenia",
        "SB": "Solomon Islands",
        "SO": "Somalia",
        "ZA": "South Africa",
        "GS": "South Georgia and the South Sandwich Islands",
        "ES": "Spain",
        "LK": "Sri Lanka",
        "SH": "St. Helena",
        "PM": "St. Pierre and Miquelon",
        "SD": "Sudan",
        "SR": "Suriname",
        "SJ": "Svalbard and Jan Mayen Islands",
        "SZ": "Swaziland",
        "SE": "Sweden",
        "CH": "Switzerland",
        "SY": "Syrian Arab Republic",
        "TW": "Taiwan Province of China",
        "TJ": "Tajikistan",
        "TZ": "Tanzania United Republic of",
        "TH": "Thailand",
        "TG": "Togo",
        "TK": "Tokelau",
        "TO": "Tonga",
        "TT": "Trinidad and Tobago",
        "TN": "Tunisia",
        "TR": "Turkey",
        "TM": "Turkmenistan",
        "TC": "Turks and Caicos Islands",
        "TV": "Tuvalu",
        "UG": "Uganda",
        "UA": "Ukraine",
        "AE": "United Arab Emirates",
        "GB": "United Kingdom",
        "US": "United States",
        "UM": "United States Minor Outlying Islands",
        "UY": "Uruguay",
        "UZ": "Uzbekistan",
        "VU": "Vanuatu",
        "VE": "Venezuela",
        "VN": "Viet Nam",
        "VG": "Virgin Islands (British)",
        "VI": "Virgin Islands (U.S.)",
        "WF": "Wallis and Futuna Islands",
        "EH": "Western Sahara",
        "YE": "Yemen",
        "ZM": "Zambia",
        "ZW": "Zimbabwe"
    }

    return countryNames[countryCode] || "Unknown Country";
}
