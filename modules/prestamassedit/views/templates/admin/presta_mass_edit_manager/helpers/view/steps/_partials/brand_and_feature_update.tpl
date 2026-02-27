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
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Brands' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions brand_and_feature_actions">
                {if isset($brands) && $brands}
                    <label for="brands_off" class="btn btn-xs">
                        <input type="radio" id="brands_off" name="presta_mass_edit[brand_feature][brands][action]" value="off" checked>
                        {l s='Off' d='Modules.Prestamassedit.Admin'}
                    </label>
                    <label for="brands_replace" class="btn btn-xs">
                        <input type="radio" id="brands_replace" name="presta_mass_edit[brand_feature][brands][action]" value="replace">
                        {l s='Replace' d='Modules.Prestamassedit.Admin'}
                    </label>
                {/if}
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                {if isset($brands) && $brands}
                    <select name="presta_mass_edit[brand_feature][brands][value]" class="chosen">
                        <option value="0">{l s='No Brand' d='Modules.Prestamassedit.Admin'}</option>
                        {foreach from=$brands item=brand}
                            <option value="{$brand['id_manufacturer']|escape:'htmlall':'UTF-8'}">{$brand['name']|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                {else}
                    <div class="alert alert-info">{l s='No Brands Available!' d='Modules.Prestamassedit.Admin'}</div>
                {/if}
            </div>
        </div>
    </div>
</div>
{* <!-- Features --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Features' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions brand_and_feature_actions">
                <label for="features_off" class="btn btn-xs">
                    <input type="radio" id="features_off" name="presta_mass_edit[brand_feature][features][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="features_add" class="btn btn-xs">
                    <input type="radio" id="features_add" name="presta_mass_edit[brand_feature][features][action]" value="add">
                    {l s='Add' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="features_remove" class="btn btn-xs">
                    <input type="radio" id="features_remove" name="presta_mass_edit[brand_feature][features][action]" value="remove">
                    {l s='Remove' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="features_remove_all" class="btn btn-xs">
                    <input type="radio" id="features_remove_all" name="presta_mass_edit[brand_feature][features][action]" value="remove_all">
                    {l s='Remove All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="features_replace_all" class="btn btn-xs">
                    <input type="radio" id="features_replace_all" name="presta_mass_edit[brand_feature][features][action]" value="replace_all">
                    {l s='Replace All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            <div id="features_container" class="list-group"></div>
            <div class="form-group col-xs-12">
                <div class="presta_me_loader" id="new_feature_adding" style="display: none;">
                    <img src="{$presta_ajax_loader|escape:'htmlall':'UTF-8'}" width="40">
                </div>
                <button type="button" id="feature_row_add_btn" class="btn">
                    <i class="icon-plus-circle"></i>&nbsp;
                    {l s='Add a feature' d='Modules.Prestamassedit.Admin'}
                </button>
            </div>
        </div>
    </div>
</div>
