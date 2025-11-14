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
*  @copyright PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="table-customer leotype type1">
	<div class="alert alert-info">
		<p>{l s='Customer data will display random with product - Empty is not display' mod='leopopupsale'}</p>
	</div>

	<table class="table leo-table">
		<thead>
			<tr class="title-table">
				<td>
					{l s='Name' mod='leopopupsale'}
				</td>
				<td>
					{l s='Phone' mod='leopopupsale'}
				</td>
				<td>
					{l s='Adress' mod='leopopupsale'}
				</td>
				<td>
					{l s='Action' mod='leopopupsale'}
				</td>
			</tr>
		</thead>
		<tbody class="leo-body">
			<tr class="tr-cat-template" style="display:none;">
				<td>
					<input required="" type="text" name="catname">
				</td>
				<td>
					<input required="" type="text" name="catphone">
				</td>
				<td>
					<input required="" type="text" name="cataddress">
				</td>
				<td>
					<a href="#" class="leo-delete btn">
						<i class="material-icons">delete</i>
					</a>
				</td>
			</tr>

			{if !empty($cat_customers)}
			{foreach from=$cat_customers item=customer}
			<tr class="item-tr item-customer">
				<td>
					<input required="" type="text" name="leo_catname[]" value="{$customer.name|escape:'htmlall':'UTF-8'}">
				</td>

				<td>
					<input required="" type="text" name="leo_catphone[]" value="{$customer.phone|escape:'htmlall':'UTF-8'}">
				</td>

				<td>
					<input required="" type="text" name="leo_cataddress[]" value="{$customer.address|escape:'htmlall':'UTF-8'}">
				</td>

				<td>
					<a href="#" class="leo-delete btn">
						<i class="material-icons">delete</i>
					</a>
				</td>
			</tr>
			{/foreach}
			{/if}
		</tbody>
	</table>
	
	<button type="button" class="leo-cat-add">
		<i class="material-icons">add_circle</i>
		{l s='Add a new row' mod='leopopupsale'}
	</button>
</div>