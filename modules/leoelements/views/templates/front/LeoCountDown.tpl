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
    {if isset($formAtts.active) && $formAtts.active == 1}
        <div  id="countdown-{$formAtts.form_id|escape:'html':'UTF-8'}" class="{(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}">

            {if isset($formAtts.title) && !empty($formAtts.title)}
                <h4 class="title_block">
                    {$formAtts.title nofilter}{* HTML form , no escape necessary *}
                </h4>
            {/if}
            {if isset($formAtts.sub_title) && $formAtts.sub_title}
                <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
            {/if}
            {if isset($formAtts.description) && !empty($formAtts.description)}
                <div class="description-widget">{$formAtts.description nofilter}{* HTML form , no escape necessary *}</div>
            {/if}


            <ul class="leocountdown-time deal-clock lof-clock-11-detail list-inline"></ul>

            <p class="ap-countdown-link">
                {if isset($formAtts.link) && $formAtts.link.url}
                    {if isset($formAtts.link.is_external) && $formAtts.link.is_external == 'on'}
                        <a href="{$formAtts.link.url|escape:'html':'UTF-8'}" target="_blank">{$formAtts.link_label|escape:'html':'UTF-8'}</a>
                    {/if}	
                    {if isset($formAtts.link.is_external) && $formAtts.link.is_external !== 'on'}
                        <a href="{$formAtts.link.url|escape:'html':'UTF-8'}">{$formAtts.link_label|escape:'html':'UTF-8'}</a>
                    {/if}			
                {/if}
            </p>
        </div>

<script type="text/javascript" class="autojs" data-form_id="{$formAtts.form_id}">
    var {$formAtts.form_id} = (function(){
        if($().lofCountDown) {
            var text_d = '{l s='days' mod='leoelements'}';
            var text_h = '{l s='hours' mod='leoelements'}';
            var text_m = '{l s='min' mod='leoelements'}';
            var text_s = '{l s='sec' mod='leoelements'}';
            $(".lof-clock-11-detail").lofCountDown({
                TargetDate:"{$formAtts.time_to|escape:'html':'UTF-8'}",
                DisplayFormat:  ''
                            {if isset($formAtts.show_day) && $formAtts.show_day}        + '<li class="z-depth-1 show_day"><span class="lcd_number">%%D%%</span><span class="lcd_text">'+text_d+'</span></li>'{/if}
                            {if isset($formAtts.show_hour) && $formAtts.show_hour}      + '<li class="z-depth-1 show_hour"><span class="lcd_number">%%H%%</span><span class="lcd_text">'+text_h+'</span></li>'{/if}
                            {if isset($formAtts.show_minute) && $formAtts.show_minute}  + '<li class="z-depth-1 show_minute"><span class="lcd_number">%%M%%</span><span class="lcd_text">'+text_m+'</span></li>'{/if}
                            {if isset($formAtts.show_second) && $formAtts.show_second}  + '<li class="z-depth-1 show_second"><span class="lcd_number">%%S%%</span><span class="lcd_text">'+text_s+'</span></li>'{/if}
                        ,
            });
        }
    });
</script>
    {/if}
{/if}