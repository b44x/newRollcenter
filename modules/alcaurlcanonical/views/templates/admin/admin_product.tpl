       
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

            <div class="col-xs-10 col-md-10 form-group">
                <label>{l s='To configure the parameters you must do it from the redirection module' mod='alcaurlcanonical'}</label>
                <a href="{$url|escape:'html':'UTF-8'}" targe="_blank">{l s='Redirect module' mod='alcaurlcanonical'}</a>
            </div>  

            <div class="form-group col-xs-12">
                {if $results}
                    {foreach $results as $k => $l}
                        <div class="col-xs-12 col-md-3 form-group row">
                            {if ($l['redirect'] == 0)}
                                <div class="col-xs-12 col-md-4">
                                    {if ($l['redirect'] == 0)}
                                        <strong>{l s='Canonical' mod='alcaurlcanonical'}</strong>
                                    {/if}
                                </div>
                                <div class="col-xs-12 col-md-8">
                                    {$l['url']|escape:'html':'UTF-8'}
                                </div>
                            {/if}
                            {if ($l['redirect'] > 0)}
                                <div class="col-xs-12 col-md-4">
                                    {if ($l['redirect'] > 0)}
                                        <strong>{l s='Redirections' mod='alcaurlcanonical'}</strong>
                                    {/if}
                                </div>
                                <div class="col-xs-12 col-md-8">
                                    {$l['url']|escape:'html':'UTF-8'}
                                
                                </div>
                            {/if}
                        </div>
                    {/foreach}
                {/if}
            </div>  
            