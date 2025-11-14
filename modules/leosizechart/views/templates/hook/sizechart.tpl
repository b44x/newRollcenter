{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{function name=sizeGuide}
<div class="leosizechart-content">
	{if $sh_measure}   
		<ul class="nav nav-tabs">
			{if isset($guide->description) && isset($guide->description) !=''}
			<li class="nav-item"><a class="nav-link-size active" href="#leosizechart-guide"  title="{$guide->title|escape:'html':'UTF-8'} " data-toggle="tab" role="tab" aria-controls="leosizechart-guide">{$guide->title|escape:'html':'UTF-8'}</a></li>
			{else}
			{if $sh_global}<li class="nav-item"><a class="nav-link-size active" href="#leosizechart-global"  title="{l s='Guide' mod='leosizechart'}" data-toggle="tab" role="tab" aria-controls="leosizechart-global">{l s='Guide' mod='leosizechart'}</a></li>{/if}
			{/if}
			{if $sh_measure}<li class="nav-item"><a class="nav-link-size" href="#leosizechart-how"  title="{l s='How to measure' mod='leosizechart'}" data-toggle="tab" role="tab" aria-controls="leosizechart-how">{l s='How to measure' mod='leosizechart'}</a></li>{/if}
		</ul>
	{/if}
	<div class="tab-content">
		{if isset($guide->description) && isset($guide->description) !=''}
			<div id="leosizechart-guide" role="tabpanel" class="tab-pane rte fade active in">
				{$guide->description nofilter} 
			</div>
		{else}
			{if $sh_global}
				<div id="leosizechart-global"  class="tab-pane rte fade active in" role="tabpanel">
					{$global|stripslashes nofilter}
				</div>
			{/if}
		{/if}
		{if $sh_measure}
			<div id="leosizechart-how"  class="tab-pane rte fade" role="tabpanel">
				{$howto|stripslashes nofilter}
			</div>
		{/if}
	</div>
</div>
{/function}

{if $sh_popup && $numberhook==1}
	<button type="button" class="show_sizechart btn btn-default">{l s='Size Guide' mod='leosizechart'}</button>
	<div id="moda_sizechart" class="moda_sizechart popup modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document" {if $width}style="max-width: {$width|escape:'html':'UTF-8'}px;"{/if}>
		    <div class="modal-content">
		    	<div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          	<span aria-hidden="true"><i class="material-icons">close</i></span>
			        </button>
			        <h4 class="modal-title text-sm-center" id="myModalLabel"><i class="material-icons rtl-no-flip"></i>{l s='Size Guide' mod='leosizechart'}</h4>
		      	</div>
				<div id="leosizechart" class="modal-body">
					{sizeGuide}
				</div>
		    </div>
		</div>
	</div>
{elseif $sh_popup}
	<button type="button" class="show_sizechart btn btn-default">{l s='Size Guide' mod='leosizechart'}</button>
{elseif !$sh_popup}
	<div class="moda_sizechart no-popup">
		<h4 class="modal-title">
			{l s='Size Guide' mod='leosizechart'}
		</h4>
		{sizeGuide}
	</div>
{/if}