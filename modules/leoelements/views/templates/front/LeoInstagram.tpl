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
    
    {if !isset($formAtts.accordion_type) || $formAtts.accordion_type == 'full'}
        <div class="block {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} instagram-block">
            {($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
            {if isset($formAtts.show_title) && $formAtts.show_title && isset($formAtts.title) && $formAtts.title}
            <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
            {/if}
            {if isset($formAtts.show_sub_title) && $formAtts.show_sub_title && isset($formAtts.sub_title) && $formAtts.sub_title}
                <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
            {/if}

            <div class="block_content elementor-slick-slider">
                <div id="instafeed" {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "list"}class='normal-list'{/if}></div>
                {if isset($formAtts.show_view_all) && $formAtts.show_view_all && isset($formAtts.profile_link) && $formAtts.profile_link}
                <div class="instagram-viewall">
                    <a class="btn-viewall" href="https://instagram.com/{$formAtts.profile_link|escape:'html':'UTF-8'}" target="_blank" title="{l s='View all in instagram' mod='leoelements'}">
                        <i class="fa fa-instagram"></i>&nbsp;&nbsp;{l s='View all in instagram' mod='leoelements'}
                    </a>
                </div>
                {/if}
            </div>

            {($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
        </div>
    {elseif isset($formAtts.accordion_type) && ($formAtts.accordion_type == 'accordion' || $formAtts.accordion_type == 'accordion_small_screen')}
        
        <div class="block {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'} instagram-block block-toggler{if $formAtts.accordion_type == 'accordion_small_screen'} accordion_small_screen{/if}">
            {($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
            {if isset($formAtts.show_title) && $formAtts.show_title && isset($formAtts.title) && $formAtts.title}
                <div class="title clearfix" data-target="#widget-instagram-{$formAtts.form_id|escape:'html':'UTF-8'}" data-toggle="collapse">
                    <h4 class="title_block">{$formAtts.title|escape:'html':'UTF-8'}</h4>
                    <span class="float-xs-right">
                      <span class="navbar-toggler collapse-icons">
                        <i class="material-icons add">&#xE313;</i>
                        <i class="material-icons remove">&#xE316;</i>
                      </span>
                    </span>
                </div>
            {/if}
            {if isset($formAtts.show_sub_title) && $formAtts.show_sub_title && isset($formAtts.sub_title) && $formAtts.sub_title}
                <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
            {/if}
            <div class="collapse block_content elementor-slick-slider" id="widget-instagram-{$formAtts.form_id|escape:'html':'UTF-8'}">
                <div id="instafeed" {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "list"}class='normal-list'{/if}></div>
                {if isset($formAtts.show_view_all) && $formAtts.show_view_all && isset($formAtts.profile_link) && $formAtts.profile_link}
                <div class="instagram-viewall">
                    <a class="btn-viewall" href="https://instagram.com/{$formAtts.profile_link|escape:'html':'UTF-8'}" target="_blank" title="{l s='View all in instagram' mod='leoelements'}">
                        <i class="fa fa-instagram"></i>&nbsp;&nbsp;{l s='View all in instagram' mod='leoelements'}
                    </a>
                </div>
                {/if}
            </div>
            {($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
        </div>

    {/if}
    
<script type="text/javascript" class="autojs" data-form_id="{$formAtts.form_id}">
    var instafeed_start = 0;
    var {$formAtts.form_id} = (function(){
        if(instafeed_start != 1){
            instafeed_start = 1;
            leo_create_instafeed();
        }
    });
    
    var leo_create_instafeed = function() {
	var feed = new Instafeed({
        {if isset($formAtts.access_token) && $formAtts.access_token}
            accessToken: "{$formAtts.access_token|escape:'html':'UTF-8'}",
        {/if}
        {if isset($formAtts.limit) && $formAtts.limit}
            limit: {$formAtts.limit|intval},
        {/if}

        {if isset($formAtts.carousel_type) && $formAtts.carousel_type == "list"}
        {literal}
                template: '<div class="col-sp-12 col-xs-6 col-sm-6 col-md-6 col-lg-4 col-xl-4"><a class="leo-instagram-size" target="_blank" href="{{link}}"><img title="{{caption}}" src="{{image}}"/></a></div>',
        {/literal}
        {else}
        {literal}
                template: '<a class="leo-instagram-size" target="_blank" href="{{link}}"><img title="{{caption}}" src="{{image}}"/></a>',
        {/literal}
        {/if}

	transform: function(item) {
		var d = new Date(item.timestamp);
		item.date = [d.getDate(), d.getMonth(), d.getYear()].join('/');
		return item;
	},

	{if isset($formAtts.carousel_type) && $formAtts.carousel_type !== "list"}
		after: function() {
		{if isset($formAtts) && isset($formAtts.lazyload) && $formAtts.lazyload}
			{* ENABLE LAZY LOAD OWL_CAROUSEL *}
			var html = $("#instafeed").html();
			html = html.replace(/ src="/g,' class="lazyOwl" data-src="');
			$("#instafeed").html(html);
		{/if}
                        
		{if $formAtts.carousel_type == "boostrap"}

		{else}

			$('#instafeed').imagesLoaded( function() {
                            
                                $('#instafeed').slick(
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
                                
                                
                        
                        
                        
			});
			function OwlLoaded(el){
				el.removeClass('owl-loading').addClass('owl-loaded').parents('.owl-row').addClass('hide-loading');
				if ($(el).parents('.tab-pane').length && !$(el).parents('.tab-pane').hasClass('active'))
					el.width('100%');
			};
		{/if}
		}
	{/if}
	});
	feed.run();
    }

    var array_chunk = function(arr, chunkSize) {
            var groups = [], i;
            for (i = 0; i < arr.length; i += chunkSize) {
                    groups.push(arr.slice(i, i + chunkSize));
            }
            return groups;
    }

    var leo_create_show = function() {
            $('#instafeed').fadeIn();
    }
</script>
{/if}