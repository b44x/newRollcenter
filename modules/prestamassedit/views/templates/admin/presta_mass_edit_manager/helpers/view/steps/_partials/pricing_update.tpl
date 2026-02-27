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
{* <!-- Price (Tex Excl.) --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Price (Tex Excl.)' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions pricing_actions">
                <label for="price_off" class="btn btn-xs">
                    <input type="radio" id="price_off" name="presta_mass_edit[pricing][price][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_add_perc" class="btn btn-xs">
                    <input type="radio" id="price_add_perc" name="presta_mass_edit[pricing][price][action]" value="add_perc">
                    {l s='+ %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_sub_perc" class="btn btn-xs">
                    <input type="radio" id="price_sub_perc" name="presta_mass_edit[pricing][price][action]" value="sub_perc">
                    {l s='- %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_add_amount" class="btn btn-xs">
                    <input type="radio" id="price_add_amount" name="presta_mass_edit[pricing][price][action]" value="add_amount">
                    {l s='+ amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_sub_amount" class="btn btn-xs">
                    <input type="radio" id="price_sub_amount" name="presta_mass_edit[pricing][price][action]" value="sub_amount">
                    {l s='- amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_replace" class="btn btn-xs">
                    <input type="radio" id="price_replace" name="presta_mass_edit[pricing][price][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="number" name="presta_mass_edit[pricing][price][value]" step="any" class="form-control" autocomplete="off">
                    <span class="input-group-addon"></span>
                </div>
            </div>
        </div>
    </div>
</div>
{* <!-- Price Per Unit (Tex Excl.) --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Price Per Unit (Tex Excl.)' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions pricing_actions">
                <label for="price_per_unit_off" class="btn btn-xs">
                    <input type="radio" id="price_per_unit_off" name="presta_mass_edit[pricing][unit_price][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_per_unit_add_perc" class="btn btn-xs">
                    <input type="radio" id="price_per_unit_add_perc" name="presta_mass_edit[pricing][unit_price][action]" value="add_perc">
                    {l s='+ %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_per_unit_sub_perc" class="btn btn-xs">
                    <input type="radio" id="price_per_unit_sub_perc" name="presta_mass_edit[pricing][unit_price][action]" value="sub_perc">
                    {l s='- %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_per_unit_add_amount" class="btn btn-xs">
                    <input type="radio" id="price_per_unit_add_amount" name="presta_mass_edit[pricing][unit_price][action]" value="add_amount">
                    {l s='+ amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_per_unit_sub_amount" class="btn btn-xs">
                    <input type="radio" id="price_per_unit_sub_amount" name="presta_mass_edit[pricing][unit_price][action]" value="sub_amount">
                    {l s='- amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="price_per_unit_replace" class="btn btn-xs">
                    <input type="radio" id="price_per_unit_replace" name="presta_mass_edit[pricing][unit_price][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="number" name="presta_mass_edit[pricing][unit_price][value]" step="any" class="form-control"
                        autocomplete="off">
                    <span class="input-group-addon">$</span>
                </div>
            </div>
        </div>
    </div>
</div>
{* <!-- Cost Price --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Cost Price' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions pricing_actions">
                <label for="cost_off" class="btn btn-xs">
                    <input type="radio" id="cost_off" name="presta_mass_edit[pricing][wholesale_price][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="cost_add_perc" class="btn btn-xs">
                    <input type="radio" id="cost_add_perc" name="presta_mass_edit[pricing][wholesale_price][action]" value="add_perc">
                    {l s='+ %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="cost_sub_perc" class="btn btn-xs">
                    <input type="radio" id="cost_sub_perc" name="presta_mass_edit[pricing][wholesale_price][action]" value="sub_perc">
                    {l s='- %' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="cost_add_amount" class="btn btn-xs">
                    <input type="radio" id="cost_add_amount" name="presta_mass_edit[pricing][wholesale_price][action]" value="add_amount">
                    {l s='+ amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="cost_sub_amount" class="btn btn-xs">
                    <input type="radio" id="cost_sub_amount" name="presta_mass_edit[pricing][wholesale_price][action]" value="sub_amount">
                    {l s='- amount' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="cost_replace" class="btn btn-xs">
                    <input type="radio" id="cost_replace" name="presta_mass_edit[pricing][wholesale_price][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <div class="input-group">
                    <input type="number" name="presta_mass_edit[pricing][wholesale_price][value]" step="any" class="form-control" autocomplete="off">
                    <span class="input-group-addon">$</span>
                </div>
            </div>
        </div>
    </div>
</div>
{* <!-- Tax Rule --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Tax Rule' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions pricing_actions">
                <label for="tax_rule_off" class="btn btn-xs">
                    <input type="radio" id="tax_rule_off" name="presta_mass_edit[pricing][id_tax_rules_group][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="tax_rule_replace" class="btn btn-xs">
                    <input type="radio" id="tax_rule_replace" name="presta_mass_edit[pricing][id_tax_rules_group][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input clearfix">
            <select name="presta_mass_edit[pricing][id_tax_rules_group][value]" class="form-control chosen">
                {foreach from=$tax_rules item=tax_rule}
                    <option value="{$tax_rule.id_tax_rules_group|escape:'htmlall':'UTF-8'}">{$tax_rule.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
{* <!-- Display (On Sale) flag --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Display the (On Sale) flag' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions pricing_actions">
                <label for="display_sale_flag_off" class="btn btn-xs">
                    <input type="radio" id="display_sale_flag_off" name="presta_mass_edit[pricing][on_sale][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="display_sale_flag_enable_all" class="btn btn-xs">
                    <input type="radio" id="display_sale_flag_enable_all" name="presta_mass_edit[pricing][on_sale][action]"
                        value="1">
                    {l s='Enable All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="display_sale_flag_disable_all" class="btn btn-xs">
                    <input type="radio" id="display_sale_flag_disable_all" name="presta_mass_edit[pricing][on_sale][action]"
                        value="0">
                    {l s='Disable All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
    </div>
</div>
