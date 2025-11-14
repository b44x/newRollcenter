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
{if isset($layout_from_profile) && $layout_from_profile}
    {assign var="slideMode" value=$layout_from_profile.mode}
    {if $layout_from_profile.layout == 'bottom'}
        {assign var="slideLayout" value='left'}
    {elseif $layout_from_profile.layout == 'none'}
        {assign var="slideLayout" value='left'}
        {assign var="leo_use_thumb" value=0}
    {else}
        {assign var="slideLayout" value=$layout_from_profile.layout}
    {/if}
    {assign var="enableZoom" value=($layout_from_profile.zoom_type=='in' || $layout_from_profile.zoom_type == 'out')}
    {assign var="zoomType" value=$layout_from_profile.zoom_type}
{else}
    {assign var="slideMode" value=$sliderConfig->mode}
    {assign var="slideLayout" value=$sliderConfig->layout}
    {assign var="enableZoom" value=$magnify->enable}
    {assign var="zoomType" value=$magnify->type}
{/if}
<style type="text/css">
    .lg-backdrop{
        z-index: 10400;
        {if $config->bg_color}
            background-color: {$config->bg_color|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-outer{
        z-index: 10500;
    }
    .lg-toolbar .lg-icon{
        {if $config->controls_color}
            color: {$config->controls_color|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-toolbar .lg-icon:hover{
        {if $config->controls_hcolor}
            color: {$config->controls_hcolor|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-sub-html, .lg-toolbar{
        {if $config->toolbar_bg}
            background-color: {$config->toolbar_bg|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-actions .lg-next, .lg-actions .lg-prev{
        {if $config->prev_next_bg}
            background-color: {$config->prev_next_bg|escape:'htmlall':'UTF-8'};
        {/if}
        {if $config->prev_next_color}
            color: {$config->prev_next_color|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-actions .lg-next:hover, .lg-actions .lg-prev:hover{
        {if $config->prev_next_hbg}
            background-color: {$config->prev_next_hbg|escape:'htmlall':'UTF-8'};
        {/if}
        {if $config->prev_next_hcolor}
            color: {$config->prev_next_hcolor|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-outer .lg-thumb-outer{
        {if $config->thumb_bg}
            background-color: {$config->thumb_bg|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-outer .lg-toogle-thumb{
        {if $config->controls_color}
            color: {$config->controls_color|escape:'htmlall':'UTF-8'};
        {/if}
        {if $config->thumb_bg}
            background-color: {$config->thumb_bg|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-outer .lg-toogle-thumb:hover, .lg-outer.lg-dropdown-active #lg-share{
        {if $config->controls_hcolor}
            color: {$config->controls_hcolor|escape:'htmlall':'UTF-8'};
        {/if}
    }
    .lg-outer .lg-thumb-item.active, .lg-outer .lg-thumb-item:hover{
        {if $config->active_thumb_border}
            border-color: {$config->active_thumb_border|escape:'htmlall':'UTF-8'};
        {/if}
    }
    {if !$config->img_captions}
        .lg-outer.lg-pull-caption-up.lg-thumb-open .lg-sub-html,
        .lg-outer.lg-pull-caption-up .lg-sub-html{
            display: none;
        }
    {else}
        .lg-outer.lg-pull-caption-up.lg-thumb-open .lg-sub-html{
            bottom: {$config->img_captions_offset|intval}px;
        }
    {/if}
    {if $magnify->width and $magnify->height}
        .magnify > .magnify-lens {
            width: {$magnify->width|intval}px;
            height: {$magnify->height|intval}px;
        }
    {/if}
    {if $magnify->fader}
        .magnify:before{
            opacity: 0;
            transition: 0.2s all;
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            {if $magnify->fader_opacity}
                background: rgba(0, 0, 0, {$magnify->fader_opacity|intval / 100});
            {else}
                background: rgba(0, 0, 0, 0.2);
            {/if}
        }
        .magnify:hover:before{
            opacity: 1;
        }
    {/if}
    .leo-pager-controls{
        position: relative;
    }
    .leo-pager-controls .leo-pager-prev,
    .leo-pager-controls .leo-pager-next{
        width: 24px;
        height: 32px;
        padding: 0;
        margin: 0;
        border: 0;
        position: absolute;
        top: -59px;
        color: #232323;
        opacity: 0.7;
        transition: 0.2s all;
        z-index: 100;
    }
    .leo-pager-controls .leo-pager-prev:hover,
    .leo-pager-controls .leo-pager-next:hover{
        opacity: 0.9;
    }
    .leo-pager-controls .leo-pager-prev{
        left: -24px;
        right: auto;
        background: url('{$path}views/img/prev.svg') 50% 50% no-repeat scroll rgba(255, 255, 255, 0.6);
    }
    .leo-pager-controls .leo-pager-next{
        right: -24px;
        left: auto;
        background: url('{$path}views/img/next.svg') 50% 50% no-repeat scroll rgba(255, 255, 255, 0.6);
    }
    .leo-pager-controls.active .leo-pager-prev{
        left: 0;
    }
    .leo-pager-controls.active .leo-pager-next{
        right: 0;
    }
    {if ($slideMode == 'vertical')}
        .lSSlideOuter .lSPager.lSGallery img{
            margin: auto;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        .lSSlideOuter .lSPager.lSGallery li{
            position: relative;
        }
        .lSSlideOuter.vertical .lightSlider .lslide{
            position: relative;
        }
        .lSSlideOuter.vertical .lightSlider .lslide img{
            margin: auto;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            height: 100%;
        }
        .vertical .lightSlider .clone img {
            margin: auto;
            height: 100%;
        }
        {if $slideLayout == 'left'}
            .lSSlideOuter.vertical{
                padding-right: 0 !important;
                padding-left: {$sliderConfig->thumb_width|intval + 5}px !important;
            }
            .lSSlideOuter.vertical .lSGallery{
                right: auto;
                left: 0;
                margin-right: 5px !important;
                margin-left: 0 !important;
            }
        {/if}
    {/if}
</style>
<script type="text/javascript">
    var leoGalleryMagnify = {if $enableZoom}1{else}0{/if};
    var leoGalleryConfig = {
        {if $isMobile}
            thumbnail: {if isset($leo_use_thumb) && !$leo_use_thumb}false{elseif $config->m_thumbnails}true{else}false{/if},
            showThumbByDefault: {if $config->m_show_thumbs}true{else}false{/if},
            animateThumb: {if $config->m_animate_thumbs}true{else}false{/if},
        {else}
            thumbnail: {if isset($leo_use_thumb) && !$leo_use_thumb}false{elseif $config->thumbnails}true{else}false{/if},
            showThumbByDefault: {if $config->show_thumbs}true{else}false{/if},
            animateThumb: {if $config->animate_thumbs}true{else}false{/if},
        {/if}
        loop: {if $config->loop}true{else}false{/if},
        
        closable: {if $config->closable}true{else}false{/if},
        escKey: {if $config->esc_key}true{else}false{/if},
        keyPress: {if $config->key_press}true{else}false{/if},
        controls: {if $config->controls}true{else}false{/if},
        slideEndAnimatoin: {if $config->slide_end_animatoin}true{else}false{/if},
        hideControlOnEnd: {if $config->hide_control_on_end}true{else}false{/if},
        mousewheel: {if $config->mousewheel}true{else}false{/if},
        preload: {$config->mousewheel|intval},
        download: {if $config->download}true{else}false{/if},
        counter: {if $config->counter}true{else}false{/if},
        enableDrag: {if $config->enable_drag}true{else}false{/if},
        enableSwipe: {if $config->enable_swipe}true{else}false{/if},
        {if $config->autoplay}
            pause: {$config->pause|intval},
            progressBar: {if $config->progress}true{else}false{/if},
        {/if}
        thumbWidth: {$config->thumbWidht|intval},
        thumbContHeight: {$config->thumbHeight|intval + 20}
    };
    var leoGalleryVertical = {if ($slideMode == 'vertical')}true{else}false{/if};
    var leoGalleryZoomSL = false;
    {if ($zoomType == 'out' || $zoomType == 'in')}
        leoGalleryZoomSL = true;
        var leoGalleryZoomSLOptions = {
            container: '#leogallery-zoom',
            innerzoom: {if $zoomType == 'in'}true{else}false{/if},
            magnifierspeedanimate: 250,
            scrollspeedanimate: 4
        };
    {/if}
    var leoGallerySliderConfig = {
        enableDrag: false,
        {if ($slideMode == 'vertical')}
            prevHtml: '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-chevron-up fa-w-14 fa-3x"><path fill="currentColor" d="M4.465 366.475l7.07 7.071c4.686 4.686 12.284 4.686 16.971 0L224 178.053l195.494 195.493c4.686 4.686 12.284 4.686 16.971 0l7.07-7.071c4.686-4.686 4.686-12.284 0-16.97l-211.05-211.051c-4.686-4.686-12.284-4.686-16.971 0L4.465 349.505c-4.687 4.686-4.687 12.284 0 16.97z" class=""></path></svg>',
            nextHtml: '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-chevron-down fa-w-14 fa-3x"><path fill="currentColor" d="M443.5 162.6l-7.1-7.1c-4.7-4.7-12.3-4.7-17 0L224 351 28.5 155.5c-4.7-4.7-12.3-4.7-17 0l-7.1 7.1c-4.7 4.7-4.7 12.3 0 17l211 211.1c4.7 4.7 12.3 4.7 17 0l211-211.1c4.8-4.7 4.8-12.3.1-17z" class=""></path></svg>',
        {else}
            prevHtml: '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M238.475 475.535l7.071-7.07c4.686-4.686 4.686-12.284 0-16.971L50.053 256 245.546 60.506c4.686-4.686 4.686-12.284 0-16.971l-7.071-7.07c-4.686-4.686-12.284-4.686-16.97 0L10.454 247.515c-4.686 4.686-4.686 12.284 0 16.971l211.051 211.05c4.686 4.686 12.284 4.686 16.97-.001z" class=""></path></svg>',
            nextHtml: '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M17.525 36.465l-7.071 7.07c-4.686 4.686-4.686 12.284 0 16.971L205.947 256 10.454 451.494c-4.686 4.686-4.686 12.284 0 16.971l7.071 7.07c4.686 4.686 12.284 4.686 16.97 0l211.051-211.05c4.686-4.686 4.686-12.284 0-16.971L34.495 36.465c-4.686-4.687-12.284-4.687-16.97 0z" class=""></path></svg>',
        {/if}
        item: 1,
        loop: true,
        rtl: {if Context::getContext()->language->is_rtl}true{else}false{/if},
        {if ($slideMode == 'vertical')}
            vertical: true,
            verticalHeight: {$sliderConfig->vertical_height|intval},
            vThumbWidth: {$sliderConfig->thumb_width|intval},
        {/if}
        slideMargin: 0,
        thumbItem: {if isset($layout_from_profile) && $layout_from_profile}{$layout_from_profile.column}{else}{$sliderConfig->thumb_item|intval}{/if},
        item: 1,
        controls: {if $sliderConfig->controls}true{else}false{/if},
        gallery: {if isset($leo_use_thumb) && !$leo_use_thumb}false{elseif $sliderConfig->gallery}true{else}false{/if},
        pager: {if isset($leo_use_thumb) && !$leo_use_thumb}false{elseif $sliderConfig->gallery}true{else}false{/if},
        {*if $sliderConfig->breakdowns}
            responsive: [
                {$sliderConfig->breakdowns nofilter}
            ],
        {/if*}
        onAfterSlide: function(el) {
            leogallery.setCurrentPagerPos(el.getCurrentSlideCount()-2);
            if (leoGalleryMagnify && leoGalleryZoomSL){
                $('#lightSlider .lslide.active img').imagezoomsl(leoGalleryZoomSLOptions);
            }
        },
        onSliderLoad: function(el) {
            var config = Object.assign({
            }, leoGalleryConfig);
            config.selector = '#lightSlider .lslide';
            el.lightGallery(config);
            {if ($sliderConfig->thumb_controls && $slideMode == 'horizontal')}
                setTimeout(function(){
                    var $controls = $('<div>', {
                        class: 'leo-pager-controls'
                    });
                    var thumbHeight = $('.lSPager').height();
                    var btnTop = thumbHeight / 2 + 16;
                    var $button = $('<button>', {
                        class: 'leo-pager-prev',
                        type: 'button',
                        onclick: 'leogallery.scrollPager(-{$sliderConfig->thumb_controls_offset|intval})'
                    });
                    $button.css({
                        top: '-' + btnTop + 'px'
                    });
                    $controls.append($button);
                    var $button = $('<button>', {
                        class: 'leo-pager-next',
                        type: 'button',
                        onclick: 'leogallery.scrollPager({$sliderConfig->thumb_controls_offset|intval})'
                    });
                    $button.css({
                        top: '-' + btnTop + 'px'
                    });
                    $controls.append($button);
                    $('.lSSlideOuter').append($controls);
                    setTimeout(function(){
                        if ($('.lSPager>li').length > 5){
                            $controls.addClass('active');
                        }
                    }, 200);
                }, 800);
            {/if}
        }
    };
    function leoGalleryInit(){
        $('{$config->root|escape:'htmlall':'UTF-8'} {$config->selector|escape:'htmlall':'UTF-8'}').each(function(index){
            var img = $(this).find('img');
            $(img).parent().attr('data-src', img.attr('data-image-large-src'));
        });
        var leoGalleryMainImg = $('{$config->main_root|escape:'htmlall':'UTF-8'}').find('[data-toggle="modal"]');

        leoGalleryMainImg.removeAttr('data-toggle').removeAttr('data-target').addClass('leo-main-container');
        $(document).on('click', '.leo-main-container', function(){
            $('{$config->root|escape:'htmlall':'UTF-8'} .{$config->current_class|escape:'htmlall':'UTF-8'}').trigger('click');
        });
        $(document).on('click', '.js-qv-product-cover', function(){
            $('{$config->root|escape:'htmlall':'UTF-8'} .{$config->current_class|escape:'htmlall':'UTF-8'}').trigger('click');
        });
        {if $sliderConfig->enabled == false}
            $(document).on('click', '.magnify', function(){
                $('{$config->root|escape:'htmlall':'UTF-8'} .{$config->current_class|escape:'htmlall':'UTF-8'}').trigger('click');
            });
        {/if}
        $('{$config->root|escape:'htmlall':'UTF-8'} {$config->selector|escape:'htmlall':'UTF-8'}').hover(function(){
            $('{$config->root|escape:'htmlall':'UTF-8'} {$config->selector|escape:'htmlall':'UTF-8'} .{$config->current_class|escape:'htmlall':'UTF-8'}').removeClass('{$config->current_class|escape:'htmlall':'UTF-8'}');
            $(this).find('img').addClass('{$config->current_class|escape:'htmlall':'UTF-8'}');
            var imgSrc = $(this).find('img').attr('data-image-large-src');
            $('.leo-main-container').parent().find('{$config->img_selector|escape:'htmlall':'UTF-8'}').attr('src', imgSrc).attr('data-magnify-src', imgSrc);
            if (leoGalleryMagnify){
                {if ($zoomType == 'out' || $zoomType == 'in')}
                    $('{$config->main_root|escape:'htmlall':'UTF-8'} {$config->img_selector|escape:'htmlall':'UTF-8'}').imagezoomsl(leoGalleryZoomSLOptions);
                {else}
                    $('{$config->main_root|escape:'htmlall':'UTF-8'} {$config->img_selector|escape:'htmlall':'UTF-8'}').magnify();
                {/if}
            }
        });
        if (leoGalleryMagnify){
            leoGalleryMainImg.hide();
            $('{$config->main_root|escape:'htmlall':'UTF-8'} {$config->img_selector|escape:'htmlall':'UTF-8'}').attr('data-magnify-src', $('{$config->main_root|escape:'htmlall':'UTF-8'} {$config->img_selector|escape:'htmlall':'UTF-8'}').attr('src'));
            {if ($zoomType == 'out' || $zoomType == 'in')}
                $('{$config->main_root|escape:'htmlall':'UTF-8'} {$config->img_selector|escape:'htmlall':'UTF-8'}').imagezoomsl(leoGalleryZoomSLOptions);
            {else}
                $('{$config->main_root|escape:'htmlall':'UTF-8'} {$config->img_selector|escape:'htmlall':'UTF-8'}').magnify();
            {/if}
            
        }

        $('{$config->root|escape:'htmlall':'UTF-8'} .fancybox').removeClass('fancybox');
        {if $sliderConfig->enabled == false}
            {if $config->selector}
                leoGalleryConfig.selector = '{$config->selector|escape:'htmlall':'UTF-8'}';
            {/if}
            leoGalleryConfig.selectWithin = '{$config->root|escape:'htmlall':'UTF-8'}';
            $('{$config->root|escape:'htmlall':'UTF-8'}').lightGallery(leoGalleryConfig);
        {/if}
    };
    {if !$quickview}
        window.addEventListener('load', function(){
            $('body').on('click', '.tracker', function(){
                $('.lightSlider .lslide.active').click();
            });

            prestashop.on('updatedProduct', function(resp){
                leoGalleryInit();
            });
            leoGalleryInit();
        });
    {else}
        $('body').on('click', '.tracker', function(){
            $('.lightSlider .lslide.active').click();
        });

        prestashop.on('updatedProduct', function(resp){
            leoGalleryInit();
        });
        leoGalleryInit();
    {/if}
</script>