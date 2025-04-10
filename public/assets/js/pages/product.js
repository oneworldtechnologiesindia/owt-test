$(document).ready(function () {
    if ($('#listTable').length > 0) {
        /* Data table for the brand listing */
        var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: true,
            order: [7, 'desc'],
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
                },
                {
                    sortable: false,
                    render: function (_, _, full) {
                        var elementId = full['id'];

                        if (elementId) {
                            var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';

                            actions += ' <a href="' + viewUrl + '/' + elementId + '" data-id="' + elementId + '" class="waves-effect waves-light pe-2 view-row" title='+view+'><i class="mdi mdi-eye-outline mdi-18px"></i></a>';
                            actions += ' <a href="' + editUrl + '/' + elementId + '" data-id="' + elementId + '" class="waves-effect waves-light pe-2 edit-row" title='+editLang+'><i class="bx bx-edit-alt bx-sm"></i></a>';
                            actions += ' <a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title='+deleteLang+'><i class="bx bx-trash bx-sm"></i></a>';

                            actions += '</div>';

                            return actions;
                        }

                        return '';
                    },
                    width: '9%',
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

    /* form submit */

    $('#add_product_form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);

        var dataString = new FormData($('#add_product_form')[0]);

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

                    showMessage("success", result.message);

                    window.location.href = indexUrl;

                } else if (!result.status && result.message) {

                    showMessage("error", result.message);

                    window.location.href = indexUrl;
                } else {
                    first_input = "";
                    $('.invalid-feedback strong').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#add_product_form #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                        $('#add_product_form #' + key).addClass('is-invalid');
                    });
                    $('#add_product_form').find("#" + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage('error', something_went_wrong);
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

    /* Open create new brand popup */
    $('.import_product').click(function (event) {
        $('.modal-lable-class').html('Import');
        $('.invalid-feedback').html('');
        $('#import-product-form .is-invalid').removeClass('is-invalid');

        $('#import-product-form')[0].reset();

        $('#import-product-modal').modal('show');
    });

    /* Import CSV form submit */

    $('#import-product-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);

        var dataString = new FormData($('#import-product-form')[0]);

        $.ajax({
            url: importUrl,
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
                    showMessage("success", result.message);

                    $('#listTable').DataTable().ajax.reload();
                    if (result.error && result.error != '' && result.error.product_csv != '') {
                        first_input = "";
                        $('.error').html("");
                        $.each(result.error, function (key) {
                            if (first_input == "") first_input = key;
                            $('#import-product-form #' + key + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                            $('#import-product-form #' + key).addClass('is-invalid');
                        });
                        $('#import-product-form').find("." + first_input).focus();
                    } else {
                        setTimeout(function () {
                            $('#import-product-modal').modal('hide');
                        }, 300);
                    }
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                    if (result.error && result.error != '') {
                        first_input = "";
                        $('.error').html("");
                        $.each(result.error, function (key) {
                            if (first_input == "") first_input = key;
                            $('#import-product-form #' + key + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                            $('#import-product-form #' + key).addClass('is-invalid');
                        });
                        $('#import-product-form').find("." + first_input).focus();
                    }
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#import-product-form #' + key + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                        $('#import-product-form #' + key).addClass('is-invalid');
                    });
                    $('#import-product-form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage('error', something_went_wrong);
            }
        });
    });

    if ($('#brand_id').length > 0) {
        $('#brand_id').select2({
            placeholder: select_brand
        });
    }
    if ($('#type_id').length > 0) {
        $('#type_id').select2({
            placeholder: select_product_type
        });
    }
    if ($('#category_id').length > 0) {
        $('#category_id').select2({
            placeholder: select_product_category
        });
    }
    if ($('#connections').length > 0) {
        $('#connections').select2({
            placeholder: select_connections
        });
    }
    if ($('#execution').length > 0) {
        $('#execution').select2({
            placeholder: select_ausfuhrung
        });
    }
});
