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

<h1 class="dw-center">Connection Step</h1>
<form id="step_dw_connection" class="defaultForm form-horizontal" method="post" enctype="multipart/form-data"
      novalidate="">
    <input type="hidden" name="submitAddconfiguration" value="1">
    <div class="panel" id="fieldset_form">
        <div class="panel-heading">
            <i class="fas fa-link"></i>
            Connection
        </div>
        <div class="alert alert-info">
            <p>
                Before getting started with the migration, please download <a
                        href="{$module_dir|escape:'javascript':'UTF-8'}assets/connector.zip"
                        target="_blank">{l s='Opencart Connector' mod='migrateopencartdw'}</a> module and install the
                module on source Opencart website. This Connector module gives you "Connector URL" and "Secure access
                token" (or data file) that is required.
            </p>
        </div>
        <div class="form-wrapper">
            <div>
                <label class="control-label required">
					<span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title=""
                          data-original-title="">
							Source OpenCart Url
                    </span>
                </label>
                <div>
                    <input type="text" name="source_shop_url" id="source_shop_url"
                           value="{$connection_details.source_shop_url|escape:'htmlall':'UTF-8'}"
                           class="" required="required">
                </div>
            </div>
            <div>
                <label class="control-label required">
				<span class="label-tooltip"
                      data-toggle="tooltip"
                      data-html="true" title="">
                    Token
					</span>
                </label>
                <div>
                    <input type="text" name="source_shop_token" id="source_shop_token"
                           value="{$connection_details.source_shop_token|escape:'htmlall':'UTF-8'}" class=""
                           required="required">
                </div>
            </div>
        </div>
    </div>
</form>
