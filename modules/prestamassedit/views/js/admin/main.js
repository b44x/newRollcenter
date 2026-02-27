/**
* 2008-2025 Prestaworld
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one website.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
*
* DISCLAIMER
*
* Do not alter or add/update to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    prestaworld
* @copyright 2008-2025 Prestaworld
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
* International Registered Trademark & Property of prestaworld
*/
$(document).ready(function () {
    let xhr; // To handle ajax requests

    // MAKE TINY MCE EDITOR
    if ($('.presta_tiny_mce').length) {
        tinySetup({
            editor_selector: "presta_tiny_mce",
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });
    }

    if (typeof(shopCurrencySymbol) == 'undefined') {
        // default value if not set by php
        var shopCurrencySymbol = '$';
    }
    if (typeof(messagesByJs) == 'undefined') {
        // default value if not set by php
        messagesByJs = {
            'on_no_product_select': 'No product has been selected for updating.',
            'on_row_delete': 'Are you certain you want to delete this row?',
            'on_update_final': 'Have you verified all the values for updating?',
        };
    }

    // Presta Tabs
    $(document).on('click', '.presta-active', function () {
        $('.presta-active').removeClass('active');
        $(this).addClass('active');
    });

    // Handle action button events
    $(document).on('change', '.actions input[type="radio"]', function () {
        var currentSection = $(this).closest('.presta_form_group');
        var selectedAction = $(this).val();
        if (selectedAction === 'off' || selectedAction === 'remove_all') {
            currentSection.find('.form_input').hide();
        } else {
            currentSection.find('.form_input').show();
            if (selectedAction == 'add_perc' || selectedAction == 'sub_perc') {
                currentSection.find('.form_input .input-group-addon').text('%');
            } else if (selectedAction == 'add_amount' || selectedAction == 'sub_amount') {
                currentSection.find('.form_input .input-group-addon').text(shopCurrencySymbol);
            }
        }
    });
    $('.presta_form_group').each(function() {
        if ($(this).find('input[type="radio"][value="off"]').is(':checked')) {
            $(this).find('.form_input').hide();
        }
    });

    // Smart Wizard
    $('#presta_sw').smartWizard({
        selected: 0,
        theme: 'round',
        justified: true,
        autoAdjustHeight: false,
        enableUrlHash: false,
        transition: {
            animation: 'none',
            speed: '200',
        },
        toolbar: {
            position: 'none',
        },
        anchor: {
            enableNavigation: true,
            enableNavigationAlways: false,
            enableDoneState: false,
            markPreviousStepsAsDone: true,
            unDoneOnBackNavigation: true,
            enableDoneStateNavigation: true
        },
        keyboard: {
            keyNavigation: false,
        },
        getContent: null
    });

    $('.sw_prev_btn').on('click', function () {
        $('#presta_sw').smartWizard("prev");
    });
    $('.sw_prev_btn_to_edit').on('click', function () {
        $('#presta_sw').smartWizard("prev");
        $('#presta_sw').smartWizard("prev");
    });


    // Handle click on "Select All" control
    $(document).on('click', '#select-all-rows', function () {
        if (!$.fn.DataTable.isDataTable('#presta_dt')) return;

        let table = $('#presta_dt').DataTable();
        // Check/uncheck all checkboxes in the table
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);

        if (this.checked) {
            // Select all rows
            table.rows().select();
        } else {
            // Deselect all rows
            table.rows().deselect();
        }
    });
    // Handle click on checkbox to set state of "Select All" checkbox
    $(document).on('change', 'input[type="checkbox"]:not(input[type="checkbox"]#select-all-rows)', function () {
        if ($.fn.DataTable.isDataTable('#presta_dt')) {
            let table = $('#presta_dt').DataTable();

            var allChecked = table.rows().data().length === table.rows({ selected: true }).data().length;
            // All Rows Selected
            if (allChecked) {
                $('#select-all-rows').prop('indeterminate', !allChecked);
                $('#select-all-rows').prop('checked', allChecked);
                $('#select-all-rows').attr('aria-label', 'Deselect All');
            }
            // No Row Selected
            if (table.rows({ selected: true }).data().length == 0) {
                $('#select-all-rows').prop('indeterminate', false);
                $('#select-all-rows').prop('checked', false);
                $('#select-all-rows').attr('aria-label', 'Select All');
            }
            // Some Rows Selected
            if (!allChecked && table.rows({ selected: true }).data().length !== 0) {
                $('#select-all-rows').prop('indeterminate', true);
            }
        }
    });

    // queryBuilder Default Config
    if (typeof ($queryBuilderLang) !== 'undefined') {
        var QueryBuilder = $.fn.queryBuilder;
        QueryBuilder.regional['en'] = $queryBuilderLang;
        QueryBuilder.defaults({
            lang_code: 'en',
            icons: {
                add_rule: 'icon-plus-circle',
                remove_rule: 'icon-trash',
                error: 'icon-warning text-danger',
            }
        });
    }
    /* querybuilder */
    var queryBuilder = $('#presta_qb').queryBuilder({
        allow_empty: false,
        allow_groups: false,
        filters: typeof ($filterData) !== 'undefined' ? $filterData : '',
    });
    queryBuilder.on('afterCreateRuleInput.queryBuilder', (e, rule) => {
        if (rule.filter.data) {
            if (rule.filter.data["class"]) {
                rule.$el.find("select").addClass(rule.filter.data['class']);
            }
            if (rule.filter.data["autocomplete"]) {
                rule.$el.find("input").attr('autocomplete', rule.filter.data['autocomplete']);
            }
        }
        $('.rule-value-container>select.chosen').chosen().trigger('chosen:updated');
    });
    // trigger chosen for initial state
    $('select.chosen').chosen().trigger('chosen:updated'); // This line should be after queryBuilder line

    // Handle QueryBuilder's filter change
    $('#presta_qb').on('change', function () {
        var builderModel = $('#presta_qb').queryBuilder('getModel').rules
        let incompleteRule = false;
        builderModel.forEach(rule => {
            if (typeof (rule.value) == 'undefined'
                || ($.isArray(rule.value) && rule.value.length == 0)
                && rule.operator.type != 'is_not_empty' && rule.operator.type != 'is_empty'
            ) {
                incompleteRule = true;
            }
        });

        if (!incompleteRule) {
            /* cancel pending ajax calls */
            if (xhr && xhr.readyState != 4) {
                xhr.abort();
            }
            let joinFp = 0;
            let joinPa = 0;
            let joinCp = 0;
            let rules = $('#presta_qb').queryBuilder('getRules').rules;
            rules.forEach(rule => {
                if (rule['field'].startsWith('fp.')) {
                    joinFp = 1;
                }
                if (rule['field'].startsWith('pa.')) {
                    joinPa = 1;
                }
                if (rule['field'].startsWith('cp.')) {
                    joinCp = 1;
                }
            });

            let sql = $('#presta_qb').queryBuilder('getSQL');
            xhr = $.ajax({
                url: presta_current_url,
                method: 'POST',
                dataType: 'json',
                cache: false,
                data: {
                    ajax: true,
                    action: 'getFilterProducts',
                    whereCond: sql,
                    joinFp: joinFp,
                    joinPa: joinPa,
                    joinCp: joinCp,
                },
                beforeSend: function () {
                    $('#presta_no_product').hide();
                    $('#ajax_loader_product').show();
                    destroyDataTableIfExists('#presta_dt');
                    $('.matched_count').text('0');
                },
                success: function (res) {
                    $('#ajax_loader_product').hide();
                    if (res.status == 'ok') {
                        $('.matched_count').text(res.data.length);
                        createMatchedTable(res);
                        $('#submitPrestaMassEditFilter').prop('disabled', false);
                    } else {
                        $('#submitPrestaMassEditFilter').prop('disabled', true);
                        $('#presta_no_product').show();
                        $('.matched_count').text('0');
                    }
                },
                error: function () {
                    $('#submitPrestaMassEditFilter').prop('disabled', true);
                    $('#ajax_loader_product').hide();
                    $('#presta_no_product').show();
                    $('.matched_count').text('0');
                }
            });
        }
    });

    // Handle Step 1 ( Filter ) Submit
    $('#presta_me_filter_form').on('submit', function(e) {
        e.preventDefault();
        let stepOneDt = '#presta_dt';
        // return if not exists
        if(!$.fn.DataTable.isDataTable(stepOneDt)) return;

        let table = $(stepOneDt).DataTable();
        let selectedRowsData = table.rows({ selected: true }).data().toArray();
        createSelectedDataTables(selectedRowsData);

        if (selectedRowsData.length === 0) {
            return $.growl.error({title: '', message: messagesByJs['on_no_product_select']});
        };

        selectedProductsIds = [];
        selectedRowsData.forEach(product => {
            selectedProductsIds.push(product['id_product']);
        });
        $('input[name="presta_mass_edit[product_id_list]"]').val(JSON.stringify(selectedProductsIds));
        destroyDataTableIfExists('#selectedProductsForEdit');
        $('.selected_count').text(selectedRowsData.length);
        // Create DataTable of Selected Products
        $('#selectedProductsForEdit').DataTable({
            data: selectedRowsData,
            ordering: false,
            language: typeof ($dataTablesLang) !== 'undefined' ? $dataTablesLang : '',
            columns: [
                {
                    title: 'ID',
                    data: 'id_product',
                    type: 'html',
                    className: 'text-center fw-bold',
                },
                {
                    title: 'Image',
                    type: 'html',
                    className: 'text-center',
                    data: 'image_path',
                    render: function (data, type, row) {
                        return `<img src="${data}" width="50" height="50" />`;
                    }
                },
                {
                    title: 'Name',
                    data: 'name',
                    className: 'text-left',
                },
                {
                    title: 'Price (Tex Excl.)',
                    data: 'price',
                    type: 'html-num-fmt',
                    className: 'text-right',
                    render: function (data, type, row) {
                        return `${shopCurrencySymbol} ${parseFloat(data).toFixed(2)}`
                    }
                },
            ]
        });
        // Go to Next Step
        $('#presta_sw').smartWizard('next');
    });

    // Features
    $(document).on('click', '.feature_row_del_btn', function () {
        if(confirm(messagesByJs['on_row_delete'])) {
            $(this).closest('.feature').remove();
        }
    });
    var feature_row_index = 0;
    $(document).on('click', '#feature_row_add_btn', function () {
        if (xhr && xhr.readyState != 4) {
            xhr.abort();
        }
        var featureContainer = document.getElementById('features_container');
        xhr = $.ajax({
            url: presta_current_url,
            method: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                ajax: true,
                action: 'getFeatureRow',
                index: feature_row_index++,
            },
            beforeSend: function() {
                $('#new_feature_adding').show();
            },
            success: function(data) {
                $('#new_feature_adding').hide();
                if (data.status == 'ok') {
                    featureContainer.insertAdjacentHTML('beforeend', data.tpl_data);
                }
            },
            error: function() {
                $('#new_feature_adding').hide();
            }
        });
    });
    $(document).on('change', 'select.feature_name_val', function () {
        let idFeature = $(this).val();
        let selectBox = $(this).closest('.feature').find('.feature_value_val')[0];
        let customBox = $(this).closest('.feature').find('.feature_custom_val')[0];
        let selectBoxLoader = $(this).closest('.feature').find('.selectBoxLoader');

        if (xhr && xhr.readyState != 4) {
            xhr.abort();
        }
        xhr = $.ajax({
            url: presta_current_url,
            method: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                ajax: true,
                action: 'getFeatureValue',
                idFeature: idFeature,
            },
            beforeSend: function() {
                selectBoxLoader.show();
                $(selectBox).attr('disabled', true);
                $(customBox).attr('disabled', true);
                $(selectBox).empty();
                let option = '<option value="0">select a value</option>';
                selectBox.insertAdjacentHTML('beforeend', option);
            },
            success: function(res) {
                selectBoxLoader.hide();
                if (res.status == 'ok') {
                    $(selectBox).attr('disabled', false);
                    $(customBox).attr('disabled', false);
                    let values = res.feature_values;
                    values.forEach(val => {
                        option = `<option value="${val.id_feature_value}">${val.value}</option>`;
                        selectBox.insertAdjacentHTML('beforeend', option);
                    });
                }
            },
            error: function() {
                selectBoxLoader.hide();
                $(selectBox).attr('disabled', false);
                $(customBox).attr('disabled', false);
            }
        });
    });
    $(document).on('keyup', 'input.feature_custom_val', function () {
        $(this).closest('.feature_value_custom').find('input:hidden').val($(this).val());
    })

    // Related Products
    $(document).on('keyup', 'input#presta-product-search-input', function (e) {
        if (xhr && xhr.readyState != 4) {
            xhr.abort();
        }
        keyword = $.trim($(this).val());
        if (keyword.length > 0) {
            $('.presta-input-group-addon').html('<i class="icon-remove"></i>').addClass('has-value');
        } else {
            $('.presta-input-group-addon').html('<i class="icon-search"></i>');
        }
        if ((e.keyCode >= 48 && e.keyCode <= 57)
            || (e.keyCode >= 65 && e.keyCode <= 90)
            || e.keyCode == 8
            || e.keyCode == 13
        ) {
            if (keyword.length >= 3) {
                xhr = $.ajax({
                    url: presta_current_url,
                    method: 'POST',
                    dataType: 'json',
                    cache: false,
                    data: {
                        ajax: true,
                        action: 'searchRelatedProd',
                        keyword: keyword,
                    },
                    beforeSend: function () {
                        $('#presta-product-search-results').empty();
                        $('#related_prod_searching').show();
                    },
                    success: function (res) {
                        $('#related_prod_searching').hide();
                        if (res.status == 'ok') {
                            let data = res.data;
                            let arr = $('input[name^="presta_mass_edit[related_prods][related_prod][value]"]').map((_, el) => {
                                return $(el).val();
                            }).get();
                            data.forEach(row => {
                                if ($.inArray(`${row.id_product}`, arr) === -1) {
                                    $('#presta-product-search-results').append(`
                                        <div class="list-group-item select_related_prod" data-id-product="${row.id_product}">
                                            (${row.id_product}) ${row.name}
                                        </div>
                                    `);
                                }
                            });
                        } else {
                            $('#presta-product-search-results').append(`
                                <div class="list-group-item text-danger">${res.msg}</div>
                            `);
                        }
                    },
                    error: function () {
                        $('#related_prod_searching').hide();
                    }
                });
            } else {
                $('#presta-product-search-results').empty();
            }
        }
    });
    $(document).on('click', '.select_related_prod', function() {
        $('#selected_related_prod_list').append(
            `<div class='list-group-item presta-related-prod-item'>
                <input type="hidden" name="presta_mass_edit[related_prods][related_prod][value]" value="${$(this).attr('data-id-product')}">
                ${$(this).text()}
                <button type="button" class="btn btn-xs btn-danger remove_me_related_prod_item"><i class="icon-trash"></i></button>
            </div>`
        );
        $(this).fadeOut(300);
        $(this).remove();
    });
    $(document).on('click', 'button.remove_me_related_prod_item', function () {
        $(this).closest('.presta-related-prod-item').remove();
    });
    $(document).on('click', '.presta-input-group-addon.has-value', function () {
        $('input#presta-product-search-input').val('');
        $('#presta-product-search-results').empty();
        $('.presta-input-group-addon').html('<i class="icon-search"></i>').removeClass('has-value');
    });

    // Specific Prices
    var spName = 'presta_mass_edit[specific_price][specific_price][value]';
    $(document).on('change', `input[name="${spName}[all_customers]"]:checked`, function () {
        if ($(this).val() == 1) {
            $('#presta-search-customer-input').prop('disabled', true).val('').attr('data-toggle', '').removeClass('dropdown-toggle');
            $('#presta_selected_customer').empty();
            $('#presta_customer_list').empty();
            $('#customer_search.ajax_search_box').removeClass('open');
            $('.presta-search-input-group-addon').html('<i class="icon-search"></i>');
        } else {
            $('#presta-search-customer-input').prop('disabled', false);
        }
    });
    if ($(`input[name="${spName}[all_customers]"]:checked`).val() == 1) {
        $('#presta-search-customer-input').prop('disabled', true);
    } else {
        $('#presta-search-customer-input').prop('disabled', false);
    }
    $(document).on('change', `input[name="${spName}[reduction]"]:checked`, function () {
        if ($(this).val() == 1) {
            $('.presta_initial_price_disable_toggle').prop('disabled', false);
            $('input.presta_initial_price_disable_toggle').focus();
        } else {
            $('.presta_initial_price_disable_toggle').prop('disabled', true);
        }
    });
    $(document).on('change', `input[name="${spName}[leave_initial_price]"]:checked`, function () {
        if ($(this).val() == 1) {
            $('.switch_target_fixed_price_tax_excluded').prop('disabled', true);
        } else {
            $('.switch_target_fixed_price_tax_excluded').prop('disabled', false).focus();
        }
    });
    $(document).on('change', `select[name="${spName}[reduction_type]"]`, function () {
        $('.presta_specific_price_impact_reduction_value_symbol').text($(this).find("option:selected").text());
        if ($(this).val() == 'amount') {
            $('.presta-js-include-tax-row').fadeIn().show();
        } else if ($(this).val() == 'percentage') {
            $('.presta-js-include-tax-row').fadeOut().hide('show');
        }
    });
    $(document).on('change', '#presta_specific_price_date_range_unlimited', function () {
        if ($(this).is(':checked')) {
            $('#presta_specific_price_date_range_to').prop('disabled', true).val('');
        } else {
            var currentdate = new Date();
            var year = currentdate.getFullYear();
            var month = (currentdate.getMonth() + 1) > 9 ? (currentdate.getMonth() + 1) : '0' + (currentdate.getMonth() + 1);
            var date = (currentdate.getDate() + 1) > 9 ? (currentdate.getDate() + 1) : '0' + (currentdate.getDate() + 1);
            var hr = (currentdate.getHours()) > 9 ? (currentdate.getHours()) : '0' + (currentdate.getHours());
            var mm = (currentdate.getMinutes()) > 9 ? (currentdate.getMinutes()) : '0' + (currentdate.getMinutes());
            var ss = (currentdate.getSeconds()) > 9 ? (currentdate.getSeconds()) : '0' + (currentdate.getSeconds());

            var datetime = year + "-" + month  + "-" + date + " " + hr + ":" + mm + ":" + ss;
            $('#presta_specific_price_date_range_to').prop('disabled', false).val(datetime);
        }
    });
    if ($('#presta_specific_price_date_range_unlimited').is(':checked')) {
        $('#presta_specific_price_date_range_to').prop('disabled', true).val('');
    } else {
        var currentdate = new Date();
        var year = currentdate.getFullYear();
        var month = (currentdate.getMonth() + 1) > 9 ? (currentdate.getMonth() + 1) : '0' + (currentdate.getMonth() + 1);
        var date = (currentdate.getDate() + 1) > 9 ? (currentdate.getDate() + 1) : '0' + (currentdate.getDate() + 1);
        var hr = (currentdate.getHours()) > 9 ? (currentdate.getHours()) : '0' + (currentdate.getHours());
        var mm = (currentdate.getMinutes()) > 9 ? (currentdate.getMinutes()) : '0' + (currentdate.getMinutes());
        var ss = (currentdate.getSeconds()) > 9 ? (currentdate.getSeconds()) : '0' + (currentdate.getSeconds());
        var datetime = year + "-" + month  + "-" + date + " " + hr + ":" + mm + ":" + ss;
        $('#presta_specific_price_date_range_to').prop('disabled', false).val(datetime);
    }
    $(document).on('keyup', '#presta-search-customer-input', function (e) {
        if (xhr && xhr.readyState != 4) {
            xhr.abort();
        }
        let key = $.trim($(this).val());
        if (key.length > 0) {
            $('.presta-search-input-group-addon').html('<i class="icon-remove"></i>').addClass('has-value');
        } else {
            $('.presta-search-input-group-addon').html('<i class="icon-search"></i>');
        }
        if ((e.keyCode >= 48 && e.keyCode <= 57)
            || (e.keyCode >= 65 && e.keyCode <= 90)
            || e.keyCode == 8
            || e.keyCode == 13
        ) {
            if (key.length >= 2) {
                $(this).attr('data-toggle', 'dropdown').addClass('dropdown-toggle');
                $('#customer_search.ajax_search_box').addClass('open');
                xhr = $.ajax({
                    url: presta_current_url,
                    method: 'POST',
                    dataType: 'json',
                    cache: false,
                    data: {
                        ajax: true,
                        action: 'searchCustomer',
                        keyword: key,
                    },
                    beforeSend: function () {
                        $('#presta_customer_list.ajax_search_result').empty();
                        $('#searching_customer.ajax_search_loader').show();
                    },
                    success: function (res) {
                        $('#searching_customer.ajax_search_loader').hide();
                        if (res.status == 'ok') {
                            let data = res.data;
                            data.forEach(row => {
                                $('#presta_customer_list').append(`
                                    <button type="button" class="dropdown-item presta_ajax_customer" data-id-customer="${row.id_customer}">
                                        ${row.firstname} ${row.lastname} - ${row.email}
                                    </button>
                                `);
                            });
                        } else {
                            $('#presta_customer_list').append(`
                                <div class="dropdown-item text-danger disabled">${res.msg}</div>
                            `);
                        }
                    },
                    error: function () {
                        $('#searching_customer.ajax_search_loader').hide();
                    }
                });
            } else {
                $(this).attr('data-toggle', '').removeClass('dropdown-toggle');
                $('#customer_search.ajax_search_box').removeClass('open');
                $('#presta_customer_list').empty();
            }
        }
    });
    $(document).on('click', '.presta_ajax_customer', function () {
        $('#presta_selected_customer').empty().append(`
            <div class="list-group-item">
                <input type="hidden" name="${spName}[id_customer]" value="${$(this).attr('data-id-customer')}">
                <span style="display:flex; justify-content:space-between; align-item:center; gap: 1rem;">
                    ${$(this).text()}
                    <i class="icon-remove text-danger presta_remove_selected_customer" style="cursor: pointer;"></i>
                </span>
            </div>
        `);
    });
    $(document).on('click', '.presta-search-input-group-addon.has-value', function () {
        $(this).closest('.input-group').removeClass('open');
        $(this).closest('.input-group').find('input').val('').attr('data-toggle', '').removeClass('dropdown-toggle');
        $(this).closest('.input-group').find('.ajax_search_result').empty();
        $(this).html('<i class="icon-search"></i>').removeClass('has-value');
    });
    $(document).on('click', '.presta_remove_selected_customer', function () {
        $(this).closest('#presta_selected_customer').empty();
    })
    $('.presta_datetimepicker').datetimepicker({
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
        minDate: 0,
        changeMonth: true,
        changeYear: true,
    });

    // combination
    $(document).on('change', '.presta_combination_checkbox', function () {
        if ($(this).is(':checked')) {
            $('#presta-selected-attributes').append(`
                <span data-tag-id="${$(this).attr('id')}" class="presta-attr-tag">
                    ${$(this).attr('data-tag-val')}
                    <span class="material-icons">delete</span>
                </span>
            `);
        }
        if (!$(this).is(':checked')) {
            $('#presta-selected-attributes').find(`span[data-tag-id="${$(this).attr('id')}"]`).remove();
        }
    });
    $(document).on('click', '.presta-attr-tag', function () {
        $(`#${$(this).attr('data-tag-id')}`).prop('checked', false);
        $(this).remove();
    });

    // customization
    var field_index = 0;
    $(document).on('click', '#customization_field_add_btn', function () {
        if (xhr && xhr.readyState != 4) {
            xhr.abort();
        }
        var customization_fieldContainer = document.getElementById('presta_customization_fields');
        xhr = $.ajax({
            url: presta_current_url,
            method: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                ajax: true,
                action: 'getNewCustomizationField',
                field_index: field_index++,
            },
            beforeSend: function() {
                $('#new_customization_field_adding').show();
            },
            success: function(data) {
                $('#new_customization_field_adding').hide();
                if (data.status == 'ok') {
                    customization_fieldContainer.insertAdjacentHTML('beforeend', data.tpl_data);
                }
            },
            error: function() {
                $('#new_customization_field_adding').hide();
            }
        });
    });
    $(document).on('click', '.customization_field_del_btn', function () {
        if(confirm(messagesByJs['on_row_delete'])) {
            $(this).closest('.customization_field').remove();
        }
    });

    // initialize Tagify
    $('.presta_tag_input').tagify({addTagPrompt: typeof ($tagifyPromptLang) !== 'undefined' ? $tagifyPromptLang : 'Add Tags'});

    // Handle Step 2 ( Edit ) Submit
    $('form#presta_me_edit_form').on('submit', function(e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: "0" });
        $('input[name^="presta_mass_edit[options][tags][value]"]').map((_, el) => {
            return $(el).tagify('serialize')
        }).get();

        // Serialize the form data
        var formDataArray = $(this).serializeArray();
        // Convert the serialized array to a nested object
        var formObject = formDataToObject(formDataArray);

        // Check For Non complete fields
        $.ajax({
            url: presta_current_url,
            method: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                ajax: true,
                action: 'getActiveFormFields',
                fields: formObject.presta_mass_edit,
            },
            success: function (res) {
                if ('error' in res) {
                    $(`.presta_me_sidebar a[href="#${res.tab_id}"]`).click();
                    $.growl.error({title: res.title, message: res['error']});
                } else if ('data' in res) {
                    destroyDataTableIfExists('#preview_before_update');
                    let dataPreview = Object.entries(res.data).map(([, pData]) => ({
                        field: pData.field,
                        action: pData.action,
                        values: pData.values
                    }));
                    $('#preview_before_update').DataTable({
                        ordering: false,
                        filter: false,
                        language: typeof ($dataTablesLang) !== 'undefined' ? $dataTablesLang : '',
                        data: dataPreview,
                        columns: [
                            {
                                title: res.headers.field,
                                data: 'field',
                            },
                            {
                                title: res.headers.action,
                                data: 'action',
                            },
                            {
                                title: res.headers.value,
                                data: 'values',
                                render: function (data, type, row) {
                                    let col = '';
                                    if (data.lang.length >= 1 && data.lang[0] !== '') {
                                        $.each(data.lang, (key, lang) => {
                                            col += `<div class="list-group-item"><p class="badge">${lang}</p> ${data.value[key]}</div>`;
                                        });
                                        return col;
                                    } else {
                                        $.each(data.value, (key, value) => {
                                            col += `<div class="list-group-item">${value}</div>`;
                                        });
                                        return col;
                                    }
                                }
                            },
                        ]
                    });
                    $('#presta_sw').smartWizard('next');
                } else {
                    $.growl.error({title: '', message: res['invalid']});
                }
            },
            error: function (xhr, xhrRequest, error) {
                $.growl.error({message: error});
            }
        });
    });

    $('#prestaMassEditUpdateSubmit').on('click', function(e) {
        e.preventDefault();
        var formDataArray = $('form#presta_me_edit_form').serializeArray();
        // Convert the serialized array to a nested object
        var formObject = formDataToObject(formDataArray);
        updateProducts(formObject);
    });

    // Handle update form changes
    $(document).on('change', '.actions input[type="radio"]', function () {
        toggleUpdateDone('.basic_actions', '#base_update_done');
        toggleUpdateDone('.category_actions', '#category_update_done');
        toggleUpdateDone('.brand_and_feature_actions', '#brand_and_feature_update_done');
        toggleUpdateDone('.related_prods_actions', '#related_prod_update_done');
        toggleUpdateDone('.pricing_actions', '#pricing_update_done');
        toggleUpdateDone('.sp_actions', '#specific_price_update_done');
        toggleUpdateDone('.quantity_actions', '#quantity_update_done');
        toggleUpdateDone('.combinations_actions', '#combinations_update_done');
        toggleUpdateDone('.shipping_actions', '#shipping_update_done');
        toggleUpdateDone('.seo_actions', '#seo_update_done');
        toggleUpdateDone('.options_actions', '#options_update_done');
    });
    // For initial state when page load
    toggleUpdateDone('.basic_actions', '#base_update_done');
    toggleUpdateDone('.category_actions', '#category_update_done');
    toggleUpdateDone('.brand_and_feature_actions', '#brand_and_feature_update_done');
    toggleUpdateDone('.related_prods_actions', '#related_prod_update_done');
    toggleUpdateDone('.pricing_actions', '#pricing_update_done');
    toggleUpdateDone('.sp_actions', '#specific_price_update_done');
    toggleUpdateDone('.quantity_actions', '#quantity_update_done');
    toggleUpdateDone('.combinations_actions', '#combinations_update_done');
    toggleUpdateDone('.shipping_actions', '#shipping_update_done');
    toggleUpdateDone('.seo_actions', '#seo_update_done');
    toggleUpdateDone('.options_actions', '#options_update_done');
});

