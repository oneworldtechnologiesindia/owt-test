$(document).ready(function () {
    /* Data table for the brand listing */
    function format(d) {
        let html = `<table id="product_list_table"
        class="table align-middle table-hover table-nowrap w-100 dataTable">
            <thead class="table-light">
                <tr>
                    <th>${product}</th>
                    <th>${type}</th>
                    <th>${category}</th>
                    <th>${retail}</th>
                    <th>${url}</th>
                    <th>${Status}</th>
                </tr>
            </thead>
        </table>`;
        return html;
    }

    var listTable = $('#listTable').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        order: [[2, 'desc']],
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
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '',
                render: function (_, _, full) {
                    // Adding an ID attribute to the column cell
                    return '<div class="brand_id" id="' + full['brand_id'] + '"></div>';
                }
            },
            {
                data: 'brand_name'
            },
            {
                data: 'created_at',
                name: 'created_at',
                width: '15%'
            },
            {
                sortable: false,
                render: function (_, _, full) {
                    var contactId = full['id'];

                    if (contactId) {
                        var actions = '';
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light text-danger pe-2 delete-row" title=' + deleteLang + '><i class="bx bx-trash bx-sm"></i></a>';
                        return actions;
                    }

                    return '';
                },
                width: '9%'
            },
        ],
    });


    /* Open create new brand popup */
    $('.add-new').click(function (event) {
        $('#edit-id').val('');
        $('.modal-lable-class').html(addLang);
        $('.invalid-feedback').html('');
        $('#add-form .is-invalid').removeClass('is-invalid');

        $('#add-form')[0].reset();
        $('#brand_id').select2({
            placeholder: select_brand,
            dropdownParent: '#add-modal'
        });

        $('#add-modal').modal('show');
    });

    $('#listTable tbody').on('click', 'td.dt-control', function () {
        $('#id').val('');

        var tr = $(this).closest('tr');
        var id = $(this).find('.brand_id').attr('id');
        var row = listTable.row(tr);

        $('#listTable tbody tr').each(function() {
            if (this !== tr[0] && listTable.row(this).child.isShown()) {
                $(this).removeClass('shown');
                listTable.row(this).child.hide();
            }
        });
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            $('#id').val(id);
            tr.addClass('shown');
            tr.next().addClass('add-extrainfo-row');
            $('#product_list_table').DataTable({
                language: changeDatatableLang(),
                searching: true,
                pageLength: 10,
                processing: true,
                serverSide: true,
                ajax: {
                    url: productList,
                    type: 'GET',
                    headers: {
                        'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
                    },
                    data: function (d) {
                        d.id = $('#id').val();
                        return d;
                    },
                },
                columns: [
                    {
                        data: 'product_name'
                    },
                    {
                        data: 'type_name'
                    },
                    {
                        data: 'category_name'
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
                            var html = '';
                            var product_attribute = full['product_attributes'];
                            if (product_attribute.length) {
                                if (product_attribute.length > 1) {
                                    html += '<strong>' + all_attributes + ':</strong><br>';
                                    $.each(product_attribute, function (key, value) {
                                        let connectionName = '';
                                        let executionName = '';
                                        if (value.connection) {
                                            $.each(value.connection, function (key, value) {
                                                connectionName = value.connection_name;
                                            });
                                        }

                                        if (value.execution) {
                                            $.each(value.execution, function (key, value) {
                                                executionName = value.execution_name;
                                            });
                                        }

                                        if (connectionName || executionName) {
                                            html += ausfuhrung + ' + ' + anschlusse + ' : ' + connectionName + (connectionName && executionName ? ' + ' : '') + executionName + '<br>';
                                        } else {
                                            html += ausfuhrung + ' + ' + anschlusse + ' : - <br>';
                                        }

                                        let existingAttribute = [];
                                        if (value.in_stocks == 1) {
                                            existingAttribute.push(in_stock);
                                        }
                                        if (value.is_useds == 1) {
                                            existingAttribute.push(is_used);
                                        }
                                        if (value.ready_for_demos == 1) {
                                            existingAttribute.push(ready_for_demo);
                                        }

                                        html += attributes + ': ' + existingAttribute.join(", ") + '<br>';
                                    });
                                } else {
                                    let singleAttribute = product_attribute[0];
                                    let connectionName = '';
                                    let executionName = '';

                                    if (singleAttribute.connection) {
                                        $.each(singleAttribute.connection, function (key, value) {
                                            connectionName = value.connection_name;
                                        });
                                    }

                                    if (singleAttribute.execution) {
                                        $.each(singleAttribute.execution, function (key, value) {
                                            executionName = value.execution_name;
                                        });
                                    }

                                    if (connectionName || executionName) {
                                        html += '<strong>' + all_attributes + ':</strong><br>';
                                        html += ausfuhrung + ' + ' + anschlusse + ' : ' + connectionName + (connectionName && executionName ? ' + ' : '') + executionName + '<br>';
                                    }

                                    let existingAttribute = [];
                                    if (singleAttribute.in_stocks == 1) {
                                        existingAttribute.push(in_stock);
                                    }
                                    if (singleAttribute.is_useds == 1) {
                                        existingAttribute.push(is_used);
                                    }
                                    if (singleAttribute.ready_for_demos == 1) {
                                        existingAttribute.push(ready_for_demo);
                                    }

                                    html += attributes + ': ' + existingAttribute.join(", ") + '<br>';
                                }
                            } else {
                                html += '-';
                            }
                            return html;
                        },
                    },
                ],
            });
        }
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
                    $('#brand_id').select2({
                        placeholder: select_brand,
                        dropdownParent: '#add-modal'
                    });
                    $('#edit-id').val(0);
                    showMessage("success", result.message);

                    $('#listTable').DataTable().ajax.reload();
                    getBrandLis();

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
        $('#add-form .is-invalid').removeClass('is-invalid');
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

                    $('#add-form').find('#brand_name').val(result.data.brand_name);
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
                        getBrandLis();
                        $('#listTable').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    $('#brand_id').select2({
        placeholder: select_brand,
        dropdownParent: '#add-modal'
    });

    function getBrandLis() {
        $('#brand_id').find('option').not(':first').remove();
        $.ajax({
            url: getBrandListUrl,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.data) {
                    $.each(result.data, function (key, row) {
                        var newOption = new Option(row, key, false, false);
                        $('#brand_id').append(newOption);
                    })
                }
                $('#brand_id').select2({
                    placeholder: select_brand,
                    dropdownParent: '#add-modal'
                });
            }
        });
    }
});
