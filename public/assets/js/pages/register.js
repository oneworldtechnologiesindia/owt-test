$(document).ready(function () {
    $("#registerForm .is-invalid").first().focus();
    $('body').on('change', '.role_type', function (event) {
        let curruntValue = $(this).val();
        $('#registerForm')[0].reset();
        $('#registerForm .is-invalid').removeClass('is-invalid');
        $('#registerForm invalid-feedback').remove();
        $("input[value='" + curruntValue + "']").prop("checked", true);
        showDiv();
    });

    $('.register-button').on('click', function () {
        $(this).attr('disabled', true);
        $("#registerForm").submit();
    });

    $('body').on('change', '.salutation', function (event) {
        let curruntValue = $(this).val();
        $("input[value='" + curruntValue + "']").prop("checked", true);
        showDiv();
    });

    $('.terms-documents').on('click', function (e) {
        e.preventDefault();
        let type = $(this).data('type')
        $.ajax({
            url: getSignupDocumentUrl,
            data: {
                type: type
            },
            type: 'post',
            success: function (result) {
                if (result.status) {
                    $('#documents-modal').find('#modal-title, #modal-description').text('');
                    $.each(result.data, function (key, value) {

                        if (localLang == 'de' && key.indexOf("german") !== -1) {

                            if (key.indexOf("title") !== -1) {
                                key = 'title';
                            } else if (key.indexOf("description") !== -1) {
                                key = 'description';
                            }

                            $('#documents-modal').find('#modal-' + key).html(value);


                        } else if (localLang == 'en') {

                            $('#documents-modal').find('#modal-' + key).html(value);

                        }
                    })
                    $('#documents-modal').modal('show')
                } else {
                    showMessage("error", 'Invalid request!');
                }
            },
            error: function (error) {
                // console.log(error)
                showMessage("error", something_went_wrong);
            }
        })
    })

    showDiv();

    function showDiv() {
        var role_type = $('input[name=role_type]:checked').val();
        $('.customer_field_add').hide();
        if (role_type == "2") {
            $('.dealer_field').show();
            $('.customer_field').hide();
        } else {
            $('.dealer_field').hide();
            $('.customer_field').show();
        }

        let salutationValue = $('input[name=salutation]:checked').val();
        if (salutationValue == 'firma') {
            $('.customer_field_add').show();
        } else {
            $('.customer_field_add').hide();
        }
    }
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
});