function showProLangField(iso_code, idLang) {
    $('.presta_caret').html(iso_code + ' <span class="caret" style="margin-left:5px;"></span>');
    $('.presta_div').hide();
    $('.presta_current_div_' + idLang).show();
}

// Function to destroy dataTable by selector if exists
function destroyDataTableIfExists(dtUniqueId) {
    if ($.fn.DataTable.isDataTable(dtUniqueId)) {
        $(dtUniqueId).DataTable().destroy();
        $(dtUniqueId).empty();
    }
}

// Function to check actions and toggle visibility
function toggleUpdateDone(actionClass, updateDoneId) {
    var actions = $(actionClass + ' input[type="radio"]:checked').map((_, el) => {
        return $(el).val();
    }).get();
    actions.some(value => value !== 'off') ? $(updateDoneId).show() : $(updateDoneId).hide();
}

function createMatchedTable(res) {
    $('#presta_dt').DataTable({
        ordering: false,
        filter: false,
        language: typeof ($dataTablesLang) !== 'undefined' ? $dataTablesLang : '',
        data: res.data,
        select: {
            style: 'multi',
            selector: 'td:first-child',
            headerCheckbox: false,
            info: false,
        },
        order: [
            [ 1, 'asc' ]
        ],
        initComplete: function() {
            var api = this.api();
            api.rows().select();
        },
        columns: [
            {
                title: '<input type="checkbox" class="dt-select-checkbox" id="select-all-rows" checked />',
                render: DataTable.render.select(),
                className: 'text-center fixed-width-xs bg-secondary cursor-pointer',
            },
            {
                title: 'ID',
                data: 'id_product',
                type: 'html',
                className: 'text-center fixed-width-xs fw-bold',
            },
            {
                title: 'Image',
                type: 'html',
                className: 'text-center fixed-width-sm',
                data: 'image_path',
                render: function (data, type, row) {
                    return `<img src="${data}" width="50" height="50" />`;
                }
            },
            {
                title: 'Name',
                data: 'name',
                className: 'text-left',
            },
            {
                title: 'Price (Tex Excl.)',
                data: 'price',
                type: 'html-num-fmt',
                className: 'text-right',
                render: function (data, type, row) {
                    return `${shopCurrencySymbol} ${parseFloat(data).toFixed(2)}`
                }
            },
        ]
    });
}

