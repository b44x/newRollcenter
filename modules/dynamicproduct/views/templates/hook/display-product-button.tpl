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
<div id="dp-product-button">

  <a style="position: relative;" href="{$product_link|escape:'htmlall':'UTF-8'}#"
     class="btn btn-primary dsn-customize"
  >{l s='Customize' mod='dynamicproduct'}</a>

    {if $product_config->required}
      <script>
        var disableCartButton = function () {
          if (!$(".dp-container").length) {
            $(".modal.quickview [data-button-action=\"add-to-cart\"]").attr("disabled", "disabled")
          }
        }
        if (typeof $ === "function") {
          $(function () {
            disableCartButton()
          })
          prestashop.on("updatedProduct", function () {
            disableCartButton()
          })
        }
      </script>
    {/if}
</div>
