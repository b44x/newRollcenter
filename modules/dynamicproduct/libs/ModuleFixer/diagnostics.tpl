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
<div class="panel" style="margin-bottom: 1em;">
  <h3><i class="icon icon-credit-card"></i> {l s='Diagnostics' mod='dynamicproduct'}</h3>

    {if !$templates_fixed}
      <div class="alert alert-warning">
        <div style="padding: 0;">{l s='To ensure that customizations are displayed correctly in the order page, please fix the templates in the troubleshoot page.' mod='dynamicproduct'}</div>
        <a href="{$module_link|escape:'htmlall':'UTF-8'}&view_troubleshooter#dp-fix-templates" target="_blank"
           class="tn-external-link">
          <i class="material-icons"
             style="font-size: 1em; vertical-align: middle">open_in_new</i>
          <span>{l s='Fix templates' mod='dynamicproduct'}</span>
        </a>
      </div>
    {/if}

    {if $has_failed_upgrades}
      <div class="alert alert-danger">
        <div style="padding: 0;">{l s='Some upgrades have failed. Please fix them in the upgrade checker page.' mod='dynamicproduct'}</div>
        <a href="{$module_link|escape:'htmlall':'UTF-8'}&view_upgrade_checker" target="_blank" class="tn-external-link">
          <i class="material-icons"
             style="font-size: 1em; vertical-align: middle">open_in_new</i>
          <span>{l s='Fix upgrades' mod='dynamicproduct'}</span>
        </a>
      </div>
    {/if}

    {if $needs_upgrade}
      <div class="alert alert-danger">
        <div style="padding: 0;">{l s='The module needs to be upgraded. Please apply the upgrade in the upgrade checker page.' mod='dynamicproduct'}</div>
        <a href="{$module_link|escape:'htmlall':'UTF-8'}&view_upgrade_checker" target="_blank" class="tn-external-link">
          <i class="material-icons"
             style="font-size: 1em; vertical-align: middle">open_in_new</i>
          <span>{l s='Upgrade module' mod='dynamicproduct'}</span>
        </a>
      </div>
    {/if}
</div>

<style>
  .tn-external-link {
    display: flex;
    align-items: center;
    gap: .2em;
    text-decoration: none !important;
  }
</style>