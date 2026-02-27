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
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="fixed-width-xs">
                        <span class="title_box">
                            <input type="checkbox" name="checkme" id="checkme" onclick="checkDelBoxes(this.form, '{$input.name|escape:'html':'UTF-8'}[]', this.checked)" />
                        </span>
                    </th>
                    <th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='alcaurlcanonical'}</span></th>
                    <th>
                        <span class="title_box">
                            {$input.text|escape:'html':'UTF-8'}
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
            {foreach $input.values.query as $value}
                <tr>
                    <td>
                        {assign var=id_checkbox value={$input.name|escape:'html':'UTF-8'}|cat:'_'|cat:$value[$input.values.id]}
                        <input type="checkbox" 
                        name="{$input.name|escape:'html':'UTF-8'}[]" 
                        class="{$id_checkbox|escape:'html':'UTF-8'}" 
                        id="{$id_checkbox|escape:'html':'UTF-8'}" 
                        value="{$value[$input.values.id]|escape:'html':'UTF-8'}" 
                        {if isset($input.value[$input.name]) && in_array($value[$input.values.id], $input.value[$input.name])}checked="checked"{/if} 
                        {if isset($input.required) && $input.required && isset($input.required_values) && in_array($value[$input.values.id], $input.required_values)} required="required" {/if} />
                    </td>
                    <td>{$value[$input.values.id|escape:'html':'UTF-8']|escape:'html':'UTF-8'}</td>
                    <td>
                        {if isset($input.required) && $input.required && isset($input.required_values) && in_array($value[$input.values.id], $input.required_values)}<label class="required_value"></label>{/if} <label for="{$id_checkbox|escape:'html':'UTF-8'}">{$value.name|escape:'html':'UTF-8'}</label>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
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