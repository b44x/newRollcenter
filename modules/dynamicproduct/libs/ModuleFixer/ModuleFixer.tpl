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
	<h3><i class="icon icon-credit-card"></i> {l s='Hooks' mod='dynamicproduct'}</h3>

	<div class="well">
		<h4>{l s='List of hooks' mod='dynamicproduct'}</h4>

      {foreach $errors as $error}
				<div class="alert alert-danger">{$error|escape:'htmlall':'UTF-8'}</div>
      {/foreach}

      {if count($unregistered_hooks)}
				<p>{l s='You can attach the module again to the following hooks' mod='dynamicproduct'}</p>
				<form action="" method="post">
					<ul>
              {foreach $unregistered_hooks as $hook}
								<li>
									<label for="id_hook_{$hook.id_hook|intval}">
										<input id="id_hook_{$hook.id_hook|intval}"
										       name="hooks[{$hook.name|escape:'htmlall':'UTF-8'}]"
										       type="checkbox"
										>
                      {$hook.name|escape:'htmlall':'UTF-8'}
										<span style="color: #AAA">({$hook.description|escape:'htmlall':'UTF-8'})</span>
									</label>
								</li>
              {/foreach}
					</ul>
					<button type="submit"
					        name="restore_hooks"
					        class="btn btn-primary"
					>{l s='Restore selected hooks' mod='dynamicproduct'}</button>
				</form>
      {elseif count($module_hooks)}
				<div class="alert alert-success">{l s='The module is hooked to all necessary hooks' mod='dynamicproduct'}</div>
				<ul style="color: #72c279; list-style: none;">
            {foreach $module_hooks as $module_hook}
							<li><strong><i class="icon-check-circle"></i> {$module_hook|escape:'htmlall':'UTF-8'}</strong></li>
            {/foreach}
				</ul>
      {/if}
	</div>

	<div class="panel-footer">
		<a href="{$module_link|escape:'htmlall':'UTF-8'}"
		   class="btn btn-default pull-left"
		><i class="process-icon-back"></i> {l s='Back' mod='dynamicproduct'}</a>
	</div>
</div>

<div class="panel" id="dp-fix-templates">
	<h3><i class="icon icon-credit-card"></i> {l s='Templates' mod='dynamicproduct'}</h3>

	<div class="well">
		<h4>{l s='Fix templates' mod='dynamicproduct'}</h4>
		<p class="help-block">{l s='Fix the backoffice order template to display the html summary correctly' mod='dynamicproduct'}</p>

      {if $templates_fixed}
				<div class="alert alert-success">{l s='The fix was correctly applied to the order templates' mod='dynamicproduct'}</div>
      {else}
				<form action="" method="post">
					<button type="submit"
					        name="fix_templates"
					        class="btn btn-primary"
					>{l s='Apply template fix' mod='dynamicproduct'}</button>
				</form>
      {/if}
		<p class="alert alert-info">
        {l s='Please clear the PrestaShop cache after applying the fix' mod='dynamicproduct'}
		</p>
	</div>

	<div class="panel-footer">
		<a href="{$module_link|escape:'htmlall':'UTF-8'}"
		   class="btn btn-default pull-left"
		><i class="process-icon-back"></i> {l s='Back' mod='dynamicproduct'}</a>
	</div>
</div>

<div class="panel" id="dp_cleanup_panel">
	<h3><i class="icon process-icon-delete"></i> {l s='Cleanup' mod='dynamicproduct'}</h3>

	<p class="help-block">
      {l s='Delete old unused customization data' mod='dynamicproduct'}
	</p>

	<div class="alert alert-warning">
      {l s='A full database + files backup is recommended before cleaning up the data' mod='dynamicproduct'}
	</div>

	<form class="dp_configuration_form" action="#dp_cleanup_panel" method="post">
		<div class="well">
			<div class="form-group">
				<label for="min_age">
            {l s='Clean unused customizations older than' mod='dynamicproduct'}
					<select id="min_age"
					        name="min_age"
					        class="form-control"
					>
              {if !isset($min_age)}
                  {assign var="min_age" value=7}
              {/if}
						<option value="7" {if $min_age == 7}selected{/if}>{l s='1 week' mod='dynamicproduct'}</option>
						<option value="14" {if $min_age == 14}selected{/if}>{l s='2 weeks' mod='dynamicproduct'}</option>
						<option value="30" {if $min_age == 30}selected{/if}>{l s='1 month' mod='dynamicproduct'}</option>
						<option value="90" {if $min_age == 90}selected{/if}>{l s='3 months' mod='dynamicproduct'}</option>
						<option value="120" {if $min_age == 120}selected{/if}>{l s='4 months' mod='dynamicproduct'}</option>
						<option value="150" {if $min_age == 150}selected{/if}>{l s='5 months' mod='dynamicproduct'}</option>
						<option value="180" {if $min_age == 180}selected{/if}>{l s='6 months' mod='dynamicproduct'}</option>
						<option value="365" {if $min_age == 365}selected{/if}>{l s='1 year' mod='dynamicproduct'}</option>
					</select>
				</label>
				<p class="help-block">
            {l s='Customizations that are used in orders will not be deleted' mod='dynamicproduct'}
				</p>
			</div>

			<div class="form-group">
				<label>{l s='Cron key' mod='dynamicproduct'}</label>
				<input class="form-control" name="cron_key" value="{$config->cron_key}" />
			</div>

			<div class="form-group">
				<label>{l s='Cron link' mod='dynamicproduct'}</label>
				<input class="form-control" name="cron_link" value="{$cron_link}" data-link="{$cron_link}" />
			</div>

        {if isset($time_limit_exceeded)}
					<div class="alert alert-warning">
              {l s='Server time limit exceeded, please run the cleanup again' mod='dynamicproduct'}
					</div>
        {/if}
        {if isset($saved_inputs_delete_count)}
					<div class="alert alert-success">
              {l s='Deleted %s unused customizations' sprintf=[$saved_inputs_delete_count|intval] mod='dynamicproduct'}
					</div>
        {/if}
		</div>

		<div class="panel-footer">
			<a href="{$module_link|escape:'htmlall':'UTF-8'}"
			   class="btn btn-default pull-left"
			><i class="process-icon-back"></i> {l s='Back' mod='dynamicproduct'}</a>
			<button type="submit" value="1" name="cleanup" class="btn btn-default btn-warning"
			        style="color: #0b0b0b; margin-left: 1em;"
			>
				<i class="process-icon-save"></i> {l s='Clean data' mod='dynamicproduct'}
			</button>
		</div>

	</form>

</div>
