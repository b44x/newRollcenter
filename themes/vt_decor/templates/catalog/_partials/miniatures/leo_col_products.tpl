{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
*  @author    Apollotheme <apollotheme@gmail.com>
*  @copyright  Apollotheme
* @description: LeoElements is module help you can build content for your shop
*}

{*define numbers of product per line in other page for desktop*}
{if (isset($page.page_name) && $page.page_name == 'index') || (isset($leo_page) && $leo_page=='index')}
    {assign var='nbItemsPerLine' value=$profile_params.pl_config.listing_product_column_module}
    {if $profile_params.pl_config.listing_product_column_module=="5"}{assign var="col_cat_product_xl" value="col-xl-2-4"}{else}{assign var="col_cat_product_xl" value="col-xl-{12/$profile_params.pl_config.listing_product_column_module}"}{/if}
{else}
    {assign var='nbItemsPerLine' value=$profile_params.pl_config.listing_product_column}
    {if $profile_params.pl_config.listing_product_column=="5"}       {assign var="col_cat_product_xl" value="col-xl-2-4"}{else}{assign var="col_cat_product_xl" value="col-xl-{12/$profile_params.pl_config.listing_product_column}"}{/if}
{/if}

{if $profile_params.pl_config.listing_product_largedevice=="5"}  {assign var="col_cat_product_lg" value="col-lg-2-4"}{else}{assign var="col_cat_product_lg" value="col-lg-{12/$profile_params.pl_config.listing_product_largedevice}"}{/if}
{assign var="colValue" value="col-sp-{12/$profile_params.pl_config.listing_product_mobile} col-xs-{12/$profile_params.pl_config.listing_product_mobile} col-sm-{12/$profile_params.pl_config.listing_product_extrasmalldevice} col-md-{12/$profile_params.pl_config.listing_product_tablet} {$col_cat_product_lg} {$col_cat_product_xl}" scope="global"}
        
{assign var='nbItemsPerLineTablet' value=$profile_params.pl_config.listing_product_tablet}
{assign var='nbItemsPerLineMobile' value=$profile_params.pl_config.listing_product_mobile}
{*define numbers of product per line in other page for tablet*}
{assign var='nbLi' value=$products|count}
{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
{math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
<!-- Products list -->
<div {if isset($id) && $id} id="{$id}"{/if} class="product_list {if isset($profile_params.pl_config.listing_product_mode)}{$profile_params.pl_config.listing_product_mode}{/if} {if isset($profile_params.pl_config.class) && $profile_params.pl_config.class != ""}{$profile_params.pl_config.class}{/if} ">
    <div class="row leo-product-ajax">
        {foreach from=$products item=product name=products}

            {math equation="(total%perLine)" total=$products|count perLine=$nbItemsPerLine assign=totModulo}

            {math equation="(total%perLineT)" total=$products|count perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
            {math equation="(total%perLineT)" total=$products|count perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
            {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
            {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
            {if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}  

            <div class="ajax_block_product {$colValue}{if $smarty.foreach.products.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLine == 1} first-in-line{/if}{if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModulo)} last-line{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}{if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModuloMobile)} last-mobile-line{/if}">
                {block name='product_miniature'}
                    {if isset($profile_params.pl_url) && $profile_params.pl_url}
                        {include file=$profile_params.pl_url product=$product}
                    {else}
                        {include file='catalog/_partials/miniatures/product.tpl' product=$product}
                    {/if}
                {/block}
            </div>
        {/foreach}
    </div>
</div>
