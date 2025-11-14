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
<div class="panel">
  <div class="form-wrapper">
      {if count($common_fields)}
        <table class="table">
          <thead>
          <tr>
            <th>ID</th>
            <th>{l s='Name' mod='dynamicproduct'}</th>
            <th>{l s='Label' mod='dynamicproduct'}</th>
            <th>{l s='Product' mod='dynamicproduct'}</th>
            <th class="fixed-width-sm center">{l s='Actions' mod='dynamicproduct'}</th>
          </tr>
          </thead>
          <tbody>
          {foreach from=$common_fields item=common_field}
              {$deletelink = "index.php?controller=AdminModules{$default}&id_common_field={$common_field.id|intval}&submit_remove_common_field&token={$token|escape:'htmlall':'UTF-8'}"}
            <tr>
              <td>{$common_field.id|intval}</td>
              <td>{$common_field.name|escape:'htmlall':'UTF-8'}</td>
              <td>{$common_field.label|escape:'htmlall':'UTF-8'}</td>
              <td>
                <a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $common_field.id_product|intval, 'updateproduct' => '1'])|escape:'html':'UTF-8'}&amp;id_product={$common_field.id_product|intval}&amp;updateproduct"
                   target="_blank"
                >
                    {Product::getProductName($common_field.id_product)|escape:'htmlall':'UTF-8'}
                </a>
              </td>
              <td class="text-right">
                <div class="btn-group-action">
                  <div class="btn-group pull-right">
                    <a href="{$deletelink|escape:'htmlall':'UTF-8'}"
                       title="{l s='The field will not be deleted' mod='dynamicproduct'}"
                       class="edit btn btn-default"
                    >
                      <i class="icon-trash"></i> {l s='Remove' mod='dynamicproduct'}
                    </a>
                  </div>
                </div>
              </td>
            </tr>
          {/foreach}
          </tbody>
        </table>
      {else}
        <div class="alert alert-info">
            {l s='No common fields yet! You can add a common field by clicking the share icon in a the field row.' mod='dynamicproduct'}
        </div>
      {/if}
  </div>
</div>
