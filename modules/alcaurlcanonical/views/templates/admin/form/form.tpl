{**
 * 2024 ALCALINK E-COMMERCE & SEO, S.L.L.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author ALCALINK E-COMMERCE & SEO, S.L.L. <info@alcalink.com>
 * @copyright  2024 ALCALINK E-COMMERCE & SEO, S.L.L.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * Registered Trademark & Property of ALCALINK E-COMMERCE & SEO, S.L.L.
*}

{foreach $fieldset as $key => $field}
    {if $key == 'alerts' && $field}
        {foreach $field as $alert}
            <div class="form-group">
                <div class="alert alert-{if isset($alert.type) && $alert.type}{$alert.type|escape:'html':'UTF-8'}{else}info{/if}">
                    {if isset($alert.header) && $alert.header}
                        <h4 class="alert-heading">{$alert.header|escape:'html':'UTF-8'}</h4>
                    {/if}
                    {if isset($alert.content) && $alert.content}
                        {foreach $alert.content as $value}
                            <p>{$value|escape:'quotes':'UTF-8'}</p>
                        {/foreach}
                    {/if}
                </div>
            </div>
        {/foreach}
    {elseif $key == 'input' && $field}
        <div class="form-group clearfix clear">
            <div class="row">
                {foreach $field as $input}
                    {if $input.type == 'hidden'}
                        <input type="hidden" name="{$input.name|escape:'html':'UTF-8'}" id="{$input.name|escape:'html':'UTF-8'}" value="{$input.value[$input.name]|escape:'html':'UTF-8'}" />
                    {else}
                        {if isset($input.label)}
                            <div class="col-xs-12 col-sm-3 nopadding-xs">
                                <label class="pts-label-tooltip col-xs-12 nopadding control-label{if isset($input.required) && $input.required} required{/if}{if isset($disp_e[$input.name])} field_label_error{/if}">
                                    {if isset($input.hint)}
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{if is_array($input.hint)}
													{foreach $input.hint as $hint}
														{if is_array($hint)}
															{$hint.text|escape:'html':'UTF-8'}
														{else}
															{$hint|escape:'html':'UTF-8'}
														{/if}
													{/foreach}
												{else}
													{$input.hint|escape:'html':'UTF-8'}
												{/if}">
                                    {/if}
                                    {$input.label|escape:'html':'UTF-8'}
                                    {if isset($input.hint)}
                                    </span>
                                    {/if}
                                </label>
                            </div>
                        {/if}

                        {if $input.type == 'text' || $input.type == 'tags'}
                            {include file='./fields/input-text.tpl'}
                        {else if $input.type == 'textbutton'}
                            {include file='./fields/input-textbutton.tpl'}
                        {else if $input.type == 'number'}
                            {include file='./fields/input-number.tpl'}
                        {elseif $input.type == 'color'}
                            {include file='./fields/input-color.tpl'}
                        {elseif $input.type == 'date'}
                            {include file='./fields/input-date.tpl'}
                        {elseif $input.type == 'datetime'}
                            {include file='./fields/input-datetime.tpl'}
                        {else if $input.type == 'swap'}
                            {include file='./fields/input-swap.tpl'}
                        {elseif $input.type == 'select'}
                            {include file='./fields/input-select.tpl'}
                        {elseif $input.type == 'radio'}
                            {include file='./fields/input-radio.tpl'}
                        {elseif $input.type == 'switch'}
                            {include file='./fields/input-switch.tpl'}
                        {elseif $input.type == 'textarea'}
                            {include file='./fields/input-textarea.tpl'}
                        {elseif $input.type == 'checkbox'}
                            {include file='./fields/input-checkbox.tpl'}
                        {elseif $input.type == 'password'}
                            {include file='./fields/input-password.tpl'}
                        {elseif $input.type == 'group'}
                            {include file='./fields/input-group.tpl'}
                        {elseif $input.type == 'file'}
                            {include file='./fields/input-file.tpl'}
                        {elseif $input.type == 'categories'}
                            {include file='./fields/input-categories.tpl'}
                        {elseif $input.type == 'search'}
                            {include file='./fields/input-search.tpl'}
                        {elseif $input.type == 'html'}
                            {include file='./fields/input-html.tpl'}
                        {elseif $input.type == 'link'}
                            {include file='./fields/link.tpl'}
                        {/if}

                        {if version_compare($ps_version, '1.6.1.2', '<')}
                            <script>
                                function countDown($source, $target) {
                                    var max = $source.attr("data-maxchar");
                                    $target.html(max-$source.val().length);

                                    $source.keyup(function(){
                                        $target.html(max-$source.val().length);
                                    });
                                }
                            </script>
                        {/if}
                    {/if}
                {/foreach}
            </div>
        </div>
    {elseif ($key == 'docs' && $field)}
        {foreach $field as $doc}
            <div class="form-group">
                {if isset($doc.text) && isset($doc.iso) && isset($doc.url)}
                    <a class="btn btn-primary documentation" href="{$doc.url|escape:'html':'UTF-8'}" target="_blank">
                        <span>{$doc.text|escape:'html':'UTF-8'}</span><div class="alc_flag alc_flag-{$doc.iso|escape:'html':'UTF-8'}"></div>
                    </a>
                {/if}
            </div>
        {/foreach}
    {/if}
{/foreach}