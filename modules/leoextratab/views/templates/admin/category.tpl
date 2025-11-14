{*
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !empty($categories)}
	<ul class="leo-cate">
		<li class="item-cate">
			<input type="checkbox" id="leo_cate_0" value="0" {if $checked == 1}checked{/if}>
			<label for="leo_cate_0">{l s='Select All' mod='leoextratab'}</label>
		</li>
		{foreach from=$categories item=category}
			<li class="item-cate">
				<input {if $category.checked == 1}checked{/if} type="checkbox" class="leo-checked" id="leo_cate_{$category.id_category}" value="{$category.id_category}">
				<label for="leo_cate_{$category.id_category}">{$category.category}</label>
			</li>
		{/foreach}
	</ul>
{/if}

<style type="text/css">
	.item-cate {
		list-style: none;
	}
</style>

<script type="text/javascript">
	"use strict";
	$(document).ready(function() {
		$('.leo-id-category').closest('.form-group').hide();

		$('#leo_cate_0').on('click', function() {
			if ($(this).is(':checked')) {
				$('.leo-checked').attr('checked','checked');
				$('.leo-id-category').val(0);
			}
		});

		$('.leo-checked').on('click', function() {
			var count = 1;
			var id = '';
			var check = 1;

			$('.leo-checked').each(function() {
				if ($(this).is(':checked')) {
					if (count == 1) {
						id += $(this).val();
					} else {
						id += ',' + $(this).val();
					}

					count ++;
				} else {
					$('#leo_cate_0').attr('checked',false);
					check = 0;
				}
			});

			if (check == 1) {
				$('#leo_cate_0').attr('checked','checked');
				$('.leo-id-category').val(0);
			} else {
				$('.leo-id-category').val(id);
			}
		});
	});
</script>