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
        <div id="top_wrapper">
            <a class="btn btn-default btn-fwidth width-default" data-width="auto">{l s='Default' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-large" data-width="1200">{l s='Large' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-medium" data-width="992">{l s='Medium' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-small" data-width="768">{l s='Small' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-extra-small" data-width="603">{l s='Extra Small' mod='leoelements'}</a>
            <a class="btn btn-default btn-fwidth width-mobile" data-width="480">{l s='Mobile' mod='leoelements'}</a>
        </div>

        <div class="col-lg-12 {$input.type|escape:'html':'UTF-8'}">
            <div class="row">
                <div id="home_wrapper" class="col-lg-12 col-md-12">
                    <div class="panel product-container">
                        <div class="desc-box">product-layout</div>
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
                    <div class="col-md-3">
                        <div class="row">
                            <div class="row group-row functional_buttons plist-element" data-element='functional_buttons'>
                                <div class="desc-box">
                                    <a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag to sort group' mod='leoelements'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i> </a> Group
                                    <a href="javascript:void(0)" title="{l s='Edit Group Class' mod='leoelements'}" class="column-action btn-edit-group" data-type="ApColumn" data-for=".column-row"><i class="icon-pencil"></i></a>
                                </div>
                                <div class="pull-right">
                                    <a class="plist-eremove"><i class="icon-trash"></i></a>
                                </div>
                                <p class="row-preactive">{l s='Drag me to layout to set element in column' mod='leoelements'}</p>
                                <div class="row-actived">
                                    <div class="group row"></div>
                                    <br/>
									<div class="group-button">
										<a href="javascript:void(0)" title="{l s='Add New Column' mod='leoelements'}" class="group-action btn-add-column" tabindex="0" data-container="body" data-toggle="popover" data-placement="top" data-trigger="focus"><i class="icon-plus"></i></a>
									</div>
                                </div>

                            </div>
                            <div class="col-md-12 code plist-element" data-element='code'>
                                <div class="desc-box"><a href="javascript:void(0)" data-toggle="tooltip" title="{l s='Drag me' mod='leoelements'}" class="group-action gaction-drag label-tooltip"><i class="icon-move"></i> </a> tpl code</div>
                                <div class="pull-right">
                                    <a class="plist-code"><i class="icon-code"></i></a>
                                    <a class="plist-eremove"><i class="icon-trash"></i></a>
                                </div>
                                <div class="content-code">
                                    <textarea name="filecontent" rows="5" class=""></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    {foreach from=$input.elements item=eItems}
                    <div class="col-md-3">
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

				<div id="image-thumb-1">
				   <div class="form-group">
							<label class="control-label col-lg-5">
								<span class="label-tooltip" data-toggle="tooltip">
									{l s='Thumb Position' mod='leoelements'}
								</span>
							</label>
							<div class="col-lg-5">
								<select class="select_thumb" name="templateview">
									<option value="bottom">{l s='Bottom' mod='leoelements'}</option>
									<option value="right">{l s='Right' mod='leoelements'}</option>
									<option value="left">{l s='Left' mod='leoelements'}</option>
									<option value="none">{l s='None' mod='leoelements'}</option>
								</select>
							</div>
					</div>
					<div class="form-group select_thumb-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Number thumb in over 1200: Extra Large' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="numberimage" value="5">
						</div>
					</div>
					
					<div class="form-group select_thumb-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Number thumb in 1200: Desktop' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="numberimage1200" value="5">
						</div>
					</div>

					<div class="form-group select_thumb-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Number thumb in 992: Small desktop' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="numberimage992" value="4">
						</div>
					</div>

					<div class="form-group select_thumb-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Number thumb in 768: Tablet' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="numberimage768" value="3"> 
						</div>
					</div>

					<div class="form-group select_thumb-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Number thumb in 576: Small tablet' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="numberimage576" value="3"> 
						</div>
					</div>


					<div class="form-group select_thumb-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Number thumb in 480: Smartphone' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="numberimage480" value="2"> 
						</div>
					</div>

					<div class="form-group select_thumb-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Number thumb in 360: Smartphone vertical' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="numberimage360" value="2"> 
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Display modal popup' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<select class="select_modal" name="templatemodal">
								<option value="1">{l s='Yes' mod='leoelements'}</option>
								<option value="0">{l s='No' mod='leoelements'}</option>                       
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Zoom Type' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<select class="select_zoom" name="templatezoomtype">
								<option value="in">{l s='Zoom In' mod='leoelements'}</option>							
								<option value="in_scrooll">{l s='Scroll To Zoom' mod='leoelements'}</option>								
								<option value="out">{l s='Zoom Out' mod='leoelements'}</option>
								<option value="none">{l s='None' mod='leoelements'}</option>
							</select>
							<p class="help-block">{l s='\'Scroll To Zoom\' doesn\'t work on Internet Explorer. Default is replaced' mod='leoelements'}</p>
						</div>
					</div>
					
					<div class="form-group select-zoom-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Zoom position' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<select class="select_position" name="zoomposition">
								<option value="right">{l s='Right' mod='leoelements'}</option>
								<option value="left">{l s='Left' mod='leoelements'}</option> 
								<option value="top">{l s='Top' mod='leoelements'}</option>
								<option value="bottom">{l s='Bottom' mod='leoelements'}</option> 								
							</select>
						</div>
					</div>
					
					<div class="form-group select-zoom-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Zoom window width' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="zoomwindowwidth" value="400"> 
						</div>
					</div>
					
					<div class="form-group select-zoom-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Zoom window height' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="zoomwindowheight" value="400">					
						</div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Use Image Gallery From Gallary Module' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="use-leo-gallery" name="use_leo_gallery">
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                                <option value="0">{l s='No' mod='leoelements'}</option>                       
                            </select>
                        </div>
                    </div>
			   </div>

			   <div id="image-thumb-2">
					<div class="form-group">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Zoom Type' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<select class="select_zoom" name="templatezoomtype">
								<option value="in">{l s='Zoom In' mod='leoelements'}</option>								
								<option value="in_scrooll">{l s='Scroll To Zoom' mod='leoelements'}</option>								
								<option value="out">{l s='Zoom Out' mod='leoelements'}</option>
								<option value="none">{l s='None' mod='leoelements'}</option>
							</select>
							<p class="help-block">{l s='\'Scroll To Zoom\' doesn\'t work on Internet Explorer. Default is replaced' mod='leoelements'}</p>
						</div>
					</div>
					
					<div class="form-group select-zoom-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Zoom position' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<select class="select_position" name="zoomposition">
								<option value="right">{l s='Right' mod='leoelements'}</option>
								<option value="left">{l s='Left' mod='leoelements'}</option> 
								<option value="top">{l s='Top' mod='leoelements'}</option>
								<option value="bottom">{l s='Bottom' mod='leoelements'}</option> 								
							</select>
						</div>
					</div>
					
					<div class="form-group select-zoom-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Zoom window width' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="zoomwindowwidth" value="400"> 
						</div>
					</div>
					
					<div class="form-group select-zoom-none">
						<label class="control-label col-lg-5">
							<span class="label-tooltip" data-toggle="tooltip">
								{l s='Zoom window height' mod='leoelements'}
							</span>
						</label>
						<div class="col-lg-5">
							<input type="text" name="zoomwindowheight" value="400">					
						</div>
					</div>
                    <div class="form-group">
                        <label class="control-label col-lg-5">
                            <span class="label-tooltip" data-toggle="tooltip">
                                {l s='Use Image Gallery From Gallary Module' mod='leoelements'}
                            </span>
                        </label>
                        <div class="col-lg-5">
                            <select class="use-leo-gallery" name="use_leo_gallery">
                                <option value="1">{l s='Yes' mod='leoelements'}</option>
                                <option value="0">{l s='No' mod='leoelements'}</option>                       
                            </select>
                        </div>
                    </div>
			   </div>
			   
				<div id="addnew-column-form">
					<ul class="list-group dropdown-menu">
						{for $i=1 to 6}
							  <li>
								  <a href="javascript:void(0);" data-col="{$i|escape:'html':'UTF-8'}" data-width="{(12/$i)|replace:'.':'-'|escape:'html':'UTF-8'}" class="column-add">
									  <span class="width-val ap-w-{$i|escape:'html':'UTF-8'}">{$i|escape:'html':'UTF-8'} {l s='column per row' mod='leoelements'} - ( {math equation="100/$i" x=$i format="%.2f"} % )</span>
								  </a>
							  </li>
						{/for}
					</ul>
				</div>
				<div id="group_config">
				   <div class="well">
						<div class="row">
						   {if $input.type == 'ApRowclass'} 
						   <label class="choise-class col-lg-12"><input type="checkbox" class="chk-row" data-value="row" value="1"> {l s='Use class row' mod='leoelements'}</label>
						   {/if}
						   <label class="control-label col-lg-1">{l s='Group Class:' mod='leoelements'}</label>
							<div class="col-lg-11"><input type="text" class="element_class" value="row" name="class"></div>
						</div><br/>
						<div class="desc-bottom">
						{l s='Insert new or select classes for toggling content across viewport breakpoints' mod='leoelements'}<br>
						<ul class="ap-col-class">
                            <li>
                                <label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-xl-down" value="1"> {l s='Hidden on All Devices' mod='leoelements'}</label>
                            </li>
							<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-lg-down" value="1"> {l s='Hidden from Large devices (screen < 1200px)' mod='leoelements'}</label>
							</li>
							<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-md-down" value="1"> {l s='Hidden from Medium devices (screen < 992px)' mod='leoelements'}</label>
							</li>
							<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-sm-down" value="1"> {l s='Hidden from Small devices (screen < 768px)' mod='leoelements'}</label>
							</li>
							<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-xs-down" value="1"> {l s='Hidden from Extra small devices (screen < 576px)' mod='leoelements'}</label>
							</li>
							{*<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="checkbox" data-value="hidden-xs-up" value="1"> {l s='Hidden from Smart Phone' mod='leoelements'}</label>
							</li>*}
						</ul>
						</div>
					</div>
			   </div>
			   <div id="column_config">
			   <div class="well">
						<div class="row">
						   <label class="control-label col-lg-1">{l s='Column Class:' mod='leoelements'}</label>
							<div class="col-lg-11"><input type="text" class="element_class" value="" name="class"></div>
						</div><br/>
						<div class="desc-bottom">
						{l s='Insert new or select classes for toggling content across viewport breakpoints' mod='leoelements'}<br>
						<ul class="ap-col-class">
                            <li>
                                <label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-xl-down" value="1"> {l s='Hidden on All Devices' mod='leoelements'}</label>
                            </li>
							<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-lg-down" value="1"> {l s='Hidden from Large devices (screen < 1200px)' mod='leoelements'}</label>
							</li>
							<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-md-down" value="1"> {l s='Hidden from Medium devices (screen < 992px)' mod='leoelements'}</label>
							</li>
							<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-sm-down" value="1"> {l s='Hidden from Small devices (screen < 768px)' mod='leoelements'}</label>
							</li>
							<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="radio" data-value="hidden-xs-down" value="1"> {l s='Hidden from Extra small devices (screen < 576px)' mod='leoelements'}</label>
							</li>
							{*<li>
								<label class="choise-class"><input class="select-class no-save" name="hidden_from[]" type="checkbox" data-value="hidden-xs-up" value="1"> {l s='Hidden from Smart Phone' mod='leoelements'}</label>
							</li>*}
						</ul>
						</div>
					</div>
				<div class="panel panel-default">
					<div class="panel-body">
						<p>{l s='Responsive: You can config width for each Devices' mod='leoelements'}</p>
					</div>
					<table class="table">
						<thead><tr>
							  <th>{l s='Devices' mod='leoelements'}</th>
							  <th>{l s='Width' mod='leoelements'}</th>
						</tr></thead>
						<tbody>
							{foreach $input.columnGrids as $gridKey=>$gridValue}
							<tr>
								<td>
									<span class="col-{$gridKey|escape:'html':'UTF-8'}"></span>
									{$gridValue|escape:'html':'UTF-8'}
								</td>
								<td>
									<div class="btn-group">
										<input type='hidden' class="col-val" name='{$gridKey|escape:'html':'UTF-8'}' value="12"/>
										<button type="button" class="btn btn-default apbtn-width dropdown-toggle" tabindex="-1" data-toggle="dropdown">
											<span class="width-val ap-w-12">12/12 - ( 100 % )</span><span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											{foreach from=$input.widthList item=itemWidth}
											<li>
												<a class="width-select width-select-{$gridKey}-{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" href="javascript:void(0);" tabindex="-1">
													<span data-width="{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}" class="width-val ap-w-{if $itemWidth|strpos:"."|escape:'html':'UTF-8'}{$itemWidth|replace:'.':'-'|escape:'html':'UTF-8'}{else}{$itemWidth|escape:'html':'UTF-8'}{/if}">{$itemWidth|escape:'html':'UTF-8'}/12 - ( {math equation="(x/y)*100" x=$itemWidth|replace:'-':'.' y=12 format="%.2f"} % )</span>
												</a>
											</li>
											{/foreach}
										</ul>
									</div>
								</td>
							</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			   </div>
			   
			   {include file='./column.tpl' defaultColumn=1}

		</div>
    
    {/if}
    
    {$smarty.block.parent}
{/block}