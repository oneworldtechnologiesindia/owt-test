$(document).ready(function () {
    $('#password_form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        var dataString = new FormData($('#password_form')[0]);
        $('.invalid-feedback').html('');
        $('#password_form .is-invalid').removeClass('is-invalid');
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
                    $this[0].reset();
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.errors, function (key) {
                        if (first_input == "") first_input = key;
                        $('#password_form #' + key + 'Error').html('<strong>' + result.errors[key] + '</strong>');
                        $('#password_form #' + key).addClass('is-invalid');
                    });
                    $('#password_form').find("." + first_input).focus();
                }
            },
            error: function (response) {
                if (response.responseJSON.errors.current_password) {
                    $('#current_passwordpError').html('<strong>' + response.responseJSON.errors.current_password + '</strong>');
                    $('#current_passwordp').addClass('is-invalid');
                }
                if (response.responseJSON.errors.password) {
                    $('#passwordpError').html('<strong>' + response.responseJSON.errors.password +
                        '</strong>');
                    $('#passwordp').addClass('is-invalid');
                }
                if (response.responseJSON.errors.password_confirmation) {
                    $('#password_confirmpError').html('<strong>' + response.responseJSON.errors
                        .password_confirmation + '</strong>');
                    $('#password_confirmp').addClass('is-invalid');
                }
                $($this).find('button[type="submit"]').prop('disabled', false);
            }
        });
    });
});
