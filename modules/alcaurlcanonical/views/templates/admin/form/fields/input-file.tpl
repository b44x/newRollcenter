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
        {if isset($input.lang) AND $input.lang}
            {foreach $languages as $language}
                {assign var='value_preview' value=$input.value[$input.name][$language.id_lang]}
                {assign var='language_id' value=$language.id_lang}
                {if $languages|count > 1}
                <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    <div class="col-lg-10 col-sm-10 col-xs-10 nopadding">
                {/if}

                <input id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" type="file" name="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}" class="hide" />
                <div class="dummyfile input-group">
                    <span class="input-group-addon"><i class="icon-file"></i></span>
                    <input id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}-name" 
                    type="text" 
                    class="disabled" 
                    name="filename" 
                    readonly  />
                    <span class="input-group-btn">
                        <button id="{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}-selectbutton" type="button" name="submitAdd{$input.name|escape:'html':'UTF-8'}" class="btn btn-default">
                            <i class="icon-folder-open"></i> {l s='Choose a file' mod='alcaurlcanonical'}
                        </button>
                    </span>
                </div>

                {if isset($input.desc) && !empty($input.desc)}
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
                <div class="clearfix"></div>
                {/if}

                {if isset($input.preview) && $input.preview}
                    {if isset($input.value[$input.name][$language.id_lang]) && $input.value[$input.name][$language.id_lang] != ''}
                        <div class="form-group">
                            <div id="{$input.name|escape:'html':'UTF-8'}-{$language.id_lang|escape:'html':'UTF-8'}-images-thumbnails" class="col-lg-12">
                                    {if isset($input.is_image) && $input.is_image}
                                        <a href="{$urlimages|escape:'html':'UTF-8'}{$input.value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" target="_blank">
                                            <img src="{$urlimages|escape:'html':'UTF-8'}{$input.value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" class="img-thumbnail"{if isset($input.preview_width) && $input.preview_width} width="{$input.preview_width|escape:'html':'UTF-8'}"{/if} />
                                        </a>
                                    {else}
                                        <a class="btn btn-primary file_download" href="{$urlfiles|escape:'html':'UTF-8'}{$input.value[$input.name][$language.id_lang]|escape:'html':'UTF-8'}" target="_blank" download>
                                            <i class="icon-download"></i> - {l s='Download' mod='alcaurlcanonical'} ({$language.iso_code|escape:'html':'UTF-8'})
                                        </a>
                                    {/if}
                                </a>
                            </div>
                        </div>
                    {/if}
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

                <script>
                    $(document).ready(function(){
                        $('#{$input.name}_{$language_id}-selectbutton').click(function(e){
                            $('#{$input.name|escape:'html':'UTF-8'}_{$language_id|escape:'html':'UTF-8'}').trigger('click');
                        });
                        $('#{$input.name|escape:'html':'UTF-8'}_{$language_id|escape:'html':'UTF-8'}').change(function(e){
                            var val = $(this).val();
                            var file = val.split(/[\\/]/);
                            $('#{$input.name|escape:'html':'UTF-8'}_{$language_id|escape:'html':'UTF-8'}-name').val(file[file.length-1]);
                        });
                    });
                </script>
            {/foreach}
        {else}
            <input id="{$input.name|escape:'html':'UTF-8'}" type="file" name="{$input.name|escape:'html':'UTF-8'}" class="hide" />
            <div class="dummyfile input-group">
                <span class="input-group-addon"><i class="icon-file"></i></span>
                <input id="{$input.name|escape:'html':'UTF-8'}-name" 
                type="text" 
                class="disabled" 
                name="filename" 
                readonly />
                <span class="input-group-btn">
                    <button id="{$input.name|escape:'html':'UTF-8'}-selectbutton" type="button" name="submitAdd{$input.name|escape:'html':'UTF-8'}" class="btn btn-default">
                        <i class="icon-folder-open"></i> {l s='Choose a file' mod='alcaurlcanonical'}
                    </button>
                </span>
            </div>

            {if isset($input.desc) && !empty($input.desc)}
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
            <div class="clearfix"></div>
            {/if}
                
            {if isset($input.preview) && $input.preview}
                {if isset($input.value[$input.name]) && $input.value[$input.name] != ''}
                <div class="form-group">
                    <div id="{$input.name|escape:'html':'UTF-8'}-images-thumbnails" class="col-lg-12">
                        <a href="{$urlimages|escape:'html':'UTF-8'}{$input.value[$input.name]|escape:'html':'UTF-8'}" target="_blank">
                            {if isset($input.is_image) && $input.is_image}
                                <img src="{$urlimages|escape:'html':'UTF-8'}{$input.value[$input.name]|escape:'html':'UTF-8'}" class="img-thumbnail"{if isset($input.preview_width) && $input.preview_width} width="{$input.preview_width|escape:'html':'UTF-8'}"{/if} />
                            {else}
                                <a class="btn btn-primary file_download" href="{$urlfiles|escape:'html':'UTF-8'}{$input.value[$input.name]|escape:'html':'UTF-8'}" target="_blank" download>
                                    <i class="icon-download"></i> - {l s='Download' mod='alcaurlcanonical'}
                                </a>
                            {/if}
                        </a>
                    </div>
                </div>
                {/if}
            {/if}

            <script>
                $(document).ready(function(){
                    $('#{$input.name}-selectbutton').click(function(e){
                        $('#{$input.name|escape:'html':'UTF-8'}').trigger('click');
                    });
                    $('#{$input.name}').change(function(e){
                        var val = $(this).val();
                        var file = val.split(/[\\/]/);
                        $('#{$input.name|escape:'html':'UTF-8'}-name').val(file[file.length-1]);
                    });
                });
            </script>
        {/if}
    </div>
</div>