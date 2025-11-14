{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
<div class="col-md-12 code plist-element" data-element='code'>
    <div class="desc-box"><a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me' mod='leoelements'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i> </a> tpl code</div>
    <div class="pull-right">
        <a class="plist-code"><i class="icon-code"></i></a>
        <a class="plist-eremove"><i class="icon-trash"></i></a>
    </div>
    <div class="content-code">
        <textarea name="filecontent" rows="5" class="">{if isset($code)}{$code nofilter}{/if}</textarea>
    </div>
</div>