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
      {if count($favorite_fields)}
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
          {foreach from=$favorite_fields item=favorite_field}
              {$deletelink = "index.php?controller=AdminModules{$default}&id_favorite_field={$favorite_field.id|intval}&submit_remove_favorite_field&token={$token|escape:'htmlall':'UTF-8'}"}
            <tr>
              <td>{$favorite_field.id|intval}</td>
              <td>{$favorite_field.name|escape:'htmlall':'UTF-8'}</td>
              <td>{$favorite_field.label|escape:'htmlall':'UTF-8'}</td>
              <td>
                <a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $favorite_field.id_product|intval, 'updateproduct' => '1'])|escape:'html':'UTF-8'}&amp;id_product={$favorite_field.id_product|intval}&amp;updateproduct"
                   target="_blank"
                >
                    {Product::getProductName($favorite_field.id_product)|escape:'htmlall':'UTF-8'}
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
            {l s='No favorite fields yet! You can add a favorite field by clicking the star icon in a the field row.' mod='dynamicproduct'}
        </div>
      {/if}
  </div>
</div>
