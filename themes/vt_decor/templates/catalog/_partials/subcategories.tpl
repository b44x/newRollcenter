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
 * @copyright PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{if !empty($subcategories)}
  {if (isset($display_subcategories) && $display_subcategories eq 1) || !isset($display_subcategories) }
    <div id="subcategories" class="card card-block hidden-sm-down">
      <h2 class="subcategory-heading">{l s='Subcategories' d='Shop.Theme.Category'}</h2>

      <ul class="subcategories-list">
        {foreach from=$subcategories item=subcategory}
          <li>
            {if (isset($profile_params.c_config) && $profile_params.c_config.scategory_image == 1) || !isset($profile_params.c_config)}
            <div class="subcategory-image">
               <a href="{$subcategory.url}" title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
                {if !empty($subcategory.image.large.url)}
                <picture>
                  {if !empty($subcategory.image.large.sources.avif)}<source srcset="{$subcategory.image.large.sources.avif}" type="image/avif">{/if}
                  {if !empty($subcategory.image.large.sources.webp)}<source srcset="{$subcategory.image.large.sources.webp}" type="image/webp">{/if}
                  <img
                    class="img-fluid"
                    src="{$subcategory.image.large.url}"
                    alt="{$subcategory.name|escape:'html':'UTF-8'}"
                    loading="lazy"
                    width="{$subcategory.image.large.width}"
                    height="{$subcategory.image.large.height}"/>
                </picture>
                {/if}
              </a>
            </div>
            {/if}

            <h5><a class="subcategory-name" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'}</a></h5>
              {if isset($profile_params.c_config) && $profile_params.c_config.scategory_des != 1 && $category.description|count_characters > $profile_params.c_config.scategory_dleng}
                    {if $profile_params.c_config.scategory_des == 2}
                        <div id="category-description" class="readmore-wrap" class="text-muted">
                            <div class="less">
                                {$subcategory.description|truncate:$profile_params.c_config.scategory_dleng:'...'|escape:'html':'UTF-8' nofilter}
                            </div>
                            <div class="more">
                                {$subcategory.description|escape:'html':'UTF-8' nofilter}
                            </div>
                             <a class="readmore"  href="javascript:void(0)" data-more="{l s='Read More' d='Shop.Theme.Catalog'}" data-less="{l s='Read Less' d='Shop.Theme.Catalog'}">{l s='Read More' d='Shop.Theme.Catalog'}</a>
                        </div>
                    {/if}    
                {else}
                {if $subcategory.description}
                <div class="cat_desc">{$subcategory.description|unescape:'html' nofilter}</div>
                {/if}
                {/if}
              
          </li>
        {/foreach}
      </ul>
    </div>
  {/if}
{/if}
