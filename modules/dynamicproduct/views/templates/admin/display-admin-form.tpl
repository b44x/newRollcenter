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
<ul class="nav nav-tabs" id="dp-module-tabs-headers">
  <li role="presentation" class="active">
    <a href="#configuration">
      <i class="icon-cogs"></i> {l s='Configuration' mod='dynamicproduct'}
    </a>
  </li>
  <li role="presentation">
    <a href="#product-configurations">
      <i class="icon-cogs"></i> {l s='Product configurations' mod='dynamicproduct'}
    </a>
  </li>
  <li role="presentation">
    <a href="#units">
      <i class="icon-calculator"></i> {l s='Units Of Measurement' mod='dynamicproduct'}
    </a>
  </li>
  <li role="presentation">
    <a href="#field-groups" data-cy="field-groups-tab">
      <i class="icon-list"></i> {l s='Field Groups' mod='dynamicproduct'}
    </a>
  </li>
  <li role="presentation">
    <a href="#steps">
      <i class="icon-list"></i> {l s='Steps' mod='dynamicproduct'}
    </a>
  </li>
  <li role="presentation">
    <a href="#common-fields">
      <i class="icon-share-alt"></i> {l s='Common fields' mod='dynamicproduct'}
    </a>
  </li>
  <li role="presentation">
    <a href="#favorite-fields">
      <i class="icon-star"></i> {l s='Favorite fields' mod='dynamicproduct'}
    </a>
  </li>
</ul>

<div class="tab-content" id="dp-module-tabs" style="display: none;">
  <div class="tab-pane active" id="configuration">
      {$main_config_html} {* prestashop form rendered by the module *}
  </div>
  <div class="tab-pane" id="product-configurations">
      {include file="./fields/configs-list.tpl"}
  </div>
  <div class="tab-pane" id="units">
      {include file="./units/unit-list.tpl"}
  </div>
  <div class="tab-pane" id="field-groups">
      {include file="./field-groups/field-group-list.tpl"}
  </div>
  <div class="tab-pane" id="steps">
      {include file="./steps/step-list.tpl"}
  </div>
  <div class="tab-pane" id="common-fields">
      {include file="./fields/common-fields-list.tpl"}
  </div>
  <div class="tab-pane" id="favorite-fields">
      {include file="./fields/favorite-list.tpl"}
  </div>
</div>

<p style="margin-top: 1em; font-size: 12px;">
  Dynamic Product • v{$version|escape:'html':'UTF-8'}
</p>

<script>
  $(document).ready(function () {
    $("#dp-module-tabs-headers a").click(function (e) {
      e.preventDefault()
      history.pushState(null, "", e.currentTarget.getAttribute("href") + "-tab")
      $(this).tab("show")
    })

    if (location.hash) {
      $("#dp-module-tabs-headers").find("a[href=\"" + location.hash.replace("-tab", "") + "\"]").tab("show")
    }

    document.getElementById("dp-module-tabs").style.display = "block"
  })
</script>

<style>
  #dp-module-tabs-headers {
    padding-left: 1em;
  }

  #dp-module-tabs-headers li:not(.active) a {
    background-color: #FAFAFA;
  }
</style>
