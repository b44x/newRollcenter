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
{* <!-- Width --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Width' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="width_off" class="btn btn-xs">
                    <input type="radio" id="width_off" name="presta_mass_edit[shipping][width][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="width_add_perc" class="btn btn-xs">
                    <input type="radio" id="width_add_perc" name="presta_mass_edit[shipping][width][action]" value="add_perc">
                    {l s='+ %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="width_sub_perc" class="btn btn-xs">
                    <input type="radio" id="width_sub_perc" name="presta_mass_edit[shipping][width][action]" value="sub_perc">
                    {l s='- %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="width_add_amount" class="btn btn-xs">
                    <input type="radio" id="width_add_amount" name="presta_mass_edit[shipping][width][action]" value="add_amount">
                    {l s='+ cm' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="width_sub_amount" class="btn btn-xs">
                    <input type="radio" id="width_sub_amount" name="presta_mass_edit[shipping][width][action]" value="sub_amount">
                    {l s='- cm' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="width_replace" class="btn btn-xs">
                    <input type="radio" id="width_replace" name="presta_mass_edit[shipping][width][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="number" step="any" min="0" name="presta_mass_edit[shipping][width][value]" class="form-control">
                    <span class="input-group-addon">cm</span>
                </div>
            </div>
        </div>
    </div>
</div>
{* <!-- Height --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Height' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="height_off" class="btn btn-xs">
                    <input type="radio" id="height_off" name="presta_mass_edit[shipping][height][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="height_add_perc" class="btn btn-xs">
                    <input type="radio" id="height_add_perc" name="presta_mass_edit[shipping][height][action]" value="add_perc">
                    {l s='+ %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="height_sub_perc" class="btn btn-xs">
                    <input type="radio" id="height_sub_perc" name="presta_mass_edit[shipping][height][action]" value="sub_perc">
                    {l s='- %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="height_add_amount" class="btn btn-xs">
                    <input type="radio" id="height_add_amount" name="presta_mass_edit[shipping][height][action]" value="add_amount">
                    {l s='+ cm' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="height_sub_amount" class="btn btn-xs">
                    <input type="radio" id="height_sub_amount" name="presta_mass_edit[shipping][height][action]" value="sub_amount">
                    {l s='- cm' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="height_replace" class="btn btn-xs">
                    <input type="radio" id="height_replace" name="presta_mass_edit[shipping][height][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="number" step="any" min="0" name="presta_mass_edit[shipping][height][value]" class="form-control">
                    <span class="input-group-addon">cm</span>
                </div>
            </div>
        </div>
    </div>
</div>
{* <!-- Depth --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Depth' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="depth_off" class="btn btn-xs">
                    <input type="radio" id="depth_off" name="presta_mass_edit[shipping][depth][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="depth_add_perc" class="btn btn-xs">
                    <input type="radio" id="depth_add_perc" name="presta_mass_edit[shipping][depth][action]" value="add_perc">
                    {l s='+ %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="depth_sub_perc" class="btn btn-xs">
                    <input type="radio" id="depth_sub_perc" name="presta_mass_edit[shipping][depth][action]" value="sub_perc">
                    {l s='- %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="depth_add_amount" class="btn btn-xs">
                    <input type="radio" id="depth_add_amount" name="presta_mass_edit[shipping][depth][action]" value="add_amount">
                    {l s='+ cm' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="depth_sub_amount" class="btn btn-xs">
                    <input type="radio" id="depth_sub_amount" name="presta_mass_edit[shipping][depth][action]" value="sub_amount">
                    {l s='- cm' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="depth_replace" class="btn btn-xs">
                    <input type="radio" id="depth_replace" name="presta_mass_edit[shipping][depth][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="number" step="any" min="0" name="presta_mass_edit[shipping][depth][value]" class="form-control">
                    <span class="input-group-addon">cm</span>
                </div>
            </div>
        </div>
    </div>
</div>
{* <!-- Weight --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Weight' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="weight_off" class="btn btn-xs">
                    <input type="radio" id="weight_off" name="presta_mass_edit[shipping][weight][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="weight_add_perc" class="btn btn-xs">
                    <input type="radio" id="weight_add_perc" name="presta_mass_edit[shipping][weight][action]" value="add_perc">
                    {l s='+ %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="weight_sub_perc" class="btn btn-xs">
                    <input type="radio" id="weight_sub_perc" name="presta_mass_edit[shipping][weight][action]" value="sub_perc">
                    {l s='- %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="weight_add_amount" class="btn btn-xs">
                    <input type="radio" id="weight_add_amount" name="presta_mass_edit[shipping][weight][action]" value="add_amount">
                    {l s='+ kg' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="weight_sub_amount" class="btn btn-xs">
                    <input type="radio" id="weight_sub_amount" name="presta_mass_edit[shipping][weight][action]" value="sub_amount">
                    {l s='- kg' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="weight_replace" class="btn btn-xs">
                    <input type="radio" id="weight_replace" name="presta_mass_edit[shipping][weight][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="number" step="any" min="0" name="presta_mass_edit[shipping][weight][value]" class="form-control">
                    <span class="input-group-addon">kg</span>
                </div>
            </div>
        </div>
    </div>
</div>
{* <!-- Delivery Time --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Delivery Time' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="delivery_time_off" class="btn btn-xs">
                    <input type="radio" id="delivery_time_off" name="presta_mass_edit[shipping][additional_delivery_times][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="delivery_time_replace" class="btn btn-xs">
                    <input type="radio" id="delivery_time_replace" name="presta_mass_edit[shipping][additional_delivery_times][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            {foreach from=$deliveryTimeOptions item=deliveryTime key=key}
                <div class="form-check form-check-radio form-radio">
                    <label class="form-check-label control-label">
                        <input
                            type="radio"
                            value="{$key|escape:'htmlall':'UTF-8'}"
                            class="form-check-input"
                            name="presta_mass_edit[shipping][additional_delivery_times][value]">
                        <i class="form-check-round"></i>
                        {$deliveryTime|escape:'htmlall':'UTF-8'}
                    </label>
                </div>
            {/foreach}
        </div>
    </div>
</div>
{* <!-- Delivery Time Out Of Stock Products --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Delivery Time Out Of Stock Products' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="delivery_time_out_of_stock_off" class="btn btn-xs">
                    <input type="radio" id="delivery_time_out_of_stock_off" name="presta_mass_edit[shipping][delivery_out_stock][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="delivery_time_out_of_stock_prepend" class="btn btn-xs">
                    <input type="radio" id="delivery_time_out_of_stock_prepend" name="presta_mass_edit[shipping][delivery_out_stock][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="delivery_time_out_of_stock_append" class="btn btn-xs">
                    <input type="radio" id="delivery_time_out_of_stock_append" name="presta_mass_edit[shipping][delivery_out_stock][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="delivery_time_out_of_stock_replace" class="btn btn-xs">
                    <input type="radio" id="delivery_time_out_of_stock_replace" name="presta_mass_edit[shipping][delivery_out_stock][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[shipping][delivery_out_stock][value][`$lang.id_lang`]"}
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
{* <!-- Delivery Time Of In-stock Products --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Delivery Time Of In-stock Products' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="delivery_time_in_stock_off" class="btn btn-xs">
                    <input type="radio" id="delivery_time_in_stock_off" name="presta_mass_edit[shipping][delivery_in_stock][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="delivery_time_in_stock_prepend" class="btn btn-xs">
                    <input type="radio" id="delivery_time_in_stock_prepend" name="presta_mass_edit[shipping][delivery_in_stock][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="delivery_time_in_stock_append" class="btn btn-xs">
                    <input type="radio" id="delivery_time_in_stock_append" name="presta_mass_edit[shipping][delivery_in_stock][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="delivery_time_in_stock_replace" class="btn btn-xs">
                    <input type="radio" id="delivery_time_in_stock_replace" name="presta_mass_edit[shipping][delivery_in_stock][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[shipping][delivery_in_stock][value][`$lang.id_lang`]"}
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
{* <!-- Shipping Fees --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Shipping Fees' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="shipping_fee_off" class="btn btn-xs">
                    <input type="radio" id="shipping_fee_off" name="presta_mass_edit[shipping][shipping_fee][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="shipping_fee_add_perc" class="btn btn-xs">
                    <input type="radio" id="shipping_fee_add_perc" name="presta_mass_edit[shipping][shipping_fee][action]" value="add_perc">
                    {l s='+ %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="shipping_fee_sub_perc" class="btn btn-xs">
                    <input type="radio" id="shipping_fee_sub_perc" name="presta_mass_edit[shipping][shipping_fee][action]" value="sub_perc">
                    {l s='- %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="shipping_fee_add_amount" class="btn btn-xs">
                    <input type="radio" id="shipping_fee_add_amount" name="presta_mass_edit[shipping][shipping_fee][action]" value="add_amount">
                    {l s='+ $' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="shipping_fee_sub_amount" class="btn btn-xs">
                    <input type="radio" id="shipping_fee_sub_amount" name="presta_mass_edit[shipping][shipping_fee][action]" value="sub_amount">
                    {l s='- $' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="shipping_fee_replace" class="btn btn-xs">
                    <input type="radio" id="shipping_fee_replace" name="presta_mass_edit[shipping][shipping_fee][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="text" name="presta_mass_edit[shipping][shipping_fee][value]" class="form-control">
                    <span class="input-group-addon">$</span>
                </div>
            </div>
        </div>
    </div>
</div>
{* <!-- Availiable Carrier --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Avaliable Carrier' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions shipping_actions">
                <label for="aval_carrier_off" class="btn btn-xs">
                    <input type="radio" id="aval_carrier_off" name="presta_mass_edit[shipping][available_carrier][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="aval_carrier_add" class="btn btn-xs">
                    <input type="radio" id="aval_carrier_add" name="presta_mass_edit[shipping][available_carrier][action]" value="add">
                    {l s='Add' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="aval_carrier_remove" class="btn btn-xs">
                    <input type="radio" id="aval_carrier_remove" name="presta_mass_edit[shipping][available_carrier][action]" value="remove">
                    {l s='Remove' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="aval_carrier_remove_all" class="btn btn-xs">
                    <input type="radio" id="aval_carrier_remove_all" name="presta_mass_edit[shipping][available_carrier][action]" value="remove_all">
                    {l s='Remove All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="aval_carrier_replace_all" class="btn btn-xs">
                    <input type="radio" id="aval_carrier_replace_all" name="presta_mass_edit[shipping][available_carrier][action]" value="replace_all">
                    {l s='Replace All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            <div class="col-lg-12">
                {foreach from=$availableCarriers item=carrier}
                    <div>
                        <label class="control-label" style="cursor: pointer;">
                            <input type="checkbox" name="presta_mass_edit[shipping][available_carrier][value]" value="{$carrier.id_reference|escape:'htmlall':'UTF-8'}">
                            {$carrier.name|escape:'htmlall':'UTF-8'} ({$carrier.delay|escape:'htmlall':'UTF-8'})
                        </label>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
