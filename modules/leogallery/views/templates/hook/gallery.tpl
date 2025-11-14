{*
 * 2007-2022 LeoTheme
 *
 * NOTICE OF LICENSE
 *
 * LeoGallery is module help you can displays amazing gallery with many features on product page
 *
 * DISCLAIMER
 *
 *  @author    LeoTheme <leotheme@gmail.com>
 *  @copyright 2007-2022 LeoTheme
 *  @license   http://apollotheme.com - prestashop template provider
*}

{include file="./head.tpl" config=$config quickview=$quickview}
<style type="text/css">
    .lSAction > a{
        {if $sliderConfig->control_bg}
            background-color: {$sliderConfig->control_bg|escape:'htmlall':'UTF-8'};
        {/if}
        {if $sliderConfig->control_color}
            color: {$sliderConfig->control_color|escape:'htmlall':'UTF-8'} !important;
        {/if}
    }
    .lSAction > a:hover{
        {if $sliderConfig->control_hbg}
            background-color: {$sliderConfig->control_hbg|escape:'htmlall':'UTF-8'};
        {/if}
        {if $sliderConfig->control_hcolor}
            color: {$sliderConfig->control_hcolor|escape:'htmlall':'UTF-8'} !important;
        {/if}
    }
    .lSSlideOuter .lSPager.lSGallery li{
        border-width: {$sliderConfig->thumb_border|intval}px;
        {if $sliderConfig->thumb_border_color}
            border-color: {$sliderConfig->thumb_border_color|escape:'htmlall':'UTF-8'};
        {/if}
        border-style: solid;
    }
    .lSSlideOuter .lSPager.lSGallery li.active{
        border-width: {$sliderConfig->active_thumb_border|intval}px;
        {if $sliderConfig->active_thumb_border_color}
            border-color: {$sliderConfig->active_thumb_border_color|escape:'htmlall':'UTF-8'};
        {/if}
        border-style: solid;
    }
</style>

<script>
    var $quickview = {$quickview};
    if (typeof leogallery === 'undefined'){
        window.addEventListener('load', function(){
            leogalleryUpdateImages();
        });
    }else{
        setTimeout(function(){
            leogalleryUpdateImages();
        }, $quickview ? 200 : 0);
    }
    function leogalleryUpdateImages() {
        leogallery.images = [];
        {foreach from=$images item=image}
            {$image.bySize['_orig']['url'] = $link->getImageLink($product->link_rewrite, $image.id_image, '')}
            leogallery.images.push({
                original: '{$image.bySize['_orig'].url nofilter}',
                thumb: '{$image.bySize[$sliderConfig->thumb_size].url nofilter}',
                slider_img: '{$image.bySize[$sliderConfig->img_size].url nofilter}',
                {if $isMobile}
                    gallery_img: '{$image.bySize[$config->m_img_size].url nofilter}',
                {else}
                    gallery_img: '{$image.bySize[$config->img_size].url nofilter}',
                {/if}
                title: "{$image.legend|escape:'htmlall':'UTF-8'|replace:"\r\n":""|replace:"\n":""}"
            });
        {/foreach}
        {if $config->img_captions}
            leogallery.displayCaption = true;
        {/if}

        leogallery.buildSlider();
    }
</script>