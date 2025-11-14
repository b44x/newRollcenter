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
<!-- ✅ ✅ ✅ If the summary is not displayed correctly, open the module configuration page and click the "Troubleshooting" button, then Fix the templates then clear the cache ✅ -->

{$displayed_count = 0}

<div class="dp_cart dp_seven_cart"
     data-id_customization="{$input->id_customization|intval}"
>
    <div class="dp_input_div dp_input_{$input->id|intval}">
        {if count($grouped_fields)}
            {foreach from=$grouped_fields item=group}
                {if $group.label}
                    <strong>{$group.label|escape:'htmlall':'UTF-8'}</strong>
                    <br>
                {/if}
                {foreach from=$group.fields item=input_field}
                    {if $input_field->isSkippedName()}{continue}{/if}
                    {if $input_field->isSkipped() && !$input_field->isAdminField()}{continue}{/if}
                    {if $input_field->name === "preview" && $is_order_detail}{continue}{/if}
                    {$displayed_count = $displayed_count + 1}
                    <span style="{if $group.label}padding-left: 1em;{/if}">
                {if $input_field->label}
                    <strong>{$input_field->label|escape:'htmlall':'UTF-8'}:</strong>
                {/if}
                        {if $input_field->getTemplatePath()}
                            {include file=$input_field->getTemplatePath()}
                        {else}
                            {$input_field->getDynamicValue($input->input_fields)|escape:'htmlall':'UTF-8'}
                        {/if}
                        {$sku = $input_field->getSKU()}
                        {if $sku}
                            ({$sku|escape:'htmlall':'UTF-8'})
                        {/if}
            </span>
                    <br>
                {/foreach}
                <br>
            {/foreach}
        {/if}

        {if $input->canDisplayWeight()}
            <span>
          <strong>{l s='Weight' mod='dynamicproduct'}:</strong>
            {$input->weight|floatval} {Configuration::get('PS_WEIGHT_UNIT')|escape:'htmlall':'UTF-8'}
        </span>
        {/if}

        {if isset($show_price) && $show_price}
            <span>
          <strong>{l s='Price' mod='dynamicproduct'}:</strong>
          {$price|escape:'htmlall':'UTF-8'}
        </span>
        {/if}

        {if !$is_pdf}
            <br>
            <a target="_blank" class="btn btn-default" data-cy="admin-edit" style="margin-top: 10px;"
               href="{$input->getEditLink(true)|escape:'htmlall':'UTF-8'}"
            >
                {l s='Edit this customization' mod='dynamicproduct'}
            </a>
            <a target="_blank" class="btn btn-default" style="margin-top: 10px;"
               href="{$input->getCSVLink()|escape:'htmlall':'UTF-8'}"
            >
                {l s='Export as CSV' mod='dynamicproduct'}
            </a>
        {/if}
    </div>

    {if $displayed_count == 0}
        <style>
            .dp_cart[data-id_customization="{$input->id_customization|intval}"] {
                display: none;
            }
        </style>
    {/if}
</div>