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
    <div class="block manufacturers_block exclusive elementor-slick-slider {(isset($formAtts.class)) ? $formAtts.class : ''|escape:'html':'UTF-8'}">
        {($apLiveEdit)?$apLiveEdit:'' nofilter}{* HTML form , no escape necessary *}
        {if isset($formAtts.title)&&!empty($formAtts.title)}
        <h4 class="title_block">
            {$formAtts.title|escape:'html':'UTF-8'}
        </h4>
        {/if}
        {if isset($formAtts.sub_title) && $formAtts.sub_title}
            <div class="sub-title-widget">{$formAtts.sub_title nofilter}</div>
        {/if}
        <div class="block_content">
            {if !empty($manufacturers)}
                {if $formAtts.carousel_type == "slickcarousel"}
                    {include file=$leo_include_file} {* LeoManufacturersCarouselSlick.tpl *}
                {/if}
            {else}
                <p class="alert alert-info">{l s='No manufacturer at this time.' mod='leoelements'}</p>
            {/if}
        </div>
        {($apLiveEditEnd)?$apLiveEditEnd:'' nofilter}{* HTML form , no escape necessary *}
    </div>
{/if}