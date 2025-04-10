$(document).ready(function () {
    /* Data table for the brand listing */

    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        order: [1, 'desc'],
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
                data: 'subject'
            },
            {
                data: 'created_at',
                name: 'created_at',
                width: '15%'
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var elementId = full['id'];

                    if (elementId) {
                        var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';

                        actions += ' <a href="javascript:void(0)" data-id="' + elementId + '" class="btn btn-sm btn-primary view-row" title='+editLang+'>'+view+'</a>';
                        actions += '</div>';

                        return actions;
                    }

                    return '';
                },
                width: '9%'
            },
            {
                name: 'updated_at',
                data: 'updated_at'
            }
        ],
        columnDefs: [
            { targets: 3, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) {}
    });


    /* edit brand */

    $('body').on('click', '.view-row', function (event) {
        var id = $(this).attr('data-id');

        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                console.log(result);
                if (result.status) {
                    console.log('Jijaji');
                    $('.email_body').html(result.data.email_body);
                    $('.email_created_at').html(result.data.created_at);
                    $('.email_subject').html(result.data.subject);
                    $('#add-modal').modal('show');
                }
            }
        });
    });
});
