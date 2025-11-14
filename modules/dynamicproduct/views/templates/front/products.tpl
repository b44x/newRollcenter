{*
* 2007-2025 TuniSoft
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
*  @author    TuniSoft <tunisoft.solutions@gmail.com>
*  @copyright 2007-2025 TuniSoft
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file='page.tpl'}
{block name='page_content'}
  <div class="container">
      {if $confirmation}
        <div class="alert alert-{$confirmation.type|escape:'htmlall':'UTF-8'}">
            {$confirmation.message|escape:'htmlall':'UTF-8'}
        </div>
      {/if}
      {foreach $inputs as $input}
          {$summary = $module->hookDisplayInputSummary($input->id,
          [
          'show_price' => true,
          'edit_button' => false
          ]
          )}
          {if $summary}
            <div class="col-lg-6 col-md-12">
              <div class="card card-block dp-shared-customization">
                <strong style="display: block;margin-bottom: .4em;">
                    {Product::getProductName($input->id_product, $input->id_attribute)|escape:'htmlall':'UTF-8'}
                </strong>
                  {if $input->name}
                    <span>{$input->name|ucfirst|escape:'htmlall':'UTF-8'}</span>
                  {/if}
                <p>
                    {$summary nofilter} {* html created by the module *}
                </p>
                <div class="flex" style="gap: .4em">
                  <a class="btn btn-primary"
                     href="{$input->getEditLink()|escape:'htmlall':'UTF-8'}"
                  >
                    <i class="material-icons">visibility</i> {l s='Open' mod='dynamicproduct'}
                  </a>

                  <a class="btn btn-danger"
                     onclick="return confirm('{l s='Are you sure that you want to delete this customization?' mod='dynamicproduct'}')"
                     href="{$link->getModuleLink('dynamicproduct', 'products', ['action' => 'delete', 'id_input' => $input->id])|escape:'htmlall':'UTF-8'}"
                  >
                    <i class="material-icons">delete</i>
                  </a>
                </div>
              </div>
            </div>
          {/if}
      {/foreach}
      {if !count($inputs)}
        <div class="alert alert-info">
            {l s='This section will contain the customizations that you saved to your profile' mod='dynamicproduct'}
        </div>
      {/if}
  </div>
{/block}
