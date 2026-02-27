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

<div class="col-xs-12 col-sm-8 nopadding-xs">
    <div class="form-group swap-container">
        <div class="form-control-static">
            <div class="col-xs-6 nopadding">
                <select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} available{$input.name|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}_available[]" multiple="multiple">
                {foreach $input.options.query AS $option}
                    {if is_object($option)}
                        {if is_array($input.value[$input.name]) && !in_array($option->$input.options.id, $input.value[$input.name])}
                            <option value="{$option->$input.options.id|escape:'html':'UTF-8'}">{$option->$input.options.name|escape:'html':'UTF-8'}</option>
                        {/if}
                    {elseif $option == "-"}
                        <option value="">-</option>
                    {else}
                        {if !isset($input.value[$input.name])}
                            <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}">{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
                        {else}
                            {if is_array($input.value[$input.name]) && !in_array($option[$input.options.id], $input.value[$input.name])}
                                <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}">{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
                            {/if}
                        {/if}
                    {/if}
                {/foreach}
                </select>
                <a href="#" class="btn btn-default btn-block add{$input.name|escape:"html":"UTF-8"}">{l s='Add' mod='alcaurlcanonical'} <i class="icon-arrow-right"></i></a>
            </div>
            <div class="col-xs-6{if isset($disp_e[$input.name])} field_error{/if}">
                <select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} selected{$input.name|escape:'html':'UTF-8'}" name="{$input.name|escape:'html':'UTF-8'}[]" multiple="multiple" {if isset($input.required) && $input.required} required="required" {/if}>
                {foreach $input.options.query AS $option}
                    {if is_object($option)}
                        {if is_array($input.value[$input.name]) && in_array($option->$input.options.id, $input.value[$input.name])}
                            <option value="{$option->$input.options.id|escape:'html':'UTF-8'}">{$option->$input.options.name|escape:'html':'UTF-8'}</option>
                        {/if}
                    {elseif $option == "-"}
                        <option value="">-</option>
                    {else}
                        {if is_array($input.value[$input.name]) && in_array($option[$input.options.id], $input.value[$input.name])}
                            <option value="{$option[$input.options.id]|escape:'html':'UTF-8'}">{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
                        {/if}
                    {/if}
                {/foreach}
                </select>
                <a href="#" class="btn btn-default btn-block remove{$input.name|escape:"html":"UTF-8"}"><i class="icon-arrow-left"></i> {l s='Remove' mod='alcaurlcanonical'}</a>
            </div>
        </div>
        {if isset($input.desc) && !empty($input.desc)}
        <div class="clearfix"></div>
        <div class="help-block">
            {if is_array($input.desc)}
                {foreach $input.desc as $p}
                    {if is_array($p)}
                        <span id="{$p.id|escape:'html':'UTF-8'}">{$p.text|escape:'html':'UTF-8'}</span><br />
                    {else}
                        {$p|escape:'html':'UTF-8'}<br />
                    {/if}
                {/foreach}
            {else}
                {$input.desc|escape:'html':'UTF-8'}
            {/if}
        </div>
        {/if}
    </div>
    <script type="text/javascript">
        function alcbindSwapSave()
        {
            if ($('.selected{$input.name|escape:"html":"UTF-8"} option').length !== 0)
                $('.selected{$input.name|escape:"html":"UTF-8"} option').attr('selected', 'selected');
            else
                $('.available{$input.name|escape:"html":"UTF-8"} option').attr('selected', 'selected');
        }

        function alcbindSwapButton(prefix_button, prefix_select_remove, prefix_select_add)
        {
            $('.'+prefix_button+'{$input.name|escape:"html":"UTF-8"}').on('click', function(e) {
                e.preventDefault();
                $('.' + prefix_select_remove + '{$input.name|escape:"html":"UTF-8"} option:selected').each(function() {
                    $('.' + prefix_select_add + '{$input.name|escape:"html":"UTF-8"}').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
                    $(this).remove();
                });
                $('.selected{$input.name|escape:"html":"UTF-8"} option').prop('selected', true);
            });
        }

        $(document).ready(function() {
            /** make sure that all the swap id is present in the dom to prevent mistake **/
            if (typeof $('.add{$input.name|escape:"html":"UTF-8"}') !== undefined && typeof $('.remove{$input.name|escape:"html":"UTF-8"}') !== undefined &&
            typeof $('.selected{$input.name|escape:"html":"UTF-8"}') !== undefined && typeof $('.available{$input.name|escape:"html":"UTF-8"}') !== undefined)
            {
                alcbindSwapButton('add', 'available', 'selected');
                alcbindSwapButton('remove', 'selected', 'available');

                $('button:submit').click(alcbindSwapSave);
            }
        });
    </script>
</div>