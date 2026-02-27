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
{* Name *}
<div class="list-group-item">
    <div class="row form-group presta_form_group" data-tab-name="basic">
        <div class="presta_label">
            <label class="control-label title">{l s='Name' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions basic_actions">
                <label for="name_off" class="btn btn-xs">
                    <input type="radio" id="name_off" name="presta_mass_edit[basic][name][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="name_prepend" class="btn btn-xs">
                    <input type="radio" id="name_prepend" name="presta_mass_edit[basic][name][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="name_append" class="btn btn-xs">
                    <input type="radio" id="name_append" name="presta_mass_edit[basic][name][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="name_replace" class="btn btn-xs">
                    <input type="radio" id="name_replace" name="presta_mass_edit[basic][name][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            <div class="row form-group">
                <div class="col-lg-10">
                    {foreach $languages as $lang}
                        {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                        {assign var="name" value="presta_mass_edit[basic][name][value][`$lang.id_lang`]"}
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
</div>
{* Summary *}
<div class="list-group-item">
    <div class="row form-group presta_form_group" data-tab-name="basic">
        <div class="presta_label">
            <label class="control-label title">{l s='Summary' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions basic_actions">
                <label for="summary_off" class="btn btn-xs">
                    <input type="radio" id="summary_off" name="presta_mass_edit[basic][description_short][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="summary_prepend" class="btn btn-xs">
                    <input type="radio" id="summary_prepend" name="presta_mass_edit[basic][description_short][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="summary_append" class="btn btn-xs">
                    <input type="radio" id="summary_append" name="presta_mass_edit[basic][description_short][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="summary_replace" class="btn btn-xs">
                    <input type="radio" id="summary_replace" name="presta_mass_edit[basic][description_short][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[basic][description_short][value][`$lang.id_lang`]"}
                    <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                        {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                        <textarea name="{$name|escape:'htmlall':'UTF-8'}" autocomplete="off"
                            class="form-control presta_tiny_mce"></textarea>
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
            <div class="help-block col-xs-12">{l s='maximum 800 characters allowed.' d='Modules.Prestamassedit.Admin'}</div>
        </div>
    </div>
</div>
{* Description *}
<div class="list-group-item">
    <div class="row form-group presta_form_group" data-tab-name="basic">
        <div class="presta_label">
            <label class="control-label title">{l s='Description' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions basic_actions">
                <label for="description_off" class="btn btn-xs">
                    <input type="radio" id="description_off" name="presta_mass_edit[basic][description][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="description_prepend" class="btn btn-xs">
                    <input type="radio" id="description_prepend" name="presta_mass_edit[basic][description][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="description_append" class="btn btn-xs">
                    <input type="radio" id="description_append" name="presta_mass_edit[basic][description][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="description_replace" class="btn btn-xs">
                    <input type="radio" id="description_replace" name="presta_mass_edit[basic][description][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[basic][description][value][`$lang.id_lang`]"}
                    <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                        {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                        <textarea name="{$name|escape:'htmlall':'UTF-8'}" autocomplete="off"
                            class="form-control presta_tiny_mce"></textarea>
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
            <div class="help-block col-xs-12 text-end">{l s='maximum 21844 characters allowed.' d='Modules.Prestamassedit.Admin'}</div>
        </div>
    </div>
</div>
{* Reference *}
<div class="list-group-item">
    <div class="row form-group presta_form_group" data-tab-name="basic">
        <div class="presta_label">
            <label class="control-label title">{l s='Reference' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions basic_actions">
                <label for="reference_off" class="btn btn-xs">
                    <input type="radio" id="reference_off" name="presta_mass_edit[basic][reference][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="reference_prepend" class="btn btn-xs">
                    <input type="radio" id="reference_prepend" name="presta_mass_edit[basic][reference][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="reference_append" class="btn btn-xs">
                    <input type="radio" id="reference_append" name="presta_mass_edit[basic][reference][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="reference_replace" class="btn btn-xs">
                    <input type="radio" id="reference_replace" name="presta_mass_edit[basic][reference][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
        <div class="form_input row">
            <div class="col-lg-12">
                <input type="text" name="presta_mass_edit[basic][reference][value]" autocomplete="off" class="form-control">
            </div>
        </div>
    </div>
</div>
{* Status *}
<div class="list-group-item">
    <div class="row form-group presta_form_group" data-tab-name="basic">
        <div class="presta_label">
            <label class="control-label title">{l s='Status' d='Modules.Prestamassedit.Admin'}</label>
            <div class="actions basic_actions">
                <label for="status_off" class="btn btn-xs">
                    <input type="radio" id="status_off" name="presta_mass_edit[basic][status][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="status_enable_all" class="btn btn-xs">
                    <input type="radio" id="status_enable_all" name="presta_mass_edit[basic][status][action]" value="1">
                    {l s='Enable all' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="status_disbale_all" class="btn btn-xs">
                    <input type="radio" id="status_disbale_all" name="presta_mass_edit[basic][status][action]" value="0">
                    {l s='Disable All' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </div>
    </div>
</div>
