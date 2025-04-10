$(document).ready(function () {
    if ($('#listTable').length > 0) {

        var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: true,
            order: [9, 'desc'],
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
                    data: 'subscription_date'
                },
                {
                    data: 'sub_period_start'
                },
                {
                    data: 'sub_period_end'
                },
                {
                    data: 'amount'
                },
                {
                    data: 'tax'
                },
                {
                    data: 'amount_total'
                },
                {
                    data: 'refundable_amount',
                },
                {
                    data: 'package_name',
                    name:'packages.name'
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
                    render: function (_, _, full) {
                        var elementId = full['id'];
                        if (elementId) {
                            var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';
                        //    if (elementStatus != 3) {
                                // actions += '<a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light pe-2 view-row" title=' + view + '><i class="mdi mdi-eye-outline mdi-18px"></i></a>';
                                actions += ' <a href="' + getInvoiceUrl + '?id=' + elementId + '" target="_blank" data-id="' + elementId + '" class="waves-effect waves-light pe-2 text-info get-inovice" title="'+ invoice +'"><i class="bx bx-receipt bx-sm"></i></a>';
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
                { targets: 11, defaultContent: "-", visible: false },
            ],
            "drawCallback": function (settings) {}
        });


    }
});
