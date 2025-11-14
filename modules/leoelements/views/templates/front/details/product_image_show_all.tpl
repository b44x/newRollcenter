{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{block name='page_content_container'}
  <section class="page-content" id="content">
    {block name='page_content'}
      <div class="images-container">
        {block name='product_cover_thumbnails'}
          {block name='product_cover'}
            <div class="product-cover">
              {block name='product_flags'}
                <ul class="product-flags">
                  {foreach from=$product.flags item=flag}
                    <li class="product-flag {$flag.type}">{$flag.label}</li>
                  {/foreach}
                </ul>
              {/block}
              {if $product.default_image}
				<img id="zoom_product" data-type-zoom="" class="js-qv-product-cover img-fluid" src="{$product.default_image.bySize.large_default.url}" {if !empty($product.default_image.legend)}
      				alt="{$product.default_image.legend}"
      				title="{$product.default_image.legend}"
      			   {else}
      				alt="{$product.name}"
      			    {/if}
      				loading="lazy"
      			>
                <div class="layer hidden-sm-down" data-toggle="modal" data-target="#product-modal">
                  <i class="material-icons zoom-in">&#xE8FF;</i>
                </div>
              {else}
                <img id="zoom_product" data-type-zoom="" class="js-qv-product-cover img-fluid" src="{$urls.no_picture_image.bySize.large_default.url}"  loading="lazy" alt="{$product.name}" title="{$product.name}">
			  {/if}
            </div>
          {/block}

          {block name='product_images'}
            <div id="thumb-gallery" class="product-thumb-images elementor-slick-slider">
              {if $product.default_image}
                {foreach from=$product.images item=image}
                  <div class="thumb-container {if $image.id_image == $product.default_image.id_image} active {/if}">
                    <a href="javascript:void(0)" data-image="{$image.bySize.large_default.url}" data-zoom-image="{$image.bySize.large_default.url}"> 
                      <img
                        class="thumb img-fluid js-thumb {if $image.id_image == $product.default_image.id_image} selected {/if}"
                        data-image-medium-src="{$image.bySize.large_default.url}"
                        data-image-large-src="{$image.bySize.large_default.url}"
                        src="{$image.bySize.small_default.url}"
                        alt="{$image.legend}"
                        title="{$image.legend}"
			loading="lazy"
                      >
                    </a>
                  </div>
                {/foreach}
              {else}
                <div class="thumb-container">
                  <a href="javascript:void(0)" data-image="{$urls.no_picture_image.bySize.large_default.url}" data-zoom-image="{$urls.no_picture_image.bySize.large_default.url}"> 
                    <img 
                      class="thumb js-thumb img-fluid" 
                      data-image-medium-src="{$urls.no_picture_image.bySize.large_default.url}"
                      data-image-large-src="{$urls.no_picture_image.bySize.large_default.url}"
                      src="{$urls.no_picture_image.bySize.small_default.url}"
                      alt="{$product.name}"
                      title="{$product.name}"
		                  loading="lazy"
                    >
                  </a>
                </div>
              {/if}
            </div>
            
            {if $product.images|@count > 1}
              <div class="arrows-product-fake slick-arrows">
                <button class="slick-prev slick-arrow" aria-label="Previous" type="button" >{l s='Previous' d='Shop.Theme.Catalog'}</button>
                <button class="slick-next slick-arrow" aria-label="Next" type="button">{l s='Next' d='Shop.Theme.Catalog'}</button>
              </div>
            {/if}
          {/block}
        
        {/block}
        {hook h='displayAfterProductThumbs'}
      </div>
    {/block}
  </section>
{/block}

{block name='product_images_modal'}
  {include file='catalog/_partials/product-images-modal.tpl'}
{/block}
