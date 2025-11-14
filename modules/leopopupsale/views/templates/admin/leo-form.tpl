{*
*  PrestaShop
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
<div class="table-customer leotype type3">
	<hr>
	<div class="alert alert-info">
		<p>{l s='Create Product Sale' mod='leopopupsale'}</p>
	</div>

	<div class="leo-product">
		<div class="leo-product-tag">
			{if !empty($products)}
				{foreach from=$products item=product}
					<span class="leo-tagger-Tag">
						<span data-id="{$product.id_product|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</span>
						<i class="material-icons leo-remove">close</i>
					</span>
				{/foreach}
			{/if}
		</div>

		<input type="hidden" class="leo-list-product" name="LEOPOPUPSALE_LIST_PRODUCT" value="{Configuration::get('LEOPOPUPSALE_LIST_PRODUCT')|escape:'htmlall':'UTF-8'}">

		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Search Product' mod='leopopupsale'}</label>

			<div class="col-lg-3">
				<input type="text" id="leo-search-product" placeholder="{l s='Search Product' mod='leopopupsale'}">

				<div class="cssload-container leo-loading">
					<div class="cssload-double-torus"></div>
				</div>
			</div>
		</div>

		<div class="leo-res">
			<div class="leo-error alert alert-danger">ooooooooo</div>

			<p class="leo-close">
				<i class="material-icons">close</i>
			</p>

			<div class="product-list">
			</div>
		</div>
	</div>

	<div class="alert alert-info">
		<p>{l s='Create Customer Sale' mod='leopopupsale'}</p>
	</div>

	<table class="table leo-table">
		<thead>
			<tr class="title-table">
				<td>
					{l s='Customer Name' mod='leopopupsale'}
				</td>
				<td>
					{l s='Customer Phone' mod='leopopupsale'}
				</td>
				<td>
					{l s='Customer Address' mod='leopopupsale'}
				</td>
				<td>
					{l s='Action' mod='leopopupsale'}
				</td>
			</tr>
		</thead>
		<tbody class="leo-body">
			<tr class="tr-template" style="display:none;">
				<td>
					<input required="" type="text" name="name">
				</td>
				<td>
					<input required="" type="text" name="phone">
				</td>
				<td>
					<input required="" type="text" name="address">
				</td>
				<td>
					<a href="#" class="leo-delete btn">
						<i class="material-icons">delete</i>
					</a>
				</td>
			</tr>

			{if !empty($customers)}
			{foreach from=$customers item=customer}
			<tr class="item-tr item-customer">
				<td>
					<input required="" type="text" name="leo_name[]" value="{$customer.name|escape:'htmlall':'UTF-8'}">
				</td>

				<td>
					<input required="" type="text" name="leo_phone[]" value="{$customer.phone|escape:'htmlall':'UTF-8'}">
				</td>
				<td>
					<input required="" type="text" name="leo_address[]" value="{$customer.address|escape:'htmlall':'UTF-8'}">
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

	<button type="button" class="leo-add">
		<i class="material-icons">add_circle</i>
		{l s='Add a new row' mod='leopopupsale'}
	</button>
</div>