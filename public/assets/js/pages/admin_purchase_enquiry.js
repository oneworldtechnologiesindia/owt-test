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
            targets: [4]
        }],
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
                                actions += '<div class="productdetail-btn-gorup"><button class="custom-plus-btn text-dark" data-bs-toggle="collapse" data-bs-target="#collapse_' + product[0].id + i + '_' + meta.row + '" aria-expanded="false" aria-controls="collapse_' + product[0].id + i + '_' + meta.row + '"><i class="bx bx-plus-circle bx-sm"></i> ' + product[0].product_name + '</button></div><div class="collapse" id="collapse_' + product[0].id + i + '_' + meta.row + '"><div class="card border shadow-none card-body text-muted my-2 bg-light border-dark enquiry-productdetail-box"><table cellpadding="5" class="table table-striped addition-info-table mb-0" cellspacing="0" border="0" style="width:100%"><tbody><tr><td>'+brand+':</td><td>' + product[0].brand_name + '</td></tr><tr><td>'+typep+':</td><td>' + product[0].type_name + '</td></tr><tr><td>'+category+':</td><td>' + product[0].category_name + '</td></tr></tbody></table></div></div>';
                                i++;
                            }
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
                sortable: false,
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
                        if ($("#time-" + meta.row).length > 0) {
                            document.getElementById("time-" + meta.row).innerHTML = hours + "h " + minutes + "m " + seconds + "s ";

                            addsecound++;
                            if (distance < 0) {
                                clearInterval(x);
                                document.getElementById("time-" + meta.row).innerHTML = "0h 0m 0s";
                            }
                        }
                    }, 1000);

                    return '<p id="time-' + meta.row + '" class="m-0" ></p>';

                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                width: '5%',
                render: function (data, type, row, meta) {
                    return '<span>' + data + '</span>';
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
                    var roleType = full['role_type'];

                    if (contactId) {
                        var actions = '';
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 view-row" title='+editLang+'><i class="mdi mdi-eye-outline mdi-18px"></i></a>';

                        if (((status == 'Expired') || (status == 'Offer Expired')) && roleType == '2') {
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect text-danger waves-light pe-2 delete-row" title='+deleteLang+'><i class="bx bx-trash bx-sm"></i></a>';
                        }
                        return actions;
                    }

                    return '';
                },
                width: '2%'
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

    if ($('#filter-purchase-enquiry-form select').length > 0) {
        intializeFilterSelect();
    }

    function intializeFilterSelect() {
        if ($('#filter-purchase-enquiry-form select').length > 0) {
            $('#filter-purchase-enquiry-form select').each(function () {
                $(this).select2({
                    placeholder: $(this).attr('placeholder')
                });
            });
        }
    }

    $('body').on('click', '.view-row', function (event) {
        var id = $(this).attr('data-id');
        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.url.length > 0) {
                    window.location = result.url;
                } else {
                    showMessage('error', something_went_wrong);
                }
            },
            error: function (error) {
                showMessage('error', something_went_wrong);
            }
        });
    });

    $('form#filter-purchase-enquiry-form').on('change', function () {
        getFilterOptions();
        $('#listTable').DataTable().ajax.reload();
    })

    function getFilterOptions() {
        let formData = new FormData($('form#filter-purchase-enquiry-form')[0]);
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
        $('form#filter-purchase-enquiry-form')[0].reset();
        $('#listTable').DataTable().ajax.reload();
        getFilterOptions();
        intializeFilterSelect();
    })
});
