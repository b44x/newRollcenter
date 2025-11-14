{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'ap_catGrid'}
        <div class="col-lg-12 leoelement_Grid category-config">
             <div class="row">
                <div class="col-xl-8 col-lg-7 col-md-7 col-sm-6 col-xs-12">
                    <div class="panel category-container">
                        <div class="left-block">

                            <div class="panel">
                                <div class="desc-box">top</div>
                                <div class="content row">
                                    

                                </div>
                            </div>
                            <div class="panel">
                                <div class="desc-box">Products</div>
                                <div class="row"></div>
                            </div>
                            <div class="panel">
                                <div class="desc-box">bottom</div>
                                <div class="content row"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5 col-md-5 col-sm-6 col-xs-12 element-list content">
                    <div class="widget-row col-md-12 plist-element" data-element="category-detail"><a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me' mod='leoelements'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i> </a>
                        <a class="show-postion" href="#postion_layout" title="Click to see postion">
                        {l s='Category Information' mod='leoelements'}
                        </a>
                        <i class="icon-file-text"></i>
                        <div class="pull-right">
                            <a href="javascript:void(0)" data-config="category-detail" title="{l s='Config element' mod='leoelements'}" class="element-config" data-type="ApColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                        </div>
                    </div>

                    <div class="widget-row col-md-12 plist-element" data-element="category-detail"><a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me' mod='leoelements'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i> </a>
                        <a class="show-postion" href="#postion_layout" title="Click to see postion">
                        {l s='Category Information' mod='leoelements'}
                        </a>
                        <i class="icon-file-text"></i>
                        <div class="pull-right">
                            <a href="javascript:void(0)" data-config="category-detail" title="{l s='Config element' mod='leoelements'}" class="element-config" data-type="ApColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                        </div>
                    </div>
                    
                    <div class="add_to_cart plist-element ui-draggable" data-element="add_to_cart"><i class="icon-file-text-o"></i> {l s='Sub Category Information' mod='leoelements'}
                        <div class="pull-right">
                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                            <a class="plist-eedit" data-element="add_to_cart"><i class="icon-edit"></i></a>
                        </div>
                    </div>

                    <div class="add_to_cart plist-element ui-draggable" data-element="add_to_cart"><i class="icon-bar-chart"></i> {l s='Function box for Product' mod='leoelements'}
                        <div class="pull-right">
                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                            <a class="plist-eedit" data-element="add_to_cart"><i class="icon-edit"></i></a>
                        </div>
                    </div>
                    <div class="add_to_cart plist-element ui-draggable" data-element="add_to_cart"><i class="icon-file-text-o"></i> {l s='Paging' mod='leoelements'}
                        <div class="pull-right">
                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                            <a class="plist-eedit" data-element="add_to_cart"><i class="icon-edit"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    
    {/if}
    
    {$smarty.block.parent}
{/block}