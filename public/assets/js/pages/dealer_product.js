$(document).ready(function () {
    /* Data table for the product listing */
    function format(d) {
        let html = '<table cellpadding="5" class="table table-striped addition-info-table" cellspacing="0" border="0" style="width:90%">' +
            '<tr>' +
            '<td width="20%">'+brand+':</td>' +
            '<td width="30%">' + d.brand_name + '</td>' +

            '<td width="20%">'+product_type+':</td>' +
            '<td width="30%">' + d.type_name + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>'+product_category+':</td>' +
            '<td>' + d.category_name + '</td>' +

            '<td>'+product_name+':</td>' +
            '<td>' + d.product_name + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>'+anschlusse+':</td>' +
            '<td>' + d.connections_html + '</td>' +

            '<td>'+ausfuhrung+':</td>' +
            '<td>' + d.execution_html + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>'+url+':</td>' +
            '<td>' + d.url + '</td>' +

            '<td>'+retail+':</td>' +
            '<td>' + d.retail + '</td>' +
            '</tr>' +
            '<tr>' +
            '<td>'+bemerkung+':</td>' +
            '<td>' + d.remark + '</td>';

        if (d.product_attributes.length) {
            if (d.product_attributes.length > 1) {
                html += '<td></td>' +
                    '<td></td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td><strong>'+all_attributes+':</strong></td>' +
                    '<td></td>' +
                    '<td></td>' +
                    '<td></td>' +
                    '</tr>';
                $.each(d.product_attributes, function (key, value) {
                    let connectionName = '';
                    let executionName = '';
                    if (value.connection) {
                        $.each(value.connection, function (key, value) {
                            connectionName = value.connection_name;
                        })
                    } else {
                        connectionName = '';
                    }

                    if (value.execution) {
                        $.each(value.execution, function (key, value) {
                            executionName = value.execution_name;
                        })
                    } else {
                        executionName = '';
                    }

                    html += '<tr>' +
                        '<td>'+ausfuhrung+' + '+anschlusse+' :</td>';
                    if (connectionName == '' && executionName == '') {
                        html += '<td> - </td>';
                    } else {

                        let jointtype = '';

                        if ((connectionName == '') || (executionName == '')) {
                            jointtype = ''
                        } else {
                            jointtype = ' + '
                        }

                        html += '<td>' + connectionName + jointtype + executionName + '</td>';
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

                    html += '<td>'+attributes+':</td>' +
                        '<td>' + existingAttribute.join(", ") + '</td>' +
                        '</tr>';

                })
                html += '</table>';
            } else {
                if (d.product_attributes[0].connection == null && d.product_attributes[0].execution == null) {
                    let existingAttribute = [];
                    if (d.product_attributes[0].in_stocks == 1) {
                        existingAttribute.push(in_stock);
                    }
                    if (d.product_attributes[0].is_useds == 1) {
                        existingAttribute.push(is_used);
                    }
                    if (d.product_attributes[0].ready_for_demos == 1) {
                        existingAttribute.push(ready_for_demo);
                    }
                    html += '<td>'+attributes+'</td>' +
                        '<td>' + existingAttribute.join(", ") + '</td>' +
                        '</tr>';
                } else {
                    html += '<td></td>' +
                        '<td></td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td><strong>'+all_attributes+':</strong></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '<td></td>' +
                        '</tr>';
                    let connectionName = '';
                    let executionName = '';

                    if (d.product_attributes[0].connection) {
                        $.each(d.product_attributes[0].connection, function (key, value) {
                            connectionName = value.connection_name;
                        })
                    } else {
                        connectionName = '';
                    }

                    if (d.product_attributes[0].execution) {
                        $.each(d.product_attributes[0].execution, function (key, value) {
                            executionName = value.execution_name;
                        })
                    } else {
                        executionName = '';
                    }

                    let jointtype = '';

                    if ((connectionName == '') || (executionName == '')) {
                        jointtype = ''
                    } else {
                        jointtype = ' + '
                    }

                    html += '<tr>' +
                        '<td>'+ausfuhrung+' + '+anschlusse+' :</td>' +
                        '<td>' + connectionName + jointtype + executionName + '</td>';
                    let existingAttribute = [];
                    if (d.product_attributes[0].in_stocks == 1) {
                        existingAttribute.push(in_stock);
                    }
                    if (d.product_attributes[0].is_useds == 1) {
                        existingAttribute.push(is_used);
                    }
                    if (d.product_attributes[0].ready_for_demos == 1) {
                        existingAttribute.push(ready_for_demo);
                    }

                    html += '<td>'+attributes+':</td>' +
                        '<td>' + existingAttribute.join(", ") + '</td>' +
                        '</tr>' +
                        '</table>';
                }
            }
        } else {
            html += '<td></td>' +
                '<td></td>' +
                '</tr>' +
                '</table>';
        }

        return html;
    }

    if ($('#filter-product-form').length > 0) {
        filterSelect2();
    }

    function filterSelect2() {
        if ($('#filter-product-form select').length > 0) {
            $('#filter-product-form select').each(function () {
                $(this).select2({
                    placeholder: $(this).attr('placeholder')
                });
            });
        }
    }

    function attributeSelect2() {
        if ($('.multiple-product-attributes select').length > 0) {
            $('.multiple-product-attributes select').each(function () {
                if (!$(this).hasClass('notexist')) {
                    $(this).select2({
                        placeholder: $(this).attr('placeholder'),
                        dropdownParent: '#add-product-attribute-modal',
                    });
                }
            });
        }
    }

    //when the Add Field button is clicked
    var count = 1;
    if ($('.multiple-product-attributes .row').length > 0) {
        count = ($('.multiple-product-attributes .row').length) + 1;
    } else {
        count = 1;
    }

    $("#add").click(function (e) {
        e.preventDefault();

        if ($('.notice-empty-row').hasClass('d-block')) {
            $('.notice-empty-row.d-block').removeClass('d-block').addClass('d-none');
            $('.notice-empty-row .empty_attribute').val('false')
        } else {
            $('.notice-empty-row').addClass('d-none');
            $('.notice-empty-row .empty_attribute').val('false')
        }

        let connectionOptions = $('.main-box select#product_connection').html(),
            executionOptions = $('.main-box select#product_execution').html(),
            attrinuteOptions = $('.main-box select#product_attribute').html(),
            connectioncol = $('.main-box .row.main-row-product-attributes').find('.connection-col').attr('data-displaycol'),
            executioncol = $('.main-box .row.main-row-product-attributes').find('.execution-col').attr('data-displaycol'),
            attributeColClass = '',
            blockClick = false;

        if (connectioncol == 'false' && executioncol == 'false') {
            blockClick = true;
            attributeColClass = 'col-lg-11'
        } else if (connectioncol == 'true' && executioncol == 'true') {
            attributeColClass = 'col-lg-5'
        } else if (connectioncol == 'true' || executioncol == 'true') {
            attributeColClass = 'col-lg-8'
        } else {
            attributeColClass = 'col-lg-5'
        }

        if (blockClick) {
            if ($('.multiple-product-attributes .row:not(".d-none")').length == 3) {
                return false;
            }
        }

        let rowHtml = '';

        if (connectioncol == 'true') {
            rowHtml += '<div class="col-lg-3"><div class="mb-3"><label for="product_connection" class="control-label">'+anschlusse+'</label><select class="form-control select2 product_connection" name="product_attribute[' + count + '][connection]" id="product_connection_' + count + '" placeholder='+select_connection+'>' + connectionOptions + '</select><span class="invalid-feedback" id="product_connection_' + count + 'Error" data-ajax-feedback="product_connection_' + count + '" role="alert"></span></div></div>';
        }

        if (executioncol == 'true') {
            rowHtml += '<div class="col-lg-3"><div class="mb-3"><label for="product_execution" class="control-label">'+ausfuhrung+'</label><select class="form-control select2 product_execution" name="product_attribute[' + count + '][execution]" id="product_execution_' + count + '" placeholder='+select_execution+'>' + executionOptions + '</select><span class="invalid-feedback" id="product_execution_' + count + 'Error" data-ajax-feedback="product_execution_' + count + '" role="alert"></span></div></div>';
        }

        rowHtml += '<div class="' + attributeColClass + '"><div class="mb-3"><label for="product_attribute" class="control-label">'+attributes+'</label><select class="form-control select2 product_attribute" name="product_attribute[' + count + '][attribute]" id="product_attribute_' + count + '" placeholder='+select_attribute+'>' + attrinuteOptions + '</select><span class="invalid-feedback" id="product_attribute' + count + 'Error" data-ajax-feedback="product_attribute' + count + '" role="alert"></span></div></div><div class="col-lg-1"><button class="btn btn-danger delete"><i class="bx bx-trash"></i></button></div>';
        $('.multiple-product-attributes').append('<div class="row added-row-product-attributes product-attributes-row-' + count + '">' + rowHtml + '</div>');
        attributeSelect2();

        $('.multiple-product-attributes .row.display-label').removeClass('display-label');
        $('.multiple-product-attributes .row:not(.d-none)').first().addClass('display-label');
        count++;
    });

    $("body").on("click", ".multiple-product-attributes .delete", function (e) {
        e.preventDefault();
        if ($('.multiple-product-attributes .row:not(".d-none")').length == 1) {
            if ($('.notice-empty-row').hasClass('d-none')) {
                $('.notice-empty-row.d-none').removeClass('d-none').addClass('d-block');
            } else {
                $('.notice-empty-row').addClass('d-block');
            }
            $('.notice-empty-row .empty_attribute').val('false')
        }

        if ($(this).parents('.row').hasClass('main-row-product-attributes')) {
            $('.main-row-product-attributes').addClass('d-none');
        } else {
            $(this).parents('.row').next('#product_attribute_duplicateError').remove();
            $(this).parents('.row').remove();
        }

        $('.multiple-product-attributes .row.display-label').removeClass('display-label');
        $('.multiple-product-attributes .row:not(.d-none)').first().addClass('display-label');
    });

    var listTable = $('#product_list_table').DataTable({
        language: changeDatatableLang(),
        searching: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        order: [
            [8, 'desc']
        ],
        ajax: {
            url: apiUrl,
            type: 'GET',
            headers: {
                'X-XSRF-TOKEN': $('meta[name=csrf-token]').attr('content'),
            },
            data: function (d) {
                d.search = $('#product_list_table_wrapper input[type="search"]').val()
                if ($('#brand_id').length > 0)
                    d['brand_id'] = $('#brand_id').val().toString();

                if ($('#brand_id').length > 0)
                    d['producttype_id'] = $('#producttype_id').val().toString();

                if ($('#productcategory_id').length > 0)
                    d['productcategory_id'] = $('#productcategory_id').val().toString();

                if ($('#product_id').length > 0)
                    d['product_id'] = $('#product_id').val().toString();

                if ($('#productexecution_id').length > 0)
                    d['productexecution_id'] = $('#productexecution_id').val().toString();

                if ($('#productconnection_id').length > 0)
                    d['productconnection_id'] = $('#productconnection_id').val().toString();

                if ($('#productattributes_id').length > 0)
                    d['productattributes_id'] = $('#productattributes_id').val().toString();
            },
        },
        columns: [{
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '',
            },
            {
                data: 'brand_name'
            },
            {
                data: 'type_name'
            },
            {
                data: 'category_name'
            },
            {
                data: 'product_name'
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
                    var contactId = full['id'];

                    if (contactId) {
                        var actions = '';
                        actions += ' <a href="javascript:void(0)" data-id="' + contactId + '" class="waves-effect waves-light pe-2 edit-attributes" title='+editLang+'><i class="bx bx-edit-alt bx-sm"></i></a>';
                        return actions;
                    }

                    return '';
                },
                width: '5%'
            },
            {
                data: 'updated_at',
                name: 'updated_at'
            },
        ],
        columnDefs: [
            { targets: 8, defaultContent: "-", visible: false },
        ],
        "drawCallback": function (settings) {}
    });

    $('#product_list_table tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = listTable.row(tr);
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
            tr.next().addClass('add-extrainfo-row');
        }
    });

    /* edit brand */

    $('body').on('click', '.edit-attributes', function (event) {
        var id = $(this).attr('data-id');
        $('.invalid-feedback').html('');
        if (!$('.default-product-attributes').hasClass('d-none')) {
            $('.default-product-attributes').addClass('d-none');
        }
        $('.main-row-product-attributes').removeClass('d-none');
        $('#add-product-attribute-form')[0].reset();
        $('.multiple-product-attributes .added-row-product-attributes').remove();
        $('.multiple-product-attributes .after-row-helpblock').remove();
        $('#add-product-attribute-form').find('.error-span').text('');
        $('#add-product-attribute-form').find('.form-group.has-error').removeClass('has-error');

        $('.main-box #product_attribute').find('option').remove();
        $('.main-box #product_connection').find('option').remove();
        $('.main-box #product_execution').find('option').remove();

        count = 1;
        $.ajax({
            url: detailUrl + '?id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                if (result.status) {
                    $('#add-product-attribute-modal').modal('show');
                    $('#attribute_product_id').val(id);
                    if (result.data) {
                        if (result.data.attributes) {
                            $optionHtml = "<option></option>";
                            $.each(result.data.attributes, function (index, value) {
                                $optionHtml += "<option value='" + index + "'>" + value + "</option>";
                            });
                            $('.main-box #product_attribute').append($optionHtml);

                            if ((Object.keys(result.data.connections).length > 0) && (Object.keys(result.data.executions).length > 0)) {
                                if ($('.attribute-col').hasClass('col-lg-8')) {
                                    $('.attribute-col').removeClass('col-lg-8').addClass('col-lg-5');
                                }
                                if ($('.attribute-col').hasClass('col-lg-11')) {
                                    $('.attribute-col').removeClass('col-lg-11').addClass('col-lg-5');
                                }
                            } else if ((Object.keys(result.data.connections).length > 0) || (Object.keys(result.data.executions).length > 0)) {
                                if ($('.attribute-col').hasClass('col-lg-5')) {
                                    $('.attribute-col').removeClass('col-lg-5').addClass('col-lg-8');
                                }
                                if ($('.attribute-col').hasClass('col-lg-11')) {
                                    $('.attribute-col').removeClass('col-lg-11').addClass('col-lg-8');
                                }
                            } else {
                                if ($('.attribute-col').hasClass('col-lg-5')) {
                                    $('.attribute-col').removeClass('col-lg-5').addClass('col-lg-11');
                                }
                                if ($('.attribute-col').hasClass('col-lg-8')) {
                                    $('.attribute-col').removeClass('col-lg-8').addClass('col-lg-11');
                                }
                            }

                            if (Object.keys(result.data.connections).length > 0) {
                                if ($('.main-box #product_connection').hasClass('notexist')) {
                                    $('.main-box #product_connection').removeClass('notexist');
                                }
                                $('.main-box #product_connection').parents('.product-attr-col').attr('data-displaycol', true)
                                $optionHtml = "<option></option>";
                                $.each(result.data.connections, function (index, value) {
                                    $optionHtml += "<option value='" + value.id + "'>" + value.connection_name + "</option>";
                                });
                                $('.main-box #product_connection').append($optionHtml);
                            } else {
                                $('.main-box #product_connection').addClass('notexist');
                                $('.main-box #product_connection').parents('.product-attr-col').attr('data-displaycol', false);
                            }
                            if (Object.keys(result.data.executions).length > 0) {
                                if ($('.main-box #product_execution').hasClass('notexist')) {
                                    $('.main-box #product_execution').removeClass('notexist');
                                }
                                $('.main-box #product_execution').parents('.product-attr-col').attr('data-displaycol', true);
                                $optionHtml = "<option></option>";
                                $.each(result.data.executions, function (index, value) {
                                    $optionHtml += "<option value='" + value.id + "'>" + value.execution_name + "</option>";
                                });
                                $('.main-box #product_execution').append($optionHtml);
                            } else {
                                $('.main-box #product_execution').addClass('notexist');
                                $('.main-box #product_execution').parents('.product-attr-col').attr('data-displaycol', false);
                            }
                            attributeSelect2();
                            if (result.data.existing.length > 0) {
                                for (var i = 1; i <= (result.data.existing.length); i++) {
                                    $('#add').click().closest('');
                                }
                            }

                            $(".multiple-product-attributes .row").each(function (index) {
                                $(this).addClass('row-product-attributes-' + index)
                            });
                            let countE = 0;
                            $.each(result.data.existing, function (key, value) {
                                if (value.connection_id) {
                                    $(".row-product-attributes-" + countE).find('select.product_connection').val(value.connection_id).trigger('change');
                                }
                                if (value.execution_id) {
                                    $(".row-product-attributes-" + countE).find('select.product_execution').val(value.execution_id).trigger('change');
                                }
                                let existingAttribute = [];
                                if (value.in_stock) {
                                    existingAttribute.push('1');
                                }
                                if (value.is_used) {
                                    existingAttribute.push('2');
                                }
                                if (value.ready_for_demo) {
                                    existingAttribute.push('3');
                                }
                                $(".row-product-attributes-" + countE).find('select.product_attribute').val(existingAttribute).trigger('change')
                                countE++;
                            });
                        }
                    } else {
                        $('.default-product-attributes').removeClass('d-none');
                        $('.main-row-product-attributes').addClass('d-none');
                    }


                    if ($('.multiple-product-attributes .row').length > 0) {
                        if ($('.notice-empty-row').hasClass('d-block')) {
                            $('.notice-empty-row.d-block').removeClass('d-block').addClass('d-none');
                        } else {
                            $('.notice-empty-row').addClass('d-none');
                        }
                        $('.notice-empty-row .empty_attribute').val('true')
                        $('.multiple-product-attributes .row.display-label').removeClass('display-label');
                        $('.multiple-product-attributes .row:not(.d-none)').first().addClass('display-label');
                        count = ($('.multiple-product-attributes .row').length) + 1;
                    } else {
                        if ($('.notice-empty-row').hasClass('d-none')) {
                            $('.notice-empty-row.d-none').removeClass('d-none').addClass('d-block');
                        } else {
                            $('.notice-empty-row').addClass('d-block');
                        }
                        $('.notice-empty-row .empty_attribute').val('false')
                        count = 1;
                    }

                    if (result.data.existing.length == 0) {
                        $('.main-row-product-attributes').addClass('d-none');
                        if ($('.notice-empty-row').hasClass('d-none')) {
                            $('.notice-empty-row.d-none').removeClass('d-none').addClass('d-block');
                        } else {
                            $('.notice-empty-row').addClass('d-block');
                        }
                        $('.notice-empty-row .empty_attribute').val('true')
                    }
                }
            }
        });
    });

    $('form#filter-product-form select').on('change', function () {
        getFilterOptions()
        $('#product_list_table').DataTable().ajax.reload();
    })

    function getFilterOptions() {
        let formData = new FormData($('form#filter-product-form')[0]);
        let brand_id = [];
        let producttype_id = [];
        let productcategory_id = [];
        let product_id = [];
        let connection_id = [];
        let execution_id = [];
        let attribute_id = [];
        for (const pair of formData.entries()) {
            if (`${pair[0]}` == 'brand_id[]') {
                brand_id.push(`${pair[1]}`);
            }
            if (`${pair[0]}` == 'producttype_id[]') {
                producttype_id.push(`${pair[1]}`);
            }
            if (`${pair[0]}` == 'productcategory_id[]') {
                productcategory_id.push(`${pair[1]}`);
            }
            if (`${pair[0]}` == 'product_id[]') {
                product_id.push(`${pair[1]}`);
            }
            if (`${pair[0]}` == 'productattributes_id[]') {
                attribute_id.push(`${pair[1]}`);
            }
            if (`${pair[0]}` == 'productattributes_id[]') {
                attribute_id.push(`${pair[1]}`);
            }
            if (`${pair[0]}` == 'productconnection_id[]') {
                connection_id.push(`${pair[1]}`);
            }
            if (`${pair[0]}` == 'productexecution_id[]') {
                execution_id.push(`${pair[1]}`);
            }
        }
        $.ajax({
            url: filterOptionsUrl,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.data) {
                    $('#filter-product-form select').find('option').remove();
                    $.each(result.data, function (id, value_array) {
                        let select2Id = id;
                        $.each(value_array, function (key, value) {
                            if (id == 'productattributes_id') {
                                $('#' + select2Id).append($("<option></option>").attr("value", key).text(value));
                            } else {
                                $('#' + select2Id).append($("<option></option>").attr("value", value.id).text(value.name));
                            }
                        })
                    })
                    if (brand_id) {
                        $('#brand_id').val(brand_id)
                    }
                    if (producttype_id) {
                        $('#producttype_id').val(producttype_id)
                    }
                    if (productcategory_id) {
                        $('#productcategory_id').val(productcategory_id)
                    }
                    if (product_id) {
                        $('#product_id').val(product_id)
                    }
                    if (attribute_id) {
                        $('#productattributes_id').val(attribute_id)
                    }
                    if (connection_id) {
                        $('#productconnection_id').val(connection_id)
                    }
                    if (execution_id) {
                        $('#productexecution_id').val(execution_id)
                    }
                    filterSelect2();
                } else {
                    if (result.message && result.status != 1) {
                        showMessage("error", result.message);
                    } else {
                        showMessage("error", something_went_wrong);
                    }
                }
            },
            error: function (error) {
                showMessage("error", something_went_wrong);
            }
        });
    }

    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        $('form#filter-product-form')[0].reset();
        getFilterOptions();
        $('#product_list_table').DataTable().ajax.reload();
    })

    /* form submit */

    $('#add-product-attribute-form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);
        $('.invalid-feedback').text('');
        $this.find('select.is-invalid').removeClass('is-invalid');
        $this.find('#product_attribute_duplicateError').remove();

        var dataString = new FormData($('#add-product-attribute-form')[0]);

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
                    showMessage("success", result.message);

                    $('#product_list_table').DataTable().ajax.reload();

                    setTimeout(function () {
                        $('#add-product-attribute-modal').modal('hide');
                    }, 300);
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    if (result.errorduplicate) {
                        let errorMessage = '<span class="invalid-feedback d-block" id="product_attribute_duplicateError" data-ajax-feedback="product_attribute_duplicate" role="alert"><strong>'+this_row_is_duplicate+'</strong></span>'
                        $.each(result.errorduplicate, function (key, value) {
                            $('.multiple-product-attributes .row:nth-child(' + value + ')').after(errorMessage);
                            $('.multiple-product-attributes .row:nth-child(' + value + ')').find('.mb-3 select').addClass('is-invalid');
                        });
                    } else {
                        $.each(result.error, function (key, value) {
                            if (key.includes(".")) {
                                let main_key = key.split('.')[2];
                                let currunt_row = parseInt(key.split('.')[1]);
                                if ($('.multiple-product-attributes .row').not(':visible').length) {
                                    currunt_row++;
                                }
                                $('.multiple-product-attributes .row.product-attributes-row-' + currunt_row).find('.' + 'product_' + main_key).closest('.mb-3').find('.invalid-feedback').html('<strong>' + value + '</strong>');
                                $('.multiple-product-attributes .row.product-attributes-row-' + currunt_row).find('.' + 'product_' + main_key).closest('.mb-3 select').addClass('is-invalid');
                            } else {
                                $('#' + key).closest('.mb-3').find('.invalid-feedback').html('<strong>' + value + '</strong>');
                                $('#' + key).closest('.mb-3 select').addClass('is-invalid');
                            }
                        });
                    }
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage('error', something_went_wrong);
            }
        });
    });
});
