{* 
* @Module Name: Leo Next Previous
* @Website: leotheme.com.com - prestashop template provider
* @copyright Leotheme
* @description: Leo Next Previous for prestashop 1.7: Displays links to the previous and next product on the product pages
*}

{if $position != 3 || (isset($buttons) && $buttons)}<div class="leonextprevious-container clearfix">{/if}
	{if $prev}
		<div class="leo-button-prev">
			<a  title="{$prev.name}" class="btn btn-primary text-left leonextprevious-btn" href="{Context::getContext()->link->getProductLink($prev.id_product, $prev.link_rewrite)}">
				<p {if !$display_image || !$display_names || !$display_price}style="margin: 0"{/if}><i class="material-icons">&#xE314;</i></i> {l s='previous product' mod='leonextprevious'}</p>
				{if $display_image}<img src="{Context::getContext()->link->getImageLink($prev.link_rewrite, $prev.id_image, 'small_default')}" title="{$prev.legend}">{/if}
				{if $display_names || $display_price}<p class="small">{if $display_names}{$prev.name}<br>{/if}{if $display_price}{$prev.price_static}<br>{/if}</p class="small">{/if}
			</a>
		</div>
	{elseif !$prev && isset($buttons) && $buttons}
		<div class="leo-button-prev"></div>
	{/if}
	{if $next}
		<div class="leo-button-next">
			<a title="{$next.name}" class="btn btn-primary leonextprevious-btn" href="{Context::getContext()->link->getProductLink($next.id_product, $next.link_rewrite)}">
				<p {if !$display_image || !$display_names || !$display_price}style="margin: 0"{/if}>{l s='next product' mod='leonextprevious'} <i class="material-icons">&#xE315;</i></p>
				{if $display_image}<img src="{Context::getContext()->link->getImageLink($next.link_rewrite, $next.id_image, 'small_default')}" title="{$next.legend}">{/if}
				{if $display_names || $display_price}<p class="small">{if $display_names}{$next.name}<br>{/if}{if $display_price}{$next.price_static}<br>{/if}</p class="small">{/if}
			</a>
		</div>
	{elseif !$next && isset($buttons) && $buttons}
		<div class="leo-button-next"></div>
	{/if}
{if $position != 3 || (isset($buttons) && $buttons)}</div>{/if}

{if $position > 0 && $position < 3}
<script type="text/javascript">
{if $position == 1}
	{literal}
		document.addEventListener("DOMContentLoaded", function() {
			$('.leonextprevious-container').prependTo('#main');
		});
	{/literal}
{elseif $position == 2}
	{literal}
		document.addEventListener("DOMContentLoaded", function() {
			$('.leonextprevious-container').clone().prependTo('#main');
		});
	{/literal}
{/if}
</script>
{/if}
