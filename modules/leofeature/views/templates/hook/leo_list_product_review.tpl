
{* 
* @Module Name: Leo Feature
* @Website: leotheme.com.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright  2007-2018 Leotheme
* @description: Leo feature for prestashop 1.7: ajax cart, review, compare, wishlist at product list 
*}
{if (isset($nbReviews) && $nbReviews > 0) || $show_zero_product_review}
	<div class="leo-list-product-reviews">
		<div class="leo-list-product-reviews-wraper">
			<div class="star_content clearfix">
				{section name="i" start=0 loop=5 step=1}
					{if $averageTotal le $smarty.section.i.index}
						<div class="star"></div>
					{else}
						<div class="star star_on"></div>
					{/if}
				{/section}
			</div>
			{if isset($nbReviews) && $nbReviews > 0 && $show_number_product_review}
				<span class="nb-revews"><span>{$nbReviews}</span> {l s='Review(s)' mod='leofeature'}</span>
			{/if}
		</div>
	</div>
{/if}