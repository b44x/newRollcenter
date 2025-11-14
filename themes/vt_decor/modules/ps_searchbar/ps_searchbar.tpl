{**
 *  PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<!-- Block search module TOP -->
<div id="search_widget" class="search-widget js-dropdown popup-over" data-search-controller-url="{$search_controller_url}"> 
	<a href="javascript:void(0)" data-toggle="dropdown" class="popup-title">
    	<i class="ti-search icons"></i>
	</a>
	<form method="get" action="{$search_controller_url}" class="popup-content dropdown-menu" id="search_form">
		<div class="search-inner">
			<input type="hidden" name="controller" value="search">
			<input type="text" name="s" value="{$search_string}" placeholder="{l s='Wpisz szukane słowo...' d='Shop.Theme.Catalog'}" aria-label="{l s='Szukaj' d='Shop.Theme.Catalog'}">
			<button type="submit">
				<span>{l s='Szukaj' d='Shop.Theme.Catalog'}</span>
			</button>
		</div>
	</form>
</div>
<!-- /Block search module TOP -->
