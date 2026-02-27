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

<div id="alc_modules">
    <div id="pts_content" class="pts bootstrap nopadding clear clearfix">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                {if isset($form_e)}
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="alert alert-success form_no_errors" {if $form_e == "0"}style="display:block;"
                            {else}style="display:none;" 
                            {/if}>
                            <p>{l s='Settings updated' mod='alcaurlcanonical'}</p>
                        </div>
                        <div class="alert alert-danger form_errors" {if $form_e == "1"}style="display:block;"
                            {else}style="display:none;" 
                            {/if}>
                            <p>{l s='Error saving changes' mod='alcaurlcanonical'}: </p>
                            {if isset($disp_e)}
                                {foreach $disp_e as $kdisp => $errors}
                                    {if is_array($errors)}
                                        {foreach $errors as $error}
                                            <p>{$error|escape:'html':'UTF-8'}</p>
                                        {/foreach}
                                    {/if}
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                {/if}

                <div class="clearfix"></div>

                <div class="col-md-2 col-sm-12 col-xs-12">
                    <div class="alc_link">
                        <a href="https://addons.prestashop.com/{$alcalang_iso|escape:'html':'UTF-8'}/125_alcalink"
                            title="Alcalink" target="_blank">
                            <img src="{$alcalogo|escape:'html':'UTF-8'}" alt="Alcalink" />
                        </a>
                    </div>
                    <ul class="nav menuizdo">
                        {foreach $fields as $f => $fieldset}
                            {capture name='fieldset_indice'}{counter name='fieldset_indice'}{/capture}
                            {foreach $fieldset as $key => $field}
                                {if $key == 'legend'}
                                    <li class="{if $smarty.capture.fieldset_indice == 1}active{/if}">
                                        {if isset($field.href)}
                                            <a href="{$field.href|escape:'html':'UTF-8'}">
                                                {if isset($field.image) && isset($field.title)}<img
                                                        src="{$field.image|escape:'html':'UTF-8'}"
                                                    alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
                                                {if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
                                                {$field.title|escape:'html':'UTF-8'}
                                            </a>
                                            <a href="#tab-form-{$smarty.capture.fieldset_indice|escape:'html':'UTF-8'}" data-toggle="tab" class="hidden"></a>
                                        {elseif isset($field.callajax)}
                                            <a href="{$url_ajax|escape:'html':'UTF-8'}{$field.callajax|escape:'html':'UTF-8'}" class="forced btn-save-general">
                                                {if isset($field.image) && isset($field.title)}<img
                                                        src="{$field.image|escape:'html':'UTF-8'}"
                                                    alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
                                                {if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
                                                {$field.title|escape:'html':'UTF-8'}
                                            </a>
                                            <a href="#tab-form-{$smarty.capture.fieldset_indice|escape:'html':'UTF-8'}"
                                                data-toggle="tab" class="hidden"></a>
                                        {else}
                                            <a href="#tab-form-{$smarty.capture.fieldset_indice|escape:'html':'UTF-8'}"
                                                data-toggle="tab"
                                                class="{if isset($field.class)}{$field.class|escape:'html':'UTF-8'}{/if}">
                                                {if isset($field.image) && isset($field.title)}<img
                                                        src="{$field.image|escape:'html':'UTF-8'}"
                                                    alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
                                                {if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
                                                {$field.title|escape:'html':'UTF-8'}
                                            </a>
                                        {/if}
                                    </li>
                                {/if}
                            {/foreach}
                        {/foreach}
                    </ul>
                    <ul class="alc_version">
                        <li>
                            {l s='Version' mod='alcaurlcanonical'}: {$module_version|escape:'htmlall':'UTF-8'}<span
                                class="separator_bar">|</span>{l s='PrestaShop' mod='alcaurlcanonical'}:
                            {$ps_version|escape:'htmlall':'UTF-8'}
                        </li>
                    </ul>
                </div>

                <div class="col-md-10 col-sm-12 col-xs-12 ">
                    <div class="panel pts-panel">
                        <div class="panel-heading main-head">
                            <span class="pts-content-current-tab">
                                {$module_name_display|escape:'html':'UTF-8'}
                            </span>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                {foreach $fields as $f => $fieldset}
                                    {capture name='fieldset_form_indice'}{counter name='fieldset_form_indice'}{/capture}
                                    {foreach $fieldset as $key => $field}
                                        {if $key == 'legend'}
                                            <div class="tab-pane {if $smarty.capture.fieldset_form_indice == 1}active{/if}"
                                                id="tab-form-{$smarty.capture.fieldset_form_indice|escape:'html':'UTF-8'}">
                                                <div class="panel">
                                                    <div class="row">
                                                        <div class="panel-heading">
                                                            {if isset($field.image) && isset($field.title)}<img
                                                                    src="{$field.image|escape:'html':'UTF-8'}"
                                                                alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
                                                            {if isset($field.icon)}<i
                                                                class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
                                                            {$field.title|escape:'html':'UTF-8'}
                                                        </div>
                                                        {if isset($fieldset.tpl) && $fieldset.tpl}
                                                            <div class="form-group"
                                                                {if isset($fieldset.tpl.id)}id="{$fieldset.tpl.id|escape:'html':'UTF-8'}"
                                                                {/if}>
                                                                {include file=$modulepath|escape:'quotes':'UTF-8'|cat:'/views/templates/admin/'|cat:$fieldset.tpl.file|escape:'quotes':'UTF-8'|cat:'.tpl'}
                                                            </div>
                                                        {else}
                                                            <form id="module_form_m"
                                                                action="{$urlform|escape:'quotes':'UTF-8'}&functionLibs=postProcess"
                                                                method="post" class="form form-horizontal clearfix"
                                                                enctype="multipart/form-data" autocomplete="off">
                                                                {include file='./form/form.tpl'}
                                                                {if !isset($field.save_and_stay) || $field.save_and_stay == true}
                                                                    <div class="panel-footer col-md-12">
                                                                        <button type="submit" value="1" id="module_form_submit_btn"
                                                                            name="submit{$module_name|escape:'html':'UTF-8'}Module"
                                                                            class="btn btn-primary pull-right has-action btn-save-general">
                                                                            <i
                                                                                class="icon-save"></i>&nbsp;{l s='Save all' mod='alcaurlcanonical'}
                                                                        </button>
                                                                    </div>
                                                                {/if}
                                                            </form>
                                                        {/if}
                                                    </div>
                                                </div>
                                            </div>
                                        {/if}
                                    {/foreach}
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>