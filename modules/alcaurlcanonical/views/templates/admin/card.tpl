       
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
        <div class="alert alert-info">
            <p>{l s='Select the type of object you want to edit and indicate the address of the canonical url or redirect' mod='alcaurlcanonical'}</p>
            <p>{l s='Example /2-category' mod='alcaurlcanonical'}</p>
        </div>
        <div class="form-group">
            <input type="hidden" name="id_alcaurl_canonical" value="{$object->id_alcaurl_canonical|escape:'html':'UTF-8'}" />
            <div class="row col-xs-4 form-group">
                <label>{l s='Page type' mod='alcaurlcanonical'}</label>
                <select id="url_type" name="type"> 
                    {foreach $page_types as $k => $l}
                        {if $l != 3 AND $l != 5}
                            <option value="{$l|escape:'html':'UTF-8'}" {if $l == $object->type}SELECTED{/if}>{$k|escape:'html':'UTF-8'}</option>
                        {/if}
                    {/foreach}
                </select>
            </div>
            <div class="row col-xs-4 form-group" data-display="1" style="padding-left: 20px; {if $object->type != 1 && $object->type}display:none{/if}">
                <label>{l s='Search Product' mod='alcaurlcanonical'}</label>
                {$helperapm->searchProductForm('setProduct')} {* HTML CONTENT *}
                <div class="row col-xs-12 form-group" id="productset" style="{if $object->id_object == 0}display:none;{/if}min-height: 30px;border: 1px solid #d0d0d0;margin-left: 0px;padding: 6px;">
                    {if $object->id_object > 0 && $object->type == 1}
                        <input type="hidden" name="id_object" value="{$object->id_object|escape:'html':'UTF-8'}"  />{$object->id_object|escape:'html':'UTF-8'} - {$name|escape:'html':'UTF-8'}
                    {/if}
                </div>
            </div>
            <div class="row col-xs-4 form-group" data-display="2" style="padding-left: 20px;{if $object->type != 2}display:none{/if}">
                <label>{l s='Categories' mod='alcaurlcanonical'}</label>
                <select {if $object->type != 2}data-{/if}name="id_object"> 
                    {foreach $categories as $k => $l}
                        <option value="{$l['id_category']|escape:'html':'UTF-8'}" {if $l['id_category'] == $object->id_object}SELECTED{/if}>{$l['name']|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <div class="row col-xs-4 form-group" data-display="4" style="padding-left: 20px;{if $object->type != 4}display:none{/if}">
                <label>{l s='Options' mod='alcaurlcanonical'}</label>
                <select {if $object->type != 3}data-{/if}name="id_object"> 
                    {foreach $cms as $k => $l}
                        <option value="{$l['id_cms']|escape:'html':'UTF-8'}" {if $l['id_cms'] == $object->id_object}SELECTED{/if}>{$l['meta_title']|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>

            
            <div class="row col-xs-12 form-group" style="margin-top:15px;">
                <label>{l s='Canonical Url' mod='alcaurlcanonical'}</label>
                {$helperapm->getInput('url', $object->url)} {* HTML CONTENT *}
            </div>

            <div class="row col-xs-12 form-group">
                <div class="row col-xs-2 form-group">
                    <label>{l s='Redirection' mod='alcaurlcanonical'}</label>
                    <select name="redirect" class="col-xs-10 alcaurlcanonicaltogglechange2" data-target=".alcaurlcanonicaltoggleredirect">
                        <option value="">{l s='None' mod='alcaurlcanonical'}</option>
                        {foreach $redirection_types as $k => $l}
                            <option value="{$k|escape:'html':'UTF-8'}" {if $object->redirect == $k}SELECTED{/if}>{$k|escape:'html':'UTF-8'} - {$l|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="row col-xs-2 form-group alcaurlcanonicaltoggleredirect">
                    <label>{l s='Redirect to' mod='alcaurlcanonical'}</label>
                    <select class="col-xs-10 alcaurlcanonicaltogglechange" name="object_redirect" data-target=".alcaurlcanonicaltoggle">
                        <option value="6" {if 6 == $object->object_redirect}SELECTED{/if}>{l s='Url' mod='alcaurlcanonical'}</option>
                        <option value="1" {if 1 == $object->object_redirect}SELECTED{/if}>{l s='Product' mod='alcaurlcanonical'}</option>
                        <option value="2" {if 2 == $object->object_redirect}SELECTED{/if}>{l s='Category' mod='alcaurlcanonical'}</option>
                        <option value="4" {if 4 == $object->object_redirect}SELECTED{/if}>{l s='CMS' mod='alcaurlcanonical'}</option>
                        {**<option value="5" {if 5 == $object->object_redirect}SELECTED{/if}>{l s='Indexpage' mod='alcaurlcanonical'}</option>**}
                    </select>
                </div>
                <div class="row col-xs-8 form-group alcaurlcanonicaltoggle alcaurlcanonicaltoggleredirect" data-attr="id_object_redirect">
                    <label>{l s='Destination' mod='alcaurlcanonical'}</label>
                    <div data-id="6" class="row col-xs-12 form-group">
                        {$helperapm->getInput('url_redirect', $object->url_redirect)} {* HTML CONTENT *}
                        {* <input  type="text" name="url_redirect" value="{$object->url_redirect|escape:'html':'UTF-8'}"> *}
                    </div>
                    <div data-id="1" class="row col-xs-12 form-group" style="display:none">
                        {$helperapm->searchProductForm('setProductObject', array(), 'type_object_product')} {* HTML CONTENT *}
                        <div class="row col-xs-12 form-group" id="setProductObject" style="min-height: 30px;border: 1px solid #d0d0d0;margin-left: 0px;padding: 6px;">
                            {if $object->id_object_redirect > 0 && $object->object_redirect == 1}
                                <input type="hidden" name="id_object" value="{$object->id_object_redirect|escape:'html':'UTF-8'}"  />{$object->id_object_redirect|escape:'html':'UTF-8'} - {$name_redirect|escape:'html':'UTF-8'}
                            {/if}
                        </div>
                    </div>
                    <div data-id="2" class="row col-xs-12 form-group" style="display:none">
                        <select> 
                            {foreach $categories as $k => $l}
                                <option value="{$l['id_category']|escape:'html':'UTF-8'}" {if $l['id_category'] == $object->id_object_redirect}SELECTED{/if}>{$l['name']|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div data-id="4" class="row col-xs-12 form-group" style="display:none">
                        <select> 
                            {foreach $cms as $k => $l}
                                <option value="{$l['id_cms']|escape:'html':'UTF-8'}" {if $l['id_cms'] == $object->id_object_redirect}SELECTED{/if}>{$l['meta_title']|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div data-id="5" class="row col-xs-12 form-group" style="display:none">
                        <input type="text" value="/" disabled />
                    </div>
                </select>
                </div>
            </div>

                
            <div class="form-group row col-xs-12" style="margin-top:30px;">
                {if $object->id > 0}
                    <a 
                        href="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getCard"
                         class="forced btn btn-secondary has-action btn-save-general">
                         <i class="icon-plus"></i>&nbsp; {l s='New' mod='alcaurlcanonical'}
                    </a>
                    <a 
                        href="{$url_ajax|escape:'html':'UTF-8'}&functionLibs=getList"
                         class="forced btn btn-secondary has-action btn-save-general">
                        <i class="icon-list"></i>&nbsp; {l s='List' mod='alcaurlcanonical'}
                    </a>
                    <button 
                        type="submit" value="1" id="module_form_submit_btn" name="submitalcabaseModule"
                        class="btn btn-primary pull-right has-action btn-save-general">
                        <i class="icon-save"></i>&nbsp; {l s='Modify' mod='alcaurlcanonical'}
                    </button>
                {else}
                    <button type="submit" value="1" id="module_form_submit_btn" name="submitalcabaseModule" class="btn btn-primary pull-right has-action btn-save-general">
                        <i class="icon-save"></i>&nbsp;{l s='Save all' mod='alcaurlcanonical'}
                    </button>
                {/if}
            </div>   
        </div>
    </form>
<style>
.alcaurlcanonicaltoggleredirect > .row.col-xs-12.form-group > div {
    display:inline-block;
}
</style>
<script>
$(document).on('change', 'select#url_type[name="type"]', function() {
  $('[data-display]').hide();
  id = $(this).find('option:selected').val();
  $('[data-display]').find('select').each(function() {
     $(this).attr('data-name', $(this).attr('name'));
     $(this).removeAttr('name');
  });
  $('[data-display="' +id + '"]').show();
  o = $('[data-display="' + id + '"]').find('select').first();
  $(o).attr('name', $(o).attr('data-name'));
});
$(document).on('change', '.alcaurlcanonicaltogglechange2', function() {
  setChangeAlcaCanonical2(this);
});
$(document).on('change', '.alcaurlcanonicaltogglechange', function() {
  setChangeAlcaCanonical(this);
});


function setChangeAlcaCanonical(obj) { 

  id = $(obj).find('option:selected').first().val();
  target = $(obj).attr('data-target');
  name = $(target).first().attr('data-attr');
     console.log(target);
  $(target + ' [data-id]').hide();
  $(target + ' [data-id]').find('input,select').each(function () { 
    if ($(this).attr('name') != 'biteateAjaxSearchProduct' && (!$(this).attr('name') || $(this).attr('name').indexOf('url_redirect') == -1)) {
        $(this).removeAttr('name');
    }
  });
  $(target + ' [data-id=' + id + ']').show();
  $(target + ' [data-id=' + id + ']').find('input,select').each(function () {
    if ($(this).attr('name') != 'biteateAjaxSearchProduct' && (!$(this).attr('name') || $(this).attr('name').indexOf('url_redirect') == -1)) {
      $(this).attr('name', name);
    }
  });
}

function setChangeAlcaCanonical2(obj) {
  val = $(obj).find('option:selected').first().val();
  target = $(obj).attr('data-target');
  if (val == '' || val == '410') {
    $(target).hide();
  } else {
    $(target).show();
  }
}
$(document).ready(function () { 
  setChangeAlcaCanonical($('.alcaurlcanonicaltogglechange').first());
  setChangeAlcaCanonical2($('.alcaurlcanonicaltogglechange2').first());
});
</script>