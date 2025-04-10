$(document).ready(function () {
    if ($('#listTable').length > 0) {
        var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: true,
            order: [8, 'desc'],
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
                    data: 'invoice_number','name':'orders.invoice_number'
                },
                {
                    data: 'dealer_payout'
                },
                {
                    data: 'dealer_payout'
                },
                {
                    data: 'site_fees'
                },
                {
                    data: 'customer_name'
                },
                {
                    data: 'dealer_name',
                    'visible' : (login_user_type==1)?true:false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    sortable: false,
                    'visible' : false,
                    render: function (_, _, full) {
                        var elementId = full['order_id'];
                        if (elementId) {
                            var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';
                        //    if (elementStatus != 3) {
                                actions += '<a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light pe-2 view-row" title=' + view + '><i class="mdi mdi-eye-outline mdi-18px"></i></a>';
                                actions += ' <a href="' + getInvoiceUrl + '?id=' + elementId + '" target="_blank" data-id="' + elementId + '" class="waves-effect waves-light pe-2 text-info get-inovice" title="Invoice"><i class="bx bx-receipt bx-sm"></i></a>';
                            // }
                            actions += '</div>';

                            return actions;
                        }

                        return '';
                    },
                },
                {
                    name: 'updated_at',
                    data: 'updated_at'
                }
            ],
            columnDefs: [
                { targets: 8, defaultContent: "-", visible: false },
            ],
            "drawCallback": function (settings) {}
        });


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
            order: [0, 'desc'],
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
                $(api.column(6).footer()).html(total.toFixed(2));
            }
        });
    }
});
