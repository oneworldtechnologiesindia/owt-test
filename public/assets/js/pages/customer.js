$(document).ready(function () {
    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        order: [4, 'desc'],
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
                data: 'phone'
            },
            {
                data: 'email'
            },
            {
                data: 'status_name',
                name: 'status',
                render: function (_, _, full) {
                    var contactId = full['id'];

                    if (contactId) {
                        let badgeClass;
                        if (full['status'] == 1) {
                            badgeClass = 'bg-primary';
                        } else if (full['status'] == 2) {
                            badgeClass = 'bg-danger';
                        } else {
                            badgeClass = 'bg-warning';
                        }
                        var actions = '<h5><span class="badge ' + badgeClass + '">' + full['status_name'] + '</span></h5>';

                        return actions;
                    }

                    return '';
                }
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var contactId = full['id'];

                    if (contactId) {
                        var actions = '<div class="datatable-btn-container d-flex align-items-center justify-content-between">';
                        if (full['status'] != 1) {
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" title=' + active + ' class="waves-effect waves-light text-success pe-2 status_update"><i class="bx bx-user-check bx-sm"></i></a>';
                        } else {
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" title=' + deactive + ' class="waves-effect waves-light text-default text-dark text-opacity-50 pe-2 status_update"><i class="bx bx-user-x bx-sm"></i></a>';
                        }
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 edit-row" title=' + editLang + '><i class="bx bx-edit-alt bx-sm"></i></a>';
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title=' + deleteLang + '><i class="bx bx-trash bx-sm"></i></a>';

                        actions += '</div>';

                        return actions;
                    }

                    return '';
                },
                width: '11%'
            },
            {
                name: 'updated_at',
                data: 'updated_at'
            }
        ],
        columnDefs: [
            { targets: 6, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) {}
    });

    $('body').on('change', '.salutation', function (event) {
        let curruntValue = $(this).val();
        $("input[value='" + curruntValue + "']").prop("checked", true);
        showDiv();
    });

    showDiv();

    function showDiv() {
        let salutationValue = $('input[name=salutation]:checked').val();
        if (salutationValue == 'firma') {
            $('.customer_field_add').show();
        } else {
            $('.customer_field_add').hide();
            $('#add-form').find('#customer_company_name').val('');
            $('#add-form').find('#customer_vat_number').val('');
        }
    }

    $('body').on('click', '.status_update', function (event) {
        var id = $(this).attr('data-id');
        $.ajax({
            url: updateStatusUrl + '/' + id,
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $(this).prop('disabled', true);
                $(this).html("<i class='fa fa-spinner fa-spin fa-spin'></i>");
            },
            success: function (result) {
                $(this).prop('disabled', false);
                $('#listTable').DataTable().ajax.reload();
            }
        });
    });

    $('.add-new').click(function (event) {
        $('#edit-id').val("");
        $('.modal-lable-class').html(addLang);
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');

        $('#document_files_tmp').attr("href", "#");
        $('#birth_date').datepicker('destroy').datepicker({ language: localLang});
        $('#add-form .password_div').show();
        $('#add-form')[0].reset();
        $('#add-modal').modal('show');
    });
    $('body').on('click', '.edit-row', function (event) {
        var id = $(this).attr('data-id');
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
        $('#add-form .password_div').hide();
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

                    $('#add-form').find("input[name=salutation][value='" + result.data.salutation + "']").prop("checked", true);
                    if (result.data.salutation == 'firma') {
                        $('.customer_field_add').show();
                        $('#add-form').find('#customer_company_name').val(result.data.company_name);
                        $('#add-form').find('#customer_vat_number').val(result.data.vat_number);
                    } else {
                        $('.customer_field_add').hide();
                    }

                    $('#add-form').find('#first_name').val(result.data.first_name);
                    $('#add-form').find('#last_name').val(result.data.last_name);
                    $('#add-form').find('#email').val(result.data.email);
                    $('#add-form').find('#phone').val(result.data.phone);
                    $('#add-form').find('#street').val(result.data.street);
                    $('#add-form').find('#house_number').val(result.data.house_number);
                    $('#add-form').find('#zipcode').val(result.data.zipcode);
                    $('#add-form').find('#city').val(result.data.city);
                    $('#add-form').find('#country').val(result.data.country);
                    $('#birth_date').datepicker("update", result.data.birth_date);
                }
            }
        });
    });
    $('#add-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        var dataString = new FormData($('#add-form')[0]);
        $('.invalid-feedback').html('');
        $('#add-modal .is-invalid').removeClass('is-invalid');
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
                    $('#add-modal').modal('hide');
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
