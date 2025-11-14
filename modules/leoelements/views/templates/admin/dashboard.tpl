{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: leoelements is module help you can build content for your shop
*}
<div id="ledask" class="row">
	<div class="col-lg-12">
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">{l s='Quick Tools' mod='leoelements'}</div>
				<div class="panel-content">
					
					<div id="quicktools" class="row panel-link">
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.mpr}" class="btn btn-link">
								<i class="icon icon-3x icon-desktop"></i> 
								<span>{l s='Manage Profiles' mod='leoelements'}</span>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.apr}" class="btn btn-link">
								<i class="icon icon-3x icon-plus-square"></i> 
								<span>{l s='Add New Profiles' mod='leoelements'}</span>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.position}" class="btn btn-link">
								<i class="icon icon-3x icon-list"></i> 
								<p>{l s='Manage Position' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.addposition}" class="btn btn-link">
								<i class="icon icon-3x icon-plus-square"></i> 
								<p>{l s='Add New Position' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-12 col-lg-12">
							<hr>	
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.position}" class="btn btn-link">
								<i class="icon icon-3x icon-reorder"></i> 
								<p>{l s='Manage Hook And Content' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.addposition}" class="btn btn-link">
								<i class="icon icon-3x icon-plus-square"></i> 
								<p>{l s='Add New Hook and Content' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.category}" class="btn btn-link">
								<i class="icon icon-3x icon-hdd"></i> 
								<p>{l s='Manage Category' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.addcategory}" class="btn btn-link">
								<i class="icon icon-3x icon-plus-square"></i> 
								<p>{l s='Add New Category' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-12 col-lg-12">
						<hr>	
						</div>
						 <div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.product}" class="btn btn-link">
								<i class="icon icon-3x icon-list"></i> 
								<p>{l s='Manage Product Detail' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.addproduct}" class="btn btn-link">
								<i class="icon icon-3x icon-plus-square"></i> 
								<p>{l s='Add New Product' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.productlist}" class="btn btn-link">
								<i class="icon icon-3x icon-list"></i> 
								<p>{l s='Manage Product List' mod='leoelements'}</p>
							</a>
						</div>
						<div class="col-xs-3 col-lg-3">
							<a href="{$quick_link.addproductlist}" class="btn btn-link">
								<i class="icon icon-3x icon-plus-square"></i> 
								<p>{l s='Add New Product List' mod='leoelements'}</p>
							</a>
						</div>
						
					</div>
				</div>	
			</div>
			<div class="panel panel-default quick-profile">
				<div class="panel-heading">{l s='Quick Profile Link' mod='leoelements'}</div>
				<div class="panel-content">
					{if !$profiles}
					<div class="alert alert-warning">
						<a href="{$quick_link.apr}">{l s='Please Create Profile. Click Here to create Profile' mod='leoelements'}</a>
					</div>
					{else}
					<ul class="nav nav-tabs le-globalconfig" role="tablist">
						{foreach from=$profiles item=profile}
						<li class="nav-item{if $profile.active} active{/if}">
							<a class="nav-link" href="#fieldset_{$profile.id_leoelements_profiles}" role="tab" data-toggle="tab"><i class="icon icon-user"> </i> {$profile.name}</a>
						</li>
						{/foreach}
					</ul>
					<div class="tab-content">
					{foreach from=$profiles item=profile}
						<div class="panel {if $profile.active} active{/if}" id="fieldset_{$profile.id_leoelements_profiles}">
							<ul class="ul-unstyled first-profile">
								<li>
									<div class="lebreadcrumb">
										<div class="legtitle"><i class="icon icon-user"> </i> <span>{l s='Profile' mod='leoelements'} - {$profile.name}</span></div>
										<div class="legcontent">
											<a href="{$profile.link}" target="blank" title="{l s='Click To Edit Profile' mod='leoelements'}"><i class="icon icon-edit"></i> {l s='Edit Profile' mod='leoelements'}</a>  |
											<a href="{$profile.preview}" target="blank" title="{l s='Click To View Profile' mod='leoelements'}"><i class="icon icon-eye"></i> {l s='Preview Profile' mod='leoelements'}</a>
										</div>
										
									</div>
									<ul class="ul-unstyled">
										{foreach from=$profile.positions item=position}
										<li>
											<div class="lebreadcrumb">
												<div class="legtitle">
													<i class="icon icon-file"> </i>
													<span>{$position.pos_name}{if isset($position.name)} - {$position.name}{/if}</span>
												</div>
												<div class="legcontent">
													{if isset($position.id)}
													<a href="{$position.edit}" target="blank"><i class="icon icon-edit"></i> {l s='Edit Position' mod='leoelements'}</a> |
													<a href="{$position.edit1}" target="blank"><i class="icon icon-edit"></i> {l s='Edit In Profile' mod='leoelements'}</a> |
													<a href="{$profile.preview}&header={$position.id}" target="blank"><i class="icon icon-eye"></i> {l s='Preview Header' mod='leoelements'}</a>
													{else}
														<a href="{$position.edit1}">{l s='Click Here to select' mod='leoelements'}</a>
													{/if}
												</div>
											</div>
											<ul class="ul-unstyled">
												{if isset($position.hooks) && $position.hooks}
													{foreach from=$position.hooks item=hook name=hookItem}
													<li>
														<div class="lebreadcrumb">
															<div class="legtitle">
																<i class="icon icon-hdd"> </i>
																<span>{$hook.hook}</span>
															</div>
															<div class="legcontent">
																<a href="{$hook.furl}" class="button button-primary button-hero edit_with_button_link" target="blank"><img src="{$icon_url}" alt="Leo Elements Logo"> {l s='Edit with Leo Elements' mod='leoelements'}</a>
															</div>
														</div>
													</li>
													{/foreach}
												{else}
												<li>
													<div class="alert alert-warning">
														<a href="{$position.edit1}">{l s='Click Here to assign hook to Profile' mod='leoelements'}</a>
													</div>
												</li>
												{/if}
											</ul>
										</li>
										{/foreach}
										<li>
											<div class="lebreadcrumb">
												<div class="legtitle">
													<i class="icon icon-file"> </i>
													<span>{l s='Product Detail' mod='leoelements'}</span>
												</div>
												<div class="legcontent">
													{if $profile.pdetail.desktop}
													<a href="{$profile.pdetail.desktop.link}" target="blank" title="{l s='Edit In Desktop' mod='leoelements'}"><i class="icon icon-desktop"> </i> {$profile.pdetail.desktop.name}</a>
													{/if}
													{if $profile.pdetail.tablet}
													| <a href="{$profile.pdetail.tablet.link}" target="blank" title="{l s='Edit In Tablet' mod='leoelements'}"><i class="icon icon-tablet"> </i> {$profile.pdetail.tablet.name}</a>
													{/if}
													{if $profile.pdetail.mobile}
													| <a href="{$profile.pdetail.mobile.link}" target="blank" title="{l s='Edit In Mobile' mod='leoelements'}"><i class="icon icon-mobile"> </i> {$profile.pdetail.mobile.name}</a>
													{/if}
												</div>
											</div>
											<ul class="ul-unstyled">
												{foreach from=$profile.product_hook item=hook name=hookItem}
												<li>
													<div class="lebreadcrumb">
														<div class="legtitle">
															<i class="icon icon-hdd"> </i>
															<span>{$hook.hook}</span>
														</div>
														<div class="legcontent">
															{if isset($hook.furl)}
															<a href="{$hook.furl}" class="button button-primary button-hero edit_with_button_link" target="blank"><img src="{$icon_url}" alt="Leo Elements Logo"> {l s='Edit with Leo Elements' mod='leoelements'}</a>
															{else}
															<a href="{$hook.burl}" target="blank"> <i class="icon icon-edit"></i> {l s='Edit In Profile' mod='leoelements'}</a>
															{/if}
														</div>
													</div>
												</li>
												{/foreach}
											</ul>
										</li>
										<li>
											<div class="lebreadcrumb">
												<div class="legtitle">
													<i class="icon icon-file"> </i>
													<span>{l s='Product List' mod='leoelements'}</span>
												</div>
												<div class="legcontent">
													{if $profile.plist.desktop}
													<a href="{$profile.plist.desktop.link}" target="blank" title="{l s='Edit in Desktop' mod='leoelements'}"><i class="icon icon-desktop"> </i> {$profile.plist.desktop.name}</a>
													{/if}
													{if $profile.plist.tablet}
													| <a href="{$profile.plist.tablet.link}" target="blank" title="{l s='Edit in Tablet' mod='leoelements'}"><i class="icon icon-tablet"> </i> {$profile.plist.tablet.name}</a>
													{/if}
													{if $profile.plist.mobile}
													| <a href="{$profile.plist.mobile.link}" target="blank" title="{l s='Edit in Mobile' mod='leoelements'}"><i class="icon icon-mobile"> </i> {$profile.plist.mobile.name}</a>
													{/if}
												</div>
											</div>
										</li>
										<li>
											<div class="lebreadcrumb">
												<div class="legtitle">
													<i class="icon icon-file"> </i>
													<span>{l s='Category' mod='leoelements'}</span>
												</div>
												<div class="legcontent">
													{if $profile.clist.desktop}
													<a href="{$profile.clist.desktop.link}" target="blank" title="{l s='Edit in Desktop' mod='leoelements'}"><i class="icon icon-desktop"> </i> {$profile.clist.desktop.name}</a>
													{/if}
													{if $profile.clist.tablet}
													| <a href="{$profile.clist.tablet.link}" target="blank" title="{l s='Edit in Tablet' mod='leoelements'}"><i class="icon icon-tablet"> </i> {$profile.clist.tablet.name}</a>
													{/if}
													{if $profile.clist.mobile}
													| <a href="{$profile.clist.mobile.link}" target="blank" title="{l s='Edit in Mobile' mod='leoelements'}"><i class="icon icon-mobile"> </i> {$profile.clist.mobile.name}</a>
													{/if}
												</div>
											</div>
											<ul class="ul-unstyled">
												{foreach from=$profile.category_hook item=hook name=hookItem}
												<li>
													<div class="lebreadcrumb">
														<div class="legtitle">
															<i class="icon icon-hdd"> </i>
															<span>{$hook.hook}</span>
														</div>
														<div class="legcontent">
															{if isset($hook.furl)}
															<a href="{$hook.furl}" class="button button-primary button-hero edit_with_button_link" target="blank"><img src="{$icon_url}" alt="Leo Elements Logo"> {l s='Edit with Leo Elements' mod='leoelements'}</a>
															{else}
															<a href="{$hook.burl}" target="blank"> <i class="icon icon-edit"></i> {l s='Edit In Profile' mod='leoelements'}</a>
															{/if}
														</div>
													</div>
												</li>
												{/foreach}
											</ul>
										</li>
									</ul>	
								</li>
							</ul>
						</div>
					{/foreach}
					</div>
					{/if}
					
				</div>
			</div>
			<div class="panel panel-default customcss">
				<form action="{$controller_url}" method="post" enctype="multipart/form-data" novalidate="">
					<div class="panel-heading">{l s='Custom css' mod='leoelements'} ({l s='Use for all profiles' mod='leoelements'})</div>
					<div class="panel-content">
						<textarea class="textarea-autosize" name="customcss" rows="20">{$customCSS}</textarea>
					</div>
					<br>
					<input class="btn btn-primary" type="submit" name="saveCSS" value="{l s='Save CSS' mod='leoelements'}">
				</form>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">{l s='Quick Link to Modules' mod='leoelements'}</div>
				<div class="panel-content">
					<div class="row panel-link">
						{foreach from=$moduleList item=module name=moduleItem}
						<div class="col-xs-3 col-lg-3">
							<a href="{$module.url}" class="btn btn-link" title="{l s='Click to config module' mod='leoelements'}">
								<i class="icon icon-3x icon-laptop"></i><span>{$module.name|escape:'html':'UTF-8'}</span></a>
						</div>
						{/foreach}
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">{l s='Statistics' mod='leoelements'}</div>
				<div class="panel-content" id="dashtrends">
						
						<div class="row" id="dashtrends_toolbar">
							<dl class="col-xs-3 col-lg-3 active">
								<dt>{l s='Profiles' mod='leoelements'}</dt>
								<dd class="data_value size_l"><span id="sales_score">{$lecount.profile}</span></dd>
							</dl>
							<dl class="col-xs-3 col-lg-3 ">
								<dt>{l s='Positions' mod='leoelements'}</dt>
								<dd class="data_value size_l"><span id="orders_score">{$lecount.position}</span></dd>
							</dl>
							<dl class="col-xs-3 col-lg-3 ">
								<dt>{l s='Hook Contents' mod='leoelements'}</dt>
								<dd class="data_value size_l"><span id="cart_value_score">{$lecount.content}</span></dd>
							</dl>
							<dl class="col-xs-3 col-lg-3 ">
								<dt>{l s='Category Profiles' mod='leoelements'}</dt>
								<dd class="data_value size_l"><span id="cart_value_score">{$lecount.category}</span></dd>
							</dl>
							<dl class="col-xs-3 col-lg-3 ">
								<dt>{l s='Product Profiles' mod='leoelements'}</dt>
								<dd class="data_value size_l"><span id="cart_value_score">{$lecount.detail}</span></dd>
							</dl>
							<dl class="col-xs-3 col-lg-3 ">
								<dt>{l s='Product List' mod='leoelements'}</dt>
								<dd class="data_value size_l"><span id="cart_value_score">{$lecount.productlist}</span></dd>
							</dl>
						</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">{l s='Theme Config' mod='leoelements'}</div>
				<form action="{$controller_url}" method="post" enctype="multipart/form-data" novalidate="">
					<div class="panel-content" id="moduleconfig">
						<div class="form-wrapper">	
							<div class="form-group row">
									<label class="control-label col-lg-4">{l s='Enable Panel Tool' mod='leoelements'}</label>
									<div class="col-lg-8">
										<span class="switch prestashop-switch fixed-width-lg">
											<input type="radio" name="LEOELEMENTS_PANEL_TOOL" id="LEOELEMENTS_PANEL_TOOL_on" value="1"{if $LEOELEMENTS_PANEL_TOOL} checked="checked"{/if}>
											<label for="LEOELEMENTS_PANEL_TOOL_on">{l s='Enabled' mod='leoelements'}</label>
											<input type="radio" name="LEOELEMENTS_PANEL_TOOL" id="LEOELEMENTS_PANEL_TOOL_off" value="0"{if !$LEOELEMENTS_PANEL_TOOL} checked="checked"{/if}>
											<label for="LEOELEMENTS_PANEL_TOOL_off">{l s='Disabled' mod='leoelements'}</label>
											<a class="slide-button btn"></a>
										</span>
																									
										<p class="help-block">{l s='This is only for show demo function of theme. Please turn off it in when you use this theme for your shop.' mod='leoelements'}</p>
									</div>
							</div>
						</div>
					</div>
					<input class="btn btn-primary" type="submit" name="submitConfig" value="{l s='Save Config' mod='leoelements'}">
				</form>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Export Sample For Dev</div>
				<p class="help-block">{l s='You can select one or more Module.' mod='leoelements'}</p>
				
				<form action="{$controller_url}" onSubmit="return confirm('{l s='Are you sure to submit form?' mod='leoelements'}')" method="post" enctype="multipart/form-data" novalidate="">
	                <table cellspacing="0" cellpadding="0" class="table">
	                    <tr>
	                        <th>
	                            <input type="checkbox" name="checkme" id="checkme" class="noborder" onclick="checkDelBoxes(this.form, 'leomodule[]', this.checked)" />
	                        </th>
	                        <th>{l s='Name' mod='leoelements'}</th>
	                        <th>{l s='Back-up File' mod='leoelements'}
	                            <p class="help-block" style="display: inline;">
	                            
	                            </p>
	                        </th>
	                    </tr>

	                    {foreach from=$moduleList item=module name=moduleItem}
	                        <tr {if $smarty.foreach.moduleItem.index % 2}class="alt_row"{/if}>
	                            <td> 
	                                <input type="checkbox" class="cmsBox" name="leomodule[]" id="chk_{$module.name|escape:'html':'UTF-8'}" value="{$module.name|escape:'html':'UTF-8'}"/>
	                            </td>
	                            <td><label for="chk_{$module.name|escape:'html':'UTF-8'}" class="t"><strong>{$module.name|escape:'html':'UTF-8'}</strong></label></td>
	                            <td>
	                                {if isset($module.sample)}
	                                <a href="{$module.sample}" onclick="return confirm('{l s='Are you sure?' mod='leoelements'}')" title="{l s='Click To Restore' mod='leoelements'}">{$module.name|escape:'html':'UTF-8'}.xml</a>
	                                {/if}
	                            </td>
	                        </tr>
	                    {/foreach}

	                </table>
	                <input class="btn btn-primary" type="submit" name="submitSample" value="{l s='Create Sample' mod='leoelements'}">
	                <input class="btn btn-primary" type="submit" name="submitRestoreSample" value="{l s='Restore Sample' mod='leoelements'}">
	                <hr/>
	                {if isset($le_struct) && $le_struct}
	                	{if isset($le_struct.db_data) && $le_struct.db_data}
	                		<a href="{$le_struct.db_data}"  title="{l s='Click To Restore' mod='leoelements'}">db_data.sql</a>
	                	{/if}
	                	<br/>
	                	{if isset($le_struct.db_structure) && $le_struct.db_structure}
	                		<a href="{$le_struct.db_structure}"  title="{l s='Click To Restore' mod='leoelements'}">db_structure.sql</a>
	                	{/if}
	                {/if}
	                <hr/>
	                <input class="btn btn-primary" type="submit" name="submitExportDBStruct" value="{l s='Export Data Struct' mod='leoelements'}">
            	</form>
			</div>

			<div class="panel panel-default">
				<form action="{$controller_url}" method="post" enctype="multipart/form-data" novalidate="">
					<div class="panel-heading">Back-up</div>
					<p class="help-block">{l s='You can select one or more Module. This tools will save all database of module to php file.' mod='leoelements'}</p>
					<p>{$backup_dir}</p>
	                <table cellspacing="0" cellpadding="0" class="table">
	                    <tr>
	                        <th>
	                            <input type="checkbox" name="checkme" id="checkme" class="noborder" onclick="checkDelBoxes(this.form, 'bumodule[]', this.checked)" />
	                        </th>
	                        <th>{l s='Name' mod='leoelements'}</th>
	                        <th>{l s='Restore File' mod='leoelements'}
	                            <p class="help-block" style="display: inline;">
	                            
	                            </p>
	                        </th>
	                    </tr>

	                    {foreach from=$moduleList item=module name=moduleItem}
	                        <tr {if $smarty.foreach.moduleItem.index % 2}class="alt_row"{/if}>
	                            <td> 
	                                <input type="checkbox" class="cmsBox" name="bumodule[]" id="chk_bu{$module.name|escape:'html':'UTF-8'}" value="{$module.name|escape:'html':'UTF-8'}"/>
	                            </td>
	                            <td><label for="chk_bu{$module.name|escape:'html':'UTF-8'}" class="t"><strong>{$module.name|escape:'html':'UTF-8'}</strong></label></td>
	                            <td>
	                                {if isset($module.files)}
		                                {foreach from=$module.files item=file name=Modulefile}
		                                    <a href="{$controller_url}&restore=1&module={$module.name|escape:'html':'UTF-8'}&file={$file|escape:'html':'UTF-8'}" onclick="return confirm('{l s='Are you sure?' mod='leoelements'}')" title="{l s='Click To Restore' mod='leoelements'}">{$file|escape:'html':'UTF-8'}</a>
		                                {/foreach}
	                                {/if}
	                            </td>
	                        </tr>
	                    {/foreach}
	                </table>
	                <input class="btn btn-primary" type="submit" name="backup" value="{l s='Backup' mod='leoelements'}">
            	</form>
			</div>
			
		</div>
		
	</div>
</div>
