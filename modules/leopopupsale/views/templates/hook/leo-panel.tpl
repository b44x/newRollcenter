{*
* PrestaShop
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
*  @copyright  PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="leo-setting row">
	<div class="leo-form-setting col-md-12">
		<div class="leo-button btn btn-primary col-md-2">
			<i class="material-icons leo-icon">settings_applications</i>
		</div>
		<div class="leo-form col-md-10">
			<form action="#" method="GET">
				<section class="form-fields">
					<div class="form-group row">
			          <label class="col-md-6 form-control-label">{l s='Select THEME' mod='leopopupsale'}</label>
			          <div class="col-md-6">
			            <select name="theme" class="fixed-width-xl">
							<option value="basic"{if $theme == 'basic'} selected="selected"{/if}>Basic</option>
							<option value="model"{if $theme == 'model'} selected="selected"{/if}>Model</option>
						    <option value="smart" {if $theme == 'smart'} selected="selected"{/if}>Smart</option>
						</select>
			          </div>
		        	</div>

					<div class="form-group row">
			          <label class="col-md-6 form-control-label">{l s='Time Interval popup' mod='leopopupsale'}</label>
			          <div class="col-md-6">
			            <input class="form-control" name="time" type="text" value="{if isset($leotime)}{$leotime|escape:'htmlall':'UTF-8'}{else}1000{/if}" placeholder="1000">
			          </div>
		        	</div>
		        	<div class="form-group row">
			          <label class="col-md-6 form-control-label">{l s='Popup Timing' mod='leopopupsale'}</label>
			          <div class="col-md-6">
			            <input class="form-control" name="interval" type="text" value="{if isset($leointerval)}{$leointerval|escape:'htmlall':'UTF-8'}{else}2000{/if}" placeholder="1000">
			          </div>
		        	</div>

		        	<div class="form-group row">
          				<label class="col-md-6 form-control-label">{l s='Position' mod='leopopupsale'}</label>
          				<div class="col-md-6">
          					<select name="position" class="form-control form-control-select">
          						<option value="1" {if isset($position) && $position == 1}selected{/if}>{l s='Top-Left' mod='leopopupsale'}</option>
          						<option value="2" {if isset($position) && $position == 2}selected{/if}>{l s='Top-Right' mod='leopopupsale'}</option>
          						<option value="3" {if isset($position) && $position == 3}selected{/if}>{l s='Bottom-Left' mod='leopopupsale'}</option>
          						<option value="4" {if isset($position) && $position == 4}selected{/if}>{l s='Bottom-Right' mod='leopopupsale'}</option>
          					</select>
          				</div>
          			</div>
					
					<input class="btn btn-primary" type="submit" name="leopopupconfig" value="{l s='Save' mod='leopopupsale'}">
	        	</section>
			</form>
		</div>
	</div>
</div>