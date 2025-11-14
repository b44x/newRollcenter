{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
    {if isset($params.type) && $params.type == 'filename'}
        {if $tr.type == 1}
            <ul class="filename">
                {foreach from=$tr.files item=file}
                    <li><i class="icon-file-text-alt"></i> {$file|escape: 'html':'UTF-8'}</li>
                {/foreach}
            </ul>
        {else}
            <ul class="filename">
                <li>{l s='Google Fonts Setup' mod='leoelements'} : {$tr.file|replace:'+':' '}</li>
            </ul>
        {/if}
        <style type="text/css">
            .filename {
                list-style: none;
                padding-left: 0;
            }
        </style>
    {elseif isset($params.type) && $params.type == 'type_text'}
        {if $tr.type == 1}
            {l s='Upload Font' mod='leoelements'}
        {else}
            {l s='Google Font' mod='leoelements'}
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}