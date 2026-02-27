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
{* <!-- meta title --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <label class="control-label presta_label">
            <span class="title">{l s='Meta Title' d='Modules.Prestamassedit.Admin'}</span>
            <div class="actions seo_actions">
                <label for="meta_title_off" class="btn btn-xs">
                    <input type="radio" id="meta_title_off" name="presta_mass_edit[seo][meta_title][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="meta_title_prepend" class="btn btn-xs">
                    <input type="radio" id="meta_title_prepend" name="presta_mass_edit[seo][meta_title][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="meta_title_append" class="btn btn-xs">
                    <input type="radio" id="meta_title_append" name="presta_mass_edit[seo][meta_title][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="meta_title_replace" class="btn btn-xs">
                    <input type="radio" id="meta_title_replace" name="presta_mass_edit[seo][meta_title][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </label>
        <div class="form_input row">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[seo][meta_title][value][`$lang.id_lang`]"}
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
{* <!-- meta description --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <label class="control-label presta_label">
            <span class="title">{l s='Meta Description' d='Modules.Prestamassedit.Admin'}</span>
            <div class="actions seo_actions">
                <label for="meta_description_off" class="btn btn-xs">
                    <input type="radio" id="meta_description_off" name="presta_mass_edit[seo][meta_description][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="meta_description_prepend" class="btn btn-xs">
                    <input type="radio" id="meta_description_prepend" name="presta_mass_edit[seo][meta_description][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="meta_description_append" class="btn btn-xs">
                    <input type="radio" id="meta_description_append" name="presta_mass_edit[seo][meta_description][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="meta_description_replace" class="btn btn-xs">
                    <input type="radio" id="meta_description_replace" name="presta_mass_edit[seo][meta_description][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </label>
        <div class="form_input row">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[seo][meta_description][value][`$lang.id_lang`]"}
                    <div class="presta_div {$presta_current_div|escape:'html':'UTF-8'}"
                        {if $current_lang->id != $lang.id_lang}style="display:none;" {/if}>
                        <textarea name="{$name|escape:'htmlall':'UTF-8'}" autocomplete="off"
                            class="form-control"></textarea>
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
{* <!-- friendly url --> *}
<div class="list-group-item">
    <div class="row form-group presta_form_group">
        <label class="control-label presta_label">
            <span class="title">{l s='Friendly Url' d='Modules.Prestamassedit.Admin'}</span>
            <div class="actions seo_actions">
                <label for="friendly_url_off" class="btn btn-xs">
                    <input type="radio" id="friendly_url_off" name="presta_mass_edit[seo][meta_url][action]" value="off" checked>
                    {l s='Off' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="friendly_url_prepend" class="btn btn-xs">
                    <input type="radio" id="friendly_url_prepend" name="presta_mass_edit[seo][meta_url][action]" value="prepend">
                    {l s='Append Before' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="friendly_url_append" class="btn btn-xs">
                    <input type="radio" id="friendly_url_append" name="presta_mass_edit[seo][meta_url][action]" value="append">
                    {l s='Append After' d='Modules.Prestamassedit.Admin'}
                </label>
                <label for="friendly_url_replace" class="btn btn-xs">
                    <input type="radio" id="friendly_url_replace" name="presta_mass_edit[seo][meta_url][action]" value="replace">
                    {l s='Replace' d='Modules.Prestamassedit.Admin'}
                </label>
            </div>
        </label>
        <div class="form_input row">
            <div class="col-lg-10">
                {foreach $languages as $lang}
                    {assign var="presta_current_div" value="presta_current_div_`$lang.id_lang`"}
                    {assign var="name" value="presta_mass_edit[seo][meta_url][value][`$lang.id_lang`]"}
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
