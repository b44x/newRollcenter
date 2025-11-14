{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'leoelement_Grid'}
        <h4>{l s='Difficult to use, you can select avail item' mod='leoelements'}: </h4> 
        <div class="row" id="product-demo-sample">
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="item-demo">
                    <div class="block-image">
                        <img class="img-fuild img-responsive" src="https://i.pinimg.com/originals/8c/16/f9/8c16f9f024af16977adc1f618872eb8b.jpg" alt="{l s='Product image thumbs bottom' mod='leoelements'}">
                        <a href="{$input.demodetaillink}&sampledetail=product_image_thumbs_bottom" class="btn btn-default" title="{l s='Product image thumbs bottom' mod='leoelements'}">{l s='Create with this layout' mod='leoelements'}</a>
                    </div>
                    <div class="block-name">{l s='Product image thumbs bottom' mod='leoelements'}</div>    
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="item-demo">
                    <div class="block-image">
                        <img class="img-fuild img-responsive" src="https://i.pinimg.com/originals/98/b4/b0/98b4b05fef8913b2a37cbb592b921e7b.jpg" alt="{l s='Product image thumbs left' mod='leoelements'}">
                        <a href="{$input.demodetaillink}&sampledetail=product_image_thumbs_left" class="btn btn-default" title="{l s='Product image thumbs left' mod='leoelements'}">{l s='Create with this layout' mod='leoelements'}</a>
                    </div>
                    <div class="block-name">{l s='Product image thumbs left' mod='leoelements'}</div>    
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="item-demo">
                    <div class="block-image">
                        <img class="img-fuild img-responsive" src="https://i.pinimg.com/originals/81/c4/41/81c441c1b2f6c3e56b3da56b65324423.jpg" alt="{l s='Product image thumbs right' mod='leoelements'}">
                        <a href="{$input.demodetaillink}&sampledetail=product_image_thumbs_right" class="btn btn-default" title="{l s='Product image thumbs right' mod='leoelements'}">{l s='Create with this layout' mod='leoelements'}</a>
                    </div>
                    <div class="block-name">{l s='Product image thumbs right' mod='leoelements'}</div>    
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="item-demo">
                    <div class="block-image">
                        <img class="img-fuild img-responsive" src="https://i.pinimg.com/originals/60/ca/57/60ca570f6a8254c3741d8c9db78eb3d5.jpg" alt="{l s='Product image no thumbs' mod='leoelements'}">
                        <a href="{$input.demodetaillink}&sampledetail=product_image_no_thumbs" class="btn btn-default" title="{l s='Product image no thumbs' mod='leoelements'}">{l s='Create with this layout' mod='leoelements'}</a>
                    </div>
                    <div class="block-name">{l s='Product image no thumbs' mod='leoelements'}</div>    
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="item-demo">
                    <div class="block-image">
                        <img class="img-fuild img-responsive" src="https://i.pinimg.com/originals/38/99/1a/38991a8c1582669d29abe889bc0d5f52.jpg" alt="{l s='Product image no thumbs center' mod='leoelements'}">
                        <a href="{$input.demodetaillink}&sampledetail=product_image_no_thumbs_center" class="btn btn-default" title="{l s='Product image no thumbs center' mod='leoelements'}">{l s='Create with this layout' mod='leoelements'}</a>
                    </div>
                    <div class="block-name">{l s='Product image no thumbs center' mod='leoelements'}</div>    
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="item-demo">
                    <div class="block-image">
                        <img class="img-fuild img-responsive" src="https://i.pinimg.com/originals/c5/d9/02/c5d9025b68250832a31eac3b6d344955.jpg" alt="{l s='Product image no thumbs fullwidth' mod='leoelements'}">
                        <a href="{$input.demodetaillink}&sampledetail=product_image_no_thumbs_fullwidth" class="btn btn-default" title="{l s='Product image no thumbs fullwidth' mod='leoelements'}">{l s='Create with this layout' mod='leoelements'}</a>
                    </div>
                    <div class="block-name">{l s='Product image no thumbs fullwidth' mod='leoelements'}</div>    
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <div class="item-demo">
                    <div class="block-image">
                        <img class="img-fuild img-responsive" src="https://i.pinimg.com/originals/b1/a8/b9/b1a8b9381d8d3e3c4d13dfe24231581f.jpg" alt="{l s='Product image gallery' mod='leoelements'}">
                        <a href="{$input.demodetaillink}&sampledetail=product_image_gallery" class="btn btn-default" title="{l s='Product image gallery' mod='leoelements'}">{l s='Create with this layout' mod='leoelements'}</a>
                    </div>
                    <div class="block-name">{l s='Product image gallery' mod='leoelements'}</div>    
                </div>
            </div>
        </div>

        <input id="main_class" type="hidden" name="main_class" value="{if isset($input.params.class)}{$input.params.class}{/if}" />
        {* <div id="top_wrapper">
            <a class="btn btn-default btn-fwidth width-default" data-width="auto">{l s='Default' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-large" data-width="1200">{l s='Large' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-medium" data-width="992">{l s='Medium' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-small" data-width="768">{l s='Small' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-extra-small" data-width="603">{l s='Extra Small' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-mobile" data-width="480">{l s='Mobile' mod='leoelements'}</a>
        </div> *}
        <div class="col-lg-12 {$input.type|escape:'html':'UTF-8'}">
            <div class="row">
                <div id="home_wrapper" class="col-lg-12 col-md-12">
                    <div class="panel product-container">
                        <div class="desc-box">category-layout</div>
                            {foreach $input.blockList key=kBlock item=vblock}
                                <div class="{$vblock.class|escape:'html':'UTF-8'}">
                                    <div class="content row {$kBlock|escape:'html':'UTF-8'}-block-content">
                                    {if isset($input.params[$kBlock])}
                                        {assign var=blockElement value=$input.params[$kBlock]}
                                        {foreach $blockElement item=gridElement}
                                                {if $gridElement.name == 'code'}
                                                    {include file='./code.tpl' code=$gridElement.code}
                                                {else if $gridElement.name == 'functional_buttons'}
                                                    <div class="row group-row functional_buttons plist-element" data-element='functional_buttons'{if isset($gridElement.dataForm)} data-form='{$gridElement.dataForm}'{/if}>
                                                        <div class="desc-box">
                                                            <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort group' mod='leoelements'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i> </a> Group
                                                            <a href="javascript:void(0)" title="{l s='Edit Group Class' mod='leoelements'}" class="column-action btn-edit-group" data-type="ApColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
                                                        </div>
                                                        <div class="pull-right">
                                                            <a class="plist-eremove"><i class="icon-trash"></i></a>
                                                        </div>
                                                        <p class="row-preactive">{l s='Drag me to layout to set element in column' mod='leoelements'}</p>
                                                        <div class="row-actived">
                                                            <div class="group">
                                                                {foreach $gridElement.columns item=column}
                                                                    {include file='./column.tpl' column=$column}
                                                                {/foreach}
                                                            </div>
                                                            <br/>
    														<div class="group-button">
    															<a href="javascript:void(0)" title="{l s='Add New Column' mod='leoelements'}" class="group-action btn-add-column" tabindex="0" data-container="body" data-toggle="popover" data-placement="top" data-trigger="focus"><i class="icon-plus"></i></a>
    														</div>	
                                                        </div>
                                                    </div>
                                                {else}
                                                    {include file='./element.tpl' eItem=$gridElement.config configElement=$gridElement.dataForm}
                                                {/if}
                                        {/foreach}
                                    {/if}
                                </div>
                            {/foreach}
						</div>
					</div>
                </div>
                <div class="row element-list content">
                    {foreach from=$input.elements item=eItems}
                    <div class="col-md-4">
                        <div class="row">
                            {foreach from=$eItems.group item=eItem}
                                {if isset($eItem.type) and $eItem.type=="sperator"}
                                <div class="col-md-12">
                                    <p>{$eItem.name}</p>
                                </div>
                                {else}
                                    {include file='./element.tpl' eItem=$eItem configElement=$eItem.dataForm}
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                    {/foreach}
                    <div id="postion_layout" class="col-lg-12 col-md-12 col-sm-12">
                        <br/>
                        <a class="postion-img-co btn btn-default" href="javascript:void(0)">{l s='Close or open image' mod='leoelements'}</a>
                        <img style="display:none" src="https://drscdn.500px.org/photo/243373101/q%3D80_m%3D1500/v2?user_id=24883015&webp=true&sig=f7caa5d610248680886907ebc15e1239a965a19a7f976a899b7a8c16ddd9408a" title="postion"/>
					</div>
				</div>
			</div>
        </div>

        <div class="modal fade" id="modal_form"  data-backdrop="0" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content modal-lg">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                <span class="sr-only">{l s='Close' mod='leoelements'}</span></button>
              </div>
              <div class="modal-body"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='leoelements'}</button>
                <button type="button" class="btn btn-primary btn-savewidget">{l s='Save changes' mod='leoelements'}</button>
              </div>
            </div>
          </div>
        </div>

		<div style="display:none">
                <div id="leo-category-1">
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Show Category Description' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="show_des" name="show_des">
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                                <option value="0">{l s='No' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group show_des-0">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Number Charactor to show read more in description' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <input type="text" name="number_des" value="200">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Category Image' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="cat_image" name="cat_image">
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                                <option value="0">{l s='No' mod='leoelements'}</option>
                                <option value="2">{l s='Show as Breadcrumb Background' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="leo-sub-category-1">
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Show Sub Category Image' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="show_sub_image" name="show_sub_image">
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                                <option value="0">{l s='No' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Show Sub Category Description' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="show_sub_des" name="show_sub_des">
                                <option value="0">{l s='No' mod='leoelements'}</option>
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Number Charactor to show read more in description' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <input type="text" name="number_subdes" value="100">
                        </div>
                    </div>
                </div>
                <div id="product-list">
                    <div class="form-group">
                        <div class="alert alert-info mt-2" role="alert">
                          <p class="alert-text">
                            {l s='Top function Config' mod='leoelements'}
                          </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Show Total Products' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="show_ptotal" name="show_ptotal">
                                <option value="0">{l s='No' mod='leoelements'}</option>
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Show Sort by' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="show_psort" name="show_psort">
                                <option value="0">{l s='No' mod='leoelements'}</option>
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Show Grid Number Select' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="show_pgrid" name="show_pgrid">
                                <option value="0">{l s='No' mod='leoelements'}</option>
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="alert alert-info mt-2" role="alert">
                          <p class="alert-text">
                            {l s='Product List' mod='leoelements'}
                          </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Showing Product Type' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="ptype" name="ptype">
                                <option value="grid">{l s='Grid' mod='leoelements'}</option>
                                <option value="list">{l s='List' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Product List Builder Type' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="ptype" name="ptype">
                                <option value="default">{l s='Default' mod='leoelements'}</option>
                                {foreach from=$input.product_list item=plist}
                                <option value="{$plist.plist_key}">{$plist.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="alert alert-info mt-2" role="alert">
                          <p class="alert-text">
                            {l s='Pagination' mod='leoelements'}
                          </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Showing Pagination Count' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="show_pg" name="show_pg">
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                                <option value="0">{l s='No' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Pagination Type' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="pg_type" name="pg_type">
                                <option value="default">{l s='Default' mod='leoelements'}</option>
                                <option value="scrool">{l s='Scrool' mod='leoelements'}</option>
                            </select>
                        </div>
                    </div>
                </div>
				
			   
			   {include file='./column.tpl' defaultColumn=1}

		</div>
    
    {/if}
    
    {$smarty.block.parent}
{/block}