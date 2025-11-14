{**
 * Copyright  PrestaShop SA and Contributors
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
 * @copyright  PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{extends file='layouts/layout-both-columns.tpl'}

{block name='right_column'}{/block}

{block name='content_wrapper'}
  <div id="content-wrapper" class="js-content-wrapper left-column col-xs-12 col-sm-12{if isset($profile_params.c_config.use_button_toggle) && $profile_params.c_config.use_button_toggle}{if $profile_params.c_config.filter_position == 2} col-md-12 col-lg-12{else} filter-toggle col-md-8 col-lg-9{/if}{else} col-md-8 col-lg-9{/if}">
    {hook h="displayContentWrapperTop"}
    {block name='content'}
      <p>Hello world! This is HTML5 Boilerplate.</p>
    {/block}
    {hook h="displayContentWrapperBottom"}
  </div>
  {if isset($profile_params.c_config) && $profile_params.c_config.filter_position == 2&& isset($listing.rendered_facets) && $listing.rendered_facets
    && (
        (isset($page.body_classes['layout-left-column']) && $page.body_classes['layout-left-column'])
        || (isset($page.body_classes['layout-full-width']) && $page.body_classes['layout-full-width'])
        )}
    <section id="products">
      {if $listing.products|count}

        {block name='product_list_top'}
          {include file='catalog/_partials/products-top.tpl' listing=$listing}
        {/block}

        {block name='product_list_active_filters'}
          <div class="hidden-sm-down">
            {$listing.rendered_active_filters nofilter}
          </div>
        {/block}

        {block name='product_list'}
          {include file='catalog/_partials/products.tpl' listing=$listing productClass="col-xs-6 col-xl-4"}
        {/block}

        {block name='product_list_bottom'}
          {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
        {/block}

      {else}
        <div id="js-product-list-top"></div>

        <div id="js-product-list">
          {capture assign="errorContent"}
            <h4>{l s='No products available yet' d='Shop.Theme.Catalog'}</h4>
            <p>{l s='Stay tuned! More products will be shown here as they are added.' d='Shop.Theme.Catalog'}</p>
          {/capture}

          {include file='errors/not-found.tpl' errorContent=$errorContent}
        </div>

        <div id="js-product-list-bottom"></div>
      {/if}
    </section>
  {/if}
{/block}
