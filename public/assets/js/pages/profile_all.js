$(document).ready(function () {

    $('body').on("change", "#document_file", function (event) {
        event.preventDefault();
        var file = event.target.files[0];
        var allowedFiles = ["pdf", 'PDF'];
        var fileUpload = document.getElementById("document_file");
        if (fileUpload) {
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
            $('#profile_form').find('#customer_company_name').val('');
            $('#profile_form').find('#customer_vat_number').val('');
        }
    }

    var fileSizeLimit = 2 * 1000 * 1000;
    var allowedFiles = ["jpg", "jpeg", "png"];
    $('body').on("change", "#company_logo", function (event) {
        event.preventDefault();
        var file = event.target.files[0];
        var fileUpload = document.getElementById("company_logo");
        if (fileUpload) {
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


    $('#profile_form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        var dataString = new FormData($('#profile_form')[0]);
        $('#profile_form').find('input.is-invalid').removeClass('is-invalid');
        if ($('#profile_form #document_file').length) {
            var document_file = $('#profile_form #document_file')[0].files.length;
            if (document_file) {
                dataString.append('document_file', $('#profile_form #document_file')[0].files);
            }
        }
        if ($('#profile_form #company_logo').length) {
            var company_logo = $('#profile_form #company_logo')[0].files.length;
            if (company_logo) {
                dataString.append('company_logo', $('#profile_form #company_logo')[0].files);
            }
        }

        $('.invalid-feedback').html('');
        $('#add-form .is-invalid').removeClass('is-invalid');

        $.ajax({
            url: updateProfileUrl,
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
                    getData();
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $('#profile_form #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                        $('#profile_form #' + key).addClass('is-invalid');
                        if (key == "vat") {
                            $('#profile_form #' + key).closest('div').addClass('is-invalid');
                        }
                    });
                    $('#profile_form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage("error", something_went_wrong);
            }
        });
    });
    getData();
    $('.view-pdf, .view-logo').hide();

    $(document).on('click', '.cancel-contract', function (e) {
        e.preventDefault();
        Swal.fire({
            title: are_you_sure,
            text: you_wont_cancel_contract,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: yes_cancel_it,
            cancelButtonText: no_i_don_t,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: contractUpdateUrl,
                    type: 'POST',
                    data: {
                        id: $('#data_id').val(),
                        ctype: 'cancelcontract'
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result.status) {
                            $('#contract-info .enddate.d-none').removeClass('d-none');
                            $('#contract-info .dates-container .contractend').text(result.data.contract_enddate);

                            $('#contract-info .canceldate.d-none').removeClass('d-none');
                            $('#contract-info .dates-container .contractcancel').text(result.data.contract_canceldate);

                            $('#contract-info .cancel-contract').after('<a href="javascrip:void(0);" class="btn btn-primary waves-effect btn-label waves-light withdraw-contract mt-3 me-3"><i class="bx bx-receipt label-icon"></i>' + withdraw_cancelation + '</a>');

                            $('#contract-info .cancel-contract').remove();
                            $('#contract-info .alert.alert-primary').removeClass('alert-primary').addClass('alert-danger');
                            showMessage("success", result.message);
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
        });
    })

    $(document).on('click', '.withdraw-contract', function (e) {
        e.preventDefault();
        Swal.fire({
            title: are_you_sure,
            text: you_wont_withdraw_contract,
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: yes_withdraw_it,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: contractUpdateUrl,
                    type: 'POST',
                    data: {
                        id: $('#data_id').val(),
                        ctype: 'withdrawcontract'
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result.status) {
                            $('#contract-info .enddate').addClass('d-none');
                            $('#contract-info .dates-container .contractend').text('');

                            $('#contract-info .canceldate').addClass('d-none');
                            $('#contract-info .dates-container .contractcancel').text('');

                            $('#contract-info .alert.alert-danger').removeClass('alert-danger').addClass('alert-primary');
                            $('#contract-info .withdraw-contract').after('<a href="javascrip:void(0);" class="btn btn-danger waves-effect btn-label waves-light cancel-contract mt-3"><i class="bx bx-receipt label-icon"></i>' + cancel_contract + '</a>');
                            $('#contract-info .withdraw-contract').remove();
                            showMessage("success", result.message);
                        }
                    },
                    error: function (error) {
                        console.log(error);
                        showMessage("error", something_went_wrong);
                    }
                });
            }
        });
    })

    function getData() {
        $('#profile_form .document_file_div').hide();
        $('#profile_form .company_logo_div').hide();

        $.ajax({
            url: profileDetailUrl,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#profile_form').find('#password').val("");
                    $('#profile_form').find('#password_confirmation').val("");

                    $('#profile_form').find('#first_name').val(result.data.first_name);
                    $('#profile_form').find('#last_name').val(result.data.last_name);
                    $('#profile_form').find('#email').val(result.data.email);
                    $('#profile_form').find('#phone').val(result.data.phone);

                    if ($('#contract-info').length > 0) {
                        if (result.data.contract_startdate) {
                            $('#contract-info .dates-container .contractstart').text(result.data.contract_startdate);
                            $('#contract-info .canceldate, #contract-info .enddate').addClass('d-none');
                            if (result.contractStatus != 'running') {
                                $('#contract-info .enddate.d-none').removeClass('d-none');
                                $('#contract-info .dates-container .contractend').text(result.data.contract_enddate);

                                $('#contract-info .canceldate.d-none').removeClass('d-none');
                                $('#contract-info .dates-container .contractcancel').text(result.data.contract_canceldate);

                                $('#contract-info .cancel-contract').remove();
                                $('#contract-info .alert.alert-primary').removeClass('alert-primary').addClass('alert-danger');
                            } else {
                                $('#contract-info .withdraw-contract').remove();
                            }
                        } else {
                            $('#contract-info').remove();
                        }
                        if (result.data.is_active_subscription) {
                            $('#subscription-info .dates-container .subscription-start').text(result.data.sub_period_start);
                            $('#subscription-info .canceldate, #subscription-info .enddate').addClass('d-none');
                            $('#subscription-info .enddate.d-none').removeClass('d-none');
                            $('#subscription-info .dates-container .subscription-end').text(result.data.sub_period_end);

                            // $('#subscription-info .canceldate.d-none').removeClass('d-none');
                            // $('#subscription-info .dates-container .contractcancel').text(result.data.contract_canceldate);

                            // $('#subscription-info .cancel-contract').remove();
                            // $('#subscription-info .alert.alert-primary').removeClass('alert-primary').addClass('alert-danger');

                        } else {
                            $('#subscription-info').remove();
                        }
                        if (result.data.stripe_account_id && result.data.stripe_account_status) {
                            if (result.data.stripe_account_status == 1) {
                                $('.account-connect-status').html('Account Connected');
                            }
                            else if (result.data.stripe_account_status == 2) {
                                $('.account-connect-status').html('Submited & Pending for verification.');
                            }
                            else if (result.data.stripe_account_status == 3) {
                                $('.account-connect-status').html('Account Restricted.');
                            }
                            else if (result.data.stripe_account_status == 5) {
                                $('.account-connect-status').html('Account Disabled.');
                            }
                            else if (result.data.stripe_account_status == 6) {
                                $('.connect-stripe-account').show();
                                $('.connect-stripe-account').html('<i class="bx bx-receipt label-icon"></i> Continue Onboarding');
                            }
                            else {
                                $('.connect-stripe-account').show();
                            }
                        } else {
                            $('.connect-stripe-account').show();
                        }
                    }
                    $('#profile_form').find("input[name=salutation][value='" + result.data.salutation + "']").prop("checked", true);
                    if (result.data.salutation == 'firma') {
                        $('.customer_field_add').show();
                        $('#profile_form').find('#customer_company_name').val(result.data.company_name);
                        $('#profile_form').find('#customer_vat_number').val(result.data.vat_number);
                    } else {
                        $('.customer_field_add').hide();
                    }
                    $('#profile_form').find('#id').val(result.data.display_id);
                    $('#profile_form').find('#company_name').val(result.data.company_name);
                    $('#profile_form').find('#vat_number').val(result.data.vat_number);
                    $('#profile_form').find('#street').val(result.data.street);
                    $('#profile_form').find('#house_number').val(result.data.house_number);
                    $('#profile_form').find('#zipcode').val(result.data.zipcode);
                    $('#profile_form').find('#city').val(result.data.city);
                    $('#profile_form').find('#country').val(result.data.country);
                    $('#profile_form').find('#bank_name').val(result.data.bank_name);
                    $('#profile_form').find('#iban').val(result.data.iban);
                    $('#profile_form').find('#bic').val(result.data.bic);
                    $('#profile_form').find('#shop_start_time').val(result.data.shop_start_time);
                    $('#profile_form').find('#shop_end_time').val(result.data.shop_end_time);
                    $('#profile_form').find('#vat').val(result.data.vat);

                    if ($('#birth_date').length > 0) {
                        $('#birth_date').datepicker("update", result.data.birth_date);
                    }

                    if ($('#shop_start_time').length > 0) {
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
                    }
                    if ($('#shop_end_time').length > 0) {
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

                    $('.view-pdf a').attr("href", "#");
                    if (result.data.document_file && $('#profile_form .view-pdf').length) {
                        $('#profile_form .view-pdf').show();
                        $('.view-pdf a').attr("href", result.data.document_file);
                    }

                    $('.view-logo a').attr("href", "#");
                    if (result.data.company_logo && $('#profile_form .view-logo').length) {
                        $('#profile_form .view-logo').show();
                        $('.view-logo a').attr("href", result.data.company_logo);
                    }
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage("error", something_went_wrong);
            }
        });
    }

    // Function to handle account deletion
    function handleAccountDeletion(formSelector) {
        $(formSelector).submit(function (event) {
            event.preventDefault();
            var delete_account = $(this).find('#delete_account').val();
            if (delete_account === '') {
                $(this).find('#delete_account').addClass('is-invalid');
                $(this).find('#delete_accountError').html('<strong>' + please_type_delete_to_confirm + '</strong>');
                return;
            }

            //remove error message
            $(this).find('#delete_accountError').html('');
            $(this).find('#delete_account').removeClass('is-invalid');

            if (delete_account === 'delete') {
                var form_url = $(this).attr('action');
                var user_id = $(this).find('input[name=user_id]').val();

                $.ajax({
                    url: form_url,
                    type: 'POST',
                    data: { user_id: user_id, delete_account: delete_account },
                    success: function (result) {
                        if (result.status) {
                            showMessage("success", result.message);
                            // Optionally, redirect the user or perform additional actions
                            setTimeout(function () {
                                window.location.href = loginRoute;
                            }, 1000);
                        } else {
                            showMessage("error", result.message || something_went_wrong);
                        }
                    },
                    error: function (error) {
                        console.error(error);
                        showMessage("error", something_went_wrong);
                    }
                });
            } else {
                //show error message on delete_account input
                $(this).find('#delete_account').addClass('is-invalid');
                $(this).find('#delete_accountError').html('<strong>' + please_type_delete_to_confirm + '</strong>');
            }
        });
    }

    // Initialize account deletion handlers
    handleAccountDeletion('#confirm-delete-account-form-dealer');
    handleAccountDeletion('#confirm-delete-account-form-customer');

});
