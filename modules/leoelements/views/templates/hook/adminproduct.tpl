{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: leoelements is module help you can build content for your shop
*}
<div class="col-md-12">
    <div class="form-group">
        <h2>{l s='Layout Type' mod='leoelements'}</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-control-label">{l s='Desktop Layout:' mod='leoelements'}</label>
                    <select id="leoe_layout" name="leoe_layout" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">
                        <option value="default">{l s='Global Layout' mod='leoelements'}</option>
                        {foreach $product_layouts as $aplayout}
                        <option value="{$aplayout.plist_key}" {if $aplayout.plist_key == $current_layout}selected="selected"{/if}>{$aplayout.name}</option>
                        {/foreach}
                    </select>
                </div>
                
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-control-label">{l s='Mobile Layout:' mod='leoelements'}</label>
                    <select id="leoe_layout_mobile" name="leoe_layout_mobile" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">
                        <option value="default">{l s='Global Layout' mod='leoelements'}</option>
                        {foreach $product_layouts as $aplayout}
                        <option value="{$aplayout.plist_key}" {if $aplayout.plist_key == $current_layout_mobile}selected="selected"{/if}>{$aplayout.name}</option>
                        {/foreach}
                    </select>
                </div>
                
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-control-label">{l s='Tablet Layout:' mod='leoelements'}</label>
                    <select id="leoe_layout_tablet" name="leoe_layout_tablet" class="form-control select2-hidden-accessible" data-toggle="select2" tabindex="-1" aria-hidden="true">
                        <option value="default">{l s='Global Layout' mod='leoelements'}</option>
                        {foreach $product_layouts as $aplayout}
                        <option value="{$aplayout.plist_key}" {if $aplayout.plist_key == $current_layout_table}selected="selected"{/if}>{$aplayout.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        <div class="row alert alert-info" role="alert">
            <i class="material-icons">help</i>
            <p class="alert-text">
              1. {l s='Please select layout to show product' mod='leoelements'}<br/>
              2. {l s='Then you can use variable' mod='leoelements'}<br/>
              $product.productLayout<br/>
              3. {l s='To select product layout in file product.tpl' mod='leoelements'}<br/>
              <br>
            </p>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-12 col-xl-12 translations tabbable">
                <label class="form-control-label">{l s='leoe_extra_1' mod='leoelements'}</label>
                <div class="translationsFields editorwys tab-content bordered">
                    {foreach from=$languages item=language }
                    <div class="translation-field tab-pane translation-label-{$language.iso_code} {if $default_language == $language.id_lang}active{/if}">
                        <textarea class="autoload_rte" name="leoe_extra_1[{$language.id_lang}]">{if isset({$leoe_extra_1[$language.id_lang]}) && {$leoe_extra_1[$language.id_lang]} != ''}{$leoe_extra_1[$language.id_lang]}{/if}</textarea>
                    </div>{/foreach}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-lg-12 col-xl-12 translations tabbable">
                <label class="form-control-label">{l s='leoe_extra_2' mod='leoelements'}</label>
                <div class="translationsFields editorwys tab-content bordered">
                    {foreach from=$languages item=language }
                    <div class="translation-field tab-pane translation-label-{$language.iso_code} {if $default_language == $language.id_lang}active{/if}">
                        <textarea class="autoload_rte" name="leoe_extra_2[{$language.id_lang}]">{if isset({$leoe_extra_2[$language.id_lang]}) && {$leoe_extra_2[$language.id_lang]} != ''}{$leoe_extra_2[$language.id_lang]}{/if}</textarea>
                    </div>{/foreach}
                </div>
            </div>
        </div>
    </div>
</div>