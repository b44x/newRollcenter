{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
<div class="elementor-LeoProductTab" data-toggle=tabs-widget data-active-tab="1">
    {assign var=_counter value=1}
    
    <div class="widget-tabs-wrapper">
	
	{foreach from=$items item=item}
	    {if $_counter < 2}
		{assign var=_active value='active'}
	    {else}
		{assign var=_active value=''}
	    {/if}
	    
	    
	    
	    
	    <div class="widget-tab-title {$_active}" data-tab="{$_counter}">
			{if $item.icon_type == 'icon'}
				<i class="{$item.item_icon_title.value}"></i>
			{elseif $item.icon_type == 'image'}
				<img src="{$item.item_image_title.url}">
			{/if}
			{if $item.item_title}{$item.item_title}{/if}
	    </div>
	    
	    {assign var=_counter value=$_counter+1}
	{/foreach}
    </div>
    
    
    <div class="widget-tabs-content-wrapper">
	{assign var=_counter value=1}
	{foreach from=$items item=item}
	    {if $_counter < 2}
		{assign var=_active value='active'}
	    {else}
		{assign var=_active value=''}
	    {/if}
	    <div class="widget-tab-content {$_active}" data-tab="{$_counter}">
		{$apwidget->renderProduct($settings, $item) nofilter}
	    </div>
	    
	    {assign var=_counter value=$_counter+1}
	{/foreach}
    </div>
</div>
