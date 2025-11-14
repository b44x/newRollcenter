{**
 * Copyright since  PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since  PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
<nav class="pagination{if isset($profile_params.pl_config)}{if $profile_params.pl_config.pg_type ==2} scrool{else if $profile_params.pl_config.pg_type ==3} loadmore{/if}{/if}" data-total="{$pagination.total_items}">
  {if (isset($profile_params.pl_config) && $profile_params.pl_config.pg_count ==1 ) || !isset($profile_params.pl_config)}
  <div id="pagination_summary" class="col-md-4{if $pagination.total_items==$pagination.items_shown_to} done{/if}">
    {block name='pagination_summary'}
      {if isset($profile_params.pl_config) && $profile_params.pl_config.pg_type != 1}
      {assign var=items_shown_from value=1}
      {else}
      {assign var=items_shown_from value=$pagination.items_shown_from}
      {/if}
      {l s='Showing %from%-%to% of %total% item(s)' d='Shop.Theme.Catalog' sprintf=['%from%' => $pagination.items_shown_from ,'%to%' => $pagination.items_shown_to, '%total%' => $pagination.total_items]}
    {/block}
  </div>
  {/if}
  {if (isset($profile_params.pl_config) && $profile_params.pl_config.pg_type ==1 ) || !isset($profile_params.pl_config)}
  <div class="col-md-6 offset-md-2 pr-0">
    {block name='pagination_page_list'}
     {if $pagination.should_be_displayed}
        <ul class="page-list clearfix text-sm-right">
          {foreach from=$pagination.pages item="page"}


            <li {if $page.current} class="current" {/if}>
              {if $page.type === 'spacer'}
                <span class="spacer">&hellip;</span>
              {else}
                <a
                  rel="{if $page.type === 'previous'}prev{elseif $page.type === 'next'}next{else}nofollow{/if}"
                  href="{$page.url}"
                  class="{if $page.type === 'previous'}previous {elseif $page.type === 'next'}next {/if}{['disabled' => !$page.clickable, 'js-search-link' => true]|classnames}"
                >
                  {if $page.type === 'previous'}
                    <i class="material-icons">&#xE314;</i>{l s='Previous' d='Shop.Theme.Actions'}
                  {elseif $page.type === 'next'}
                    {l s='Next' d='Shop.Theme.Actions'}<i class="material-icons">&#xE315;</i>
                  {else}
                    {$page.page}
                  {/if}
                </a>
              {/if}
            </li>
          {/foreach}
        </ul>
      {/if}
    {/block}
  </div>
  {/if}
  {if isset($profile_params.pl_config) && $profile_params.pl_config.pg_type ==3}
  <div class="col-md-12 offset-md-2 pr-0">
      <a class="btn btn-primary btn-leloadmorep">
          {l s='Load More Products' d='Shop.Theme.Actions'}<i class="material-icons">&#xE315;</i>
      </a>
  </div>
  {/if}

</nav>
