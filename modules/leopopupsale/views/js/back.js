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
$(document).ready(function() {
	var type = Number($('#LEOPOPUPSALE_SELECT_TYPE').val());

	$('.table-customer').parent().removeClass('col-lg-9');
	$('.table-customer').parent().removeClass('col-lg-offset-3');

	if (type == 3) {
		$('.table-customer').closest('.form-group').show();
		$('#LEOPOPUPSALE_CATEGORY').closest('.form-group').show();
	} else if (type == 2) {
		$('.table-customer').closest('.form-group').hide();
		$('#LEOPOPUPSALE_CATEGORY').closest('.form-group').hide();
	} else {
		$('.table-customer').closest('.form-group').hide();
		$('#LEOPOPUPSALE_CATEGORY').closest('.form-group').show();
	}

	selectType();
	addCustomer();
	deleteCustomer();
	leoSearchProduct();
	removeProduct();
});

function removeProduct()
{
	$('.leo-remove').click(function() {
		$(this).closest('.leo-tagger-Tag').remove();
		var check = 1;
		var id = '';

		$('.leo-tagger-Tag').each(function() {
			if (check == 1) {
				id += $(this).find('span').data('id');
			} else {
				id += ',' + $(this).find('span').data('id');
			}

			check++;
		});

		$('.leo-list-product').val(id);
	});
}

function setLeoSearchAction() {	
	$('#leo-search-product').keyup(function() {
		var name = $(this).val();
		var id_product = $('.leo-list-product').val();
		$('.leo-error').hide();
		$('leo-res').hide();
		$('.leo-close').hide();
		$('.product-list').html('');

		if (name.length >= 3) {
			$('.leo-loading').show();

			$.ajax({
		        type: 'GET',
		        url: url,
		        headers: { "cache-control": "no-cache" },
		        async: true,
		        cache: false,
		        dataType: "json",
		        data: {
		            token: leo_token,
		            id_product: id_product,
		            name: name,
		            action: 'searchProduct'
		        },
		        success: function (data)
		        {
					$('.leo-loading').hide();
		            $('leo-res').show();

		            if (data.success == 0) {
		            	$('.leo-error').html(data.message);
		            	$('.leo-error').show();
		            } else {
		            	$('.leo-close').show();
		            	$('.product-list').html(data.message);
		            	selectProduct(id_product);
		            }
		        },
    		});
		}
	});
}
function leoSearchProduct()
{
	$('.leo-close').click(function() {
		$('.product-list').html('');
		$('.leo-close').hide();
	});

	setLeoSearchAction();
}

function selectProduct(id_product)
{
	$('.leo-product-item').click(function() {
		var id = $(this).data('id');
		var name = $(this).data('name');

		var html = '<span class="leo-tagger-Tag">';
		html += '<span data-id="'+id+'">'+name+'</span>';
		html += '<i class="material-icons leo-remove">close</i>';
		html += '</span>';

		if (id_product != '') {
			id_product += ',' + id;
		} else {
			id_product += id;
		}

		$('.leo-product-tag').append(html);
		$('.leo-list-product').val(id_product);
		$('.product-list').html('');
		$('.leo-close').hide();
		removeProduct();
	});
}

function selectType()
{
	var val = Number($('#LEOPOPUPSALE_SELECT_TYPE').val());
	hideShow(val);
	
	$('#LEOPOPUPSALE_SELECT_TYPE').change(function() {
		val = Number($(this).val());
		hideShow(val);
	});
}

function hideShow(val)
{	
	$('.leotype').each(function(){		
		$(this).closest('.form-group').hide();
	});
	$('.type'+val).each(function(){		
		$(this).closest('.form-group').show();
	});
	if(val==1){
		$("#LEOPOPUPSALE_CATEGORY").closest('.form-group').show();
	}else{
		$("#LEOPOPUPSALE_CATEGORY").closest('.form-group').hide();
	}
}

function addCustomer()
{
	$('.leo-add').click(function() {
		var html = '<tr class="item-tr item-customer">' + $('.tr-template').html() + '</tr>';
		wraper = $(this).closest();

		html = html.replace('name="name"', 'name="leo_name[]"');
		html = html.replace('name="phone"', 'name="leo_phone[]"');
		html = html.replace('name="address"', 'name="leo_address[]"');
		
		$(this).parent().find('.leo-body').first().append(html);
		deleteCustomer();
	});

	$('.leo-cat-add').click(function() {
		var html = '<tr class="item-tr item-customer">' + $('.tr-cat-template').html() + '</tr>';
		wraper = $(this).closest();

		html = html.replace('name="catname"', 'name="leo_catname[]"');
		html = html.replace('name="catphone"', 'name="leo_catphone[]"');
		html = html.replace('name="cataddress"', 'name="leo_cataddress[]"');
		
		$(this).parent().find('.leo-body').first().append(html);
		deleteCustomer();
	});
}

function deleteCustomer()
{
	$('.leo-delete').on('click', function(e) {
		e.preventDefault();
		$(this).closest('.item-customer').remove();
	});
}