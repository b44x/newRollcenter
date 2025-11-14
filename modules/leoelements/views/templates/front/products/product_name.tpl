{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{block name='product_name'}
  <h3 class="h3 product-title"><a href="{$product.url}">{$product.name|truncate:70:'...'}</a></h3>
{/block}
