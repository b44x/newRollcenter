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
<div id="dp_units_data">
  <form action="{$req|escape:'htmlall':'UTF-8'}" method="post" id="dp_units_form">
    <div class="panel">
      <div class="form-wrapper">
        <table class="table">
          <thead>
          <tr>
            <th>ID</th>
            <th>{l s='Unit Name' mod='dynamicproduct'}</th>
            <th>{l s='Symbol' mod='dynamicproduct'}</th>
            <th class="fixed-width-sm center">{l s='Actions' mod='dynamicproduct'}</th>
          </tr>
          </thead>
          <tbody>
          {foreach from=$units item=unit}
              {$editlink = "index.php?controller=AdminModules{$default}&id_unit={$unit->id|intval}&display_update_unit&token={$token|escape:'htmlall':'UTF-8'}"}
              {$deletelink = "index.php?controller=AdminModules{$default}&id_unit={$unit->id|intval}&submit_delete_unit&token={$token|escape:'htmlall':'UTF-8'}"}
            <tr>
              <td>{$unit->id|intval}</td>
              <td>{$unit->name|escape:'htmlall':'UTF-8'}</td>
              <td>{$unit->symbol|escape:'htmlall':'UTF-8'}</td>
              <td class="text-right">
                <div class="btn-group-action">
                  <div class="btn-group pull-right">
                    <a href="{$editlink|escape:'htmlall':'UTF-8'}"
                       title="{l s='Edit' mod='dynamicproduct'}"
                       class="edit btn btn-default"
                    >
                      <i class="icon-pencil"></i> {l s='Edit' mod='dynamicproduct'}
                    </a>
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                      <i class="icon-caret-down"></i>&nbsp;
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <a href="{$deletelink|escape:'htmlall':'UTF-8'}"
                           title="{l s='Delete' mod='dynamicproduct'}"
                           class="dp_confirm_delete"
                        >
                          <i class="icon-trash"></i> {l s='Delete' mod='dynamicproduct'}
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </td>
            </tr>
          {/foreach}
          </tbody>
        </table>
      </div>
      <div class="panel-footer">
        <a class="btn btn-default pull-right" href="{$dp_module_link|escape:'htmlall':'UTF-8'}&display_add_unit">
          <i class="process-icon-new"></i>
          <span>{l s='Add a new unit' mod='dynamicproduct'}</span>
        </a>
      </div>
    </div>
  </form>
</div>
