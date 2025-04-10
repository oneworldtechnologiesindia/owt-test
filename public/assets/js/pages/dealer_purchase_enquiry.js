$(document).ready(function () {
    /* Data table for the brand listing */

    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: false,
        order: [7, 'desc'],
        ajax: {
            url: apiUrl,
            type: 'GET',
            dataType: "json",
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
                            actions += '<div class="productdetail-btn-gorup"><button class="custom-plus-btn text-dark" data-bs-toggle="collapse" data-bs-target="#collapse_' + product[0].id + i + '_' + meta.row + '" aria-expanded="false" aria-controls="collapse_' + product[0].id + i + '_' + meta.row + '"><i class="bx bx-plus-circle bx-sm"></i> ' + product[0].product_name + '</button></div><div class="collapse" id="collapse_' + product[0].id + i + '_' + meta.row + '"><div class="card border shadow-none card-body text-muted my-2 bg-light border-dark enquiry-productdetail-box"><table cellpadding="5" class="table table-striped addition-info-table mb-0" cellspacing="0" border="0" style="width:100%"><tbody><tr><td>' + brand + ':</td><td>' + product[0].brand_name + '</td></tr><tr><td>' + typep + ':</td><td>' + product[0].type_name + '</td></tr><tr><td>' + category + ':</td><td>' + product[0].category_name + '</td></tr></tbody></table></div></div>';
                            i++;
                        });

                        return actions;
                    }

                    return '';
                },
            },
            {
                data: 'valid_upto',
                name: 'valid_upto',
                width: '12%',
                'render': function (data, type, row, meta) {
                    var startdatetimeObj = row['now_time'];
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
                sortable: false,
                render: function (_, _, full) {
                    var contactId = full['id'];
                    var status = full['status_name'];

                    if (contactId) {
                        var actions = '';
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 view-row" title=' + view + '><i class="mdi mdi-eye-outline mdi-18px"></i></a>';

                        if ((status == 'Expired') || (status == 'Offer Expired')) {
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect text-danger waves-light pe-2 delete-row" title=' + deleteLang + '><i class="bx bx-trash bx-sm"></i></a>';
                        }
                        return actions;
                    }

                    return '';
                },
                width: '2%'
            },
            {
                data: 'updated_at',
                name: 'updated_at',
            }
        ],
        columnDefs: [
            { targets: 7, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) {}
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

    $('body').on('keypress keydown focus blur change', '.offer_amount', function (e) {
        var charCode = e.keyCode;
        if ((charCode > 95 && charCode < 106) || (charCode > 47 && charCode < 58) || charCode == 8) {
            return true;
        } else {
            return false;
        }
    });


    $('body').on('keyup change', '.offer_amount', function (e) {
        var autoNumericInstance = $(this).data('autoNumericInstance');
        var offer_amount = autoNumericInstance.getNumericString();

        e.preventDefault();
        let isnum = /^[0-9]*\.?[0-9]*$/.test(offer_amount);
        if (isnum) {
            totalOfferAmount();
        }
    })

    function totalOfferAmount() {
        var total_amount = 0;
        $('#enquiryProductTable .offer_amount').each(function () {
            // var amountValue = $(this).val();
            var autoNumericInstance = $(this).data('autoNumericInstance');
            var amountValue = autoNumericInstance.getNumericString();
            // var amountValue = $(this).val();
            qtyProduct = $(this).parents('tr').find('.qty_cell').text();
            qtyProduct = qtyProduct.replaceAll(' ', '')
            let isnum = /^[0-9]*\.?[0-9]*$/.test(amountValue);
            if (amountValue && isnum && parseFloat(amountValue) != 'NaN') {
                total_amount = total_amount + (parseFloat(amountValue) * parseInt(qtyProduct));
            }
        });
        total_amount=total_amount.toFixed(2);
        if(is_exclude_vat_from_offer){
            $('#total_offer_ammount').val(total_amount)

            var total_vat_amount=0;
            let isnum = /^[0-9]*\.?[0-9]*$/.test(vat_per);
            if(isnum && vat_per>0){
                total_vat_amount=((total_amount*vat_per)/100).toFixed(2);
            }
            $('#total_vat_amount').val(total_vat_amount);
            total_amount=parseFloat(total_amount)+parseFloat(total_vat_amount);
            $('#final_offer_amount').val(total_amount.toFixed(2));
        }
        else{
            $('#total_offer_ammount').val(total_amount);
        }

    }

    $('.offer-create-btn').on('click', function (e) {
        e.preventDefault();
        totalOfferAmount();
        var id = $('#customer_enquiry_id').val();
        $('#offer-form .offer_description').val("");
        $('#offer-form .is-invalid').removeClass('is-invalid');
        $('#offer-form .invalid-feedback').html('');

        $.ajax({
            url: getOfferProductListUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#enquiry-view-modal').modal('show');
                    var enquiryProductTable = "";
                    if (result.pro_data) {
                        var i = 0;
                        $.each(result.pro_data, function (key, row) {
                            enquiryProductTable += "<tr>";
                            enquiryProductTable += "<td>" + row.product + "</td>";
                            enquiryProductTable += "<td class='qty_cell'>" + row.qty + "</td>";
                            enquiryProductTable += "<td>";
                            enquiryProductTable += '<input type="hidden" name="pro_id" value="' + key + '" class="form-control pro_id">';
                            enquiryProductTable += '<input type="text" name="offer_amount[]" value="0" class="form-control offer_amount autoNumeric" '+ac_data_field+'>';
                            enquiryProductTable += '<span class="invalid-feedback" id="offer_amount_' + i + 'Error" data-ajax-feedback="offer_amount_' + i + '" role="alert"></span>';
                            enquiryProductTable += "</td>";
                            enquiryProductTable += "</tr>";
                            i++;
                        })
                    }
                    $('#enquiryProductTable tbody').html(enquiryProductTable);
                    reInitNumberic();
                    reInitAutoNumberic();
            // var amountValue = $(this).val();
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                }
            }
        });
    })

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

    $('#offer-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        // $(this).find('.autoNumeric').each(function() {
        //     var autoNumericInstance = $(this).data('autoNumericInstance');
        // });
        $('.changenumber_w_o_d').each(function(i){
            var self = $(this);
            try{
                var v = self.val();
                self.removeData('numFormat');
                self.removeClass('changenumber_w_o_d');
                self.addClass('changenumber_w_o_d_reint');
                self.val(v);
            }catch(err){
                console.log("Not an autonumeric field: " + self.attr("name"));
            }
        });
        var dataString = new FormData($('#offer-form')[0]);
        dataString.append('id', $('#customer_enquiry_id').val());
        $('.invalid-feedback strong').html('');
        $.ajax({
            url: sendOfferUrl,
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
                    $('#offer-form .offer_description').val("");
                    $('#offer-form .offer_amount').val("0");
                    showMessage("success", result.message);
                    $('#enquiry-view-modal').modal('hide');
                    $('#offerListTable').DataTable().ajax.reload();
                    $('.offer-create-btn').remove();
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.error').html("");
                    var i = 0;
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        if (key == "offer_description" || key == "delivery_time" || key == "delivery_time_type" || key == "total_vat_amount") {
                            $('#offer-form #' + key + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                            $('#offer-form #' + key).addClass('is-invalid');
                        } else {
                            errKey = key.replace(".", "_");
                            let main_key = key.split('.')[0];
                            let currunt_row = parseInt(key.split('.')[1]);
                            if (result.error[key][0]) {
                                $('#offer-form #' + errKey + ' .invalid-feedback').html('<strong>' + result.error[key][0] + '</strong>');
                                $('#offer-form .' + main_key).eq(currunt_row).addClass('is-invalid');
                            }
                        }
                    });
                    $('#offer-form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage('error', something_went_wrong);
            },
            complete:function(hrx){
                $('.changenumber_w_o_d_reint').each(function(i){
                    var self = $(this);
                    try{
                        var v = self.val();
                        self.removeData('numFormat');
                        self.addClass('changenumber_w_o_d');
                        self.removeClass('changenumber_w_o_d_reint');
                        if(window.currency_type=="eur"){
                            v=v.replace('.',',');
                        }
                        self.val(v);
                    }catch(err){
                        console.log("Not an autonumeric field: " + self.attr("name"));
                    }
                });
                reInitNumberic();
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
                    name: 'updated_at',
                    data: 'updated_at'
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
            order: [5, 'desc'],
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
                    data: 'offer_description',
                    name: 'offer_description'
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

                            if (status == 'Sending' || status == 'Offer Received') {
                                badgeClass = 'bg-info';
                                status = 'Offer Sent';
                            }

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
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 offer-view-row" title=' + editLang + '><i class="mdi mdi-eye-outline mdi-18px"></i></a>';

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
                { targets: 5, defaultContent: "-", visible: false },
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
                    name: 'product_name',
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
            "drawCallback": function (settings) {},
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
            },
            "initComplete": function( settings, json ) {
                custom_currency_format();
            },
        });
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
                    url: delaerDeleteUrl + '?id=' + id,
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

    $('#offer-form .delivery_time').on('keypress', function (e) {
        var charCode = e.keyCode;
        if ((charCode > 95 && charCode < 106) || (charCode > 47 && charCode < 58) || charCode == 8) {
            return true;
        } else {
            return false;
        }
    });
});
