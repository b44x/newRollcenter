{**
 * Copyright PrestaShop SA and Contributors
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
 * @copyright PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
<div id="js-product-list-top" class="products-selection">
  <div class="row">
    {assign var=leocol value=6}
    {if isset($profile_params.pl_config)}
      {if $profile_params.pl_config.top_total == 1 && $profile_params.pl_config.top_sortby == 1 && $profile_params.pl_config.top_grid == 1}
      {assign var=leocol value=6}
      {/if}
    {/if}
    {if (isset($profile_params.pl_config) && $profile_params.pl_config.top_total == 1) || !isset($profile_params.pl_config) || (isset($profile_params.c_config.use_button_toggle) && $profile_params.c_config.use_button_toggle)}
    <div class="col-md-{$leocol} col-lg-3 hidden-md-down total-products {if isset($profile_params.c_config.use_button_toggle) && $profile_params.c_config.use_button_toggle}filter-toggle{/if}">
      {if isset($profile_params.c_config.use_button_toggle) && $profile_params.c_config.use_button_toggle}
        <a href="#" class="filter-toggle-button" aria-expanded="false">

          {if isset($profile_params.c_config.use_button_toggle) && $profile_params.c_config.use_button_toggle && $profile_params.c_config.filter_position == 2} {l s='Filter' d='Shop.Theme.Catalog'}{/if}
          <i class="las la-sliders-h"></i>
        </a>
      {/if}
      {if (isset($profile_params.pl_config) && $profile_params.pl_config.top_total == 1) || !isset($profile_params.pl_config)}
        {if $listing.pagination.total_items > 1}
          <p>{l s='There are %product_count% products.' d='Shop.Theme.Catalog' sprintf=['%product_count%' => $listing.pagination.total_items]}</p>
        {elseif $listing.pagination.total_items > 0}
          <p>{l s='There is 1 product.' d='Shop.Theme.Catalog'}</p>
        {/if}
      {/if}
    </div>
    {/if}
    {if isset($profile_params.pl_config.top_grid) && $profile_params.pl_config.top_grid == 1}
  <div class="col-md-{$leocol} col-lg-4" id="btn_view_product" data-mode="{$profile_params.pl_config.listing_product_mode}" data-col-xl="{$profile_params.pl_config.listing_product_column}" data-col-lg="{$profile_params.pl_config.listing_product_largedevice}" data-col-md="{$profile_params.pl_config.listing_product_tablet}" data-col-sm="{$profile_params.pl_config.listing_product_extrasmalldevice}" data-col-xs="{$profile_params.pl_config.listing_product_mobile}" data-col-sp="{$profile_params.pl_config.listing_product_mobile}">
    <span class="grid-select grid-select-list view-list" data-col="list"></span>
      <span class="grid-select grid-select-grid view-1" data-col="12"></span>
      <span class="grid-select grid-select-grid view-2" data-col="6"></span>
      <span class="grid-select grid-select-grid view-3 active" data-col="4"></span>
      <span class="grid-select grid-select-grid view-4" data-col="3"></span>
      <span class="grid-select grid-select-grid view-5" data-col="2-4"></span>
    </div>
    {/if}

    {if (isset($profile_params.pl_config) && $profile_params.pl_config.top_sortby == 1) || !isset($profile_params.pl_config)}
    <div class="col-md-{$leocol} col-lg-5 grid-selecting">
      <div class="row sort-by-row">

        {block name='sort_by'}
          {include file='catalog/_partials/sort-orders.tpl' sort_orders=$listing.sort_orders}
        {/block}

        {if !empty($listing.rendered_facets)}
          <div class="col-sm-3 col-xs-4 hidden-md-up filter-button">
            <button id="search_filter_toggler" class="btn btn-secondary js-search-toggler">
              {l s='Filter' d='Shop.Theme.Actions'}
            </button>
          </div>
        {/if}
      </div>
    </div>
    {else}
    <div class="col-md-{$leocol} col-lg-6 grid-selecting">
      <div class="row sort-by-row">
        {if !empty($listing.rendered_facets)}
          <div class="col-sm-12 col-xs-12 hidden-md-up filter-button">
            <button id="search_filter_toggler" class="btn btn-secondary js-search-toggler">
              {l s='Filter' d='Shop.Theme.Actions'}
            </button>
          </div>
        {/if}
      </div>
    </div>

    {/if}
    <div class="col-sm-12 hidden-md-up text-sm-center text-xs-center showing">
      {l s='Showing %from%-%to% of %total% item(s)' d='Shop.Theme.Catalog' sprintf=[
      '%from%' => $listing.pagination.items_shown_from ,
      '%to%' => $listing.pagination.items_shown_to,
      '%total%' => $listing.pagination.total_items
      ]}
    </div>
  </div>
</div>
