{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
<div {if isset($defaultColumn)}id="default_column" class="column-row plist-element col-sp-12 col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"{else}
class="column-row{foreach $column.form key=ckey item=citem} col-{$ckey}-{$citem}{/foreach} plist-element" data-form='{$column.dataForm}'{/if}>
    <div class="cover-column">
        <div class="pull-left">
            <a href="javascript:void(0)" title="{l s='Edit Column' mod='leoelements'}" class="column-action btn-edit-column" data-type="ApColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
        </div>
        <div class="pull-right">
            <a class="plist-eremove"><i class="icon-trash"></i></a>
        </div>
    
		<div class="content row">
			{if !isset($defaultColumn)}
			{foreach $column.sub item=columnsub}
				{if $columnsub.name == 'code'}
					{include file='./code.tpl' code=$columnsub.code}
				{else}
					{include file='./element.tpl' eItem=$columnsub.config configElement=$columnsub.dataForm}
				{/if}
			{/foreach}
			{/if}
		</div>
	</div>
</div>