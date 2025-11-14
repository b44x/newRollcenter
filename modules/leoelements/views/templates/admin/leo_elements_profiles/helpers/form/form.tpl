{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'product_list_data'}
        <input type="hidden" data-title="{$input.title}" name="{$input.name}" value='{$input.product_list}' id="{$input.id}" data-url="{$input.url}">
    {/if}
    {if $input.type == 'chose_image'}
        <div class="col-lg-8">
            <p> 
                <input id="{$input.name}" type="text" name="{$input.name}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}"> 
            </p>
            <span>{l s='Put image with url https or' mod='leoelements'}</span>
            <a href="filemanager/dialog.php?type=1&field_id={$input.name}" class="btn btn-default img-upload"  data-input-name="{$input.name}" type="button">
                {l s='Select image' mod='leoelements'} <i class="icon-angle-right"></i>
            </a>

        </div>
    {/if}
    {if $input.type == 'mutiple_position_hook'}
        {if $input.is_edit}
        <div class="col-lg-12">
            {foreach $input.content as $content}
            <div class="panel panel-default panel-{$input.name} {$content.position_key}">
                <div class="panel-collapse collapse in">
                    <p>{l s='Please select content for each hook.' mod='leoelements'}</p>
                    {foreach $input.hook_list as $hookl}
                    <h2>{$hookl}</h2>
                    <div class="row">
                        <div class="col-lg-6 loading-wraper">
                            <select data-hook="{$hookl}" name="{$hookl}_{$content.position_key}" data-position="{$content.position_key}" class="select-hook-position" data-title="{$content.name}">
                                <option value="0">{l s='None' mod='leoelements'}</option>
                                {if isset($input.leoelements_contents_hook[$hookl])}
                                    {foreach $input.leoelements_contents_hook[$hookl] as $hookc}
                                    <option data-url="{$hookc.url}"
                                    {if isset($content.params[$hookl]) && $content.params[$hookl] == $hookc.content_key}
                                        selected = "selected"
                                    {/if}
                                    value="{$hookc.content_key}">{$hookc.name}</option>
                                    {/foreach}
                                {/if}
                                <option value="createnew">{l s='Create New' mod='leoelements'}</option>
                            </select>

                        </div>
                        <div class="col-lg-6 hook-button" style="display:none">
                            <a href="" target="_blank" class="button button-primary button-hero edit_with_button_link"><img src="{$input.icon_url}" alt="Leo Elements Logo"> {l s='Edit with Leo Elements' mod='leoelements'}</a>
                        </div>
                    </div>
                    {/foreach}
                </div>   
            </div>
            {/foreach}

            <div class="panel panel-postion-content panel-{$input.name}-new" style="display:none">
                <h2>
                    {l s='Please save the profile before using.' mod='leoelements'}
                </h2>
            </div>
        </div>
        {else}
        <div class="col-lg-12">
        <div class="panel">
            <h2>
                {l s='Please save the profile before using' mod='leoelements'}
            </h2>
        </div>
        </div>
        {/if}
    {/if}
    {if $input.type == 'position_hook'}
        {if $input.is_edit}
        <div class="col-lg-12">
            <div class="panel panel-default panel-{$input.name}">
                <div id="collapse{$input.name}" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <p>{l s='Please select content for each hook.' mod='leoelements'}</p>
                        {foreach $input.hook_list as $hookl}
                            <h2>{$hookl}</h2>

                            <div class="row">
                                <div class="col-lg-6 loading-wraper">
                                    <select data-hook="{$hookl}" name="{$hookl}" data-position="" class="select-hook-position">
                                        <option value="0">{l s='None' mod='leoelements'}</option>
                                        {if isset($input.leoelements_contents_hook[$hookl])}
                                            {foreach $input.leoelements_contents_hook[$hookl] as $hookc}
                                            <option data-url="{$hookc.url}"
                                            {if isset($fields_value[$input.name])}
                                                {foreach $fields_value[$input.name] as $hookname => $hookval}
                                                    {if ($hookname == $hookl) && $hookc.content_key == $hookval}
                                                    selected = "selected"
                                                    {/if}
                                                {/foreach}
                                            {/if}
                                            value="{$hookc.content_key}">{$hookc.name}</option>
                                            {/foreach}
                                        {/if}
                                        <option value="createnew">{l s='Create New' mod='leoelements'}</option>
                                    </select>

                                </div>
                                <div class="col-lg-6 hook-button" style="display:none">
                                    <a href="" target="_blank" class="button button-primary button-hero edit_with_button_link"><img src="{$input.icon_url}" alt="Leo Elements Logo"> {l s='Edit with Leo Elements' mod='leoelements'}</a>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
            <div class="panel panel-{$input.name}-new" style="display:none">
                <h2>
                    {l s='Please save the profile before using.' mod='leoelements'}
                </h2>
            </div>
        </div>
        {else}
        <div class="col-lg-12">
        <div class="panel">
            <h2>
                {l s='Please save the profile before using' mod='leoelements'}
            </h2>
        </div>
        </div>
        {/if}
    {/if}
    {if $input.type == 'font_h'}
        <div>
            <div class="col-lg-3">
                <h2>{$input.htitle}</h2>
                <p class="help-block">{$input.desc}</p>
            </div>
            <div class="col-lg-9">
                {foreach $input.items as $ikey => $item}
                    
                    {if ($item.type == 'select')}
                        <div class="t_span4 col-lg-4">
                        {if isset($item.label) }<h4 class="title-item">{$item.label}</h4>{/if}
                        <select name="{$item.name|escape:'html':'UTF-8'}"
                                class="{if isset($item.class)}{$item.class|escape:'html':'UTF-8'}{/if}"
                                id="{if isset($item.id)}{$item.id|escape:'html':'UTF-8'}{else}{$item.name|escape:'html':'UTF-8'}{/if}"
                                {if isset($item.multiple) && $item.multiple} multiple="multiple"{/if}
                                {if isset($item.size)} size="{$item.size|escape:'html':'UTF-8'}"{/if}
                                {if isset($item.onchange)} onchange="{$item.onchange|escape:'html':'UTF-8'}"{/if}
                                {if isset($item.disabled) && $item.disabled} disabled="disabled"{/if}>
                            {if isset($item.options.default)}
                                <option value="{$item.options.default.value|escape:'html':'UTF-8'}">{$item.options.default.label|escape:'html':'UTF-8'}</option>
                            {/if}
                            {if isset($item.options.optiongroup)}
                                {foreach $item.options.optiongroup.query AS $optiongroup}
                                    <optgroup label="{$optiongroup[$item.options.optiongroup.label]}">
                                        {foreach $optiongroup[$item.options.options.query] as $option}
                                            <option value="{$option[$item.options.options.id]}"
                                                {if isset($item.multiple)}
                                                    {foreach $fields_value[$input.name][$item.name] as $field_value}
                                                        {if $field_value == $option[$item.options.options.id]}selected="selected"{/if}
                                                    {/foreach}
                                                {else}
                                                    {if isset($fields_value[$input.name]) && isset($fields_value[$input.name][$item.name]) && $fields_value[$input.name][$item.name] == $option[$item.options.options.id]}selected="selected"{/if}
                                                {/if}
                                            >{$option[$item.options.options.name]}</option>
                                        {/foreach}
                                    </optgroup>
                                {/foreach}
                            {else}
                                {foreach $item.options.query AS $option}
                                    {if is_object($option)}
                                        <option value="{$option->$item.options.id}"
                                            {if isset($item.multiple)}
                                                {foreach $fields_value[$input.name][$item.name] as $field_value}
                                                    {if $field_value == $option->$item.options.id}
                                                        selected="selected"
                                                    {/if}
                                                {/foreach}
                                            {else}
                                                {if isset($fields_value[$input.name]) && isset($fields_value[$input.name][$item.name]) && $fields_value[$input.name][$item.name] == $option->$item.options.id}
                                                    selected="selected"
                                                {/if}
                                            {/if}
                                        >{$option->$item.options.name}</option>
                                    {elseif $option == "-"}
                                        <option value="">-</option>
                                    {else}
                                        <option value="{$option[$item.options.id]}"
                                            {if isset($item.multiple)}
                                                {foreach $fields_value[$input.name][$item.name] as $field_value}
                                                    {if $field_value == $option[$item.options.id]}
                                                        selected="selected"
                                                    {/if}
                                                {/foreach}
                                            {else}
                                                {if isset($fields_value[$input.name]) && isset($fields_value[$input.name][$item.name]) && $fields_value[$input.name][$item.name] == $option[$item.options.id]}
                                                    selected="selected"
                                                {/if}
                                            {/if}
                                        >{$option[$item.options.name]}</option>

                                    {/if}
                                {/foreach}
                            {/if}
                        </select>
                        </div>
                        
                        
                    {elseif $item.type == 'color'}
                        <div class="t_span4 col-lg-4">
                            {if isset($item.label) }<h4 class="title-item">{$item.label}</h4>{/if}
                            <div class="input-group col-lg-5">
                                <input type="color"
                                data-hex="true"
                                {if isset($item.class)} class="{$item.class}"
                                {else} class="color mColorPickerInput"{/if}
                                name="{$input.name}"
                                value="{$fields_value[$input.name][$item.name]|escape:'html':'UTF-8'}" />
                            </div>
                        </div>
                        
                    {elseif $item.type == 'text'}
                        <div class="t_span4 col-lg-4">
                            {if isset($item.label) }<h4 class="title-item">{$item.label}</h4>{/if}
                            {if isset($fields_value[$input.name]) && isset($fields_value[$input.name][$item.name])}
                                {assign var='value_text' value=$fields_value[$input.name][$item.name]}
                            {else}
                                {assign var='value_text' value=''}
                            {/if}
                            
                            {if isset($item.maxchar) || isset($item.prefix) || isset($item.suffix)}
                            <div class="input-group{if isset($item.class)} {$item.class}{/if}">
                            {/if}
                            {if isset($item.maxchar) && $item.maxchar}
                            <span id="{if isset($item.id)}{$item.id}{else}{$item.name}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$item.maxchar|intval}</span></span>
                            {/if}
                            {if isset($item.prefix)}
                            <span class="input-group-addon">
                              {$item.prefix}
                            </span>
                            {/if}
                            <input type="text"
                                name="{$item.name}"
                                id="{if isset($item.id)}{$item.id}{else}{$item.name}{/if}"
                                value="{if isset($item.string_format) && $item.string_format}{$value_text|string_format:$item.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                class="{if isset($item.class)}{$item.class}{/if}{if $item.type == 'tags'} tagify{/if}"
                                {if isset($item.size)} size="{$item.size}"{/if}
                                {if isset($item.maxchar) && $item.maxchar} data-maxchar="{$item.maxchar|intval}"{/if}
                                {if isset($item.maxlength) && $item.maxlength} maxlength="{$item.maxlength|intval}"{/if}
                                {if isset($item.readonly) && $item.readonly} readonly="readonly"{/if}
                                {if isset($item.disabled) && $item.disabled} disabled="disabled"{/if}
                                {if isset($item.autocomplete) && !$item.autocomplete} autocomplete="off"{/if}
                                {if isset($item.required) && $item.required } required="required" {/if}
                                {if isset($item.placeholder) && $item.placeholder } placeholder="{$item.placeholder}"{/if}
                                />
                            {if isset($item.suffix)}
                            <span class="input-group-addon">
                              {$item.suffix}
                            </span>
                            {/if}

                            {if isset($item.maxchar) || isset($item.prefix) || isset($item.suffix)}
                            </div>
                            {/if}
                            {if isset($item.maxchar) && $item.maxchar}
                            <script type="text/javascript">
                            $(document).ready(function(){
                                countDown($("#{if isset($item.id)}{$item.id}{else}{$item.name}{/if}"), $("#{if isset($item.id)}{$item.id}{else}{$item.name}{/if}_counter"));
                            });
                            </script>
                            {/if}
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    {/if}
    {$smarty.block.parent}
{/block}
