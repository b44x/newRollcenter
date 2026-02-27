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

    <div class="row">
        <a
            class="btn-save-general btn btn-primary tooltip-link btn-sm forced pull-right"
            href="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getCard&id"
                ><i class="icon-plus"></i>&nbsp;&nbsp; 
            {l s='New' mod='alcaurlcanonical'}
         </a>   
    </div>
    <form
        action="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getList"
         class="form-group ">
        <table class="table">
            <thead>
                <tr>
                    <th>
                        {l s='ID' mod='alcaurlcanonical'}
                    </th>
                    <th >
                        {l s='Type' mod='alcaurlcanonical'}
                    </th>
                    <th>
                        {l s='Name' mod='alcaurlcanonical'}
                    </th>
                    <th>
                        {l s='Url Canonical' mod='alcaurlcanonical'}
                    </th>
                    <th>
                        {l s='Redirection' mod='alcaurlcanonical'}
                    </th>
                    <th>
                        {l s='Redirect Url' mod='alcaurlcanonical'}
                    </th>
                    <th>
                        {l s='Object Redirect' mod='alcaurlcanonical'}
                    </th>
                    <th>
                        {l s='Id object redirect' mod='alcaurlcanonical'}
                    </th>
                    <th style="width:50px"></th>
                    <th style="width:50px"></th>
                </tr>

                <tr class="column-filters ">                       
                        <td width="100">
                            <input name="id_alcaurl_canonical" id="id_alcaurl_canonical" class="form-control" value="{if isset($smarty_filters['id_alcaurl_canonical'])}{$smarty_filters['id_alcaurl_canonical']|escape:'html':'UTF-8'}{/if}">
                        </td>
                        <td>
                            <select name="type"> 
                                <option value="">--</option>
                                {foreach $page_types as $k => $l}
                                    <option value="{$l|escape:'html':'UTF-8'}">{$k|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </td> 
                        <td>
                            <input name="name" id="name" class="form-control" value="{if isset($smarty_filters['name'])}{$smarty_filters['name']|escape:'html':'UTF-8'}{/if}">
                        </td>
                        <td>
                            <input name="url" id="url" class="form-control" value="{if isset($smarty_filters['url'])}{$smarty_filters['url']|escape:'html':'UTF-8'}{/if}">
                        </td>
                        <td>
                            <input name="redirect" id="url" class="form-control" value="{if isset($smarty_filters['redirect'])}{$smarty_filters['redirect']|escape:'html':'UTF-8'}{/if}">
                        </td>          
                        <td>
                            <input name="url_redirect" id="url" class="form-control" value="{if isset($smarty_filters['url_redirect'])}{$smarty_filters['url_redirect']|escape:'html':'UTF-8'}{/if}">
                        </td>  
                        <td>
                            <select name="object_redirect"> 
                                <option value="">--</option>
                                {foreach $page_types as $k => $l}
                                    <option value="{$l|escape:'html':'UTF-8'}">{$k|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </td>          
                        <td>
                            <input name="id_object_redirect" id="url" class="form-control" value="{if isset($smarty_filters['id_object_redirect'])}{$smarty_filters['id_object_redirect']|escape:'html':'UTF-8'}{/if}">
                        </td>  
                        <td colspan="2">
                            <input type="hidden" name="filterlist" value="true">
                            <button type="submit" class="col-xs-12 btn-save-general btn btn-primary grid-search-button d-block pull-right" title="Buscar" name="category[actions][search]"><i class="material-icons">search</i>
                                Search
                            </button>
                        </td>
                </tr>



            </thead>
        {if $list}
            {foreach $list as $k => $line}
                <tr>
                    <td>
                        {$line['id_alcaurl_canonical']|escape:'html':'UTF-8'}
                    </td>
                    <td>
                        {foreach $page_types as $k => $l}
                            {if $line['type'] == $l}
                                {$k|escape:'html':'UTF-8'}
                            {/if}
                        {/foreach}
                    </td>
                    <td>
                        {$line['name']|escape:'html':'UTF-8'}
                    </td>
                    <td>
                        {if $line['url']}
                            {$line['url']|escape:'html':'UTF-8'}
                        {else}
                            -
                        {/if}
                    </td>
                    <td>
                        {if $line['redirect']}
                            {$line['redirect']|escape:'html':'UTF-8'}
                        {else}
                            -
                        {/if}
                    </td>
                    <td>
                        {if $line['url_redirect']}
                            {$line['url_redirect']|escape:'html':'UTF-8'}
                        {else}
                            -
                        {/if}
                    </td>
                    <td>
                        {if $line['object_redirect'] != 6}
                        {foreach $page_types as $k => $l}
                            {if $line['object_redirect'] == $l}
                                {$k|escape:'html':'UTF-8'}
                            {/if}
                        {/foreach}
                        {else}
                            -
                        {/if}
                    </td>
                    <td>
                        {if $line['id_object_redirect'] > 0}
                            {$line['id_object_redirect']|escape:'html':'UTF-8'}
                        {else}
                            -
                        {/if}
                    </td>
                    <td >
                        <a
                            class="btn-save-general btn btn-default btn-open tooltip-link btn-sm forced"
                            href="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getCard&id_alcaurl_canonical={$line['id_alcaurl_canonical']|escape:'html':'UTF-8'}"
                            >
                                <i class="material-icons">mode_edit</i> 
                        </a>
                    </td>
                    <td >
                        <a
                            class="btn-save-general btn btn-open tooltip-link btn-sm btn-danger alertdelete forced"
                            href="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=delete&id_alcaurl_canonical={$line['id_alcaurl_canonical']|escape:'html':'UTF-8'}"
                            >
                                <i class="material-icons">delete</i>
                        </a>
                    </td>
                </tr>
            {/foreach}
             {$helperapm->getListPagination()} {* HTML CONTENT *}
        {/if}
        </table>
    </form>
