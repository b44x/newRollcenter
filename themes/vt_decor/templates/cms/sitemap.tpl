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
{extends file='page.tpl'}

{block name='page_title'}
  <span class="sitemap-title">{l s='Sitemap' d='Shop.Theme.Global'}</span>
{/block}

{block name='page_content_container'}
  <div class="container-fluid">
    <div class="row sitemap col-xs-12">
        {if isset($leo_sitemap_profiles) && !empty($leo_sitemap_profiles)}
          <div class="col-md-3">
            <h2>{l s='Home' mod='leoelements'}</h2>
            <ul>
              {foreach from=$leo_sitemap_profiles item=leo_sitemap_profile}
                <li>
                  <a href="{$leo_sitemap_profile.url}">{$leo_sitemap_profile.name}</a>
                </li>
              {/foreach}
            </ul>
          </div>
        {/if}
        <div class="col-md-3">
          <h2>{$our_offers}</h2>
          {include file='cms/_partials/sitemap-nested-list.tpl' links=$links.offers}
        </div>
        <div class="col-md-3">
          <h2>{$categories}</h2>
          {include file='cms/_partials/sitemap-nested-list.tpl' links=$links.categories}
        </div>
        <div class="col-md-3">
          <h2>{$your_account}</h2>
          {include file='cms/_partials/sitemap-nested-list.tpl' links=$links.user_account}
        </div>
        <div class="col-md-3">
          <h2>{$pages}</h2>
          {include file='cms/_partials/sitemap-nested-list.tpl' links=$links.pages}
        </div>
    </div>
  </div>
{/block}
