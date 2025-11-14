/**
 *  @Website: leotheme.com - prestashop template provider
 *  @author Leotheme <leotheme@gmail.com>
 *  @copyright  Leotheme
 *  @description: 
 */
$(document).ready(function() {
    //only for product generate
    $('.see-file').click(function(){
        $('.filecontent').toggle();
    });
    $('.plist-eedit').click(function(){
        element = $(this).data('element');
        $.fancybox.open([{
                type: 'iframe', 
                href : ($('#leoelements_product_list_form').length?$('#leoelements_product_list_form').attr('action'):$('#appagebuilder_details_form').attr('action')) + '&pelement=' + element,
                afterLoad:function(){
                    if( $('body',$('.fancybox-iframe').contents()).find("#main").length  ){
                        hideSomeElement();
                        $('.fancybox-iframe').load( hideSomeElement );
                    }else { 
                        $('body',$('.fancybox-iframe').contents()).find("#psException").html('<div class="alert error">Can not find this element</div>');
                    }
                },
                afterClose: function (event, ui) { 
                }
            }], {
            padding: 10
        });
    });
    
    $('.element-list .plist-element').draggable({
        connectToSortable: ".product-container .content",
        revert: "true",
        helper: "clone",
        stop: function() {
         setProFormAction();
         setSortAble();
        }
    });
    
    $('#leoelements_product_list_form').submit(function() {
        genreateForm();
    });
    
    setProFormAction();
    setSortAble();
});

function genreateForm(){
    //generate grid first
    var ObjectFrom = {};
    ObjectFrom.gridLeft = returnObjElemnt('.leoelement_Grid .gridLeft-block-content');
    ObjectFrom.gridRight = returnObjElemnt('.leoelement_Grid .gridRight-block-content');
    $('input[name=params]').val(JSON.stringify(ObjectFrom));
    return false;
}

function returnObjElemnt(element){
    var Object = {};
    $(element).children().each(function(iElement){
        var Obj = {};
        Obj.name = $(this).data('element');
        
        if($(this).hasClass('functional_buttons')){
            Obj.element = returnObjElemnt($('.content', $(this)));
        }
        if($(this).hasClass('code')){
            Obj.code = replaceSpecialString($('textarea', $(this)).val());
        }
        Object[iElement] = Obj;
    });
    return Object;
}

function hideSomeElement(){
    $('body',$('.fancybox-iframe').contents()).addClass("page-sidebar-closed");
}

function setSortAble(){
    $( ".product-container .content" ).sortable({
      connectWith: ".content",
    });
}
function setProFormAction(){
    $('.plist-code').click(function(){
        textAre = $(this).closest('.plist-element').find('textarea').first();
        if(textAre.attr('rows') == 20)
            $(textAre).attr('rows',5);
        else
            $(textAre).attr('rows',20);
    });
    
    $('.plist-eremove').click(function(){
        $(this).closest('.plist-element').remove();
    });
}