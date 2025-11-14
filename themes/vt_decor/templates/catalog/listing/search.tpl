{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author LeoTheme <leotheme@gmail.com>
* @copyright LeoTheme
* @description: Leo Elements is module help you can build content for your shop
*}
{*
 * This file allows you to customize your search page.
 * You can safely remove it if you want it to appear exactly like all other product listing pages
 *}
{extends file='catalog/listing/product-list.tpl'}

{block name="error_content"}
  <h4>{l s='Nie znaleziono wyników dla Twojego wyszukiwania' d='Shop.Theme.Catalog'}</h4>
  <p>{l s='Spróbuj użyć innych słów kluczowych, aby opisać to, czego szukasz.' d='Shop.Theme.Catalog'}</p>
{/block}

{block name='product_list'}
  {include file='catalog/_partials/products.tpl' listing=$listing productClass="col-xs-6 col-xl-3"}
{/block}

