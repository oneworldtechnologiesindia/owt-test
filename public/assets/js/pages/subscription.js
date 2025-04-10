$(document).ready(function () {

    // $('body').on("click", ".subscribe_btn", function (event) {
    //     $('.stripe-button-el').trigger('click');
    // });
    $('body').on("click", ".subscribe_btn", function (event) {
        var package_id=$(this).attr('data-package');
        Swal.fire({
            title: window.subscription_var.lang.title_of_confirm_subscription,
            html: window.subscription_var.lang.description_of_confirm_subscription,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: window.subscription_var.lang.checkout,
            cancelButtonText: no_cancel,
            confirmButtonClass: 'btn btn-success mt-2',
            cancelButtonClass: 'btn btn-danger ms-2 mt-2',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                console.log(package_id);
                $.ajax({
                    url: window.subscription_var.URL.processSubscription,
                    type: 'POST',
                    data: {'package_id':package_id},
                    dataType: "json",
                    beforeSend: function () {
                        $('#my_custom_loader').show();
                    },
                    success: function (result) {
                        if (result.redirect_url) {
                            window.open(result.redirect_url, '_blank');
                        } else if (!result.status && result.message) {
                            showMessage("error", result.message);
                        } else {
                            showMessage("error", something_went_wrong);
                        }
                    },
                    error: function (error) {
                        showMessage("error", something_went_wrong);
                    },
                    complete:function(ext){
                        $('#my_custom_loader').hide();
                    }
                });
            }
        });
    });
});
