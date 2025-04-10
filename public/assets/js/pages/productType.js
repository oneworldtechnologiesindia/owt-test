$(document).ready(function () {
    /* Data table for the brand listing */

    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        order: [3, 'desc'],
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
                data: 'type_name'
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

                        actions += ' <a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light pe-2 edit-row" title='+editLang+'><i class="bx bx-edit-alt bx-sm"></i></a>';
                        actions += ' <a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title='+deleteLang+'><i class="bx bx-trash bx-sm"></i></a>';

                        actions += '</div>';

                        return actions;
                    }

                    return '';
                },
                width: '9%'
            },
            {
                data: 'updated_at',
                name: 'updated_at'
            }
        ],
        columnDefs: [
            { targets: 3, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) {}
    });


    /* Open create new brand popup */
    $('.add-new').click(function (event) {
        $('#edit-id').val('');
        $('.modal-lable-class').html(addLang);
        $('.invalid-feedback').html("");
        $('#add-form .is-invalid').removeClass('is-invalid');

        $('#add-form')[0].reset();

        $('#add-modal').modal('show');
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

    /* edit brand */

    $('body').on('click', '.edit-row', function (event) {
        var id = $(this).attr('data-id');
        $('.invalid-feedback').html("");
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

                    $('#add-form').find('#type_name').val(result.data.type_name);
                }
            }
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
});
