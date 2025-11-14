/**
 *  @Website: leotheme.com - prestashop template provider
 *  @author Leotheme <leotheme@gmail.com>
 *  @copyright  Leotheme
 *  @description: 
 */

$(document).ready(function() {
    $('.leo_delete_position').each(function(){
        
        $(this).closest('a').attr('href',"javascript:void(0);");
        
        $('<input>', {
            type: 'hidden',
            id : 'leo_delete_position',
            name: 'leo_delete_position',
            value: '0'
        }).appendTo( $(this).parent() );
        
        $(this).closest('a').click(function(){
            if (confirm(leo_confirm_text)){
                $('#leo_delete_position').val('1');
                $(this).closest('form').attr('action', leo_form_submit);
                $(this).closest('form').submit();
            }
        });
    });
});