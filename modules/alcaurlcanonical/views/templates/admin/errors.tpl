       
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
    <form 
        action="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=add"
        class="form-group">
        <div class="row form-group">
            <label>{l s='Select the type of object you want to edit and indicate the address of the canonical url or redirect' mod='alcaurlcanonical'}</label>
            <label>{l s='Example /2-category' mod='alcaurlcanonical'}</label>
        </div>
        <div class="form-group">
            <input type="hidden" name="id" value="{$object->id|escape:'html':'UTF-8'}" />
            <div class="row col-xs-4 form-group">
                <strong class="col-xs-12">{l s='Page type' mod='alcaurlcanonical'}</strong>
                <select name="type"> 
                    {foreach $page_types as $k => $l}
                        <option value="{$l|escape:'html':'UTF-8'}" {if $l == $object->type}SELECTED{/if}>{$k|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <div class="row col-xs-4 form-group" data-display="1" style="padding-left: 20px; {if $object->type != 1 && $object->type}display:none{/if}">
                <strong class="col-xs-12">{l s='Search Procut' mod='alcaurlcanonical'}</strong>
                {$helperapm->searchProductForm('setProduct')} {* HTML CONTENT *}
                <div class="row col-xs-12 form-group" id="productset" style="min-height: 30px;border: 1px solid #d0d0d0;margin-left: -1px;padding: 6px;">
                    {if $object->id_object > 0 && $object->type == 1}
                        <input type="hidden" name="id_object" value="{$object->id_object|escape:'html':'UTF-8'}"  />{$name|escape:'html':'UTF-8'}
                    {/if}
                </div>
            </div>
            <div class="row col-xs-4 form-group" data-display="2" style="padding-left: 20px;{if $object->type != 2}display:none{/if}">
                <strong class="col-xs-12">{l s='Categories' mod='alcaurlcanonical'}</strong>
                <select {if $object->type != 2}data-{/if}name="id_object"> 
                    {foreach $categories as $k => $l}
                        <option value="{$l['id_category']|escape:'html':'UTF-8'}" {if $l['id_category'] == $object->id_object}SELECTED{/if}>{$l['name']|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <div class="row col-xs-4 form-group" data-display="4" style="padding-left: 20px;{if $object->type != 4}display:none{/if}">
                <strong class="col-xs-12">{l s='Options' mod='alcaurlcanonical'}</strong>
                <select {if $object->type != 4}data-{/if}name="id_object"> 
                    <option value="1">{l s='Main category page' mod='alcaurlcanonical'}</option>
                </select>
            </div>

            
            <div class="row col-xs-12 form-group">
                <strong class="col-xs-12">{l s='Url' mod='alcaurlcanonical'}</strong>
                {$helperapm->getInput('url', $object->url)} {* HTML CONTENT *}
            </div>

                
            <div class="form-group row col-xs-12">
                {if $object->id > 0}
                    <a 
                        href="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getCard"
                         class="forced btn btn-primary  has-action btn-save-general">
                        <i class="icon-save"></i>&nbsp;{l s='New' mod='alcaurlcanonical'}
                    </a>
                    <button 
                        type="submit" value="1" id="module_form_submit_btn" name="submitalcabaseModule"
                        class="btn btn-primary pull-right has-action btn-save-general">
                        <i class="icon-save"></i>&nbsp;{l s='Modify' mod='alcaurlcanonical'}
                    </button>
                {else}
                    <button type="submit" value="1" id="module_form_submit_btn" name="submitalcabaseModule" class="btn btn-primary pull-right has-action btn-save-general">
                        <i class="icon-save"></i>&nbsp;{l s='Save all' mod='alcaurlcanonical'}
                    </button>
                {/if}
            </div>   
        </div>
    </form>
            