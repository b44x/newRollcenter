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
{* Visibility *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Visibility' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="visibility_off" class="btn btn-xs">
                    <input type="radio" id="visibility_off" name="presta_mass_edit[options][visibility][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="visibility_replace" class="btn btn-xs">
                    <input type="radio" id="visibility_replace" name="presta_mass_edit[options][visibility][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <select name="presta_mass_edit[options][visibility][value]" class="form-control chosen">
                    <option value="" selected></option>
                    {foreach from=$visibilityTypeOptions item=visibilityType key=key}
                        <option value="{$key|escape:'htmlall':'UTF-8'}">{$visibilityType|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>
{* Condition *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='condition' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="condition_off" class="btn btn-xs">
                    <input type="radio" id="condition_off" name="presta_mass_edit[options][condition][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="condition_replace" class="btn btn-xs">
                    <input type="radio" id="condition_replace" name="presta_mass_edit[options][condition][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <select name="presta_mass_edit[options][condition][value]" class="form-control chosen">
                    <option value=""></option>
                    {foreach from=$conditionOptions item=condition key=key}
                        <option value="{$key|escape:'htmlall':'UTF-8'}">{$condition|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>
{* Available For Order *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Available For Order' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="available_for_order_off" class="btn btn-xs">
                    <input type="radio" id="available_for_order_off" name="presta_mass_edit[options][available_for_order][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="available_for_order_enable_all" class="btn btn-xs">
                    <input type="radio" id="available_for_order_enable_all" name="presta_mass_edit[options][available_for_order][action]" value="1">
                    {l s='Enable All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="available_for_order_disable_all" class="btn btn-xs">
                    <input type="radio" id="available_for_order_disable_all" name="presta_mass_edit[options][available_for_order][action]" value="0">
                    {l s='Disable All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
    </div>
</div>
{* Web Only (Not Sold in Your Retail Store) *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Web Only (Not Sold in Your Retail Store)' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="web_only_off" class="btn btn-xs">
                    <input type="radio" id="web_only_off" name="presta_mass_edit[options][online_only][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="web_only_enable_all" class="btn btn-xs">
                    <input type="radio" id="web_only_enable_all" name="presta_mass_edit[options][online_only][action]" value="1">
                    {l s='Enable All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="web_only_disable_all" class="btn btn-xs">
                    <input type="radio" id="web_only_disable_all" name="presta_mass_edit[options][online_only][action]" value="0">
                    {l s='Disable All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
    </div>
</div>
{* Display Condition On Products *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Display Condition On Products' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="display_condition_off" class="btn btn-xs">
                    <input type="radio" id="display_condition_off" name="presta_mass_edit[options][show_condition][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="display_condition_enable_all" class="btn btn-xs">
                    <input type="radio" id="display_condition_enable_all" name="presta_mass_edit[options][show_condition][action]" value="1">
                    {l s='Enable All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="display_condition_disable_all" class="btn btn-xs">
                    <input type="radio" id="display_condition_disable_all" name="presta_mass_edit[options][show_condition][action]" value="0">
                    {l s='Disable All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
    </div>
</div>
{* ISBN *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='ISBN' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="isbn_off" class="btn btn-xs">
                    <input type="radio" id="isbn_off" name="presta_mass_edit[options][isbn][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="isbn_replace" class="btn btn-xs">
                    <input type="radio" id="isbn_replace" name="presta_mass_edit[options][isbn][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="text" name="presta_mass_edit[options][isbn][value]" class="form-control">
            </div>
        </div>
    </div>
</div>
{* EAN-13 Or JAN Barcode *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='EAN-13 Or JAN Barcode' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="ean_or_barcode_off" class="btn btn-xs">
                    <input type="radio" id="ean_or_barcode_off" name="presta_mass_edit[options][ean13][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="ean_or_barcode_replace" class="btn btn-xs">
                    <input type="radio" id="ean_or_barcode_replace" name="presta_mass_edit[options][ean13][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="text" name="presta_mass_edit[options][ean13][value]" class="form-control">
            </div>
        </div>
    </div>
</div>
{* UPC Barcode *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='UPC Barcode' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="upc_barcode_off" class="btn btn-xs">
                    <input type="radio" id="upc_barcode_off" name="presta_mass_edit[options][upc][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="upc_barcode_replace" class="btn btn-xs">
                    <input type="radio" id="upc_barcode_replace" name="presta_mass_edit[options][upc][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="text" name="presta_mass_edit[options][upc][value]" class="form-control">
            </div>
        </div>
    </div>
</div>
{* MPN *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='MPN' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="mpn_barcode_off" class="btn btn-xs">
                    <input type="radio" id="mpn_barcode_off" name="presta_mass_edit[options][mpn][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="mpn_barcode_replace" class="btn btn-xs">
                    <input type="radio" id="mpn_barcode_replace" name="presta_mass_edit[options][mpn][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="text" name="presta_mass_edit[options][mpn][value]" class="form-control">
            </div>
        </div>
    </div>
</div>
{* Tags *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Tags' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="tags_off" class="btn btn-xs">
                    <input type="radio" id="tags_off" name="presta_mass_edit[options][tags][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="tags_add" class="btn btn-xs">
                    <input type="radio" id="tags_add" name="presta_mass_edit[options][tags][action]" value="add">
                    {l s='Add' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="tags_remove" class="btn btn-xs">
                    <input type="radio" id="tags_remove" name="presta_mass_edit[options][tags][action]" value="remove">
                    {l s='Remove' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="tags_remove_all" class="btn btn-xs">
                    <input type="radio" id="tags_remove_all" name="presta_mass_edit[options][tags][action]" value="remove_all">
                    {l s='Remove All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="tags_replace_all" class="btn btn-xs">
                    <input type="radio" id="tags_replace_all" name="presta_mass_edit[options][tags][action]" value="replace_all">
                    {l s='Replace All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="col-lg-10">
                    {foreach $languages as $lang}
                        {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                        {assign var="name" value="presta_mass_edit[options][tags][value][`$lang.id_lang`]"}
                        <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                            {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                            <input type="text" class="form-control presta_tag_input" name='{$name|escape:'htmlall':'UTF-8'}' autocomplete="off" autofocus>
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

                <span class="help-block">{l s='Use a comma " , " to create separate tags. E.g.: new product, cotton, trending, popular this month, etc.' d='Modules.Prestamassedit.Admin'}</span>
            </div>
        </div>
    </div>
</div>
{* Customizing *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Customizing' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions options_actions">
                <label for="customizing_off" class="btn btn-xs">
                    <input type="radio" id="customizing_off" name="presta_mass_edit[options][customizing][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="customizing_add" class="btn btn-xs">
                    <input type="radio" id="customizing_add" name="presta_mass_edit[options][customizing][action]" value="add">
                    {l s='Add' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="customizing_remove_all" class="btn btn-xs">
                    <input type="radio" id="customizing_remove_all" name="presta_mass_edit[options][customizing][action]" value="remove_all">
                    {l s='Remove All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="customizing_replace_all" class="btn btn-xs">
                    <input type="radio" id="customizing_replace_all" name="presta_mass_edit[options][customizing][action]" value="replace_all">
                    {l s='Replace All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            <p class="alert alert-info mt-3">
                {l s='Customers can personalize the product by entering some text or by
                providing custom image files.' d='Modules.Prestamassedit.Admin'}
            </p>

            <div id="presta_customization_fields" class="list-group"></div>
            <div class="form-group col-xs-12">
                <div class="presta_me_loader" id="new_customization_field_adding" style="display: none;">
                    <img src="{$presta_ajax_loader|escape:'htmlall':'UTF-8'}" width="40">
                </div>
                <button type="button" id="customization_field_add_btn" class="btn">
                    <i class="icon-plus-circle"></i>&nbsp;
                    {l s='Add a Customization Field' d='Modules.Prestamassedit.Admin'}
                </button>
            </div>
        </div>
    </div>
</div>
