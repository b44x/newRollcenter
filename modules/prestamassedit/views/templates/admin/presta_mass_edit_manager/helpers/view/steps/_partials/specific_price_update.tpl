{**
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
*}
{* <!-- Specific Prices --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">
                {l s='Specific Prices' d='Modules.Prestamassedit.Admin'}
            </label>
            <div class="actions sp_actions">
                <label for="specific_price_off" class="btn btn-xs">
                    <input type="radio" id="specific_price_off" name="presta_mass_edit[specific_price][specific_price][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="specific_price_add" class="btn btn-xs">
                    <input type="radio" id="specific_price_add" name="presta_mass_edit[specific_price][specific_price][action]" value="add">
                    {l s='Add' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="specific_price_remove_all" class="btn btn-xs">
                    <input type="radio" id="specific_price_remove_all" name="presta_mass_edit[specific_price][specific_price][action]" value="remove_all">
                    {l s='Remove All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="specific_price_replace" class="btn btn-xs">
                    <input type="radio" id="specific_price_replace" name="presta_mass_edit[specific_price][specific_price][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            <h4 class="mb-0"><label class="mb-0">{l s='Conditions' d='Modules.Prestamassedit.Admin'}</label></h4>
            <div>
                <div class="mb-2">
                    <label class="control-label">
                        {l s='Apply to:' d='Modules.Prestamassedit.Admin'}
                    </label>
                    <div class="presta-apply-to-css row">
                        <div class="col-md-4 mb-2">
                            <select name="presta_mass_edit[specific_price][specific_price][value][id_currency]" class="form-control chosen">
                                <option value="0" selected="selected">{l s='All Currencies' d='Modules.Prestamassedit.Admin'}</option>
                                {foreach from=$currencies item=currency}
                                    <option value="{$currency.id_currency|escape:'htmlall':'UTF-8'}">{$currency.name|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <select name="presta_mass_edit[specific_price][specific_price][value][id_country]" class="form-control chosen">
                                <option value="0" selected="selected">{l s='All Countries' d='Modules.Prestamassedit.Admin'}</option>
                                {foreach from=$countries item=country}
                                    <option value="{$country.id_country|escape:'htmlall':'UTF-8'}">{$country.name|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <select name="presta_mass_edit[specific_price][specific_price][value][id_group]" class="form-control chosen">
                                <option value="0" selected="selected">{l s='All Groups' d='Modules.Prestamassedit.Admin'}</option>
                                {foreach from=$groups item=group}
                                    <option value="{$group.id_group|escape:'htmlall':'UTF-8'}">{$group.name|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex mb-2">
                        <label class="control-label">
                            {l s='Apply to all customers' d='Modules.Prestamassedit.Admin'}
                        </label>
                        <span class="switch prestashop-switch fixed-width-xs" id="presta_mass_edit[specific_price][specific_price][value][all_customers]">
                            <input
                                type="radio"
                                name="presta_mass_edit[specific_price][specific_price][value][all_customers]"
                                id="presta_mass_edit[specific_price][specific_price][value][all_customers]_on"
                                class="form-control"
                                value="1"
                                {if isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][all_customers])
                                    && $smarty.post.presta_mass_edit[specific_price][specific_price][value][all_customers] == '1'}
                                    checked="checked"
                                {elseif !isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][all_customers])}
                                    checked="checked"
                                {/if}>
                            <input
                                type="radio"
                                name="presta_mass_edit[specific_price][specific_price][value][all_customers]"
                                id="presta_mass_edit[specific_price][specific_price][value][all_customers]_off"
                                class="form-control"
                                value="0"
                                {if isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][all_customers])
                                    && $smarty.post.presta_mass_edit[specific_price][specific_price][value][all_customers] == '0'}
                                    checked="checked"
                                {/if}>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="customer_search" class="input-group dropdown ajax_search_box mb-2">
                                <input class="form-control" autocomplete="off" id="presta-search-customer-input"
                                    placeholder="Search customer" type="text" spellcheck="false" aria-haspopup="true" aria-expanded="false">
                                <label for="presta-search-customer-input" class="input-group-addon presta-search-input-group-addon">
                                    <i class="icon-search"></i>
                                </label>
                                <div class="dropdown-menu">
                                    <div class="ajax_search_result" id="presta_customer_list"></div>
                                    <div id="searching_customer" class="presta_me_loader ajax_search_loader">
                                        <img src="{$presta_ajax_loader|escape:'htmlall':'UTF-8'}" width="40" height="30">
                                    </div>
                                </div>
                            </div>
                            <div id="presta_selected_customer"></div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="control-label" for="specific_price_combination_id">
                        {l s='Combination' d='Modules.Prestamassedit.Admin'}
                        <label class="required"></label>
                    </label>
                    <select
                        name="presta_mass_edit[specific_price][specific_price][value][id_product_attribute]"
                        class="form-control" disabled>
                        <option value="0" selected="selected">{l s='All combinations' d='Modules.Prestamassedit.Admin'}</option>
                    </select>
                    <span class="help-block">{l s='Note: Specific price will be applied on all combinations in selected products' d='Modules.Prestamassedit.Admin'}</span>
                </div>
                <div class="mb-2">
                    <label class="control-label" for="presta_sp_from_quantity">
                        {l s='Minimum number of units purchased' d='Modules.Prestamassedit.Admin'}
                        <label class="required"></label>
                    </label>
                    <div class="input-group col-md-3">
                        <input type="text" id="presta_sp_from_quantity"
                            name="presta_mass_edit[specific_price][specific_price][value][from_quantity]" class="form-control"
                            value="1" min="1">
                        <label class="input-group-addon" for="presta_sp_from_quantity">
                            {l s='Unit(s)' d='Modules.Prestamassedit.Admin'}
                        </label>
                    </div>
                </div>
            </div>

            <h4 class="mt-3 mb-0"><label class="mb-0">{l s='Duration' d='Modules.Prestamassedit.Admin'}</label></h4>
            <div class="row mb-2">
                <div class="col col-md-4">
                    <div class="date_picker-widget">
                        <label class="control-label" for="presta_specific_price_date_range_from">
                            {l s='Start date' d='Modules.Prestamassedit.Admin'}
                        </label>
                        <div class="input-group">
                            <input type="text"
                                id="presta_specific_price_date_range_from"
                                name="presta_mass_edit[specific_price][specific_price][value][date_from]"
                                placeholder="YYYY-MM-DD HH:mm:ss"
                                class="form-control presta_datetimepicker" readonly>
                            <label class="input-group-addon" for="presta_specific_price_date_range_from">
                                <i class="icon-calendar"></i>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col col-md-4">
                    <div class="date_picker-widget">
                        <label class="control-label" for="presta_specific_price_date_range_to">
                            {l s='End date' d='Modules.Prestamassedit.Admin'}
                        </label>
                        <div class="input-group">
                            <input type="text" id="presta_specific_price_date_range_to"
                                name="presta_mass_edit[specific_price][specific_price][value][date_to]"
                                placeholder="YYYY-MM-DD HH:mm:ss"
                                class="form-control presta_datetimepicker" disabled="disabled" readonly>
                            <label class="input-group-addon" for="presta_specific_price_date_range_to">
                                <i class="icon-calendar"></i>
                            </label>
                        </div>
                    </div>
                    <div class="form-checkbox">
                        <label class="control-label">
                            <input type="checkbox"
                                id="presta_specific_price_date_range_unlimited"
                                name="presta_mass_edit[specific_price][specific_price][value][date_unlimited]"
                                class="form-check-input" value="1"
                                checked="checked">
                            {l s='Unlimited' d='Modules.Prestamassedit.Admin'}
                        </label>
                    </div>
                </div>
            </div>

            <h4 class="mt-3 mb-0"><label class="mb-0">{l s='Impact on price' d='Modules.Prestamassedit.Admin'}</label></h4>
            <div class="alert alert-info mt-3">{l s='At least one of the following must be activated' d='Modules.Prestamassedit.Admin'}</div>
            <div id="specific_price_impact">
                <div class="mb-2">
                    <div class="d-flex mb-2">
                        <label class="control-label" for="presta_sp_impact_switch_reduction">
                            {l s='Apply a discount to the initial price' d='Modules.Prestamassedit.Admin'}
                        </label>
                        <span class="switch prestashop-switch fixed-width-xs" id="presta_sp_impact_switch_reduction">
                            <input
                                type="radio"
                                name="presta_mass_edit[specific_price][specific_price][value][reduction]"
                                id="presta_sp_impact_switch_reduction_on"
                                class="form-control"
                                value="1"
                                {if isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][reduction])
                                    && $smarty.post.presta_mass_edit[specific_price][specific_price][value][reduction] == '1'}
                                    checked="checked"
                                {/if}>
                            <input
                                type="radio"
                                name="presta_mass_edit[specific_price][specific_price][value][reduction]"
                                id="presta_sp_impact_switch_reduction_off"
                                class="form-control"
                                value="0"
                                {if isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][reduction])
                                    && $smarty.post.presta_mass_edit[specific_price][specific_price][value][reduction] == '0'}
                                    checked="checked"
                                {elseif !isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][reduction])}
                                    checked="checked"
                                {/if}>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>

                    <div id="specific_price_impact_reduction" class="row">
                        <div class="price-reduction-value col col-md-3">
                            <div class="input-group">
                                <label for="presta_specific_price_impact_reduction_value" class="input-group-addon">
                                    <span class="input-group-text presta_specific_price_impact_reduction_value_symbol">{$current_currency_symbol|escape:'htmlall':'UTF-8'}</span>
                                </label>
                                <input type="number" name="presta_mass_edit[specific_price][specific_price][value][reduction_value]"
                                    id="presta_me_sp_reduction_value"
                                    step="any"
                                    min="0"
                                    class="form-control presta_initial_price_disable_toggle" disabled="disabled"
                                    value="0.000000">
                            </div>
                        </div>
                        <div class="col col-md-3">
                            <select id="presta_specific_price_impact_reduction_type"
                                name="presta_mass_edit[specific_price][specific_price][value][reduction_type]"
                                disabled="disabled"
                                class="custom-select form-control presta_initial_price_disable_toggle">
                                <option value="amount" selected="selected">{$current_currency_symbol|escape:'htmlall':'UTF-8'}</option>
                                <option value="percentage">%</option>
                            </select>
                        </div>
                        <div class="presta-js-include-tax-row col col-md-3">
                            <select id="specific_price_impact_reduction_include_tax"
                                name="presta_mass_edit[specific_price][specific_price][value][reduction_tax]"
                                disabled="disabled"
                                class="custom-select form-control disabled presta_initial_price_disable_toggle">
                                <option value="1" selected="selected">{l s='Tax included' d='Modules.Prestamassedit.Admin'}</option>
                                <option value="0">{l s='Tax excluded' d='Modules.Prestamassedit.Admin'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="help-block">
                        {l s='Note: For customers meeting the conditions, the initial price will be
                        crossed out and the discount will be highlighted.' d='Modules.Prestamassedit.Admin'}
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex mb-2">
                        <label class="control-label">
                            {l s='Leave initial price' d='Modules.Prestamassedit.Admin'}
                        </label>
                        <span class="switch prestashop-switch fixed-width-xs"
                            id="presta_mass_edit[specific_price][specific_price][value][leave_initial_price]">
                            <input
                                type="radio"
                                name="presta_mass_edit[specific_price][specific_price][value][leave_initial_price]"
                                id="presta_mass_edit[specific_price][specific_price][value][leave_initial_price]_on"
                                class="form-control"
                                value="1"
                                {if isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][leave_initial_price])
                                    && $smarty.post.presta_mass_edit[specific_price][specific_price][value][leave_initial_price] == '1'}
                                    checked="checked"
                                {elseif !isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][leave_initial_price])}
                                    checked="checked"
                                {/if}>
                            <input
                                type="radio"
                                name="presta_mass_edit[specific_price][specific_price][value][leave_initial_price]"
                                id="presta_mass_edit[specific_price][specific_price][value][leave_initial_price]_off"
                                class="form-control"
                                value="0"
                                {if isset($smarty.post.presta_mass_edit[specific_price][specific_price][value][leave_initial_price])
                                    && $smarty.post.presta_mass_edit[specific_price][specific_price][value][leave_initial_price] == '0'}
                                    checked="checked"
                                {/if}>
                            <a class="slide-button btn"></a>
                        </span>
                    </div>

                    <label for="specific_price_impact_fixed_price_tax_excluded" class="control-label">
                        {l s='Retail price (tax excl.)' d='Modules.Prestamassedit.Admin'}
                    </label>
                    <div class="input-group col-md-4">
                        <label for="specific_price_impact_fixed_price_tax_excluded" class="input-group-addon">
                            {$current_currency_symbol|escape:'htmlall':'UTF-8'}
                        </label>
                        <input type="text" id="specific_price_impact_fixed_price_tax_excluded"
                            name="presta_mass_edit[specific_price][specific_price][value][price]"
                            disabled="disabled"
                            class="form-control switch_target_fixed_price_tax_excluded">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
