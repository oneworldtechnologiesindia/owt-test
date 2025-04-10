$(document).ready(function () {
    /* Data table for the product listing */
    function format(d) {
        return '<table class="table table-striped addition-info-table" cellpadding="5" cellspacing="0" border="0" style="width:90%">' +
            '<tr>' +
            '<td width="20%">'+brand+':</td>' +
            '<td width="30%">' + d.brand_name + '</td>' +

            '<td width="20%">'+product_type+':</td>' +
            '<td width="30%">' + d.type_name + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>'+product_category+':</td>' +
            '<td>' + d.category_name + '</td>' +

            '<td>'+product_name+':</td>' +
            '<td>' + d.product_name + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>'+anschlusse+':</td>' +
            '<td>' + d.connections_html + '</td>' +

            '<td>'+ausfuhrung+':</td>' +
            '<td>' + d.execution_html + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>'+url+':</td>' +
            '<td>' + d.url + '</td>' +

            '<td>'+retail+':</td>' +
            '<td>' + d.retail + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>'+bemerkung+':</td>' +
            '<td>' + d.remark + '</td>' +

            '<td></td>' +
            '<td></td>' +
            '</tr>' +
            '</table>';
    }
    var listTable = $('#product_list_table').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        order: [
            [1, 'asc']
        ],
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
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '',
            },
            {
                data: 'brand_name'
            },
            {
                data: 'type_name'
            },
            {
                data: 'category_name'
            },
            {
                data: 'product_name'
            },
            {
                data: 'retail'
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var url = full['url'];
                    if (url) {
                        return ' <a target="_blank" href=' + url + ' ><i class="bx bx-link-external bx-sm"></i></a>';
                    }
                    return '-';
                },
            }
        ],
        "drawCallback": function (settings) {}
    });
    $('#product_list_table tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = listTable.row(tr);
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
            tr.next().addClass('add-extrainfo-row');
        }
    });
});
