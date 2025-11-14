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



<section id="content" class="page-content page-not-found">
  {block name='page_content'}
  <div class="image-404">
    <img src="{$urls.img_url}404-image.png">
    <div class="content">
      <h2>{l s='4 0 4' d='Shop.Theme.Global'}</h2>
      <p>{l s='Oops, it looks like you are lost ...' d='Shop.Theme.Global'}</p>
      <a class="btn btn-primary" href="{$urls.base_url}">{l s='Back to Homepage' d='Shop.Theme.Global'}</a>
    </div>
  
    
    
    {block name='search'}
    {hook h='displaySearch'}
    {/block}
  
    {block name='hook_not_found'}
    {hook h='displayNotFound'}
    {/block}
    
  
  
    {/block}
</section>
