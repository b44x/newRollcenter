{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'leoelement_file'}
        <div class="col-lg-12">
            <div class="alert alert-info">
                <label class="control-label">
                    {$input.title}
                </label> <br/>
                <a href="javascript:void(0)" class="see-file" title="{l s='Click to open or close file' mod='leoelements'}" data-element="hook_display_product_additional_info">
                    {$input.file_link}
                </a>
            </div>
        </div>
        <div class="col-lg-12 filecontent" style="display: none;">
            <textarea rows="40">{$input.file_content}</textarea>
        </div>
    {/if}
    {if $input.type == 'leoelement_Sample'}
        <div class="row">
            <label class="control-label col-lg-4"></label>
            <a class="btn btn-primary col-lg-8" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">{l s='Click Here to Import From Demo Product List' mod='leoelements'}</a>
            <br/>
        </div>    
      
        <div class="row">
            <div class="col">
                <div class="collapse multi-collapse" id="multiCollapseExample1">
                  <div class="card card-body">
                    {for $foo=1 to 12}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                            <div class="item-demo">
                                <a href="{$input.demoplistlink}&sampleplist=type{$foo}" class="btn-select-plist btn btn-default" title="{l s='Product image thumbs bottom' mod='leoelements'}">
                                <span class="block-image product-list-sample pltype{$foo}">
                                    
                                </span>{l s='Create Product List with Type' mod='leoelements'} {$foo}</a>
                            </div>
                        </div>
                    {/for}
                  </div>
                </div>
            </div>
        </div>
    {/if}
    {if $input.type == 'leoelement_Grid'}
        <div class="{if version_compare(_PS_VERSION_, '1.7.7.9', '>')}col-lg-8{else}col-lg-9{/if} {$input.type|escape:'html':'UTF-8'}">
            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="panel product-container">
                        <div class="desc-box">product-container</div>
                            {foreach $input.blockList key=kBlock item=vblock}
                                <div class="{$vblock.class|escape:'html':'UTF-8'}">
                                    <div class="panel-heading">{$vblock.title|escape:'html':'UTF-8'}</div>
                                    <div class="content {$kBlock|escape:'html':'UTF-8'}-block-content">
                                    {if isset($input.params[$kBlock])}
                                    {assign var=blockElement value=$input.params[$kBlock]}
                                    {foreach $blockElement item=gridElement}
                                        {if $gridElement.name == 'functional_buttons'}
                                            {assign var=iconVal value='icon-puzzle-piece'}
                                            {assign var=NameVal value=$gridElement.name}
                                        {else if $gridElement.name == 'code'}
                                            {assign var=iconVal value='icon-code'}
                                            {assign var=NameVal value='code'}
                                        {else}
                                            {assign var=iconVal value=$input.elements[$gridElement.name]['icon']}
                                            {assign var=NameVal value=$input.elements[$gridElement.name]['name']}
                                        {/if}
                                        <div class="{$gridElement.name|escape:'html':'UTF-8'} plist-element" data-element='{$gridElement.name|escape:'html':'UTF-8'}'><i class="{$iconVal|escape:'html':'UTF-8'}"></i> {$NameVal|escape:'html':'UTF-8'}
                                            {if $gridElement.name == 'code'}
                                            <div class="desc-box"><i class="icon-code"></i> tpl code</div>
                                            {/if}
                                            <div class="pull-right">
                                                <a class="plist-eremove"><i class="icon-trash"></i></a>
                                                <a class="plist-eedit" data-element='{$gridElement.name|escape:'html':'UTF-8'}'><i class="icon-edit"></i></a>
                                            </div>
                                            {if $gridElement.name == 'functional_buttons'}
                                                <div class="content">
                                                    {if isset($gridElement.element)}
                                                    {foreach $gridElement.element item=gridSubElement}
                                                        {if $gridSubElement.name == 'code'}
                                                            {assign var=iconVal value='icon-code'}
                                                            {assign var=NameVal value='code'}
                                                        {else}
                                                            {assign var=iconVal value=$input.elements[$gridSubElement.name]['icon']}
                                                            {assign var=NameVal value=$input.elements[$gridSubElement.name]['name']}
                                                        {/if}
                                                        <div class="{$gridSubElement.name|escape:'html':'UTF-8'} plist-element" data-element='{$gridSubElement.name|escape:'html':'UTF-8'}'><i class="{$iconVal|escape:'html':'UTF-8'}"></i> {$NameVal|escape:'html':'UTF-8'}
                                                            {if $gridSubElement.name == 'code'}
                                                            <div class="desc-box"><i class="icon-code"></i> tpl code</div>
                                                            {/if}
                                                            <div class="pull-right">
                                                                <a class="plist-eremove"><i class="icon-trash"></i></a>
                                                                <a class="plist-eedit" data-element='{$gridSubElement.name|escape:'html':'UTF-8'}'><i class="icon-edit"></i></a>
                                                            </div>
                                                            {if $gridSubElement.name == 'code'}
                                                                <div class="content-code">
                                                                    <textarea name="filecontent" id="filecontent" rows="5" value="" class="">{$gridSubElement.code nofilter}{* HTML form , no escape necessary *}</textarea>
                                                                </div>
                                                            {/if}
                                                        </div>
                                                    {/foreach}
                                                    {/if}
                                                </div>
                                            {/if}
                                            {if $gridElement.name == 'code'}
																								
                                                <div class="content-code">
                                                    <textarea name="filecontent" rows="5" class="">{$gridElement.code nofilter}{* HTML form , no escape necessary *}</textarea>
                                                </div>
                                            {/if}
                                        </div>
                                    {/foreach}
                                    {/if}
                                    </div>
                                </div>
                            {/foreach}
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 element-list content">
                    {foreach from=$input.elements key=eKey item=eItem}
                    <div class="{$eKey|escape:'html':'UTF-8'} plist-element" data-element='{$eKey|escape:'html':'UTF-8'}'><i class="{$eItem.icon|escape:'html':'UTF-8'}"></i> {$eItem.name|escape:'html':'UTF-8'}
                        <div class="pull-right">
                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                            <a class="plist-eedit" data-element='{$eKey|escape:'html':'UTF-8'}'><i class="icon-edit"></i></a>
                        </div>
                    </div>
                    {/foreach}
                    <div class="code plist-element" data-element='code'>
                        <div class="desc-box"><i class="icon-code"></i> tpl code</div>
                        <div class="pull-right">
                            <a class="plist-code"><i class="icon-code"></i></a>
                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                        </div>
                        <div class="content-code">
                            <textarea name="filecontent" rows="5" class=""></textarea>
                        </div>
                    </div>
                    
                    <div class="functional_buttons plist-element" data-element='functional_buttons'>
                        <div class="desc-box"><i class="icon-puzzle-piece"></i> functional-buttons</div>
                        <div class="pull-right">
                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                        </div>
                        <div class="content"></div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    {if $input.type == 'position_hook'}
        {if $input.is_edit}
<div class="modal fade" id="position-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">{l s='Create New' mod='leoelements'}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
            <div class="row">
                <label class="control-label col-lg-4 required">{l s='Name' mod='leoelements'}</label>
                <div class="col-lg-8">
                    <input type="text" name="position-name" id="position-name" value="" class="" required="required">
                </div>
            </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='leoelements'}</button>
        <button type="button" class="btn btn-primary btn-save-position">{l s='Create New' mod='leoelements'}</button>
      </div>
    </div>
  </div>
</div><!-- comment -->


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
                {l s='Please save the Product Lists Builder before using' mod='leoelements'}
            </h2>
        </div>
        </div>
        {/if}
        
    {/if}
    
    {$smarty.block.parent}
{/block}