/**
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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
"use strict";
$(document).ready(function() {
	if (leo_type == 2) {
		tabVertical();
	} else if (leo_type == 3) {
		tabAccordion();
	} else {
		tabHorizontal();
	}
});

function tabAccordion()
{
	if (theme_name != 'classic') {
		$('#accordion').append(leo_content);
	} else {
		if (leo_title != '') {
			$('ul.nav-tabs').append(leo_title);
			$('#tab-content').append(leo_content);
		}
		var tab = '';

		$('.tab .nav-item').each(function() {
			tab += '<h3 class="leo-title">'+ $(this).find('a').html() + '</h3>';

			var id_tab = $(this).find('a').attr('href');
			tab += '<div class="leo-content">' + $(''+ id_tab +'').html() + '</div>';
		});

		var html = '<div id="leo_accordion">';
		html += tab;
		html += '</div>';

		$('.product-information .tabs').html(html);
		$("#leo_accordion").accordion();
	}
	
}

function tabVertical()
{
	if (theme_name != 'classic') {
		$('.more-info-product').append(leo_content);
	} else {
		if (leo_title != '') {
			$('ul.nav-tabs').append(leo_title);
			$('#tab-content').append(leo_content);
		}
		$('.product-information .tabs').addClass('row');

		var ul = '<div class="col-md-3"><ul class="nav nav-tabs" role="tablist">';
		ul += $('.product-information .tabs .nav-tabs').html() + '</ul></div>';

		var li = '<div class="col-md-9"><div class="tab-content" id="tab-content">';
		li += $('.product-information .tabs #tab-content').html() + '</div></div>'; 

		$('.product-information .tabs').html(ul);
		$('.product-information .tabs').append(li);
	}
}

function tabHorizontal()
{
	if (leo_title != '') {
		$('.product-tabs ul.nav-tabs').append(leo_title);
		$('#tab-content').append(leo_content);
	}
}
