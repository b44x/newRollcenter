{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
<div class="LeoBlockLink">
	<div class="linklist-toggle h6" data-toggle="linklist-widget">
		{if $settings['link']['url']}
		    <a 
			href="{$settings['link']['url']}"
			{if $settings['link']['is_external']} target="_blank"{/if}
			{if $settings['link']['nofollow']} rel="nofollow"{/if}
		    >
			<div class="title_block">{$settings['title']}</div>
			<span class="icon-toggle fa fa-angle-down"></span>
		    </a>
                        
                        
			
		{else}
		{if $settings['show_title']}
			<div class="title_block">{$settings['title']}</div>
			{/if}
			<span class="icon-toggle fa fa-angle-down"></span>
                        
		{/if}
	</div>
        
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
        <div class="sub-title-widget">{$formAtts.sub_title}</div>
        {/if}
                        
                        
	<div class="linklist-menu">
            {if isset($items) && $items}
                <ul class="list-items {if isset($formAtts.type) && $formAtts.type}{$formAtts.type}{/if}">
                {foreach from=$items item="item"}
                    {if $item['item_link']['url']}
                            <li class="list-item {if isset($formAtts.type) && $formAtts.type}{$formAtts.type}{/if}">
                                <a class='item-text' href="{$item['item_link']['url']}"
                                    {if $item['item_link']['is_external']} target="_blank"{/if}
                                    {if $item['item_link']['nofollow']} rel="nofollow"{/if}
                                    {if $item['item_link']['nofollow']} rel="nofollow"{/if}>
                                    {$item['item_title']}
                                </a>
                            </li>
                    {else}
                            <li class="list-item">
                                <span class='item-text'>{$item['item_title']}</span>
                            </li>
                    {/if}
                {/foreach}
                </ul>
            {/if}
	</div>
</div>
