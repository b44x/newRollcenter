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
<div class="alert alert-info">
    <p>{l s='Note: If the selected product list includes products with combinations, the changes will be applied to all combinations of those products.' d='Modules.Prestamassedit.Admin'}</p>
</div>
{* <!-- Quantity --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Quantity' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions quantity_actions">
                <label for="quantity_off" class="btn btn-xs">
                    <input type="radio" id="quantity_off" name="presta_mass_edit[quantity][qty][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="quantity_add_amount" class="btn btn-xs">
                    <input type="radio" id="quantity_add_amount" name="presta_mass_edit[quantity][qty][action]" value="add_amount">
                    + {l s='amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="quantity_sub_amount" class="btn btn-xs">
                    <input type="radio" id="quantity_sub_amount" name="presta_mass_edit[quantity][qty][action]" value="sub_amount">
                    - {l s='amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="quantity_replace" class="btn btn-xs">
                    <input type="radio" id="quantity_replace" name="presta_mass_edit[quantity][qty][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="number" name="presta_mass_edit[quantity][qty][value]" min="0" class="form-control">
            </div>
        </div>
    </div>
</div>
{* <!-- Minimum Quantity For Sale --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Minimum Quantity For Sale' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions quantity_actions">
                <label for="min_quantity_off" class="btn btn-xs">
                    <input type="radio" id="min_quantity_off" name="presta_mass_edit[quantity][min_qty][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="min_quantity_add_amount" class="btn btn-xs">
                    <input type="radio" id="min_quantity_add_amount" name="presta_mass_edit[quantity][min_qty][action]" value="add_amount">
                    + {l s='amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="min_quantity_sub_amount" class="btn btn-xs">
                    <input type="radio" id="min_quantity_sub_amount" name="presta_mass_edit[quantity][min_qty][action]" value="sub_amount">
                    - {l s='amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="min_quantity_replace" class="btn btn-xs">
                    <input type="radio" id="min_quantity_replace" name="presta_mass_edit[quantity][min_qty][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="number" name="presta_mass_edit[quantity][min_qty][value]" min="1" class="form-control">
            </div>
        </div>
    </div>
</div>
{* <!-- Low Stock Level --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Low Stock Level' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions quantity_actions">
                <label for="low_stock_off" class="btn btn-xs">
                    <input type="radio" id="low_stock_off" name="presta_mass_edit[quantity][low_stock][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="low_stock_add_amount" class="btn btn-xs">
                    <input type="radio" id="low_stock_add_amount" name="presta_mass_edit[quantity][low_stock][action]" value="add_amount">
                    + {l s='amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="low_stock_sub_amount" class="btn btn-xs">
                    <input type="radio" id="low_stock_sub_amount" name="presta_mass_edit[quantity][low_stock][action]" value="sub_amount">
                    - {l s='amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="low_stock_replace" class="btn btn-xs">
                    <input type="radio" id="low_stock_replace" name="presta_mass_edit[quantity][low_stock][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="number" min="0" name="presta_mass_edit[quantity][low_stock][value]" class="form-control">
            </div>
        </div>
    </div>
</div>
{* <!-- Mail Me On Low Quantity --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Mail Me On Low Quantity' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions quantity_actions">
                <label for="mail_on_low_quantity_off" class="btn btn-xs">
                    <input type="radio" id="mail_on_low_quantity_off" name="presta_mass_edit[quantity][mail_on_low_stock][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="mail_on_low_quantity_enable_all" class="btn btn-xs">
                    <input type="radio" id="mail_on_low_quantity_enable_all" name="presta_mass_edit[quantity][mail_on_low_stock][action]" value="1">
                    {l s='Enable All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="mail_on_low_quantity_disable_all" class="btn btn-xs">
                    <input type="radio" id="mail_on_low_quantity_disable_all" name="presta_mass_edit[quantity][mail_on_low_stock][action]" value="0">
                    {l s='Disable All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
    </div>
</div>
{* <!-- When out of stock (Availability Preferences) --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='When out of stock (Availability Preferences)' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions quantity_actions">
                <label for="aval_preference_off" class="btn btn-xs">
                    <input type="radio" id="aval_preference_off" name="presta_mass_edit[quantity][availability_preference][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="aval_preference_replace" class="btn btn-xs">
                    <input type="radio" id="aval_preference_replace" name="presta_mass_edit[quantity][availability_preference][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            {foreach from=$availabilityPreferenceOptions item=availabilityPreference key=key}
                <div class="form-check form-check-radio form-radio">
                    <label class="form-check-label control-label">
                        <input
                            type="radio"
                            value="{$key|escape:'htmlall':'UTF-8'}"
                            class="form-check-input"
                            name="presta_mass_edit[quantity][availability_preference][value]">
                        <i class="form-check-round"></i>
                        {$availabilityPreference|escape:'htmlall':'UTF-8'}
                    </label>
                </div>
            {/foreach}
        </div>
    </div>
</div>
{* <!-- Label When In Stock --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Label When In Stock' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions quantity_actions">
                <label for="label_in_stock_off" class="btn btn-xs">
                    <input type="radio" id="label_in_stock_off" name="presta_mass_edit[quantity][label_in_stock][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="label_in_stock_prepend" class="btn btn-xs">
                    <input type="radio" id="label_in_stock_prepend" name="presta_mass_edit[quantity][label_in_stock][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="label_in_stock_append" class="btn btn-xs">
                    <input type="radio" id="label_in_stock_append" name="presta_mass_edit[quantity][label_in_stock][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="label_in_stock_replace" class="btn btn-xs">
                    <input type="radio" id="label_in_stock_replace" name="presta_mass_edit[quantity][label_in_stock][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[quantity][label_in_stock][value][`$lang.id_lang`]"}
                    <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                        {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                        <input type="text" name="{$name|escape:'htmlall':'UTF-8'}" autocomplete="off"
                            class="form-control" />
                    </div>
                {/foreach}
            </div>
            <div class="col-lg-2">
                {if count($languages) > 1}
                    <button type="button" class="btn btn-default dropdown-toggle presta_caret"
                        data-toggle="dropdown">
                        {$current_lang->iso_code|escape:'html':'UTF-8'}
                        <span class="caret" style="margin-left:5px;"></span>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach from=$languages item=language}
                            <li>
                                <a href="javascript:void(0)"
                                    onclick="showProLangField('{$language.iso_code|escape:'html':'UTF-8'}', '{$language.id_lang|escape:'html':'UTF-8'}');">
                                    {$language.name|escape:'html':'UTF-8'}
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                {/if}
            </div>
        </div>
    </div>
</div>
{* <!-- Label When Out Of Stock (And Back Order Allowed) --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Label When Out Of Stock (And Back Order Allowed)' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions quantity_actions">
                <label for="label_out_of_stock_off" class="btn btn-xs">
                    <input type="radio" id="label_out_of_stock_off" name="presta_mass_edit[quantity][label_out_stock][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="label_out_of_stock_prepend" class="btn btn-xs">
                    <input type="radio" id="label_out_of_stock_prepend" name="presta_mass_edit[quantity][label_out_stock][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="label_out_of_stock_append" class="btn btn-xs">
                    <input type="radio" id="label_out_of_stock_append" name="presta_mass_edit[quantity][label_out_stock][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="label_out_of_stock_replace" class="btn btn-xs">
                    <input type="radio" id="label_out_of_stock_replace" name="presta_mass_edit[quantity][label_out_stock][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[quantity][label_out_stock][value][`$lang.id_lang`]"}
                    <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                        {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                        <input type="text" name="{$name|escape:'htmlall':'UTF-8'}" autocomplete="off"
                            class="form-control" />
                    </div>
                {/foreach}
            </div>
            <div class="col-lg-2">
                {if count($languages) > 1}
                    <button type="button" class="btn btn-default dropdown-toggle presta_caret"
                        data-toggle="dropdown">
                        {$current_lang->iso_code|escape:'html':'UTF-8'}
                        <span class="caret" style="margin-left:5px;"></span>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach from=$languages item=language}
                            <li>
                                <a href="javascript:void(0)"
                                    onclick="showProLangField('{$language.iso_code|escape:'html':'UTF-8'}', '{$language.id_lang|escape:'html':'UTF-8'}');">
                                    {$language.name|escape:'html':'UTF-8'}
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                {/if}
            </div>
        </div>
    </div>
</div>
{* <!-- Stock Location --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Stock Location' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions quantity_actions">
                <label for="stock_location_off" class="btn btn-xs">
                    <input type="radio" id="stock_location_off" name="presta_mass_edit[quantity][stock_location][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="stock_location_prepend" class="btn btn-xs">
                    <input type="radio" id="stock_location_prepend" name="presta_mass_edit[quantity][stock_location][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="stock_location_append" class="btn btn-xs">
                    <input type="radio" id="stock_location_append" name="presta_mass_edit[quantity][stock_location][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="stock_location_replace" class="btn btn-xs">
                    <input type="radio" id="stock_location_replace" name="presta_mass_edit[quantity][stock_location][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="text" name="presta_mass_edit[quantity][stock_location][value]" class="form-control">
            </div>
        </div>
    </div>
</div>
