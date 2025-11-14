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
	$('.leo_hide').each(function() {
		if ($(this).hasClass('leo_type_1')) {
			$(this).parent().parent().parent().parent().closest('.form-group').hide();
		} else {
			$(this).closest('.form-group').hide();
		}
	});

	$('#type').on('change', function() {
		var type = $(this).val();

		if (type == 1) {
			$('.leo_type_1').parent().parent().parent().parent().closest('.form-group').show();
			$('.leo_type_2').closest('.form-group').hide();
		} else {
			$('.leo_type_1').parent().parent().parent().parent().closest('.form-group').hide();
			$('.leo_type_2').closest('.form-group').show();
		}
	});

	if($('#type_product').val() == 3){
		$('.leo-cate').closest('.form-group').hide();
	}else if($('#type_product').val() == 1){
		$('#product').closest('.form-group').children('label').text('Except products');
		$('#product').parent().children('p').text('Not show in products. Ex: 1,2,3,4,5,... ');
		$('.leo-cate').closest('.form-group').hide();
	}else{
		$('#product').closest('.form-group').children('label').text('Except products');
		$('#product').parent().children('p').text('Not show in products. Ex: 1,2,3,4,5,... ');
		$('.leo-cate').closest('.form-group').show();
	}
	$('#type_product').on('change', function(){
		if($(this).val() == 3){
			$('#product').closest('.form-group').children('label').text('Product ID');
			$('#product').parent().children('p').text('Show in products. Ex: 1,2,3,4,5,...')
			$('.leo-cate').closest('.form-group').hide();
		}else if($(this).val() == 1){
			$('.leo-cate').closest('.form-group').hide();
			$('#product').closest('.form-group').children('label').text('Except products');
			$('#product').parent().children('p').text('Not show in products. Ex: 1,2,3,4,5,... ');
		}else{
			$('#product').closest('.form-group').children('label').text('Except products');
			$('#product').parent().children('p').text('Not show in products. Ex: 1,2,3,4,5,... ');
			$('.leo-cate').closest('.form-group').show();
		}
	});

	if ($('#leoextratab-group #fieldset_0').length){
		$('#leoextratab-group #fieldset_0').addClass('active');
	}
});