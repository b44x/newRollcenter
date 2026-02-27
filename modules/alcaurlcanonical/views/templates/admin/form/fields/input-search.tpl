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

<div class="col-xs-12 col-sm-8 nopadding-xs field_search">
    <div class="form-group{if isset($disp_e[$input.name])} field_error{/if}">
        <div class="alcaborder">
            <input type="hidden" 
                name="{$input.name|escape:'html':'UTF-8'}" 
                id="{$input.name|escape:'html':'UTF-8'}"
                value="{foreach from=$input.value[$input.name] item=value name=foo}{$value.id|escape:'html':'UTF-8'}{if !$smarty.foreach.foo.last},{/if}{/foreach}"

            />

            <div id="ajax_choose_obj_{$input.name|escape:'html':'UTF-8'}">
                <div class="input-group">
                    <input type="text" id="obj_autocomplete_{$input.name|escape:'html':'UTF-8'}" name="obj_autocomplete_{$input.name|escape:'html':'UTF-8'}" />
                    <span class="input-group-addon"><i class="icon-search"></i></span>
                </div>
            </div>

            <div id="div{$input.name|escape:'html':'UTF-8'}">
            {foreach from=$input.value[$input.name] item=value}
                <div class="form-control-static" data-item-id="{$value.id|escape:'html':'UTF-8'}">
                    <button type="button" class="btn btn-default del{$input.name|escape:'html':'UTF-8'}" name="{$value.id|escape:'html':'UTF-8'}">
                        <i class="icon-remove text-danger"></i>
                    </button>
                    {if isset($value.image) && $value.image}
                    <img src="{$value.image|escape:'html':'UTF-8'}" title="{$value.name|escape:'html':'UTF-8'}" alt="{$value.name|escape:'html':'UTF-8'}" width="40" height="40" />
                    {/if}
                    {$value.name|escape:'html':'UTF-8'}{if isset($value.reference) && !empty($value.reference)}&nbsp;{l s='(ref: %s)' sprintf=$value.reference mod='alcaurlcanonical'}{/if}
                </div>
            {/foreach}
            </div>
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
    </div>
    <script type="text/javascript">

        function get{$input.name|escape:'html':'UTF-8'}Ids() {
            var ids = '';
            ids += $('#{$input.name|escape:'html':'UTF-8'}').attr('value').replace(/\\,$/,''); /* .replace(/\\-/g,',') */
            ids = ids.replace(/\,$/,'');
            return ids;
        }

        function add{$input.name|escape:'html':'UTF-8'}(event, data, formatted) {
            if (data == null)
                return false;

            var objId = data[data.length - 1];
            var objName = '';
            var objImage = '';
            if (typeof data[0] !== 'undefined') {
                objName += ' ' + data[0];
            }

            if (typeof data[1] !== 'undefined') {
                objImage += ' <img src="'+data[1]+'" title="'+objName+'" alt="'+objName+'" width="40" />';
            }

            var $div{$input.name|escape:'html':'UTF-8'} = $('#div{$input.name|escape:'html':'UTF-8'}');
            var ${$input.name|escape:'html':'UTF-8'} = $('#{$input.name|escape:'html':'UTF-8'}');

            /* delete obj from select + add obj line to the div, input_name, input_ids elements */
            $div{$input.name|escape:'html':'UTF-8'}.html($div{$input.name|escape:'html':'UTF-8'}.html() + '<div class="form-control-static" data-item-id="'+objId+'"><button type="button" class="del{$input.name|escape:'html':'UTF-8'} btn btn-default" name="' + objId + '"><i class="icon-remove text-danger"></i></button>'+objImage+'&nbsp;'+ objName +'</div>');
            
            if(${$input.name}.val() == ''){
                ${$input.name|escape:'html':'UTF-8'}.val(objId);
            } else {
                ${$input.name|escape:'html':'UTF-8'}.val(${$input.name|escape:'html':'UTF-8'}.val() +','+ objId);
            }
            
            $('#obj_autocomplete_{$input.name|escape:'html':'UTF-8'}').val('');
            $('#obj_autocomplete_{$input.name|escape:'html':'UTF-8'}').setOptions({
                extraParams: {
                    ajax : true,
                    action : '{$input.ajax_method|escape:'html':'UTF-8'}',
                    excludeIds : get{$input.name|escape:'html':'UTF-8'}Ids()
                }
            });
        }

        function del{$input.name|escape:'html':'UTF-8'}(id) {
            var input = getE('{$input.name|escape:'html':'UTF-8'}');
            var item_block = $('#div{$input.name|escape:'html':'UTF-8'}').find('div[data-item-id="'+id+'"]');
            
            // Cut hidden fields in array
            var inputCut = input.value.split(',');

            // Reset all hidden fields
            input.value = '';
            /*name.value = '';*/
            item_block.remove();
            /*div.innerHTML = '';*/
            for (i in inputCut)
            {
                // Add to hidden fields no selected objs OR add to select field selected obj
                if (inputCut[i] !== '' && inputCut[i] != id)
                {
                    input.value += inputCut[i] + ',';
                }
            }

            // Remove last comma of string
            input.value = input.value.replace(/,\s*$/, "");

            $('#obj_autocomplete_{$input.name|escape:'html':'UTF-8'}').setOptions({
                extraParams: {
                    ajax : true,
                    action : '{$input.ajax_method|escape:'html':'UTF-8'}',
                    excludeIds : get{$input.name|escape:'html':'UTF-8'}Ids()
                }
            });
        }

        $('#obj_autocomplete_{$input.name|escape:'html':'UTF-8'}').autocomplete('{$input["ajax_url"]|escape:'html':'UTF-8'}', {
            minChars: 1,
            autoFill: true,
            max:20,
            matchContains: true,
            mustMatch:false,
            scroll:false,
            cacheLength:0,
            formatItem: function(item) {
                var itemIdToReturn = item[item.length - 1];
                var itemNameToReturn = '';
                var itemImageToReturn = '';

                if (typeof item[0] !== 'undefined') {
                    itemNameToReturn = ' - ' + item[0];
                }

                if (typeof item[1] !== 'undefined') {
                    itemImageToReturn = '<img src="'+item[1]+'" title="'+itemNameToReturn+'" alt="'+itemNameToReturn+'" width="40" height="40" /> - ';
                }

                return itemImageToReturn+itemIdToReturn+itemNameToReturn;

            }, 
            extraParams: {
                ajax : true,
                action : '{$input.ajax_method|escape:'html':'UTF-8'}',
                excludeIds : get{$input.name|escape:'html':'UTF-8'}Ids()
            }

        }).result(add{$input.name|escape:'html':'UTF-8'});

        $('#div{$input.name|escape:'html':'UTF-8'}').delegate('.del{$input.name|escape:'html':'UTF-8'}', 'click', function(){
			del{$input.name|escape:'html':'UTF-8'}($(this).attr('name'));
		});

    </script>
</div>