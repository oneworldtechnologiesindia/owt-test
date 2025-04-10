$(document).ready(function () {
    getDocumentData();
    inittinymceEditor();

    $(".dark-light-switch #theme").on("change", function (e) {
        inittinymceEditor();
    });

    function inittinymceEditor() {
        let themeType = 'light'
        if ($(".dark-light-switch #theme").prop("checked") == true) {
            themeType = 'dark';
        }
        if ($(".editor-tinymce").length > 0) {
            while (tinymce.editors.length > 0) {
                tinymce.remove(tinymce.editors[0]);
            }
            tinymce.init({
                selector: "textarea.editor-tinymce",
                height: 500,
                menubar: false,
                content_css: (themeType == 'dark') ? 'dark' : '',
                plugins: [
                    "autolink link lists hr anchor",
                    "wordcount code nonbreaking",
                    "save paste"
                ],
                toolbar: "insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code"
            });
        }
    }
    $('#document-tab a').on('click', function (e) {
        getDocumentData();
        $('.invalid-feedback').html('');
        $('#document-tabContent form .is-invalid').removeClass('is-invalid');
    });

    $('#document-tabContent form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        var dataString = new FormData($this[0]);
        dataString.append('type', $(this).attr('name'));
        $('.invalid-feedback').html('');
        $('#document-tabContent form .is-invalid').removeClass('is-invalid');
        $.ajax({
            url: addUpdateUrl,
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
                    $('.invalid-feedback').html('');
                    $('#document-tabContent form .is-invalid').removeClass('is-invalid');
                    showMessage("success", result.message);
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.error').html("");
                    $.each(result.error, function (key) {
                        if (first_input == "") first_input = key;
                        $this.find('.' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                        $this.find('.' + key).addClass('is-invalid');
                    });
                    $this.find("." + first_input).focus();
                }
            },
            error: function (error) {
                // console.log(error)
                showMessage("error", something_went_wrong);
                $($this).find('button[type="submit"]').prop('disabled', false);
            }
        });
    });
});

function getDocumentData() {
    let $this = $('.document-tabContent form');
    $.ajax({
        url: getUrl,
        type: 'POST',
        beforeSend: function () {
            $($this).find('button[type="submit"]').prop('disabled', true);
        },
        success: function (result) {
            $($this).find('button[type="submit"]').prop('disabled', false);
            if (result.status) {
                $('.invalid-feedback').html('');
                $('#document-tabContent form .is-invalid').removeClass('is-invalid');
                $.each(result.data, function (form, formdata) {
                    let formid = '#' + form;

                    $.each(formdata, function (input, value) {
                        if (form == 'hq-terms-condition') {
                            if(input.indexOf("german") !== -1){
                                tinymce.get("hqtc-description-german").setContent(value);
                            } else {
                                tinymce.get("hqtc-description").setContent(value);
                            }
                        } else if (form == 'hq-privacy-policy') {
                            if(input.indexOf("german") !== -1){
                                tinymce.get("hqpp-description-german").setContent(value);
                            } else {
                                tinymce.get("hqpp-description").setContent(value);
                            }
                        } else if (form == 'dealer-terms-condition') {
                            if(input.indexOf("german") !== -1){
                                tinymce.get("dtc-description-german").setContent(value);
                            } else {
                                tinymce.get("dtc-description").setContent(value);
                            }
                        } else if (form == 'dealer-withdraw-policy') {
                            if(input.indexOf("german") !== -1){
                                tinymce.get("dwp-description-german").setContent(value);
                            } else {
                                tinymce.get("dwp-description").setContent(value);
                            }
                            
                        }
                        $(formid).find('.' + input).val(value);
                    })
                })
            } else {
                showMessage("error", something_went_wrong);
            }
        },
        error: function (error) {
            // console.log(error)
            showMessage("error", something_went_wrong);
            $($this).find('button[type="submit"]').prop('disabled', false);
        }
    });
}
