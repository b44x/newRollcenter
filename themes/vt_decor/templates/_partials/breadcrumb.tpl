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

{if isset($profile_params.leobcimg) && ($profile_params.leobcimg || $profile_params.breadcrumb.bgcolor)}
  <div data-depth="{$breadcrumb.count}" class="breadcrumb-bg {$profile_params.breadcrumb.textposition} {if $profile_params.breadcrumb.bgfull}breadcrumb-full{/if}" style="{if $profile_params.breadcrumb.bgcolor} background-color:{$profile_params.breadcrumb.bgcolor};{/if}{if $profile_params.breadcrumb.use_background}background-image: url({if $page.page_name == 'category' && $profile_params.breadcrumb.category == 'catimg' && isset($category.image.large.url) && $category.image.large.url != ''}{$category.image.large.url}{else if $profile_params.leobcimg}{$profile_params.leobcimg}{/if});{/if}{if $profile_params.breadcrumb.height} min-height:{$profile_params.breadcrumb.height};{/if} ">
    {if isset($profile_params.breadcrumb.bgfull) && $profile_params.breadcrumb.bgfull}
    <div class="container">
    {/if}
    <nav data-depth="{$breadcrumb.count}" class="breadcrumb hidden-sm-down">
    <ol>
        {block name='breadcrumb'}
          {foreach from=$breadcrumb.links item=path name=breadcrumb}
            {block name='breadcrumb_item'}
            <li>
              {if not $smarty.foreach.breadcrumb.last}
                <a href="{$path.url}"><span>{$path.title}</span></a>
              {else}
                <span>{$path.title}</span>
              {/if}
              </li>
            {/block}
          {/foreach}
        {/block}
      </ol>
    </nav>
    {if isset($profile_params.breadcrumb.bgfull) && $profile_params.breadcrumb.bgfull}
    </div>
    {/if}
  </div>
{else}
  <nav data-depth="{$breadcrumb.count}" class="breadcrumb hidden-sm-down">
    <ol>
      {block name='breadcrumb'}
        {foreach from=$breadcrumb.links item=path name=breadcrumb}
          {block name='breadcrumb_item'}
            <li>
              {if not $smarty.foreach.breadcrumb.last}
                <a href="{$path.url}"><span>{$path.title}</span></a>
              {else}
                <span>{$path.title}</span>
              {/if}
            </li>
          {/block}
        {/foreach}
      {/block}
    </ol>
  </nav>
{/if}