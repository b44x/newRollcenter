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
<div class="feature list-group-item">
    <div class="feature_name">
        <label class="control-label">{l s='Feature' d='Modules.Prestamassedit.Admin'}</label>
        <select name="presta_mass_edit[brand_feature][features][value][feature_name]" class="form-control feature_name_val">
            <option value="0">{l s='choose...' d='Modules.Prestamassedit.Admin'}</option>
            {foreach from=$features item=feature}
                <option value="{$feature.id_feature|escape:'htmlall':'UTF-8'}">{$feature.name|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
        </select>
    </div>
    <div class="feature_value">
        <label class="control-label">{l s='Pre-defined Value' d='Modules.Prestamassedit.Admin'}</label>
        <div style="position: relative;">
            <select name="presta_mass_edit[brand_feature][features][value][feature_value]" disabled class="form-control feature_value_val">
                <option value="0">{l s='select a value' d='Modules.Prestamassedit.Admin'}</option>
            </select>
            <div class="presta_me_loader selectBoxLoader" style="display: none;">
                <img src="{$presta_ajax_loader|escape:'htmlall':'UTF-8'}" width="40" style="position: absolute; top: 0">
            </div>
        </div>
    </div>
    <div class="feature_value_custom">
        <label class="control-label">{l s='OR Customized value' d='Modules.Prestamassedit.Admin'}</label>
        <input type="hidden" name="presta_mass_edit[brand_feature][features][value][feature_custom_value][{$index|escape:'htmlall':'UTF-8'}]">
        <input type="text" disabled class="form-control feature_custom_val">
    </div>
    <div class="feature_row_delete">
        <button type="button" class="btn btn-danger feature_row_del_btn">
            <i class="icon icon-trash" style="font-size: 1rem;"></i>
        </button>
    </div>
</div>
