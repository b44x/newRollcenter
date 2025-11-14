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
    <script>
        var labelNext = '{$labels.next|addslashes|escape:'javascript':'UTF-8'}';
        var labelPrevious = '{$labels.previous|addslashes|escape:'javascript':'UTF-8'}';
        var labelFinish = '{$labels.finish|addslashes|escape:'javascript':'UTF-8'}';
    </script>
    <div class="row">
        <div class="col-sm-12">
            <div id="dw_wizard">
                <ul class="steps nbr_steps_{$wizard_steps.steps|count}">
                    {foreach from=$wizard_steps.steps key=step_nbr item=step}
                        <li>
                            <a href="#step-{$step_nbr|escape:'html':'UTF-8' + 1}">
                                <span class="stepNumber">{$step_nbr|escape:'html':'UTF-8' + 1}</span>
                                <span class="stepDesc">
							{$step.title|escape:'html':'UTF-8'}<br/>
                                    {if isset($step.desc)}
                                        <small>{$step.desc}</small>{/if}
						</span>
                                <span class="chevron"></span>
                            </a>
                        </li>
                    {/foreach}
                </ul>
                {foreach from=$wizard_contents.contents key=step_nbr item=content}
                    <div id="step-{$step_nbr|escape:'html':'UTF-8' + 1}" class="step_container">
                        {if !empty($processes)}
                            <div>
                                <h1 class="dw-center">Migration Step</h1>
                                <div class="progress-containerw">
                                    <div class="panel kpi-container">
                                        <div class="panel-heading">
                                            <i class="fas fa-exchange-alt"></i>
                                            Migartion Process
                                        </div>
                                            {assign var="processCount" value={$processes|@count|escape:'htmlall':'UTF-8'}}
                                            {foreach from=$processes item=process}
                                                {math equation="x / y" x=12 y=$processCount assign="lgvalue"}
                                                {math equation="x / y" x=24 y=$processCount assign="smvalue"}

                                                    <div id="{$process.type|escape:'htmlall':'UTF-8'}"
                                                         class="box-stats">
                                                        <div>
                                                        
                                                            {*<i class="{$icon|escape:'htmlall':'UTF-8'}"></i>*}
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
                            <div>
                                <a href="#" class="buttonResume btn btn-success">Resume</a>
                            </div>
                        {/if}
                        <br>
                        {$content}
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
{/block}
