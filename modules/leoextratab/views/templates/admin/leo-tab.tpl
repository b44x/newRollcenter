{*
* 2007-2019 PrestaShop
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
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($type)}
	{foreach from=$tabs item=tab}
		{if Configuration::get('LEOEXTRATAB_TYPE_TAB') == 3}
			<div id="leo_extratab_{$tab.id_tab|escape:'html':'UTF-8'}" class="card">
			    <div class="card-header" role="tab" id="heading_extra_tab_{$tab.id_tab}">
		            <h5 class="h5">
		                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse_extra_tab_{$tab.id_tab|escape:'html':'UTF-8'}" aria-expanded="false" aria-controls="collapse_extra_tab_{$tab.id_tab|escape:'html':'UTF-8'}">
		                    {$tab.name|escape:'html':'UTF-8'}
		                </a>
		            </h5>
		        </div>
				<div id="collapse_extra_tab_{$tab.id_tab}" class="collapse" role="tabpanel" aria-labelledby="heading_extra_tab_{$tab.id_tab|escape:'html':'UTF-8'}">
		            <div class="card-block">
		                {$tab.content nofilter}
		            </div>
		        </div>
		    </div>
		{elseif Configuration::get('LEOEXTRATAB_TYPE_TAB') == 2}
			<div id="leo_extratab_{$tab.id_tab|escape:'html':'UTF-8'}">
				<h4 class="title-info-product">{$tab.name|escape:'html':'UTF-8'}</h4>
		       	<div class="leo-product-tabextra">
		       		{$tab.content nofilter}
		       	</div>
			</div>
		{else}
			{if $type == 'title'}
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#tab_extra_{$tab.id_tab|escape:'html':'UTF-8'}" role="tab" aria-controls="product-extra-{$tab.id_tab|escape:'html':'UTF-8'}">
						{$tab.name|escape:'html':'UTF-8'}
					</a>
				</li>
			{/if}

			{if $type == 'content'}
				<div class="tab-pane fade" id="tab_extra_{$tab.id_tab|escape:'html':'UTF-8'}" role="tabpanel" aria-expanded="true">
					{$tab.content nofilter}
				</div>
			{/if}
		{/if}
	{/foreach}
{/if}