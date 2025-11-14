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
    <div class="block block_carousel exclusive LeoBlockCarousel elementor-slick-slider {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}">
        {($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
        <div class="block_content">
            {if !empty($formAtts.slides)}
                {if $formAtts.carousel_type == "slickcarousel"}
                    {include file=$leo_include_file}
                {else}
                    {if $formAtts.carousel_type == 'boostrap'}
                        {assign var=leo_include_file value=$leo_helper->getTplTemplate('ApBlockCarouselItem.tpl', $formAtts['override_folder'])}
                        {include file=$leo_include_file}
                    {else}
                        {assign var=leo_include_file value=$leo_helper->getTplTemplate('ApBlockOwlCarouselItem.tpl', $formAtts['override_folder'])}
                        {include file=$leo_include_file}
                    {/if}
                {/if}
            {else}
                <p class="alert alert-info">{l s='No slide at this time.' mod='leoelements'}</p>
            {/if}
        </div>
        {($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
    </div>
{/if}
