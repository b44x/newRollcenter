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
    <p>{l s='Note: Changes will only be applied to products that already belong to combinations.' d='Modules.Prestamassedit.Admin'}</p>
</div>
{* <!-- Combinations --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <div class="presta_label">
            <label class="control-label title">{l s='Combinations' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions combinations_actions">
                <label for="combination_off" class="btn btn-xs">
                    <input type="radio" id="combination_off" name="presta_mass_edit[combinations][combination][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="combination_add" class="btn btn-xs">
                    <input type="radio" id="combination_add" name="presta_mass_edit[combinations][combination][action]" value="add">
                    {l s='Add' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="combination_remove" class="btn btn-xs">
                    <input type="radio" id="combination_remove" name="presta_mass_edit[combinations][combination][action]" value="remove">
                    {l s='Remove' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="combination_remove_all" class="btn btn-xs">
                    <input type="radio" id="combination_remove_all" name="presta_mass_edit[combinations][combination][action]" value="remove_all">
                    {l s='Remove All' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="combination_replace_all" class="btn btn-xs">
                    <input type="radio" id="combination_replace_all" name="presta_mass_edit[combinations][combination][action]" value="replace_all">
                    {l s='Replace All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-xs-12">
                <p class="alert alert-info">
                    {l s='Combinations are the different variations of a product, with attributes like its size,
                        weight or color taking different values. To create a combination, you need to create your
                        product attributes first. Go to' d='Modules.Prestamassedit.Admin'} <strong>{l s='Catalog > Attributes & Features' d='Modules.Prestamassedit.Admin'}</strong> {l s='for this!' d='Modules.Prestamassedit.Admin'}
                </p>
            </div>
            <div class="col-xs-12 col-lg-7">
                <p><strong>{l s='Selected Attributes:' d='Modules.Prestamassedit.Admin'}</strong></p>
                <div id="presta-selected-attributes"></div>
            </div>
            <div class="col-xs-12 col-lg-5">
                <div class="presta-attribute-group clearfix">
                    {foreach from=$attributes item=attribute key=key}
                        <div class="presta-attribute-group-header">
                            <a class="presta-attribute-group-name collapsed" data-toggle="collapse" href="#presta-attribute-group-{$key|escape:'htmlall':'UTF-8'}" aria-expanded="false">
                                <label>{str_replace('-', ' ', $key)|escape:'htmlall':'UTF-8'}</label>
                            </a>
                        </div>
                        <div class="presta-attribute-group-content presta-attributes collapse" id="presta-attribute-group-{$key|escape:'htmlall':'UTF-8'}">
                            {foreach from=$attribute item=attr}
                                <div class="presta-attribute-item">
                                    <input
                                        type="checkbox"
                                        class="form-check-input presta_combination_checkbox"
                                        name="presta_mass_edit[combinations][combination][value]"
                                        data-tag-val="{str_replace('-', ' ', $key)|escape:'htmlall':'UTF-8'} : {$attr.name|escape:'htmlall':'UTF-8'}"
                                        value="{$attr.id_attribute|escape:'htmlall':'UTF-8'}"
                                        id="presta-attribute_{$attr.id_attribute|escape:'htmlall':'UTF-8'}">
                                    <label class="presta-attribute-item-content control-label" for="presta-attribute_{$attr.id_attribute|escape:'htmlall':'UTF-8'}">
                                        <div class="presta-attribute-item-name">
                                            {if $attr.color}
                                                <div class="presta-color-preview" style="background: {$attr.color|escape:'htmlall':'UTF-8'};"></div>
                                            {/if}
                                            {$attr.name|escape:'htmlall':'UTF-8'}
                                        </div>
                                    </label>
                                </div>
                            {/foreach}
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
</div>
