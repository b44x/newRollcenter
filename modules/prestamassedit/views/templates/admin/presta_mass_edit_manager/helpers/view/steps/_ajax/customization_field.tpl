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
<div class="list-group-item customization_field">
    <div class="customization_field_label">
        <input type="text" name="presta_mass_edit[options][customizing][label]" required class="form-control" placeholder="{l s='Enter your label here' d='Modules.Prestamassedit.Admin'}">
    </div>
    <div class="customization_field_type">
        <select name="presta_mass_edit[options][customizing][type]" class="form-control">
            <option value="1">{l s='Text' d='Modules.Prestamassedit.Admin'}</option>
            <option value="0">{l s='File' d='Modules.Prestamassedit.Admin'}</option>
        </select>
    </div>
    <div class="customization_field_delete fixed-width-xs">
        <button type="button" class="btn btn-danger customization_field_del_btn">
            <i class="icon icon-trash" style="font-size: 1rem;"></i>
        </button>
    </div>
    <div class="customization_field_required">
        <label for="presta_customization_{$field_index|escape:'htmlall':'UTF-8'}">
            <input type="hidden" name="presta_mass_edit[options][customizing][required]" value="0" id="is_required_{$field_index|escape:'htmlall':'UTF-8'}">
            <input type="checkbox" id="presta_customization_{$field_index|escape:'htmlall':'UTF-8'}"
            data-field-index="{$field_index|escape:'htmlall':'UTF-8'}"
            onchange="handleCustomizationRequired(this)">
            {l s='Required' d='Modules.Prestamassedit.Admin'}
        </label>
    </div>
</div>
