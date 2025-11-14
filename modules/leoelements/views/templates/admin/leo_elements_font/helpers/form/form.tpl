{* 
* @Module Name: Leo Elements
* @Website: leotheme.com - prestashop template provider
* @author Leotheme <leotheme@gmail.com>
* @copyright Leotheme
* @description: LeoElements is module help you can build content for your shop
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}
    
    {if $input.type == 'font_setup_gg'}
        <div class="gfont-block tab-font">
            <h4 style="" class="t-group-attr">Google Fonts Setup </h4>
            <p class="t-help-block">Here you can setup the <a href="//www.google.com/fonts" target="blank">Google web fonts</a> that you want to use in your site.</p>
            <div>
                <select class="select_gfont {$input.class|escape:'html':'UTF-8'}" name="gfont" style="font-size: 13px;height: 34px;margin-bottom: 0;margin-left: 0;margin-right: 0;margin-top: 0;max-width: 250px;width: 100%;display: inline-block">
                    <option value="">Please select a font</option>
                    {assign var=lgfonts value=$input.list_google_font}
                    {foreach $lgfonts as $key => $value name="lgfont"}
                        <option value="{$value|escape:'html':'UTF-8'}" {if $value == $fields_value.gfont}selected{/if}>{$value|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <script>
                // $fields_value.gfont
                var global_gfont_api = jQuery.parseJSON('{$fields_value.gfont_api}');
                $(document).ready(function(){
                    loadFormByType($('.form-group-type').val());

                    $('.form-group-type').on('change', function(){
                        loadFormByType($(this).val());
                    });
                });

                function loadFormByType($type=1)
                {
                    if($type == 1) {
                        $('.form-group-google').closest('.form-group').hide();
                        $('.form-group-upload').closest('.form-group').show();
                    }else{
                        $('.form-group-google').closest('.form-group').show();
                        $('.form-group-upload').closest('.form-group').hide();
                    }
                }
            </script>
        </div>
        <style type="text/css">
            .font_setup {
                border: 1px solid #dfdfdf;
                border-radius: 5px;
                padding: 10px;
            }
        </style>
    {/if}
    {if $input.type == 'font_setup'}
        <div class="font-face-block tab-font form-wrapper {$input.class|escape:'html':'UTF-8'} col-lg-8">
            {if isset($fields_value.font_face)}
                {assign var="files" value=","|explode:$fields_value.font_face}
                {foreach from=$files item=file}
                    <div class="font-face-item form-group">
                        <div class="col-lg-5">
                                <p class="filename">{$file|escape:'html':'UTF-8'}</p>
                            <input type="hidden" name="fface_filename[]" class="fface-filename" value="{$file|escape:'html':'UTF-8'}">
                        </div>
                        <i class="icon-trash"></i>
                    </div>
                {/foreach}
            {/if}
            <button class="btn btn-success add_new_fontf">{l s='Add More Font Face' mod='leoelements'}</button>
            <input type="hidden" class="delete_fface" name="delete_fface" value="">
            <script>
                $(document).ready(function(){

                    function checkFormatFile(format, file) {
                        var end_file = file.split('.')[file.split('.').length-1];
                        if(format.includes(end_file)){
                            return true;
                        }
                        return false
                    }
                    var file_valid = ['woff', 'woff2', 'otf', 'eot', 'ttf', 'svg', 'ijmap'];
                    var ffaceItem = '<div class="font-face-item form-group">';
                        ffaceItem += '<div><input class="file" type="file" name="file_fface[]" multiple required="required"><input type="hidden" name="fface_filename[]" class="fface-filename" value=""><p class="help-block">{l s='You can select multiple files at once.' mod='leoelements'}'+' '+'{l s='Format' mod='leoelements'}'+': '+file_valid.join(', ')+'</p></div>';
                        ffaceItem += '<i class="icon-trash"></i>';
                        ffaceItem += '</div>';
                    $('.add_new_fontf').click(function(e){
                        e.preventDefault();
                        $('.add_new_fontf').before(ffaceItem);
                    })
                    $('body').on('click', '.font-face-block .font-face-item .icon-trash', function(e){
                        var input_filename_value = $(this).parent().find('input.fface-filename').val(),
                            delete_fface = $('.delete_fface').val(),
                            check = 0; // equal to 1 if the file duplicate
                        $(this).closest('.font-face-item').remove();
                        $('input.fface-filename').each(function(){
                            if($(this).val() == input_filename_value){
                                check = 1;
                            }
                        })
                        if(check == 0){ // file not duplicate -> remove file ()
                            delete_fface += delete_fface != '' ? ','+input_filename_value : input_filename_value;
                            $('.delete_fface').val(delete_fface);
                        }
                    })
                    $('body').on('change', '.font-face-item .file', function(){
                        var check_file = 1,
                            value = '';
                        for (var i = 0; i < $(this)[0].files.length; i++) {
                            if(!checkFormatFile(file_valid, $(this)[0].files[i].name)){
                                check_file = 0;
                            }
                            value += value ? ','+$(this)[0].files[i].name : $(this)[0].files[i].name;
                        }
                        if(check_file){
                            $(this).parent().find('.fface-filename').val(value);
                        }else{
                            $(this).val('').parent().find('.fface-filename').val('');;
                            alert('You need to upload files with format: '+file_valid.join(', '));
                        }
                        
                    });
                });
            </script>
        </div>
        <style type="text/css">
            .font_setup {
                border: 1px solid #dfdfdf;
                border-radius: 5px;
                padding: 10px;
            }
            .tab-font {
                display: grid;
            }
            .font-face-block .font-face-item {
                position: relative;
            }
            .font-face-block .font-face-item .icon-trash {
                position: absolute;
                right: 20px;
                top: 10px;
                font-size: 20px;
                cursor: pointer;
            }
        </style>
    {/if}
    {$smarty.block.parent}
{/block}
