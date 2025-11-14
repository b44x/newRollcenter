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

<style type="text/css">
    #leo-config-tabs{
        opacity: 0;
        transition: 0.2s all;
    }
    #leo-config-tabs.active{
        opacity: 1;
    }
    #leo-config .form-group .form-group{
        margin-bottom: 0;
    }
    #leo-config .form-group .form-group .color{
        margin-left: 5px;
    }
    .field_fullscreen, .field_loop, .field_key_press, .field_controls, .field_autoplay, .field_hash, .field_thumbnails, .field_bg_color, .field_m_thumbnails{
        border-top: 1px solid #eeeeee;
        padding-top: 15px;
    }
    #leo-slider .tab-content .panel-heading{
        display: none;
    }
    #leo-slider .tab-content .panel{
        border-radius: 0 5px 5px 5px;
    }
</style>
<div class="row" id="leo-config">
    <div class="col-lg-2 col-md-3">
        <div class="list-group leoTabs">
            <a class="list-group-item {if empty($active_tab) or $active_tab === 'LeoGalleryConfigForm'}active{/if}" data-tab="1" id="leo-tab-1" data-target="leo-gallery" href="#">
                <i class="icon-cog"></i> {l s='Gallery settings' mod='leogallery'}
            </a>
            <a class="list-group-item {if $active_tab === 'LeoGallerySliderConfigForm' || $active_tab === 'LeoGallerySliderMobileConfigForm'}active{/if}" data-tab="2" id="leo-tab-2" data-target="leo-slider" href="#">
                <i class="icon-cog"></i> {l s='Slider settings' mod='leogallery'}
            </a>
            <a class="list-group-item {if $active_tab === 'LeoGalleryMagnifyConfigForm'}active{/if}" data-tab="3" id="leo-tab-3" data-target="leo-magnify" href="#">
                <i class="icon-cog"></i> {l s='Magnifier settings' mod='leogallery'}
            </a>
        </div>
    </div>
    <div class="col-lg-10 col-md-9" id="leo-config-tabs">
        {include file="./_partials/_gallery.tpl"}
        {include file="./_partials/_slider.tpl"}
        {include file="./_partials/_magnify.tpl"}
    </div>
</div>

