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
      {if count($product_configs)}
        <table class="table">
          <thead>
          <tr>
            <th>ID</th>
            <th>{l s='Product' mod='dynamicproduct'}</th>
            <th>{l s='Linked products' mod='dynamicproduct'}</th>
            <th class="fixed-width-sm center">{l s='Actions' mod='dynamicproduct'}</th>
          </tr>
          </thead>
          <tbody>
          {foreach from=$product_configs item=product_config}
              {$id_product = $product_config.id_product}
              {$viewLink = "{$link->getAdminLink('AdminProducts', true, ['id_product' => $id_product|intval, 'updateproduct' => '1'])|escape:'html':'UTF-8'}&id_product={$id_product|intval}&updateproduct#tab-hooks"}
            <tr>
              <td>{$id_product|intval}</td>
              <td>
                <a href="{$viewLink|escape:'htmlall':'UTF-8'}"
                   target="_blank"
                >
                    {Product::getProductName($id_product)|escape:'htmlall':'UTF-8'}
                </a>
              </td>
              <td>
                  {if $product_config.nb_linked}
                      {$product_config.nb_linked|intval}
                  {else}
                    --
                  {/if}
              </td>
              <td class="text-right">
                <div class="btn-group-action">
                  <div class="btn-group pull-right">
                    <a href="{$viewLink|escape:'htmlall':'UTF-8'}"
                       target="_blank"
                       title="{l s='View this configuration' mod='dynamicproduct'}"
                       class="edit btn btn-default"
                    >
                      <i class="icon-edit"></i> {l s='View' mod='dynamicproduct'}
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
            {l s='You have no dynamic products yet! You can open a product edit page to make it dynamic.' mod='dynamicproduct'}
        </div>
      {/if}
  </div>
</div>
