$(document).ready(function () {
    /* Data table for the product execution listing */

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
        columns: [
            {
                data: 'title'
            },
            {
                data: 'size_name',
                name: 'size',
                render: function (_, _, full) {
                    var contactId = full['id'];

                    if (contactId) {
                        let badgeClass = 'bg-info';
                        var actions = '<h5><span class="badge ' + badgeClass + '">' + full['size_name'] + '</span></h5>';

                        return actions;
                    }

                    return '';
                },
                width: '10%'
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
                },
                width: '10%'
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

                        actions += ' <a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light pe-2 edit-row" title=' + editLang + '><i class="bx bx-edit-alt bx-sm"></i></a>';
                        actions += ' <a href="javascript:void(0)" data-id="' + elementId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title=' + deleteLang + '><i class="bx bx-trash bx-sm"></i></a>';

                        actions += '</div>';

                        return actions;
                    }

                    return '';
                },
                width: '5%'
            },
        ],
        columnDefs: [
            { targets: 5, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) { }
    });


    /* Open create new product execution popup */
    $('.add-new').click(function (event) {
        $('#edit-id').val('');
        $('.modal-lable-class').html(addLang);
        $('.invalid-feedback').html("");
        $('#add-form .is-invalid').removeClass('is-invalid');
        $('#image-preview').attr('src', defaultimg);

        $('#add-form')[0].reset();

        $('#add-modal').modal('show');
    });

    /* form submit */

    // Modify your form submit handler to include dimension validation
    $('#add-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);

        // Reset previous error messages
        $('.invalid-feedback').html("");
        $('#add-form .is-invalid').removeClass('is-invalid');

        const imageFile = document.getElementById('image').files[0];
        const selectedSize = $('#size').val();
        const hiddenImage = $('.hidden_image').val();

        // Proceed with validation only if a new image is uploaded
        if (imageFile) {
            validateImageDimensions(imageFile, selectedSize)
                .then(() => {
                    // Image dimensions are valid, proceed with form submission
                    submitForm($this);
                })
                .catch((errorMessage) => {
                    // Display error message
                    $('#imageError').html('<strong>' + errorMessage + '</strong>');
                    $('#image').addClass('is-invalid');
                    $this.find('button[type="submit"]').prop('disabled', false);
                });
        } else if (!hiddenImage && selectedSize) {
            // No image uploaded and no previous image exists
            $('#imageError').html('<strong>Please upload an image</strong>');
            $('#image').addClass('is-invalid');
        } else {
            // Either there's a hidden image already or no size selected yet
            // Proceed with form submission
            submitForm($this);
        }
    });

    /* edit product execution */

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


                    $('#add-form').find('#size').val(result.data.size);
                    $('#add-form').find('#title').val(result.data.title);
                    $('#add-form').find('#url').val(result.data.url);
                    $('#add-form').find('.status[value="' + result.data.status + '"]').prop('checked', true);
                    if (result.data.image) {
                        $('#image-preview').attr('src', basepath + result.data.image);
                        $('.hidden_image').val(result.data.image);
                    } else {
                        $('#image-preview').attr('src', defaultimg);
                    }
                }
            }
        });
    });

    /* delete product execution */
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

    function readURL(input, id) {
        id = id || '#image-preview';
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(id).attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
            $('#image-preview').removeClass('hidden');
        }
    }

    function fileInputSet(name = '') {
        if (name) {
            $('#imageUploadLabel').text(name);
        }
    }

    // Add validation when size dropdown changes
    $('#size').change(function () {
        const imageFile = document.getElementById('image').files[0];
        const selectedSize = $(this).val();
        const hiddenImage = $('.hidden_image').val();

        // Reset error message
        $('#imageError').html("");
        $('#image').removeClass('is-invalid');

        // If an image is already selected, validate its dimensions when size changes
        if (imageFile && selectedSize) {
            validateImageDimensions(imageFile, selectedSize)
                .catch((errorMessage) => {
                    $('#imageError').html('<strong>' + errorMessage + '</strong>');
                    $('#image').addClass('is-invalid');
                });
        }
    });

    $(".imageUpload").change(function () {
        const imageFile = this.files[0];
        const selectedSize = $('#size').val();

        // Reset error message
        $('#imageError').html("");
        $('#image').removeClass('is-invalid');

        // Always show preview regardless of validation
        readURL(this);
        var fileName = $(this).val().split('\\').pop();
        fileInputSet(fileName);

        // If size is selected, validate dimensions
        if (selectedSize && imageFile) {
            validateImageDimensions(imageFile, selectedSize)
                .catch((errorMessage) => {
                    $('#imageError').html('<strong>' + errorMessage + '</strong>');
                    $('#image').addClass('is-invalid');
                });
        }
    })
});


function validateImageDimensions(imageFile, selectedSize) {
    return new Promise((resolve, reject) => {
        // Size constraints (min and max dimensions)
        const sizeConstraints = {
            '1': { min: { width: 200, height: 50 }, max: { width: 300, height: 100 } },
            '2': { min: { width: 150, height: 200 }, max: { width: 200, height: 300 } },
            '3': { min: { width: 500, height: 200 }, max: { width: 700, height: 300 } },
            '4': { min: { width: 800, height: 200 }, max: { width: 1000, height: 300 } }
        };

        if (!imageFile || !selectedSize) {
            reject("Image file or size selection missing");
            return;
        }

        if (!sizeConstraints[selectedSize]) {
            reject("Invalid size selection");
            return;
        }

        const img = new Image();
        img.onload = function () {
            const width = this.width;
            const height = this.height;
            const constraints = sizeConstraints[selectedSize];

            if (width < constraints.min.width || height < constraints.min.height) {
                reject(`Image is too small. Minimum dimensions for ${selectedSize} are ${constraints.min.width}x${constraints.min.height}px`);
            } else if (width > constraints.max.width || height > constraints.max.height) {
                reject(`Image is too large. Maximum dimensions for ${selectedSize} are ${constraints.max.width}x${constraints.max.height}px`);
            } else {
                resolve(true);
            }
        };

        img.onerror = function () {
            reject("Failed to load image for validation");
        };

        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
        };
        reader.readAsDataURL(imageFile);
    });
}


// Extract the form submission logic to a separate function
function submitForm($form) {
    var dataString = new FormData($('#add-form')[0]);

    $.ajax({
        url: addUrl,
        type: 'POST',
        data: dataString,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $($form).find('button[type="submit"]').prop('disabled', true);
        },
        success: function (result) {
            $($form).find('button[type="submit"]').prop('disabled', false);
            if (result.status) {
                showMessage("success", result.message);
                $('#edit-id').val(0);
                $('#listTable').DataTable().ajax.reload();
                setTimeout(function () {
                    $('#add-modal').modal('hide');
                    $form[0].reset();
                    $('#image-preview').attr('src', defaultimg);
                }, 300);
            } else if (!result.status && result.message) {
                showMessage("error", result.message);
            } else {
                let first_input = "";
                $.each(result.error, function (key) {
                    if (first_input == "") first_input = key;
                    $('#add-form #' + key + 'Error').html('<strong>' + result.error[key] + '</strong>');
                    $('#add-form #' + key).addClass('is-invalid');
                });
                $('#add-form').find("." + first_input).focus();
            }
        },
        error: function (error) {
            $($form).find('button[type="submit"]').prop('disabled', false);
            showMessage('error', something_went_wrong);
        }
    });
}