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
  <h3><i class="icon icon-credit-card"></i> {l s='Upgrade checker' mod='dynamicproduct'}</h3>

  <div class="well">
    <h4>{l s='Versions list' mod='dynamicproduct'}</h4>

      {foreach $messages as $message}
        <div class="alert alert-success">{$message|escape:'htmlall':'UTF-8'}</div>
      {/foreach}

      {foreach $warnings as $warning}
        <div class="alert alert-warning">{$warning|escape:'htmlall':'UTF-8'}</div>
      {/foreach}

      {foreach $errors as $error}
        <div class="alert alert-danger">{$error|escape:'htmlall':'UTF-8'}</div>
      {/foreach}

    <p>
        {if $needs_upgrade}
    <form method="post">
      <button class="btn btn-primary" type="submit" name="upgrade">
          {l s='Upgrade module' mod='dynamicproduct'}
      </button>
    </form>
      {/if}

      {if !$active}
        <form method="post">
          <button class="btn btn-primary" type="submit" name="activate">
              {l s='Activate module' mod='dynamicproduct'}
          </button>
        </form>
      {/if}
    </p>

    <ul class="tn-versions-list">
        {foreach from=$upgrades item=upgrade}
            {$success = $upgrade.success}
          <li class="{if $success}tn-upgrade-success{else}tn-upgrade-fail{/if}"
              id="version-{$upgrade.version|escape:'htmlall':'UTF-8'}"
              style="display: flex; align-items: center; gap: .2em;"
          >
            {$upgrade.version|escape:'htmlall':'UTF-8'}
            &nbsp;
            {if $success}
              <i class="material-icons">check_circle</i>
            {else}
              <i class="material-icons">cancel</i>
              <a href="{$upgrade.retry_link|escape:'htmlall':'UTF-8'}"
                 class="btn btn-primary btn-xs"
                 title="{l s='Retry' mod='dynamicproduct'}"
              >
                <i class="material-icons">replay</i> {l s='Retry' mod='dynamicproduct'}
              </a>
            {/if}
          </li>
        {/foreach}
    </ul>
  </div>

  <div class="panel-footer">
    <a href="{$module_link|escape:'htmlall':'UTF-8'}"
       class="btn btn-default pull-left"
    ><i class="process-icon-back"></i> {l s='Back' mod='dynamicproduct'}</a>
  </div>
</div>
