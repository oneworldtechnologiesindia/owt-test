$(document).ready(function () {
    /* Data table for the brand listing */
    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: false,
        order: [0, 'desc'],
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
                data: 'name',
                name: 'users.name',
                width: '20%'
            },
            {
                data: 'product_name',
                name: 'product.product_name'
            },
            {
                data: 'created_at',
                name: 'created_at',
                width: '5%'
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

                        if (status == 'Offer Sent')
                            badgeClass = 'bg-info';

                        actions += '<h5><span class="badge ' + badgeClass + '">' + status + '</span></h5>';


                        return actions;
                    }

                    return '';
                },
                width: '5%'
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var contactId = full['id'];

                    if (contactId) {
                        var actions = '';
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 view-row" title=' + editLang + '><i class="mdi mdi-eye-outline mdi-18px"></i></a>';
                        return actions;
                    }

                    return '';
                },
                width: '2%'
            },
        ],
        "drawCallback": function (settings) {}
    });

    $('body').on('click', '.view-row', function (event) {
        var id = $(this).attr('data-id');

        $('#offer-form #customer_enquiry_id').val(id);
        $('#offer-form .offer_description').val("");

        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                window.location = result.url;
            }
        });
    });

    if ($('#product-information').length > 0) {
        viewEnquiryProductListTable();
    }
    if ($('#offer-information').length > 0) {
        viewOfferListTable();
    }

    var enrquiryProductListTable = "";

    function viewEnquiryProductListTable() {
        if (enrquiryProductListTable) {
            enrquiryProductListTable.destroy();
        }
        enrquiryProductListTable = $('#enrquiryProductListTable').DataTable({
            language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: false,
            order: [7, 'desc'],
            ajax: {
                url: getEnquiryProductListUrl,
                type: 'GET',
                headers: {
                    'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                },
                data: function (d) {
                    d.search = $('input[type="search"]').val(),
                        d.customer_enquiry_id = $('#customer_enquiry_id').val()
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
                    data: 'updated_at',
                    name: 'updated_at'
                }
            ],
            columnDefs: [
                { targets: 7, defaultContent: "-", visible: false },
            ],
            "drawCallback": function (settings) {}
        });
    }

    var offerListTable = "";

    function viewOfferListTable() {
        if (offerListTable) {
            offerListTable.destroy();
        }
        offerListTable = $('#offerListTable').DataTable({
            language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: false,
            order: [8, 'desc'],
            ajax: {
                url: getOfferListUrl,
                type: 'GET',
                headers: {
                    'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                },
                data: function (d) {
                    d.search = $('input[type="search"]').val(),
                        d.customer_enquiry_id = $('#customer_enquiry_id').val()
                },
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'dealer_name',
                    name: 'dealer_name',
                    visible: false
                },
                {
                    data: 'dealer_phone',
                    name: 'dealer_phone',
                    visible: false
                },
                {
                    data: 'total_amount',
                    name: 'total_amount',
                    width: '10%',
                },
                {
                    data: 'valid_upto',
                    name: 'valid_upto',
                    width: '12%',
                    'render': function (data, type, row, meta) {
                        var startdatetimeObj = row.now_time;
                        var startdatetimeObjTmp = startdatetimeObj.replace(' ', 'T').split(/[^0-9]/);
                        var startdatetime = new Date(Date.UTC(startdatetimeObjTmp[0], startdatetimeObjTmp[1] - 1, startdatetimeObjTmp[2], startdatetimeObjTmp[3], startdatetimeObjTmp[4], startdatetimeObjTmp[5]));

                        var enddatetimeObj = data;
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
                            document.getElementById("time-" + meta.row).innerHTML = hours + "h " + minutes + "m " + seconds + "s ";

                            addsecound++;
                            if (distance < 0) {
                                clearInterval(x);
                                document.getElementById("time-" + meta.row).innerHTML = "0h 0m 0s";
                            }
                        }, 1000);

                        return '<p id="time-' + meta.row + '" class="m-0" ></p>';

                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'status_name',
                    name: 'status',
                    render: function (_, _, full) {
                        var contactId = full['id'];
                        var status = full['status_name'];
                        if (contactId) {
                            var actions = '';

                            let badgeClass = 'bg-info';
                            if (status == 'Accepted')
                                badgeClass = 'bg-success';

                            if (status == 'Rejected')
                                badgeClass = 'bg-danger';

                            if (status == 'Expired')
                                badgeClass = 'bg-warning';

                            if (status == 'Sending' || status == 'Offer Received')
                                badgeClass = 'bg-primary';

                            actions += '<h5><span class="badge ' + badgeClass + '">' + status + '</span></h5>';


                            return actions;
                        }
                        return '';
                    }
                },
                {
                    sortable: false,
                    render: function (_, _, full) {
                        var contactId = full['id'];
                        var roleType = full['role_type'];

                        if (contactId) {
                            var actions = '';
                            if ((full['status_name'] == 'Sending' || full['status_name'] == 'Offer Received') && roleType == 3) {
                                actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="btn-sm btn-success offer_status pe-2" data-code="2" title=' + accepted + '><i class="bx bx-check-square bx-sm"></i></a>';
                            }
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="btn-sm btn-info offer-view-row" title=' + view + '><i class="mdi mdi-eye-outline mdi-24px"></i></a>';

                            return actions;
                        }

                        return '';
                    },
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                }
            ],
            columnDefs: [
                { targets: 8, defaultContent: "-", visible: false },
            ],
            "drawCallback": function (settings) {}
        });
    }
    var offerViewTable = "";
    $('body').on('click', '.offer-view-row', function (event) {
        var offer_id = $(this).attr('data-id');
        $('#offer-view-modal').modal('show');
        if (offerViewTable) {
            offerViewTable.destroy();
        }
        offerViewTable = $('#offerViewTable').DataTable({
            language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: false,
            order: [5, 'desc'],
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
                {
                    data: 'updated_at',
                    name: 'updated_at'
                }
            ],
            columnDefs: [
                { targets: 5, defaultContent: "-", visible: false },
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

    if (notifyOfferId != '') {
        setTimeout(function () {
            $('.nav-link.active').removeClass('active');
            $('.tab-pane.active').removeClass('active');
            $('.nav-item').last().find('.nav-link').addClass('active');
            $('#offer-information').addClass('active');
            $('#offerListTable').find('.offer-view-row[data-id="' + notifyOfferId + '"]').trigger('click');
        }, 1000)
    }

    $('body').on('click', '.offer_status', function (event) {
        event.preventDefault();
        $('#confirm-offer-modal').modal('show');
        $('#offerid').val($(this).attr('data-id'));
        $('#offerstatus').val($(this).attr('data-code'));
        var offer_id=$(this).attr('data-id');
        $.ajax({
            url: getDealerInfoUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                offer_id: offer_id,
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


    $('.enquiry-documents').on('click', function (e) {
        e.preventDefault();
        window.open($(this).attr('href') + '?type=' + $(this).data('type') + '&offerid=' + $('#offerid').val(), "_blank");
    })
});
