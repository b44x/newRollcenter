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
var $leoPopupSaleWrapper = $('.product-notification');
var $leoPopupSaleClose = $('.close-notifi');
var leotime = $leoPopupSaleWrapper.data('time') || 0;
var leointerval = $leoPopupSaleWrapper.data('interval') || 0;

if (leotime == 0 || leotime == '') {
	leotime = 1000;
	leointerval = 2000;
}

// alert(leotime+''+leointerval);

var productSuggestCookie = 'cookieSuggest';

$(document).ready(function() {
	SomeonePurchased();
	settingPopup();
});

function settingPopup()
{
	if ($('.leo-setting').length > 0) {
		$('.leo-color').mColorPicker({imageFolder: baseAdminDir + '/img/admin/'});

		$('.leo-button').click(function() {
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
				$(this).find('.leo-icon').html('settings_applications');
			} else {
				$(this).addClass('active');
				$(this).find('.leo-icon').html('clear');
			}
		});
	}
}

function SomeonePurchased()
{
	if ($.cookie(productSuggestCookie) == 'closed') {
		$leoPopupSaleWrapper.remove();
	}

	$leoPopupSaleClose.on('click',function() {
		$leoPopupSaleWrapper.remove();
		$.cookie(productSuggestCookie, 'closed', {expires:1, path:'/'});
	});

	function toggleSomething() {
		if ($leoPopupSaleWrapper.hasClass('active')) {

			//diplay time
			setTimeout(function(){ 
    			$leoPopupSaleWrapper.removeClass('active');
  			}, leotime);
		} else {
			var randomProduct = Math.floor(Math.random() * data_product_popup.length),
			data = data_product_popup[randomProduct];

			$leoPopupSaleWrapper.addClass('active');
			$leoPopupSaleWrapper.find('.product-image').attr('href', data['url']).find('img').attr('src', data['image']);
			$leoPopupSaleWrapper.find('.product-name').text(data['title']).attr('href', data['url']);

			$leoPopupSaleWrapper.find('.time-ago').text(data['time']);
			$leoPopupSaleWrapper.find('.leo-phone').text(data['phone']);
			$leoPopupSaleWrapper.find('.leo-address').text(data['address']);
		}
	}

	if (leointerval !== 0) {
		//duration call function
		setInterval(toggleSomething, leointerval);
	}
}