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
{$values = $input_field->getDisplayedValues($group.fields)}
{if count($values) === 1}
    {$urls = $input_field->getImagesUrls()}
  <span {if isset($urls['image_url'])}data-url="{$urls['image_url']|escape:'htmlall':'UTF-8'}"{/if}>{$values[0]|escape:'htmlall':'UTF-8'}</span>
{else}
  <ul>
      {foreach $values as $value}
        <li>{$value|escape:'htmlall':'UTF-8'}</li>
      {/foreach}
  </ul>
{/if}
