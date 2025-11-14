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
{if count($dp_oos_inputs) || count($dp_disabled_options)}
  <div id="dp_errors" style="clear: both; padding-top: 15px; margin: 5px 20px">
      {foreach from=$dp_oos_inputs item=dp_oos_input}
        <div class="alert alert-warning">
            {l s='An item in your cart is no longer available in this quantity' mod='dynamicproduct'}
          <br>
          <a style="color: #0e0e0e" href="{$dp_oos_input->getEditLink()|escape:'htmlall':'UTF-8'}">
            <strong>{l s='Click here to edit the customization' mod='dynamicproduct'}</strong>
          </a>
        </div>
      {/foreach}
      {foreach from=$dp_disabled_options item=$disabled_option}
        <div class="alert alert-warning">
            {l s='The option' mod='dynamicproduct'}
          <strong>{$disabled_option.option|escape:'htmlall':'UTF-8'}</strong>
            {l s='of product' mod='dynamicproduct'}
          <strong>{$disabled_option.product|escape:'htmlall':'UTF-8'}</strong>
            {l s='is no longer available' mod='dynamicproduct'}
          <br>
          <a style="color: #0e0e0e" href="{$disabled_option.edit_link|escape:'htmlall':'UTF-8'}">
            <strong>{l s='Click here to edit the customization' mod='dynamicproduct'}</strong>
          </a>
        </div>
      {/foreach}
  </div>
  <script type="text/javascript">
    var dp_intv = setInterval(function () {
      if (typeof $ === "function") {
        hidePaymentMethods()
        clearInterval(dp_intv)
      }
    }, 100)

    function hidePaymentMethods() {
      $(".payment-options").remove()
      $("#conditions-to-approve").remove()
      $("#payment-confirmation").remove()
    }
  </script>
{else}
  <script type="text/javascript">
    var dp_intv = setInterval(function () {
      if (typeof $ === "function") {
        showPaymentMethods()
        clearInterval(dp_intv)
      }
    }, 100)

    function showPaymentMethods() {
      $("#opc_payment_methods-content").show()
      $("#HOOK_PAYMENT .row").show()
      // for 1.7
      $(".payment-options").show()
      $("#conditions-to-approve").show()
    }
  </script>
{/if}
