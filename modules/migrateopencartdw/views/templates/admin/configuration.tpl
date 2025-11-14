
{*
* 2007-2024 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h1 class="dw-center">Configuration Step</h1>
<div class="panel">
    <form id="configuration_form" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data"
          novalidate="">
        <input type="hidden" name="submitAddconfiguration" value="1">
        <div class="panel" id="fieldset_0">
            <div class="panel-heading">
                <i class="fas fa-shopping-basket"></i> Shops
            </div>
            {* <div class="form-wrapper">
                {foreach $multiShopsInputs as $k => $multiShopsInput}
                    <div>
                        <label class="control-label required">
						<span class="label-tooltip"
                              data-toggle="tooltip"
                              data-html="true">
                            {$multiShopsInput.label|escape:'htmlall':'UTF-8'}
                        </span>
                        </label>
                        <div>
                            <select name="{$multiShopsInput.name|escape:'htmlall':'UTF-8'}" id="{$multiShopsInput.name|escape:'htmlall':'UTF-8'}">
                                {foreach $multiShopsInput.options as $keyOption => $multiShopsOption}
                                    <option value="{$multiShopsOption.id_shop|escape:'htmlall':'UTF-8'}" {if $multiShopsInput.local_id == $multiShopsOption.id_lang} selected="selected"{/if}>{$multiShopsOption.name|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/foreach}

            </div> *}
        </div>
        {if isset($ordersStatusInputs)}
            <div class="panel" id="fieldset_3_3">
                <div class="panel-heading">
                    <i class="fas fa-shopping-bag"></i> Order status
                </div>
                <div class="form-wrapper">
                    {foreach $ordersStatusInputs as $k => $ordersStatusInput}
                        <div class="form-group">

                            <label class="control-label required">
                                        <span class="label-tooltip"
                                            data-toggle="tooltip"
                                            data-html="true" title="">
                                            {$ordersStatusInput.label|escape:'htmlall':'UTF-8'}
                                        </span>
                            </label>
                            <div>
                                <select name="{$ordersStatusInput.name|escape:'htmlall':'UTF-8'}" id="{$ordersStatusInput.name|escape:'htmlall':'UTF-8'}">
                                    {foreach $ordersStatusInput.options as $keyOption => $ordersStatusOption}
                                        <option value="{$ordersStatusOption.id_order_state|escape:'htmlall':'UTF-8'}" {if $ordersStatusInput.local_id == $ordersStatusOption.id_order_state} selected="selected"{/if}>{$ordersStatusOption.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        {/if}
        {if isset($currenciesInputs)}
            <div class="panel" id="fieldset_1_1">
                <div class="panel-heading">
                    <i class="fas fa-euro-sign"></i> Currencies
                </div>
                <div class="form-wrapper">
                    {foreach $currenciesInputs as $k => $currenciesInput}
                        <div>
                            <label class="control-label required">
                                    <span class="label-tooltip"
                                        data-toggle="tooltip"
                                        data-html="true" title="">
                                                    {$currenciesInput.label|escape:'htmlall':'UTF-8'}
                                    </span>
                            </label>
                            <div>
                                <select name="{$currenciesInput.name|escape:'htmlall':'UTF-8'}" id="{$currenciesInput.name|escape:'htmlall':'UTF-8'}">
                                    {foreach $currenciesInput.options as $keyOption => $currenciesOption}
                                        <option value="{$currenciesOption.id_currency|escape:'htmlall':'UTF-8'}" {if $currenciesInput.local_id == $currenciesOption.id} selected="selected"{/if}>{$currenciesOption.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {/foreach}

                </div>
            </div>
        {/if}
        {if isset($taxRulesInputs)}
            <div class="panel" id="fieldset_1_1">
                <div class="panel-heading">
                    <i class="fas fa-euro-sign"></i> Tax Rules
                </div>
                <div class="form-wrapper">
                    {foreach $taxRulesInputs as $k => $taxRulesInput}
                        <div>
                            <label class="control-label required">
                                    <span class="label-tooltip"
                                        data-toggle="tooltip"
                                        data-html="true" title="">
                                                    {$taxRulesInput.label|escape:'htmlall':'UTF-8'}
                                    </span>
                            </label>
                            <div>
                                <select name="{$taxRulesInput.name|escape:'htmlall':'UTF-8'}" id="{$taxRulesInput.name|escape:'htmlall':'UTF-8'}">
                                    {foreach $taxRulesInput.options as $keyOption => $taxRulesOption}
                                        <option value="{$taxRulesOption.id_tax_rules_group|escape:'htmlall':'UTF-8'}" {if $taxRulesInput.local_id == $taxRulesOption.id_tax_rules_group} selected="selected"{/if}>{$taxRulesOption.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {/foreach}

                </div>
            </div>
        {/if}
        {if isset($languagesInputs)}
            <div class="panel" id="fieldset_2_2">
                <div class="panel-heading">
                    <i class="fas fa-globe-asia"></i> Languages
                </div>
                <div class="form-wrapper">
                    {foreach $languagesInputs as $k => $languagesInput}
                        <div>
                            <label class="control-label required">
                            <span class="label-tooltip"
                                data-toggle="tooltip"
                                data-html="true"
                                title="">
                                Default Store View
                            </span>
                            </label>
                            <div>
                                <select name="{$languagesInput.name|escape:'htmlall':'UTF-8'}" id="{$languagesInput.name|escape:'htmlall':'UTF-8'}">
                                    {foreach $languagesInput.options as $keyOption => $languagesOption}
                                        <option value="{$languagesOption.id_lang|escape:'htmlall':'UTF-8'}" {if $languagesInput.local_id == $languagesOption.id_lang} selected="selected"{/if}>{$languagesOption.name|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        {/if}
        <div class="alert alert-info">
            <p>We recommend you to import all kinds of data for migration purpose. However, you can deselect some kinds
                of data if you really do not need them.</p>
        </div>
        <div class="panel" id="fieldset_5_5">
            <div class="panel-heading">
                <i class="fas fa-exchange-alt"></i> Select Entities
            </div>
            <div class="form-wrapper">
                <div>
                    <label class="control-label">
					<span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title=""
                          data-original-title="Select All Boxes">
									Select All
					</span>
                    </label>
                    <div>
                    <span class="switch prestashop-switch ">
						<input type="radio"
                               name="entities_select_all"
                               id="entities_select_all_on"
                               value="1">
                        <label for="entities_select_all_on">Yes</label>
                        <input type="radio"
                               name="entities_select_all"
                               id="entities_select_all_off"
                               value="" checked="checked">
                        <label for="entities_select_all_off">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </div>
                </div>
                <div>
                    <label class="control-label ">
                    <span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title="">
                        Manufacturers
                    </span>
                    </label>
                    <div>
                    <span class="switch prestashop-switch ">
                        <input type="radio"
                               name="entities_manufacturers"
                               id="entities_manufacturers_on"
                               value="1">
                        <label for="entities_manufacturers_on">Yes</label>
                        <input type="radio"
                               name="entities_manufacturers"
                               id="entities_manufacturers_off"
                               value="" checked="checked">
                        <label for="entities_manufacturers_off">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </div>
                </div>
                <div>
                    <label class="control-label ">
                    <span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title="">
                        Categories
                    </span>
                    </label>
                    <div>
                    <span class="switch prestashop-switch ">
                        <input type="radio"
                               name="entities_categories"
                               id="entities_categories_on"
                               value="1">
                        <label for="entities_categories_on">Yes</label>
                        <input type="radio"
                               name="entities_categories"
                               id="entities_categories_off"
                               value="" checked="checked">
                        <label for="entities_categories_off">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </div>
                </div>
                <div>
                    <label class="control-label ">
                    <span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title="">
                        Products
                    </span>
                    </label>
                    <div>
                    <span class="switch prestashop-switch ">
                        <input type="radio"
                               name="entities_products"
                               id="entities_products_on"
                               value="1">
                        <label for="entities_products_on">Yes</label>
                        <input type="radio"
                               name="entities_products"
                               id="entities_products_off"
                               value="" checked="checked">
                        <label for="entities_products_off">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </div>
                </div>
                <div>
                    <label class="control-label ">
                    <span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title="">
                        Customers
                    </span>
                    </label>
                    <div>
                    <span class="switch prestashop-switch ">
                        <input type="radio"
                               name="entities_customers"
                               id="entities_customers_on"
                               value="1">
                        <label for="entities_customers_on">Yes</label>
                        <input type="radio"
                               name="entities_customers"
                               id="entities_customers_off"
                               value="" checked="checked">
                        <label for="entities_customers_off">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </div>
                </div>
                <div>
                    <label class="control-label ">
                    <span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title="">
                        Orders
                    </span>
                    </label>
                    <div>
                    <span class="switch prestashop-switch ">
                        <input type="radio"
                               name="entities_orders"
                               id="entities_orders_on"
                               value="1">
                        <label for="entities_orders_on">Yes</label>
                        <input type="radio"
                               name="entities_orders"
                               id="entities_orders_off" value=""
                               checked="checked">
                        <label for="entities_orders_off">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel" id="fieldset_6_6">
            <div class="panel-heading">
                <i class="fas fa-cog"></i> Additional Options
            </div>
            <div class="alert alert-info">
                <p>Enable this option will delete the same kinds of data (that you are migrating) on your website before
                    importing new data so make sure you want to do that</p>
            </div>
            <div class="form-wrapper">
                <div>
                    <label class="control-label ">
                    <span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title="">
                        Clean Target
                    </span>
                    </label>
                    <div>
                    <span class="switch prestashop-switch ">
                        <input type="radio" name="clear_data"
                               id="clear_data_on" value="1">
                        <label for="clear_data_on">Yes</label>
                        <input type="radio" name="clear_data"
                               id="clear_data_off" value=""
                               checked="checked">
                        <label for="clear_data_off">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </div>
                </div>
                <div class="alert alert-info" style="margin-top: 10px">
                    <p>This feature migrates data that are not yet in the target PrestaShop</p>
                </div>
                <div>
                    <label class="control-label ">
                    <span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title="">
                        Migrate recent data
                    </span>
                    </label>
                    <div>
                    <span class="switch prestashop-switch ">
                        <input type="radio"
                               name="migrate_recent_data"
                               id="migrate_recent_data_on"
                               value="1">
                        <label for="migrate_recent_data_on">Yes</label>
                        <input type="radio"
                               name="migrate_recent_data"
                               id="migrate_recent_data_off"
                               value="" checked="checked">
                        <label for="migrate_recent_data_off">No</label>
                        <a class="slide-button btn"></a>
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>