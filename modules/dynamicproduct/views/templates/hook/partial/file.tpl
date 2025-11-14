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
{if is_array($input_field->data_obj) && !empty($input_field->data_obj)}
    <ul class="dp-files">
        {foreach from=$input_field->data_obj item=upload}
            <li>
                <a target="_blank"
                   href="{$input_field->getFileUrl($upload.file)|escape:'htmlall':'UTF-8'}"
                   title="{$upload.filename|escape:'htmlall':'UTF-8'}"
                >
                    {$upload.filename|escape:'htmlall':'UTF-8'}
                </a>
            </li>
        {/foreach}
    </ul>
    {if isset($is_admin) && $is_admin && !$is_pdf}
        <div class="form-group mt-1">
            <a class="btn btn-default"
               href="{$input_field->getDownloadUrl(Order::getOrderByCartId($input->id_cart))|escape:'htmlall':'UTF-8'}"
            >{l s='Download' mod='dynamicproduct'}</a>
        </div>
    {/if}
{else}
    <a target="_blank" href="{$input_field->getFileUrl($input_field->value)|escape:'htmlall':'UTF-8'}">
        {$input_field->value|escape:'htmlall':'UTF-8'}
    </a>
{/if}
