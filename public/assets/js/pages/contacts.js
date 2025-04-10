$(document).ready(function () {
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
                data: 'name'
            },
            {
                data: 'surname'
            },
            {
                data: 'contact_type',
                name: 'contact_type',
            },
            {
                data: 'salutation',
                name: 'salutation',
            },
            {
                data: 'company'
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
                        actions += ' <a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light pe-2 contact-view-row" title=' + editLang + '><i class="mdi mdi-eye-outline bx-sm"></i></a>';
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
                name: 'updated_at',
                data: 'updated_at'
            }
        ],
        columnDefs: [
            { targets: 7, defaultContent: "-", visible: false },
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

    $(document).on('click', '.closeContactmodal', function() {
        Swal.fire({
            title: are_you_sure_unsaved_content_will_be_dismissed,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: no_cancel,
            confirmButtonText: yes_close_it
        }).then((result) => {
            if (result.isConfirmed) {
                $("[data-bs-dismiss=modal]").trigger({
                    type: "click"
                });
            }
        })
    });

    $('#add-modal').modal({
        backdrop: 'static',
        keyboard: false
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

                    $('#add-form').find('#contact_type').val(result.data.contact_type);
                    $('#add-form').find('#company').val(result.data.company);
                    $('#add-form').find('#salutation').val(result.data.salutation);
                    $('#add-form').find('#name').val(result.data.name);
                    $('#add-form').find('#surname').val(result.data.surname);
                    $('#add-form').find('#email').val(result.data.email);
                    $('#add-form').find('#street').val(result.data.street);
                    $('#add-form').find('#street_nr').val(result.data.street_nr);
                    $('#add-form').find('#zipcode').val(result.data.zipcode);
                    $('#add-form').find('#city').val(result.data.city);
                    $('#add-form').find('#country').val(result.data.country);
                    $('#add-form').find('#telephone').val(result.data.telephone);
                    $('#add-form').find('#note').val(result.data.note);
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

    $('body').on('click', '.contact-view-row', function(event) {
        var id = $(this).attr('data-id');
        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            data:{
                view_data : true,
            },
            success: function(result) {
                if (result.status) {
                    $(viewModalId).modal('show');
                    console.log(result.data);
                    $(viewModalId).find('.contact_type').html(result.data.contact_type);
                    $(viewModalId).find('.salutation').html(result.data.salutation);
                    $(viewModalId).find('.company').html(result.data.company);
                    $(viewModalId).find('.name').html(result.data.name);
                    $(viewModalId).find('.surname').html(result.data.surname);
                    $(viewModalId).find('.email').html(result.data.email);
                    $(viewModalId).find('.street').html(result.data.street);
                    $(viewModalId).find('.street_nr').html(result.data.street_nr);
                    $(viewModalId).find('.zipcode').html(result.data.zipcode);
                    $(viewModalId).find('.city').html(result.data.city);
                    $(viewModalId).find('.country').html(result.data.country);
                    $(viewModalId).find('.telephone').html(result.data.telephone);
                    $(viewModalId).find('.note').html(result.data.note);
                    $(viewModalId).find('.created_at').html(result.data.created_at_view);
                } else {
                    if (result.message) {
                        showToastMessage("error", result.message);
                    }
                }
            },
            error: function(error) {
                alert('Something went wrong!');
                location.reload();
            }
        });
    });

    /* Open create new brand popup */
    $('.import_contacts').click(function (event) {
        $('.modal-lable-class').html('Import');
        $('.invalid-feedback').html('');
        $('#import-contact-form .is-invalid').removeClass('is-invalid');

        $('#import-contact-form')[0].reset();

        $('#import-contact-modal').modal('show');
    });

    /* Import CSV form submit */

    $('#import-contact-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);

        var dataString = new FormData($('#import-contact-form')[0]);

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
                            $('#import-contact-form #' + key + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                            $('#import-contact-form #' + key).addClass('is-invalid');
                        });
                        $('#import-contact-form').find("." + first_input).focus();
                    } else {
                        setTimeout(function () {
                            $('#import-contact-modal').modal('hide');
                        }, 300);
                    }
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                    if (result.error && result.error != '') {
                        first_input = "";
                        $('.error').html("");
                        $.each(result.error, function (key) {
                            if (first_input == "") first_input = key;
                            $('#import-contact-form #' + key + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                            $('#import-contact-form #' + key).addClass('is-invalid');
                        });
                        $('#import-contact-form').find("." + first_input).focus();
                    }
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#import-contact-form #' + key + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                        $('#import-contact-form #' + key).addClass('is-invalid');
                    });
                    $('#import-contact-form').find("." + first_input).focus();
                }
            },

            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                if(error.status || error.status == 400){
                    showMessage('error', error.responseJSON.message);
                    return false;
                }
                showMessage('error', something_went_wrong);
            }
        });
    });
});