<script type="text/javascript">
    window.addEventListener('load', function(){
        $(".leoTabs a").click(function(e){
            e.preventDefault();
            $(".leoTabs .active").removeClass('active');
            $(this).addClass('active');
            $('#leo-config .leo-config-panel').addClass('hidden');
            $('#' + $(this).data('target')).removeClass('hidden');
            $('#leoActiveTab').remove();
            $('#leoActiveTab').val($(this).data('tab'));
        });
        switchFields();
        switchMagnifierType();
        switchSliderLayout();
        switchSliderTabletLayout();
        switchSliderMobileLayout();

        toggleLoopOptions();
        toggleKeyboardOptions();
        toggleAutoplayOptions();
        toggleThumbsOptions();
        $('#LEOGALLERY_SLIDE_MODE').change(function(){
            switchSliderLayout();
        });
        $('#LEOGALLERY_SLIDET_MODE').change(function(){
            switchSliderTabletLayout();
        });
        $('#LEOGALLERY_SLIDEM_MODE').change(function(){
            switchSliderMobileLayout();
        });
        $('#LEOGALLERY_MAGNIFY_TYPE').change(function(){
            switchMagnifierType();
        });
        $('[name="LEOGALLERY_IMG_CAPTIONS"]').parent().click(function(){
            switchFields();
        });
        
        $('.field_loop .switch').click(function(){
            toggleLoopOptions();
        });
        
        $('.field_thumbnails .switch').click(function(){
            toggleThumbsOptions();
        });
        
        $('.field_key_press .switch').click(function(){
            toggleKeyboardOptions();
        });
        
        $('.field_autoplay .switch').click(function(){
            toggleAutoplayOptions();
        });
        
        $('#leo-config-tabs').addClass('active');
    });
    
    function switchSliderLayout(){
        if ($('#LEOGALLERY_SLIDE_MODE').val() == 'vertical') {
            $('#leo-slider-desktop .field_vertical_height, #leo-slider-desktop .field_thumb_width, #leo-slider-desktop .field_layout').removeClass('hidden');
            $('#leo-slider-desktop .field_thumb_controls').addClass('hidden');
        } else {
            $('#leo-slider-desktop .field_vertical_height, #leo-slider-desktop .field_thumb_width, #leo-slider-desktop .field_layout').addClass('hidden');
            $('#leo-slider-desktop .field_thumb_controls').removeClass('hidden');
        }
    }
    
    function switchSliderTabletLayout(){
        if ($('#LEOGALLERY_SLIDET_MODE').val() == 'vertical') {
            $('#leo-slider-tablet .field_vertical_height, #leo-slider-tablet .field_thumb_width, #leo-slider-tablet .field_layout').removeClass('hidden');
            $('#leo-slider-tablet .field_thumb_controls').addClass('hidden');
        } else {
            $('#leo-slider-tablet .field_vertical_height, #leo-slider-tablet .field_thumb_width, #leo-slider-tablet .field_layout').addClass('hidden');
            $('#leo-slider-tablet .field_thumb_controls').removeClass('hidden');
        }
    }

    function switchSliderMobileLayout(){
        if ($('#LEOGALLERY_SLIDEM_MODE').val() == 'vertical') {
            $('#leo-slider-mobile .field_vertical_height, #leo-slider-mobile .field_thumb_width, #leo-slider-mobile .field_layout').removeClass('hidden');
            $('#leo-slider-mobile .field_thumb_controls').addClass('hidden');
        } else {
            $('#leo-slider-mobile .field_vertical_height, #leo-slider-mobile .field_thumb_width, #leo-slider-mobile .field_layout').addClass('hidden');
            $('#leo-slider-mobile .field_thumb_controls').removeClass('hidden');
        }
    }
    
    function switchMagnifierType(){
        if ($('#LEOGALLERY_MAGNIFY_TYPE').val() == 'lens') {
            $('#leo-magnify .field_width, #leo-magnify .field_height, #leo-magnify .field_fader, #leo-magnify .field_fader_opacity').removeClass('hidden');
        } else {
            $('#leo-magnify .field_width, #leo-magnify .field_height, #leo-magnify .field_fader, #leo-magnify .field_fader_opacity').addClass('hidden');
        }
    }
    function switchFields(){
        if($('[name="LEOGALLERY_IMG_CAPTIONS"').parent().find('input:first-of-type:checked').length){
            $('.field_img_captions_offset').show();
        }else{
            $('.field_img_captions_offset').hide();
        }
    }
    
    function toggleThumbsOptions(){
        if ($('#LEOGALLERY_THUMBNAILS_on').is(':checked')){
            $('.field_animate_thumbs, .field_show_thumbs').removeClass('hidden');
        }else{
            $('.field_animate_thumbs, .field_show_thumbs').addClass('hidden');
        }
    }
    
    function toggleAutoplayOptions(){
        if ($('#LEOGALLERY_AUTOPLAY_on').is(':checked')){
            $('.field_pause, .field_progress').removeClass('hidden');
        }else{
            $('.field_pause, .field_progress').addClass('hidden');
        }
    }
    
    function toggleKeyboardOptions(){
        if ($('#LEOGALLERY_KEY_PRESS_on').is(':checked')){
            $('.field_esc_key').removeClass('hidden');
        }else{
            $('.field_esc_key').addClass('hidden');
        }
    }
    
    function toggleLoopOptions(){
        if ($('#LEOGALLERY_LOOP_on').is(':checked')){
            $('.field_slide_end_animatoin, .field_hide_control_on_end').addClass('hidden');
        }else{
            $('.field_slide_end_animatoin, .field_hide_control_on_end').removeClass('hidden');
        }
    }
    
</script>