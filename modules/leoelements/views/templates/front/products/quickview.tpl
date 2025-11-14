{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
<div class="quickview{if !$product.main_variants} no-variants{/if} hidden-sm-down">
<a
  href="#"
  class="quick-view"
  data-link-action="quickview" title="{l s='Quick view' mod='leoelements'}"
>
	<span class="leo-quickview-bt-loading cssload-speeding-wheel"></span>
	<span class="leo-quickview-bt-content">
		<i class="material-icons search">&#xE8B6;</i>
		<span>{l s='Quick view' mod='leoelements'}</span>
	</span>
</a>
</div>
