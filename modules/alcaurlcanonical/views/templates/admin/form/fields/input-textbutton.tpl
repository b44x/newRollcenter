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

{assign var='value_text' value=$input.value[$input.name]}
<div class="col-xs-12 col-sm-8 nopadding-xs">
    <div class="form-group{if isset($disp_e[$input.name])} field_error{/if}">
        {if isset($input.maxchar)}
        <div class="input-group">
            <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
                <span class="text-count-down">{$input.maxchar|intval|escape:'html':'UTF-8'}</span>
            </span>
        {/if}
            <input type="text"
            name="{$input.name|escape:'html':'UTF-8'}"
            id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
            value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
            class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
            {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval|escape:'html':'UTF-8'}"{/if}
            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval|escape:'html':'UTF-8'}"{/if}
            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
            {if isset($input.required) && $input.required} required="required" {/if}
            {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}
            />
        {if isset($input.suffix)}{$input.suffix|escape:'html':'UTF-8'}{/if}
        {if isset($input.maxchar) && $input.maxchar}
        </div>
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
        <div class="col-lg-2 nopadding">
            <button type="button" class="btn btn-default{if isset($input.button.attributes['class'])} {$input.button.attributes['class']|escape:'html':'UTF-8'}{/if}{if isset($input.button.class)} {$input.button.class|escape:'html':'UTF-8'}{/if}"
                {foreach from=$input.button.attributes key=name item=value}
                    {if $name|lower != 'class'}
                        {$name|escape:'html':'UTF-8'}="{$value|escape:'html':'UTF-8'}"
                    {/if}
                {/foreach} >
                {$input.button.label|escape:'html':'UTF-8'}
            </button>
            <div class="clearfix"></div>
            <br>
        </div>
        {if isset($input.maxchar) && $input.maxchar}
            <script type="text/javascript">
                $(document).ready(function() {
                    countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
                });
            </script>
        {/if}
    </div>
</div>