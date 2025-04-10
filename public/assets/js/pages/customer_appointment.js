$(document).ready(function () {

    var today = new Date();
    // Format the date as "mm/dd/yyyy"
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
    var yyyy = today.getFullYear();
    var formattedDate = mm + '/' + dd + '/' + yyyy;

    $('#appo_date_container').datepicker({
        startDate: formattedDate,
    });

    $('#appo_date_container').datepicker('setDate', formattedDate);
    let formattedDate2 = today.toLocaleDateString('en-GB');
    getTimeslotsFun(formattedDate2);
    let time_slot_val = '';

    let isShowMsg = 1;
    $('body, html').on('click', function (e) {
        if ($('.bootstrap-timepicker-widget.dropdown-menu.open').length == 0 && isShowMsg == 0) {
            isShowMsg = 1;
            getDealerOfBrand();
        }
    });
    $('body').on('change', ".appo_type" ,function (e) {
        getDealerOfBrand();
    });
    $('#appo_time').on('click', function (e) {
        isShowMsg = 0;
    });

    $('#appo_date_container').datepicker().on('changeDate', function(e) {
        var date = new Date(e.date);
        formattedDate2 = date.toLocaleDateString('en-GB');
        $('#appo_date').val(formattedDate2);
        getTimeslotsFun(formattedDate2);
    });

    $('body').on('click','.timeslotbtn',function(){
        $('#list-group-slots a').removeClass('selected');
        $(this).addClass('selected');
        time_slot_val = $(this).attr('time-slot');
        $('#appo_time').val(time_slot_val);
        getDealerOfBrand();
    })

    function getDealerOfBrand() {
        $('#add_form .filter-option-container select').select2("enable")
        let product_ids = [];
        $(".sproduct_id").each(function () {
            product_ids.push($(this).val());
        });
        $.get("https://ipinfo.io", function(response) {
            var countryName = getCountryByCode(response.country);
            $.ajax({
                url: getDealerOfBrandUrl,
                type: "POST",
                data: {
                    appo_type: $('input[name=appo_type]:checked').val(),
                    brand_id: $('#brand_id').val(),
                    product_ids: product_ids,
                    appo_date: formattedDate2,
                    appo_time: time_slot_val,
                    country: countryName
                },
                beforeSend: function () {
                    $('#add_form #dealer_id').select2("enable", false)
                },
                dataType: "json",
                success: function (result) {
                    $('#add_form select#dealer_id').select2("enable")
                    $('#dealer_id').find('option').not(':first').remove();
                    if (result.status && result.dealer) {
                        $.each(result.dealer, function (key, data) {
                            var newOption = new Option(data.name + '( Rating : ' + data.average_rating+ ' )', data.id , false, false);
                            $('#dealer_id').append(newOption);
                        });
                        $('#dealer_id').select2({
                            placeholder: $('#dealer_id').attr('placeholder'),
                            allowClear: $('#dealer_id').attr('data-allow-clear')
                        });
                    } else if (result.message && status == false) {
                        showMessage("error", result.message);
                    }
                }
            });
        }, "jsonp");

    }

    /* form submit */
    $('#add_form').submit(function (event) {
        event.preventDefault();
        var $this = $(this);

        var dataString = new FormData($('#add_form')[0]);

        $.ajax({
            url: addAppointmentUrl,
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
                    setTimeout(function(){
                        window.location.href = indexUrl;
                    }, 1000)
                } else if (!result.status && result.message) {
                    showMessage("error", result.message);
                } else {
                    first_input = "";
                    $('.invalid-feedback strong').html("");
                    $('#add_form .is-invalid').removeClass('is-invalid');
                    $.each(result.error, function (key) {
                        let id = '';
                        if (key == 'sproduct_id') {
                            id = 'product_id'
                        } else {
                            id = key;
                        }

                        if (first_input == "") first_input = id;
                        $('#add_form .error-' + id).html('<strong>' + result.error[key] + '</strong>');
                        $('#add_form #' + id).addClass('is-invalid');
                    });
                    $('#add_form').find("." + first_input).focus();
                }
            },
            error: function (error) {
                $($this).find('button[type="submit"]').prop('disabled', false);
                showMessage("error", something_went_wrong);
                location.reload();
            }
        });
    });

    function getProductByFilter() {
        var dataString = new FormData($('#add_form')[0]);
        $('#product_id').find('option').remove();
        $.ajax({
            url: getProductsUrl,
            type: 'POST',
            data: dataString,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#add_form').find('button[type="submit"]').prop('disabled', true);
            },
            success: function (response) {
                $('#add_form').find('button[type="submit"]').prop('disabled', false);
                $('#product_id').select2('enable');
                if (response.status == '200') {
                    $('#product_id').select2('data', null);
                    $("#product_id").select2({
                        placeholder: select_product,
                        data: response.data
                    });
                } else {
                    showMessage("error", response.message);
                    $('#product_id').prop('disabled', true);
                    $("#product_id").select2({
                        placeholder: select_product,
                    });
                }
            },
            error: function (error) {
                console.log(error)
            }
        });
    }

    $("form#add_form .filter-option-container select").change(function () {
        if ($(this).attr('id') != 'dealer_id') {
            getFilterOptions();
            if ($(this).attr('id') == 'product_id') {
                let product = $(this).select2('data');
                let phtml = '',
                    exists_id = 0;


                if (product.length > 0) {

                    $(".sproduct_id").each(function () {
                        if ($(this).val() == product[0].id) {
                            exists_id = 1;
                        }
                    });

                    if (exists_id == 0) {
                        phtml += '<div class="product-box border border-2 border-dark">';
                        phtml += '<input type="hidden" name="sproduct_id[]" class="sproduct_id" value="' + product[0].id + '">';
                        phtml += '<i class="delete-btn bx bxs-trash waves-effect waves-light"></i>';
                        phtml += '<span>' + product[0].text + '</span>';
                        phtml += '</div>';
                        $('.notice-empty.d-block').removeClass('d-block').addClass('d-none');
                        $('.selected-products-list').removeClass('d-none').append(phtml);
                        getDealerOfBrand();
                    }
                }
            }
        }
    });

    $(document).on('click', '.product-box .delete-btn', function (e) {
        e.preventDefault();
        $(this).parent().remove();
        if ($('.product-box').length == 0) {
            $('.notice-empty.d-none').removeClass('d-none').addClass('d-block')
            $('.selected-products-list').addClass('d-none')
        } else {
            getDealerOfBrand();
        }
    })

    function getFilterOptions(type = 'form') {
        let formData = '';
        let brand_id = [];
        let producttype_id = [];
        let productcategory_id = [];
        let product_id = '';
        if (type == 'form') {
            formData = new FormData($('form#add_form')[0]);
            formData.append('appointment_type', 'yes');
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
            }
        }

        $.ajax({
            url: filterOptionsUrl,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#add_form .filter-option-container select').select2("enable", false)
            },
            success: function (result) {
                $('#add_form .filter-option-container select').select2("enable")
                if (result.data) {
                    $('#add_form .filter-option-container select').find('option').remove();
                    $.each(result.data, function (id, value_array) {
                        if (id == 'product_id') {
                            $('#' + id).append('<option></option>');
                        }
                        $.each(value_array, function (key, value) {
                            if (id == 'productattributes_id') {
                                $('#' + id).append($("<option></option>").attr("value", key).text(value));
                            } else {
                                if (id == 'product_id') {
                                    $('#' + id).append($("<option value='" + value.id + "' data-brand_id='" + value.brand_id + "'>" + value.text + "</option>"));
                                } else {
                                    $('#' + id).append($("<option value='" + value.id + "'>" + value.text + "</option>"));
                                }
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
                    filterSelect2();
                    $('#product_id').select2('enable');
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

    if ($('#add_form').length > 0) {
        filterSelect2();
    }

    $('.reset-form').on('click', function (e) {
        e.preventDefault();
        getFilterOptions('reset');
        $('form#add_form input, form#add_form textarea').val('');
        $('form#add_form #dealer_id').val('').trigger('change');
        $('form#add_form .is-invalid').removeClass('is-invalid')
        $('form#add_form .invalid-feedback').html('');
        $('.selected-products-list').html('');
        $('.notice-empty.d-none').removeClass('d-none').addClass('d-block')
        $('.selected-products-list').addClass('d-none')
    })

    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        getFilterOptions('reset');
    })

    function filterSelect2() {
        if ($('#add_form select').length > 0) {
            $('#add_form select').each(function () {
                $(this).select2({
                    placeholder: $(this).attr('placeholder'),
                    allowClear: $(this).attr('data-allow-clear')
                });
            });
        }
    }

    function getTimeslotsFun(selected_date){
        $.ajax({
            url: getTimePickerUrl,
            type: 'POST',
            data: {
                selectedDate:selected_date,
            },
            success: function (response) {
                var html = "<div class='list-group' id='list-group-slots'>";
                $.each(response.timeslot, function (index, value) {
                    html += '<a href="javascript:void(0)" class="list-group-item list-group-item-action mb-2 timeslotbtn" time-slot="'+value+'">'+value+'</a>';
                });
                html +='</div>'
                $('#appo_time_container').html(html);
                $('.timeslotbtn').first().addClass('selected');
                time_slot_val = $('#list-group-slots .selected').attr('time-slot');
                $('#appo_time').val(time_slot_val);
                $('#appo_date').val(selected_date);
                $('#appo_time_container').slimScroll({
                    marginLeft: '100px',
                    height: '283px',
                    width: '140px',
                    alwaysVisible: true,
                    color: '#000',
                    size: '5px',
                    railVisible: true,
                    railColor: '#f3f3f3',
                    railOpacity: 1,
                });
            },
            error: function (error) {
                console.log(error)
            }
        });
    }

});

function getCountryByCode(countryCode) {
    var countryNames = {
        "AF": "Afghanistan",
        "AL": "Albania",
        "DZ": "Algeria",
        "AS": "American Samoa",
        "AD": "Andorra",
        "AO": "Angola",
        "AI": "Anguilla",
        "AQ": "Antarctica",
        "AG": "Antigua and Barbuda",
        "AR": "Argentina",
        "AM": "Armenia",
        "AW": "Aruba",
        "AU": "Australia",
        "AT": "Austria",
        "AZ": "Azerbaijan",
        "BS": "Bahamas",
        "BH": "Bahrain",
        "BD": "Bangladesh",
        "BB": "Barbados",
        "BY": "Belarus",
        "BE": "Belgium",
        "BZ": "Belize",
        "BJ": "Benin",
        "BM": "Bermuda",
        "BT": "Bhutan",
        "BO": "Bolivia",
        "BA": "Bosnia and Herzegowina",
        "BW": "Botswana",
        "BV": "Bouvet Island",
        "BR": "Brazil",
        "IO": "British Indian Ocean Territory",
        "BN": "Brunei Darussalam",
        "BG": "Bulgaria",
        "BF": "Burkina Faso",
        "BI": "Burundi",
        "KH": "Cambodia",
        "CM": "Cameroon",
        "CA": "Canada",
        "CV": "Cape Verde",
        "KY": "Cayman Islands",
        "CF": "Central African Republic",
        "TD": "Chad",
        "CL": "Chile",
        "CN": "China",
        "CX": "Christmas Island",
        "CC": "Cocos (Keeling) Islands",
        "CO": "Colombia",
        "KM": "Comoros",
        "CG": "Congo",
        "CD": "Congo the Democratic Republic of the",
        "CK": "Cook Islands",
        "CR": "Costa Rica",
        "CI": "Cote d'Ivoire",
        "HR": "Croatia (Hrvatska)",
        "CU": "Cuba",
        "CY": "Cyprus",
        "CZ": "Czech Republic",
        "DK": "Denmark",
        "DJ": "Djibouti",
        "DM": "Dominica",
        "DO": "Dominican Republic",
        "TP": "East Timor",
        "EC": "Ecuador",
        "EG": "Egypt",
        "SV": "El Salvador",
        "GQ": "Equatorial Guinea",
        "ER": "Eritrea",
        "EE": "Estonia",
        "ET": "Ethiopia",
        "FK": "Falkland Islands (Malvinas)",
        "FO": "Faroe Islands",
        "FJ": "Fiji",
        "FI": "Finland",
        "FR": "France",
        "GF": "French Guiana",
        "PF": "French Polynesia",
        "TF": "French Southern Territories",
        "GA": "Gabon",
        "GM": "Gambia",
        "GE": "Georgia",
        "DE": "Germany",
        "GH": "Ghana",
        "GI": "Gibraltar",
        "GR": "Greece",
        "GL": "Greenland",
        "GD": "Grenada",
        "GP": "Guadeloupe",
        "GU": "Guam",
        "GT": "Guatemala",
        "GN": "Guinea",
        "GW": "Guinea-Bissau",
        "GY": "Guyana",
        "HT": "Haiti",
        "HM": "Heard and Mc Donald Islands",
        "VA": "Holy See (Vatican City State)",
        "HN": "Honduras",
        "HK": "Hong Kong",
        "HU": "Hungary",
        "IS": "Iceland",
        "IN": "India",
        "ID": "Indonesia",
        "IR": "Iran (Islamic Republic of)",
        "IQ": "Iraq",
        "IE": "Ireland",
        "IL": "Israel",
        "IT": "Italy",
        "JM": "Jamaica",
        "JP": "Japan",
        "JO": "Jordan",
        "KZ": "Kazakhstan",
        "KE": "Kenya",
        "KI": "Kiribati",
        "KP": "Korea Democratic People's Republic of",
        "KR": "Korea Republic of",
        "KW": "Kuwait",
        "KG": "Kyrgyzstan",
        "LA": "Lao People's Democratic Republic",
        "LV": "Latvia",
        "LB": "Lebanon",
        "LS": "Lesotho",
        "LR": "Liberia",
        "LY": "Libyan Arab Jamahiriya",
        "LI": "Liechtenstein",
        "LT": "Lithuania",
        "LU": "Luxembourg",
        "MO": "Macau",
        "MK": "Macedonia The Former Yugoslav Republic of",
        "MG": "Madagascar",
        "MW": "Malawi",
        "MY": "Malaysia",
        "MV": "Maldives",
        "ML": "Mali",
        "MT": "Malta",
        "MH": "Marshall Islands",
        "MQ": "Martinique",
        "MR": "Mauritania",
        "MU": "Mauritius",
        "YT": "Mayotte",
        "MX": "Mexico",
        "FM": "Micronesia Federated States of",
        "MD": "Moldova Republic of",
        "MC": "Monaco",
        "MN": "Mongolia",
        "MS": "Montserrat",
        "MA": "Morocco",
        "MZ": "Mozambique",
        "MM": "Myanmar",
        "NA": "Namibia",
        "NR": "Nauru",
        "NP": "Nepal",
        "NL": "Netherlands",
        "AN": "Netherlands Antilles",
        "NC": "New Caledonia",
        "NZ": "New Zealand",
        "NI": "Nicaragua",
        "NE": "Niger",
        "NG": "Nigeria",
        "NU": "Niue",
        "NF": "Norfolk Island",
        "MP": "Northern Mariana Islands",
        "NO": "Norway",
        "OM": "Oman",
        "PK": "Pakistan",
        "PW": "Palau",
        "PA": "Panama",
        "PG": "Papua New Guinea",
        "PY": "Paraguay",
        "PE": "Peru",
        "PH": "Philippines",
        "PN": "Pitcairn",
        "PL": "Poland",
        "PT": "Portugal",
        "PR": "Puerto Rico",
        "QA": "Qatar",
        "RE": "Reunion",
        "RO": "Romania",
        "RU": "Russian Federation",
        "RW": "Rwanda",
        "KN": "Saint Kitts and Nevis",
        "LC": "Saint Lucia",
        "VC": "Saint Vincent and the Grenadines",
        "WS": "Samoa",
        "SM": "San Marino",
        "ST": "Sao Tome and Principe",
        "SA": "Saudi Arabia",
        "SN": "Senegal",
        "SC": "Seychelles",
        "SL": "Sierra Leone",
        "SG": "Singapore",
        "SK": "Slovakia Slovak Republic",
        "SI": "Slovenia",
        "SB": "Solomon Islands",
        "SO": "Somalia",
        "ZA": "South Africa",
        "GS": "South Georgia and the South Sandwich Islands",
        "ES": "Spain",
        "LK": "Sri Lanka",
        "SH": "St. Helena",
        "PM": "St. Pierre and Miquelon",
        "SD": "Sudan",
        "SR": "Suriname",
        "SJ": "Svalbard and Jan Mayen Islands",
        "SZ": "Swaziland",
        "SE": "Sweden",
        "CH": "Switzerland",
        "SY": "Syrian Arab Republic",
        "TW": "Taiwan Province of China",
        "TJ": "Tajikistan",
        "TZ": "Tanzania United Republic of",
        "TH": "Thailand",
        "TG": "Togo",
        "TK": "Tokelau",
        "TO": "Tonga",
        "TT": "Trinidad and Tobago",
        "TN": "Tunisia",
        "TR": "Turkey",
        "TM": "Turkmenistan",
        "TC": "Turks and Caicos Islands",
        "TV": "Tuvalu",
        "UG": "Uganda",
        "UA": "Ukraine",
        "AE": "United Arab Emirates",
        "GB": "United Kingdom",
        "US": "United States",
        "UM": "United States Minor Outlying Islands",
        "UY": "Uruguay",
        "UZ": "Uzbekistan",
        "VU": "Vanuatu",
        "VE": "Venezuela",
        "VN": "Viet Nam",
        "VG": "Virgin Islands (British)",
        "VI": "Virgin Islands (U.S.)",
        "WF": "Wallis and Futuna Islands",
        "EH": "Western Sahara",
        "YE": "Yemen",
        "ZM": "Zambia",
        "ZW": "Zimbabwe"
    }

    return countryNames[countryCode] || "Unknown Country";
}