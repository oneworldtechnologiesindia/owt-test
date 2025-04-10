$(document).ready(function () {
    if ($('#listTable').length > 0) {
        var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
            searching: true,
            pageLength: 10,
            processing: true,
            serverSide: true,
            order: [5, 'desc'],
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
                    data: 'company_name'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'email'
                },
                {
                    data: 'is_distributor',
                    name: 'is_distributor',
                    render: function (_, _, full) {
                        var contactId = full['id'];

                        if (contactId) {
                            let is_distributor_checked='';
                            if (full['is_distributor'] == 1) {
                                is_distributor_checked = 'checked="checked"';
                            }
                            var is_distributor_div = `<div class="form-check form-check-success mb-3">
                                                <input class="form-check-input distributor_checkbox" data-id="${contactId}" type="checkbox"
                                                    ${is_distributor_checked}>
                                            </div>`;

                            return is_distributor_div;
                        }

                        return '';
                    }
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
                                actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" title='+active+' class="waves-effect waves-light text-success pe-2 status_update"><i class="bx bx-user-check bx-sm"></i></a>';
                            } else {
                                actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" title='+deactive+' class="waves-effect waves-light text-default text-dark text-opacity-50 pe-2 status_update"><i class="bx bx-user-x bx-sm"></i></a>';
                            }
                            actions += ' <a href=' + editUrl + '/' + contactId + ' data-id="' + contactId + '" class="waves-effect waves-light pe-2 edit-row" title='+editLang+'><i class="bx bx-edit-alt bx-sm"></i></a>';
                            actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title='+deleteLang+'><i class="bx bx-trash bx-sm"></i></a>';

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
                { targets: 7, defaultContent: "-", visible: false },
            ],
            "drawCallback": function (settings) {}
        });
    }
    $('body').on('click', '.status_update', function (event) {
        var id = $(this).attr('data-id');
        $.ajax({
            url: updateStatusUrl + '/' + id,
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $(this).prop('disabled', true);
            },
            success: function (result) {
                $(this).prop('disabled', false);
                $('#listTable').DataTable().ajax.reload();
            }
        });
    });
    if ($('#shop_start_time').length) {
        $('#shop_start_time').timepicker({
            showMeridian: false,
            minuteStep: 30,
            defaultTime: null,
            icons: {
                up: 'mdi mdi-chevron-up',
                down: 'mdi mdi-chevron-down'
            },
            appendWidgetTo: "#timepicker-input-shop_start_time"
        }).on('change', function (e) {
            onchangeform($(this));
        });
        $('#shop_end_time').timepicker({
            showMeridian: false,
            minuteStep: 30,
            defaultTime: null,
            icons: {
                up: 'mdi mdi-chevron-up',
                down: 'mdi mdi-chevron-down'
            },
            appendWidgetTo: "#timepicker-input-shop_end_time"
        }).on('change', function (e) {
            onchangeform($(this));
        });
    }

    $('body').on("change", "#document_file", function (event) {
        event.preventDefault();
        var file = event.target.files[0];
        var allowedFiles = ["pdf", 'PDF'];
        var fileUpload = document.getElementById("document_file");
        if (fileUpload && file) {
            var fileName = file.name;
            var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
            if ($.inArray(fileNameExt, allowedFiles) == -1) {
                fileUpload.value = '';
                var msg = please_upload_a_valid_pdf_file;
                $(this).addClass('is-invalid');
                $("#document_fileError").html('<strong>' + msg + '</strong>');
                return false;
            }
        }
        return true;
    });
    var fileSizeLimit = 2 * 1000 * 1000;
    var allowedFiles = ["jpg", "jpeg", "png"];
    $('body').on("change", "#company_logo", function (event) {
        event.preventDefault();
        var file = event.target.files[0];
        var fileUpload = document.getElementById("company_logo");
        if (fileUpload && file) {
            var file_size = event.target.files[0].size;
            if (file_size < fileSizeLimit) {
                var fileName = file.name;
                var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
                if ($.inArray(fileNameExt, allowedFiles) == -1) {
                    fileUpload.value = '';
                    var msg = please_upload_valid;
                    $(this).addClass('is-invalid');
                    $("#company_logoError").html('<strong>' + msg + '</strong>');
                    return false;
                }
            } else {
                $(this).addClass('is-invalid');
                var msg = file_size_cannot_exceed;
                $("#company_logoError").html('<strong>' + msg + '</strong>');
                $('#company_logo').val('');
                return false;
            }
        }
        return true;
    });

    $('body').on('keyup', '#add-modal input', function (event) {
        if ($(this).val().length > 0) {
            $(this).closest('.mb-3').find('.invalid-feedback').html('');
            if ($(this).hasClass('is-invalid'))
                $(this).removeClass('is-invalid');
        }
    });

    $('#add-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        var dataString = new FormData($('#add-form')[0]);

        $('.invalid-feedback').html('');
        $('#add-form .is-invalid').removeClass('is-invalid');
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
                    window.location.href = listUrl;
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#add-form #' + key + 'Error.invalid-feedback').html('<strong>' + result.error[key] + '</strong>');
                        $('#add-form #' + key).addClass('is-invalid');
                        if(key=='vat'){
                            $('#add-form #' + key).closest('.row').find('.input-group').addClass('is-invalid');
                        }
                    });
                    $('#add-form').find("#" + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                alert(something_went_wrong, 'error');
                location.reload();
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
    $('body').on('click','.distributor_checkbox',function(event){
        var dealer_id=$(this).attr('data-id');
        var $this = $(this);
        var status=$(this).is(':checked')?1:0;
        if(confirm(are_you_sure)){
            $.ajax({
                url: updateDistributorStatusUrl,
                type: 'POST',
                data: {'status':status,'dealer_id':dealer_id},
                beforeSend: function () {
                    $($this).prop('disabled', true);
                },
                success: function (result) {
                    if (result.status) {
                        showMessage("success", result.message);
                    } else {
                        showMessage("error", result.message);
                    }
                },
                error: function (error) {
                    alert(something_went_wrong, 'error');
                    location.reload();
                },
                complete:function(res){
                    $($this).prop('disabled', false);
                }
            });
        }
    });
});
