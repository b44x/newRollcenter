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
        <div class="input-group col-lg-4">
            <input
                id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                type="text"
                data-hex="true"
                class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"
                name="{$input.name|escape:'html':'UTF-8'}"
                value="{$input.value[$input.name]|escape:'html':'UTF-8'}" 
                autocomplete="off" 
                {if isset($input.required) && $input.required} required="required" {/if} />
            <span class="input-group-addon">
                <i class="icon-calendar-empty"></i>
            </span>
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

        <script type="text/javascript">
            if ($('input[name="{$input.name|escape:'html':'UTF-8'}"]').length > 0) {
                $('input[name="{$input.name|escape:'html':'UTF-8'}"]').datetimepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd',
                    // Define a custom regional settings in order to use PrestaShop translation tools
                    currentText: '{l s='Now' mod='alcaurlcanonical'}',
                    closeText: '{l s='Done' mod='alcaurlcanonical'}',
                    ampm: false,
                    amNames: ['AM', 'A'],
                    pmNames: ['PM', 'P'],
                    timeFormat: 'hh:mm:ss tt',
                    timeSuffix: '',
                    timeOnlyTitle: '{l s='Choose Time' mod='alcaurlcanonical'}',
                    timeText: '{l s='Time' mod='alcaurlcanonical'}',
                    hourText: '{l s='Hour' mod='alcaurlcanonical'}',
                    minuteText: '{l s='Minute' mod='alcaurlcanonical'}',
                });
            }
        </script>
    </div>
</div>