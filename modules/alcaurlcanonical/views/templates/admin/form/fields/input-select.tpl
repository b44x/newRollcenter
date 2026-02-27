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
    <div class="form-group{if isset($disp_e[$input.name])} field_error{/if}">
    {if isset($input.options.query) && !$input.options.query && isset($input.empty_message)}
        {$input.empty_message|escape:'html':'UTF-8'}
        {$input.required = false}
        {$input.desc = null}
    {else}
        <select name="{$input.name|escape:'html':'UTF-8'}{if isset($input.multiple) && $input.multiple}[]{/if}"
                class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} fixed-width-xl"
                id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                {if isset($input.multiple) && $input.multiple} multiple="multiple"{/if}
                {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                {if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if}
                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                {if isset($input.required) && $input.required} required="required" {/if}>
            {if isset($input.options.default)}
                <option value="{$input.options.default.value|escape:'html':'UTF-8'}">{$input.options.default.label|escape:'html':'UTF-8'}</option>
            {/if}
            {if isset($input.options.optiongroup)}
                {foreach $input.options.optiongroup.query AS $optiongroup}
                    <optgroup label="{$optiongroup[$input.options.optiongroup.label]|escape:'html':'UTF-8'}">
                        {foreach $optiongroup[$input.options.options.query] as $option}
                            <option value="{$option[$input.options.options.id]|escape:'html':'UTF-8'}"
                                {if isset($input.multiple)}
                                    {foreach $input.value[$input.name] as $field_value}
                                        {if $field_value == $option[$input.options.options.id]}selected="selected"{/if}
                                    {/foreach}
                                {else}
                                    {if $input.value[$input.name] == $option[$input.options.options.id]}selected="selected"{/if}
                                {/if}
                            >{$option[$input.options.options.name]|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </optgroup>
                {/foreach}
            {else}

                {foreach $input.options.query as $option}
                    {if is_object($option)}
                        <option value="{$option->$input.options.id|escape:'html':'UTF-8'}"
                            {if isset($input.multiple)}
                                {foreach $input.value[$input.name] as $field_value}
                                    {if $field_value == $option->$input.options.id}
                                        selected="selected"
                                    {/if}
                                {/foreach}
                            {else}
                                {if $input.value[$input.name] == $option->$input.options.id}
                                    selected="selected"
                                {/if}
                            {/if}
                        >{$option->$input.options.name|escape:'html':'UTF-8'}</option>
                    {elseif $option == "-"}
                        <option value="">-</option>
                    {else}
                        {if isset($option@iteration) && $option@iteration == 1}
                            {if isset($input.default_option) && is_array($input.default_option)}
                                <option value="{$input.default_option.value|escape:'html':'UTF-8'}">{$input.default_option.field|escape:'html':'UTF-8'}</option>
                            {/if}
                        {/if}
                        <option value="{$option[$input.options.id|escape:'html':'UTF-8']|escape:'html':'UTF-8'}"
                            {if isset($input.multiple)}
                                {foreach $input.value[$input.name] as $field_value}
                                    {if $field_value == $option[$input.options.id]}
                                        selected="selected"
                                    {/if}
                                {/foreach}
                            {else}
                                {if $input.value[$input.name] == $option[$input.options.id]}
                                    selected="selected"
                                {/if}
                            {/if}
                        >{$option[$input.options.name]|escape:'html':'UTF-8'}</option>
                    {/if}
                {/foreach}
            {/if}
        </select>
    {/if}
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
</div>