$(document).ready(function () {
    /* Data table for the package listing */

    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        order: [0, 'asc'],
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
        columns: [
            {
                data: 'plan_type',
                name: 'plan_type',
            },
            {
                data: 'silver_level',
                name: 'silver_level',
            },
            {
                data: 'gold_level',
                name: 'gold_level',
            },
            {
                data: 'platinum_level',
                name: 'platinum_level',
            },
            {
                data: 'diamond_level',
                name: 'diamond_level',
            },
            {
                data: 'type',
                name: 'type',
            },
            {
                data: 'created_at',
                name: 'created_at',
                width: '15%'
            },
            {
                data: 'updated_at',
                name: 'updated_at'
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var elementId = full['id'];

                    if (elementId) {
                        var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';

                        actions += ' <a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light pe-2 edit-row" title=' + editLang + '><i class="bx bx-edit-alt bx-sm"></i></a>';

                        actions += '</div>';

                        return actions;
                    }

                    return '';
                },
                width: '9%'
            },
        ],
        columnDefs: [
            // { targets: 3, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) { }
    });


    /* form submit */
    $('#add-form').submit(function (event) {
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

    /* edit plan type */
    $('body').on('click', '.edit-row', function (event) {
        var id = $(this).attr('data-id');
        $('.invalid-feedback').html('');
        $('#add-form .is-invalid').removeClass('is-invalid');

        $('#add-form')[0].reset();

        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#edit-id').val(id);

                    $('.modal-lable-class').html(editLang);
                    $('#add-modal').modal('show');

                    $('#add-form').find('#plan_type').val(result.data.plan_type);
                    $('#add-form').find('#silver_level').val(result.data.silver_level);
                    $('#add-form').find('#gold_level').val(result.data.gold_level);
                    $('#add-form').find('#platinum_level').val(result.data.platinum_level);
                    $('#add-form').find('#diamond_level').val(result.data.diamond_level);
                    $('#add-form').find('#type').val(result.data.type);
                }
            }
        });
    });
});