// Function to convert serialized array to a nested object based on keys
function formDataToObject(formDataArray) {
    var obj = {};

    // Iterate through all formData entries
    $.each(formDataArray, function(_, field) {
        // Split the name into parts based on the nested structure, handling both [] and {}
        var keys = field.name.split(/[\[\]\{\}]+/).filter(Boolean);
        // Reference to the current level in the resulting object
        var currentLevel = obj;

        // Iterate through all keys except the last one
        for (var i = 0; i < keys.length - 1; i++) {
            // If the key doesn't exist at the current level, create an empty object
            if (!currentLevel[keys[i]]) {
                currentLevel[keys[i]] = {};
            }
            currentLevel = currentLevel[keys[i]]; // Move to the next level
        }
        // Handle checkboxes: accumulate values in an array if the key already exists
        var lastKey = keys[keys.length - 1];
        if (currentLevel[lastKey]) {
            if (Array.isArray(currentLevel[lastKey])) {
                currentLevel[lastKey].push(field.value);
            } else {
                currentLevel[lastKey] = [currentLevel[lastKey], field.value];
            }
        } else {
            currentLevel[lastKey] = field.value;
        }
    });

    return obj;
}

function handleCustomizationRequired(checkbox) {
    if (!checkbox.checked) {
        $(`#is_required_${$(checkbox).data('field-index')}`).val('0');
    } else {
        $(`#is_required_${$(checkbox).data('field-index')}`).val('1');
    }
}

