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
{if $pages > 0}
    <tr class="column-filters ">
            <td colspan="10" style="text-align: center;">
                <div class="apm_pagination_footer"  style="display: inline-flex;align-items: baseline;">
                    <strong>{$p|escape:'html':'UTF-8'}/{$pages|escape:'html':'UTF-8'}</strong>
                    {if $p > 0}
                        <a 
                        href="{$url_ajax|escape:'quotes':'UTF-8'}&filterlist=1&functionLibs={$functionLibs|escape:'html':'UTF-8'}&{$pagination_url|escape:'quotes':'UTF-8'}&p={($p - 1)|escape:'htmlall':'UTF-8'}&submitFilter{$definition['table']|escape:'html':'UTF-8'}={($p - 1)|escape:'htmlall':'UTF-8'}"
                        type="submit"
                        name="p"
                        class="page-link btn btn-save-general forced" aria-label="Nrev" 
                        >
                            <i class="icon-angle-left"></i>
                        </a>
                    {/if}
                    {if $pages > 0}
                        <input 
                        href="{$url_ajax|escape:'quotes':'UTF-8'}&filterlist=1&functionLibs={$functionLibs|escape:'html':'UTF-8'}&{$pagination_url|escape:'quotes':'UTF-8'}"
                        {* name="p" *}
                        name="submitFilter{$definition['table']|escape:'html':'UTF-8'}"
                        style="max-width: 50px;float: none; text-align: center;"
                        type="p"
                        class="page-link forced " aria-label="Next" 
                        value="{$p|escape:'html':'UTF-8'}"
                        disabled
                        />
                        {if $pages > $p}
                            <a 
                            href="{$url_ajax|escape:'quotes':'UTF-8'}&filterlist=1&functionLibs={$functionLibs|escape:'html':'UTF-8'}&{$pagination_url|escape:'quotes':'UTF-8'}&p={($p + 1)|escape:'htmlall':'UTF-8'}&submitFilter{$definition['table']|escape:'html':'UTF-8'}={($p + 1)|escape:'htmlall':'UTF-8'}"
                            name="p"
                            type="submit"
                            class="page-link btn btn-save-general forced" aria-label="Next" 
                            value="{($p + 2)|escape:'htmlall':'UTF-8'}"
                            >
                                <i class="icon-angle-right"></i>
                            </a>
                        {/if}
                    {/if}
                </div>
            </td>
    <tr>
{/if}

