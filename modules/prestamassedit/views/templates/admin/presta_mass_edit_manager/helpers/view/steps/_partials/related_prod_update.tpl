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
{* <!-- Related Products --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Related Products' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions related_prods_actions">
                <label for="related_prod_off" class="btn btn-xs">
                    <input type="radio" id="related_prod_off" name="presta_mass_edit[related_prods][related_prod][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="related_prod_add" class="btn btn-xs">
                    <input type="radio" id="related_prod_add" name="presta_mass_edit[related_prods][related_prod][action]" value="add">
                    {l s='Add' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="related_prod_remove" class="btn btn-xs">
                    <input type="radio" id="related_prod_remove" name="presta_mass_edit[related_prods][related_prod][action]" value="remove">
                    {l s='Remove' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="related_prod_remove_all" class="btn btn-xs">
                    <input type="radio" id="related_prod_remove_all" name="presta_mass_edit[related_prods][related_prod][action]" value="remove_all">
                    {l s='Remove All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="related_prod_replace_all" class="btn btn-xs">
                    <input type="radio" id="related_prod_replace_all" name="presta_mass_edit[related_prods][related_prod][action]" value="replace_all">
                    {l s='Replace All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            <div class="col-lg-12">
                <div id="selected_related_prod_list" class="list-group"></div>
                <div class="presta-search-container">
                    <div class="input-group">
                        <input type="text" id="presta-product-search-input" class="form-control" autocomplete="off" spellcheck="false">
                        <span class="input-group-addon presta-input-group-addon">
                            <i class="icon-search"></i>
                        </span>
                    </div>
                    <div style="position: relative;">
                        <div class="presta_me_loader list-group" id="related_prod_searching" style="display: none;">
                            <img src="{$presta_ajax_loader|escape:'htmlall':'UTF-8'}" width="40">
                        </div>
                        <div id="presta-product-search-results" class="list-group"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
