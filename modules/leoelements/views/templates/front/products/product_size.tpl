{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{if isset($product.attribute) && $product.attribute && isset($product.attribute.Size) && $product.attribute.Size}
{if !isset($leoajax)}
<div class="product-size-attribute">
{/if}
	<ul class="product-attr">
	    {foreach from=$product.attribute.Size item=size}
	        <li class="product-{$size.group_name} {if $size.quantity == 0}Sold-Out{/if}">
	            <a class="{$size.group_name}" title="{$size.name}" href="{$size.url}">{$size.name}</a>
	        </li>
	    {/foreach}
	</ul>
{if !isset($leoajax)}
</div>
{/if}
{else}
<div class="product-item-size product-size-attribute product-size-{$product.id_product}" data-idproduct="{$product.id_product}"></div>
{/if}