{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
<script>
	/**
	 * List functions will run when document.ready()
	 */
	var ap_list_functions = [];
	/**
	 * List functions will run when window.load()
	 */
	var ap_list_functions_loaded = [];

	/**
	 * List functions will run when document.ready() for theme
	 */

	var products_list_functions = [];
</script>


{if isset($ajax_enable) && $ajax_enable}
<script type='text/javascript'>
	var leoOption = {
		category_qty:{if $category_qty}{$category_qty|escape:'html':'UTF-8'}{else}0{/if},
		product_list_image:{if $product_list_image}{$product_list_image|escape:'html':'UTF-8'}{else}0{/if},
		product_one_img:{if $product_one_img}{$product_one_img|escape:'html':'UTF-8'}{else}0{/if},
		productCdown: {if $productCdown}{$productCdown|escape:'html':'UTF-8'}{else}0{/if},
		productColor: {if $productColor}{$productColor|escape:'html':'UTF-8'}{else}0{/if},
		homeWidth: {if $homeSize}{$homeSize.width|escape:'html':'UTF-8'}{else}0{/if},
		homeheight: {if $homeSize}{$homeSize.height|escape:'html':'UTF-8'}{else}0{/if},
	}

	ap_list_functions.push(function(){
		if (typeof $.LeoCustomAjax !== "undefined" && $.isFunction($.LeoCustomAjax)) {
			var leoCustomAjax = new $.LeoCustomAjax();
			leoCustomAjax.processAjax();
		}
	});
</script>
{/if}
