{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}
    {if $input.type == 'position_hook'}
        {if $input.is_edit}
            <label class="control-label col-lg-4">Content</label>
            <div class="col-lg-8">
                <div class="col-lg-6 hook-button" style="">
                    <a url_params="{$input.url_params}" select_profile="" select_hook="" href="{$input.url_params}" target="_blank" class="button button-primary button-hero edit_with_button_link url_params"><img src="{$input.icon_url}" alt="Leo Elements Logo"> Edit with Leo Elements</a>
                </div>
            </div>
            
            
            <script>
                $(document).ready(function(){
                    eventChangeProfile();
                    eventChangeHook();
                });
                
                function autosetLink() {
                    var select_profile = $('.edit_with_button_link.url_params').attr('select_profile');
                    var select_hook = $('.edit_with_button_link.url_params').attr('select_hook');
                    var link_cur = $('.edit_with_button_link.url_params').attr('url_params');
                    var link_new = link_cur + '&id_profile=' + select_profile + '&cw_hook=' + select_hook;
                    
                    $('.edit_with_button_link.url_params').attr('href', link_new);
                    
                }

                function eventChangeProfile()
                {
                    $(document).on('change', '#select_profile', function(e){
                        var select_profile = $('#select_profile').val();
                        $('.edit_with_button_link.url_params').attr('select_profile', select_profile);
                        autosetLink();
                        {*var link_cur = $('.edit_with_button_link.url_params').attr('url_params');
                        var link_new = link_cur + '&id_profile=' + $('#select_profile').val();
                        $('.edit_with_button_link.url_params').attr('href', link_new);*}
                    });
                }

                function eventChangeHook()
                {
                    $(document).on('change', '#select_hook', function(e){
                        var select_hook = $('#select_hook').val();
                        $('.edit_with_button_link.url_params').attr('select_hook', select_hook);
                        autosetLink();
                        
                        
                        {*$('#select_hook').val();
                        var link_cur = $('.edit_with_button_link.url_params').attr('url_params');
                        var link_new = link_cur + '&cw_hook=' + $('#select_hook').val();
                        $('.edit_with_button_link.url_params').attr('href', link_new);*}
                    });
                }
                
            </script>
            
            
            
        {*<div class="col-lg-12">
            <div class="panel panel-default panel-{$input.name}">
                <div id="collapse{$input.name}" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <p>{l s='Please select content for each hook.' mod='leoelements'}</p>
                        {foreach $input.hook_list as $hookl}
                            <h2>{$hookl}</h2>

                            <div class="row">
                                <div class="col-lg-6 loading-wraper">
                                    <select data-hook="{$hookl}" data-position="" class="select-hook-position">
                                        <option value="0">{l s='None' mod='leoelements'}</option>
                                        {if isset($input.leoelements_contents_hook[$hookl])}
                                            {foreach $input.leoelements_contents_hook[$hookl] as $hookc}
                                            <option data-url="{$hookc.url}"
                                            {if isset($fields_value[$input.name])}
                                                {foreach $fields_value[$input.name] as $hookname => $hookval}
                                                    {if ($hookname == $hookl) && $hookc.content_key == $hookval}
                                                    selected = "selected"
                                                    {/if}
                                                {/foreach}
                                            {/if}
                                            value="{$hookc.content_key}">{$hookc.name}</option>
                                            {/foreach}
                                        {/if}
                                        <option value="createnew">{l s='Create New' mod='leoelements'}</option>
                                    </select>

                                </div>
                                <div class="col-lg-6 hook-button" style="display:none">
                                    <a href="" target="_blank" class="button button-primary button-hero edit_with_button_link">
                                        <img src="{$input.icon_url}" alt="Leo Elements Logo"> {l s='Edit with Leo Elements' mod='leoelements'}
                                    </a>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
            <div class="panel panel-{$input.name}-new" style="display:none">
                <h2>
                    {l s='Please save the profile before using.' mod='leoelements'}
                </h2>
            </div>
        </div>*}
        {else}
        {*<div class="col-lg-12">
        <div class="panel">
            <h2>
                {l s='Please save the profile before using' mod='leoelements'}
            </h2>
        </div>
        </div>*}
        {/if}
    {/if}
    
    {$smarty.block.parent}
{/block}
