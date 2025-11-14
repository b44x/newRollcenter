{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{if isset($formAtts.lib_has_error) && $formAtts.lib_has_error}
    {if isset($formAtts.lib_error) && $formAtts.lib_error}
        <div class="alert alert-warning leo-lib-error">{$formAtts.lib_error}</div>
    {/if}
{else}
    {if !empty($manufacturers)}
		<div class="slick-row">
			<div class="slick-manufacturers slick-slider slick-loading" id="{$formAtts.form_id|escape:'html':'UTF-8'}">
			{foreach from=$manufacturers item=manu name=mypLoop}
                            <div class="slick-slide {if $smarty.foreach.mypLoop.index == 0} first{/if}">
                                <div class="item">
                                        <div class="manufacturer-container manufacturer-block">
		    				<div class="left-block">
		    					<div class="manufacturer-image-container image">
                                                            <a title="{$manu.name}" href="{$link->getmanufacturerLink($manu.id_manufacturer, $manu.link_rewrite)|escape:'html':'UTF-8'}" ><p class="name-manufacturer">{$manu.name}</p>
                                                                <img class="img-fluid" 
                                                                    src="{$img_manu_dir}{$manu.id_manufacturer|intval}-{$formAtts.imagetype}.jpg"
                                                                    alt="{$manu.name|escape:'html':'UTF-8'}"/>
                                                            </a>
		    					</div>
		    				</div>
		    			</div>
		    		</div>
                            </div>
			{/foreach}
			</div>
		</div>
                        
                        
                  
                        
                        
                        
<script type="text/javascript" class="autojs" data-form_id="{$formAtts.form_id}">
    var {$formAtts.form_id} = (function(){
	$('#{$formAtts.form_id|escape:'html':'UTF-8'}').imagesLoaded( function() {
		$('#{$formAtts.form_id|escape:'html':'UTF-8'}').slick(
			{if $formAtts.slick_custom_status}
				{$formAtts.slick_custom}
			{else}
			{
				centerMode: {if isset($formAtts.slick_centermode) && $formAtts.slick_centermode}true{else}false{/if},
				centerPadding: '{if isset($formAtts.slick_centerpadding) && $formAtts.slick_centerpadding}{$formAtts.slick_centerpadding}{else}0{/if}px',
				dots: {if $formAtts.slick_dot}true{else}false{/if},
				infinite: {if isset($formAtts.infinite) && $formAtts.infinite}true{else}false{/if},
				vertical: {if $formAtts.slick_vertical}true{else}false{/if},
				verticalSwiping : {if $formAtts.slick_vertical}true{else}false{/if},
				autoplay: {if $formAtts.autoplay}true{else}false{/if},
				pauseonhover: {if $formAtts.pause_on_hover}true{else}false{/if},
				autoplaySpeed: {if isset($formAtts.autoplay_speed) && $formAtts.autoplay_speed}{$formAtts.autoplay_speed}{else}5000{/if},
                                speed: {if isset($formAtts.speed) && $formAtts.speed}{$formAtts.speed}{else}500{/if},
				arrows: {if $formAtts.slick_arrows}true{else}false{/if},
				rows: {$formAtts.per_col},
				slidesToShow: {$formAtts.slides_to_show},
				slidesToScroll: {if $formAtts.slick_dot}{$formAtts.slides_to_show}{else}{$formAtts.slides_to_show}{/if},
				rtl: {if isset($IS_RTL) && $IS_RTL}true{else}false{/if},
                                responsive: [{
                                    breakpoint: 1025,
                                    settings: {
                                        slidesToShow: {$formAtts.slides_to_show_tablet},
                                        slidesToScroll: {$formAtts.slides_to_scroll_tablet}
                                    }
                                }, {
                                    breakpoint: 768,
                                    settings: {
                                        slidesToShow: {$formAtts.slides_to_show_mobile},
                                        slidesToScroll: {$formAtts.slides_to_scroll_mobile}
                                    }
                                }]
			}
			{/if}
		);
		$('#{$formAtts.form_id|escape:'html':'UTF-8'}').removeClass('slick-loading').addClass('slick-loaded').parents('.slick-row').addClass('hide-loading');
	});
    });
    
</script>




    {else}
        <p class="alert alert-info">{l s='No manufacturer at this time.' mod='leoelements'}</p>
    {/if}
{/if}
