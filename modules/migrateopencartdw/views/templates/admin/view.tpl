{*
* 2007-2024 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
    <div class="row">
        <div id="step-3-clear" class="alert-success wizard_error" style="display:none">
            <ul>
                <li>Clear cache and re-build search index done.</li>
            </ul>
        </div>

        <div id="step-3-done" class="alert alert-success" style="display:none">
            <ul>
                <li>Migration successfully done.</li>
            </ul>
        </div>
    </div>
    <h1 class="dw-center">Migration Step</h1>
    <div class="row">
        <div class="col-xs-12 col-sm-12 progress-container">
            <div class="panel kpi-container">
                <div class="panel-heading">
                    <i class="fas fa-exchange-alt"></i>
                    Migration Process
                </div>
                {assign var="processCount" value={$processes|@count|escape:'htmlall':'UTF-8'}}
                {if !$processes}
                    <h2>{l s="There is no new data on your source."  mod='migrateopencartdw'}</h2>
                {/if}
                {foreach from=$processes item=process}
                    {math equation="x / y" x=12 y=$processCount assign="lgvalue"}
                    {math equation="x / y" x=24 y=$processCount assign="smvalue"}
                    <div id="{$process.type|escape:'htmlall':'UTF-8'}"
                         class="box-stats">
                        <div>
                            <span class="title">{$process.type|ucfirst|escape:'htmlall':'UTF-8'}</span>
                            <span class="subtitle">imported {$process.total|escape:'htmlall':'UTF-8'}
                                /{$process.imported|escape:'htmlall':'UTF-8'}</span>
                            {math equation="y / x * 100" x=$process.total y=$process.imported assign="percents"}
                            <span class="value">{if $percents == false}0{else}{$percents|string_format:"%d"|escape:'htmlall':'UTF-8'}{/if}
                                %</span>

                            <div class="progress-info">
                                <div class="progress">
                                            <span style="width: {if $percents == false}0{else}{$percents|escape:'htmlall':'UTF-8'}{/if}%;"
                                                  class="progress-bar"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
    <div class="row">
        <a href="#" class="buttonTry btn btn-success" style="display: none; float: right">Try Import</a>
        <a href="#" class="buttonClear btn btn-success" style="display: none; float: right">Clear cache & Re-Buil
            Index</a>
    </div>
{/block}