function updateProducts(formObject) {
    $.ajax({
        url: presta_current_url,
        method: 'POST',
        dataType: 'json',
        cache: false,
        data: {
            ajax: true,
            action: 'updateProducts',
            formData: formObject.presta_mass_edit,
        },
        beforeSend: function (xhr) {
            if (confirm(messagesByJs['on_update_final'])) {
                $('#presta_sw').smartWizard("loader", "show");
            } else {
                xhr.abort();
            }
        },
        success: function (res) {
            $('#presta_sw').smartWizard("loader", "hide");
            $('form#presta_me_edit_form').trigger('reset');
            toggleUpdateDone('.basic_actions', '#base_update_done');
            toggleUpdateDone('.category_actions', '#category_update_done');
            toggleUpdateDone('.brand_and_feature_actions', '#brand_and_feature_update_done');
            toggleUpdateDone('.related_prods_actions', '#related_prod_update_done');
            toggleUpdateDone('.pricing_actions', '#pricing_update_done');
            toggleUpdateDone('.sp_actions', '#specific_price_update_done');
            toggleUpdateDone('.quantity_actions', '#quantity_update_done');
            toggleUpdateDone('.combinations_actions', '#combinations_update_done');
            toggleUpdateDone('.shipping_actions', '#shipping_update_done');
            toggleUpdateDone('.seo_actions', '#seo_update_done');
            toggleUpdateDone('.options_actions', '#options_update_done');
            $('.presta_form_group').each(function() {
                if ($(this).find('input[type="radio"][value="off"]').is(':checked')) {
                    $(this).find('.form_input').hide();
                }
            });
            $('#successful_message').text(res['message']);
            $('#presta_sw').smartWizard("next");
        },
        error: function (xhr, status, error) {
            $('#presta_sw').smartWizard("loader", "hide");
            $.growl.error({message: error});
        }
    });
}

function createSelectedDataTables(selectedRowsData) {
    destroyDataTableIfExists('#selectedProductsForEdit2');
    $('.selected_count').text(selectedRowsData.length);
    $('#selectedProductsForEdit2').DataTable({
        data: selectedRowsData,
        ordering: false,
        language: typeof ($dataTablesLang) !== 'undefined' ? $dataTablesLang : '',
        columns: [
            {
                title: 'ID',
                data: 'id_product',
                type: 'html',
                className: 'text-center fw-bold',
            },
            {
                title: 'Image',
                type: 'html',
                className: 'text-center',
                data: 'image_path',
                render: function (data, type, row) {
                    return `<img src="${data}" width="50" height="50" />`;
                }
            },
            {
                title: 'Name',
                data: 'name',
                className: 'text-left',
            },
            {
                title: 'Price (Tex Excl.)',
                data: 'price',
                type: 'html-num-fmt',
                className: 'text-right',
                render: function (data, type, row) {
                    return `${shopCurrencySymbol} ${parseFloat(data).toFixed(2)}`
                }
            },
        ]
    });
}
