{*
* 2007-2025 TuniSoft
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    TuniSoft <tunisoft.solutions@gmail.com>
*  @copyright 2007-2025 TuniSoft
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<form action="{$req|escape:'htmlall':'UTF-8'}{$urlhash|escape:'htmlall':'UTF-8'}"
      method="post"
      id="unit_form"
      class="form-horizontal"
>
  <div class="panel dp_lang_div">
    <div class="panel-heading">
      <i class="icon-cogs"></i>
        {l s='Unit Of Measurement' mod='dynamicproduct'}:
    </div>
    <div class="form-wrapper">
      <div class='form-group'>
        <div><label for="unit_name" class='control-label col-lg-3'>{l s='Unit Name' mod='dynamicproduct'}:</label></div>
        <div class="col-lg-4 dp_lang_column">
          <div class="dp_group dp_input_lang">
            <div class="dp_lang_container">
                {foreach from=$dp_languages item=lang}
                  <div class="dp_lang">
                    <input type="text"
                           name="name[{$lang.id_lang|intval}]"
                           value="{if isset($unit->name[$lang.id_lang])}{$unit->name[$lang.id_lang]|escape:'htmlall':'UTF-8'}{/if}"
                           class="dp_lang_input"
                    >
                    <img class="dp_flag"
                         width="16"
                         title="{$lang.name|escape:'htmlall':'UTF-8'}"
                         src="{$ps_base_url|escape:'htmlall':'UTF-8'}img/l/{$lang.id_lang|intval}.jpg"
                    />
                  </div>
                {/foreach}
            </div>
          </div>
        </div>
      </div>
      <div class='form-group'>
        <div><label for="symbol" class='control-label col-lg-3'>{l s='Symbol' mod='dynamicproduct'}:</label></div>
        <div class="col-lg-4"><input class="form-control "
                                     name="symbol"
                                     id="symbol"
                                     type="text"
                                     value="{$unit->symbol|escape:'htmlall':'UTF-8'}"
          ></div>
      </div>
      <input type="hidden" id="id_unit" name="id_unit" value="{$unit->id|intval}">
    </div>
    <div class="panel-footer">
      <button type="submit" class="btn btn-default pull-right" name="submit_edit_unit">
        <i class="process-icon-save"></i>
          {l s='Save' mod='dynamicproduct'}
      </button>
      <a class="btn btn-default pull-right" href="{$dp_module_link|escape:'htmlall':'UTF-8'}">
        <i class="process-icon-cancel"></i>
        <span>{l s='Cancel' mod='dynamicproduct'}</span>
      </a>
    </div>
  </div>
</form>
