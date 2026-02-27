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

<div class="{**col-xs-12 col-sm-8 nopadding-xs**}">
    <div class="form-group{if isset($disp_e[$input.name])} field_error{/if}">
    {if isset($input.lang) AND $input.lang}
        {foreach $languages as $language}
            {assign var='value_text' value=$input.value[$input.name][$language.id_lang]}
                {if $languages|count > 1}
                <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    <div class="col-lg-10 col-sm-10 col-xs-10 nopadding">
                {/if}
                    {if $input.type == 'tags'}
                        {literal}
                            <script type="text/javascript">
                                $().ready(function () {
                                    var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}{literal}';
                                    $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' mod='alcaurlcanonical'}{literal}'});
                                    $({/literal}'#module_form_m'{literal}).submit( function() {
                                        $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                                    });
                                });
                            </script>
                        {/literal}
                    {/if}
                    {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                        <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
                    {/if}

                    {if isset($input.maxchar) && $input.maxchar}
                            <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
                                <span class="text-count-down">{$input.maxchar|intval|escape:'html':'UTF-8'}</span>
                            </span>
                    {/if}

                    {if isset($input.prefix)}
                            <span class="input-group-addon">
                            {$input.prefix|escape:'html':'UTF-8'}
                            </span>
                    {/if}

                            <input type="text"
                                id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                                name="{$input.name|escape:'html':'UTF-8'}[{$language.id_lang|escape:'html':'UTF-8'}]"
                                class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
                                onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                                {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval|escape:'html':'UTF-8'}"{/if}
                                {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval|escape:'html':'UTF-8'}"{/if}
                                {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                {if isset($input.required) && $input.required} required="required" {/if}
                                {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />

                            {if isset($input.suffix)}
                            <span class="input-group-addon">
                            {$input.suffix|escape:'html':'UTF-8'}
                            </span>
                            {/if}

                    {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
                        </div>
                    {/if}

                {if $languages|count > 1}
                    </div>
                    <div class="col-lg-2 col-sm-2 col-xs-2">
                        <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                            {$language.iso_code|escape:'html':'UTF-8'}
                            <i class="icon-caret-down"></i>
                        </button>
                        <ul class="dropdown-menu">
                            {foreach from=$languages item=language}
                            <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
                {/if}
        {/foreach}
        {if isset($input.maxchar) && $input.maxchar}
            <script type="text/javascript">
                $(document).ready(function(){
                {foreach from=$languages item=language}
                    countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter"));
                {/foreach}
                });
            </script>
        {/if}
    {else}
        {if $input.type == 'tags'}
            {literal}
                <script type="text/javascript">
                    $().ready(function () {
                        var input_id = '{/literal}{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}{literal}';
                        $('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '{/literal}{l s='Add tag' mod='alcaurlcanonical'}{literal}'});
                        $({/literal}'#module_form_m'{literal}).submit( function() {
                            $(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
                        });
                    });
                </script>
            {/literal}
        {/if}
        {assign var='value_text' value=$input.value[$input.name]}
        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
            <div class="input-group{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}">
        {/if}
        {if isset($input.maxchar) && $input.maxchar}
                <span id="{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon"><span class="text-count-down">{$input.maxchar|intval|escape:'html':'UTF-8'}</span></span>
        {/if}
        {if isset($input.prefix)}
                <span class="input-group-addon">
                {$input.prefix|escape:'html':'UTF-8'}
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
                    {if isset($input.required) && $input.required } required="required" {/if}
                    {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if}
                    />
        {if isset($input.suffix)}
                <span class="input-group-addon">
                {$input.suffix|escape:'html':'UTF-8'}
                </span>
        {/if}
        {if isset($input.maxchar) || isset($input.prefix) || isset($input.suffix)}
            </div>
        {/if}
        {if isset($input.maxchar) && $input.maxchar}
            <script type="text/javascript">
                $(document).ready(function(){
                    countDown($("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}"), $("#{if isset($input.id)}{$input.id|escape:'html':'UTF-8'}{else}{$input.name|escape:'html':'UTF-8'}{/if}_counter"));
                });
            </script>
        {/if}
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