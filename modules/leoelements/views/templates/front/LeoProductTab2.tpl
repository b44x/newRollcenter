{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{if isset($leo_products) && $leo_products && $leo_products|@count > 0}
    
    
    
	{$i = 0}
    	{if isset($settings.per_col) && $settings.per_col}
		{$y = $settings.per_col}
	{else}
		{$y = 1}
	{/if}
	
	
	
	
    {*<div class="wrapper-items">*}
    {*<div class="swiper-wrapper">*}
    {if isset($settings) && isset($settings.view_type) && $settings.view_type == 'carousel'}
	{* CAROUSEL *}
	<div class="elementor-LeoProductCarousel-wrapper elementor-slick-slider {$formAtts.pl_class}">
	    <div class="elementor-LeoProductCarousel ApSlick products slick-arrows-inside slick-dots-outside" >
		{foreach from=$leo_products item="product"}
		{if $i mod $y eq 0}
		<div class="slick-slide item">
		{/if}
		    {$i = $i+1}
		    {*_PS_THEME_DIR_ . 'templates/catalog/_partials/miniatures/product.tpl',*}
		    {include file="$theme_template_path" product=$product}
		{if $i mod $y eq 0 || $i eq count($leo_products)}
		</div>
		{/if}
		{/foreach}
	    </div>
	</div>
	    

    {else}
	{* GRID *}
	<div class="elementor-LeoProductCarousel-wrapper {$formAtts.pl_class}">
	    <div class="elementor-LeoProductCarousel grid products" >
		
		{foreach from=$leo_products item="product"}
		    <div class="swiper-slide item">
		  {include file="$theme_template_path" product=$product}
		  </div>
		{/foreach}
	    </div>
	</div>
	    
	    {*</div>*}
	{*</div>*}
    {/if}
	
    

    

{/if}