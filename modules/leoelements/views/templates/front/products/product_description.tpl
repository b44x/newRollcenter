{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{block name='product_description'}
  <div class="product-description">
  	{$product.description|strip_tags nofilter}
  </div>
{/block}