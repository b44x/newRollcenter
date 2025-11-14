/**
 *  @Website: leotheme.com - prestashop template provider
 *  @author Leotheme <leotheme@gmail.com>
 *  @copyright  Leotheme
 *  @description: 
 */
$(function() {
    let selectelement = '';
    $(function() {
        if($("#leoprofile-tabs")){
            var c = 0;
            anchor = window.location.hash.substr(1);
            $("#leoprofile-tabs").find('span').each(function(){
                if($(this).data('tab') == anchor) {
                    c=1;
                    $(this).trigger('click');
                }
            });
            if(!c && $('#id_leoelements_profiles').length) {
                var t = legetCookie('leoprofile-tabs');
                $("#leoprofile-tabs").find('span').each(function(){
                    if($(this).data('tab') == t) {
                        c=1;
                        $(this).trigger('click');
                    }
                });
            }
        }
    });
    if($('.adminleoelementsproductlist').length){
        if($("#listing_product_mode").val() == "list") {
            $('.form-group.productlist').hide();
        }
        $("#listing_product_mode").change(function(){
            if($(this).val() == 'list') {
                $('.form-group.productlist').hide();
            } else {
                $('.form-group.productlist').show();
            }
        });
    }
    $('.leofieldset').each(function(){
        if(!$(this).hasClass('fieldset_general')){
            $(this).hide();
        }
    });
    $('#leoprofile-tabs span').click(function(){
        field = $(this).attr('data-tab');
        window.location.hash = field;
        lesetCookie('leoprofile-tabs',field,1);
        $('#leoprofile-tabs span').removeClass('active');
        $(this).addClass('active');

        $('.leofieldset').hide();
        $('.leofieldset').each(function(){
            if($(this).hasClass(field)){
                $(this).show();

                if(field == "fieldset_productlist" && $("#productlist_layout").length && $("#productlist_layout").val() != 0 && $(this).hasClass('productlist_layout')) {
                    $(this).hide();
                }else{
                    $(this).show();
                }
            }
        });
    });
    function legetCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
        }
        return "";
    }
    function lesetCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }
    if($("#productlist_layout").length) {
        $("#productlist_layout").change(function(){
            if($(this).val() != 0) {
                $('.productlist_layout.fieldset_productlist').hide();
            } else {
                $('.productlist_layout.fieldset_productlist').show();
            }
        });
    }
    if ($('[name="plist_load_more_product_img"]:checked').val() == "0") {
        $('#plist_load_more_product_img_option').closest('.form-group').hide();
    }
    $('[name="plist_load_more_product_img"]').change(function(){
        if ($('[name="plist_load_more_product_img"]:checked').val() == "0") {
            $('#plist_load_more_product_img_option').closest('.form-group').hide();
        }else{
            $('#plist_load_more_product_img_option').closest('.form-group').show();
        }
    })
    // $('#leoprofile-tabs span.spanheader').trigger('click');
    setleoposition('header');
    setleoposition('content');
    setleoposition('footer');
    setleoposition('product');
    setleoposition('category');
    loadEditLink();


    function loadEditLink() {
        if($('.plist-link').length) {
            data = $("#product_list_data").val();
            obj = JSON.parse(data);

            $('.plist-link select').each(function(){
                if($(this).val() != 0) {
                    select = $(this);
                    $.each(obj, function(i,item){
                        if($(select).val() == item.id) {
                            $('<a class="btn btnedit" target="_blank" href="'+$("#product_list_data").data('url')+item.id_link+'"><i class="icon icon-edit"></i> '+$("#product_list_data").data('title')+'</a>').insertAfter($(select));
                        }
                    });
                }
            });

            $('.plist-link select').change(function(){
                if($(this).val() != 0){
                    select = $(this);
                    $(select).parent().find('a').remove();
                    $.each(obj, function(i,item){
                        if($(select).val() == item.id) {
                            if($(this).parent().find('a').length){
                                $(this).parent().find('a').attr('href', $("#product_list_data").data('url')+item.id_link);
                            }else{
                                $('<a class="btn btnedit" target="_blank" href="'+$("#product_list_data").data('url')+item.id_link+'"><i class="icon icon-edit"></i> '+$("#product_list_data").data('title')+'</a>').insertAfter($(select));
                            }
                        }
                    });
                }else{
                    $(this).parent().find('a').remove();
                }
            });
        }

        if($('#category_list_data').length) {
            data = $("#category_list_data").val();
            obj = JSON.parse(data);

            $('.category-link select').each(function(){
                if($(this).val() != 0) {
                    select = $(this);
                    $.each(obj, function(i,item){
                        if($(select).val() == item.id) {
                            $('<a class="btn btnedit" target="_blank" href="'+$("#category_list_data").data('url')+item.id_link+'"><i class="icon icon-edit"></i> '+$("#category_list_data").data('title')+'</a>').insertAfter($(select));
                        }
                    });
                }
            });

            $('.category-link select').change(function(){
                if($(this).val() != 0){
                    select = $(this);
                    $(select).parent().find('a').remove();
                    $.each(obj, function(i,item){
                        if($(select).val() == item.id) {
                            if($(this).parent().find('a').length){
                                $(this).parent().find('a').attr('href', $("#category_list_data").data('url')+item.id_link);
                            }else{
                                $('<a class="btn btnedit" target="_blank" href="'+$("#category_list_data").data('url')+item.id_link+'"><i class="icon icon-edit"></i> '+$("#category_list_data").data('title')+'</a>').insertAfter($(select));
                            }
                        }
                    });
                }else{
                    $(this).parent().find('a').remove();
                }
            });
        }

        if($('#product_detail_data').length) {
            data = $("#product_detail_data").val();
            obj = JSON.parse(data);

            $('.pdetail-link select').each(function(){
                if($(this).val() != 0) {
                    select = $(this);
                    $.each(obj, function(i,item){
                        if($(select).val() == item.id) {
                            $('<a class="btn btnedit" target="_blank" href="'+$("#product_detail_data").data('url')+item.id_link+'"><i class="icon icon-edit"></i> '+$("#product_detail_data").data('title')+'</a>').insertAfter($(select));
                        }
                    });
                }
            });

            $('.pdetail-link select').change(function(){
                if($(this).val() != 0){
                    select = $(this);
                    $(select).parent().find('a').remove();
                    $.each(obj, function(i,item){
                        if($(select).val() == item.id) {
                            if($(this).parent().find('a').length){
                                $(this).parent().find('a').attr('href', $("#product_detail_data").data('url')+item.id_link);
                            }else{
                                $('<a class="btn btnedit" target="_blank" href="'+$("#product_detail_data").data('url')+item.id_link+'"><i class="icon icon-edit"></i> '+$("#product_detail_data").data('title')+'</a>').insertAfter($(select));
                            }
                        }
                    });
                }else{
                    $(this).parent().find('a').remove();
                }
            });
        }
    }

    $('.img-upload').fancybox({  
        'width'     : 900,
        'height'    : 600,
        'type'      : 'iframe',
        'autoScale' : false,
        'autoDimensions': false,
        'fitToView' : false,
        'autoSize' : false,
        onUpdate : function(){ 
            $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
            $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));
        },
        afterShow: function(){
            $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
            $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));
        }
    });

    function setleoposition(panel){        
        //select hook
        if(panel == 'product' || panel == 'category') {
            //id_leoelements_profiles
            $('.panel-'+panel+'_content').attr('hook-id', 0);
            $('.panel-'+panel+'_content select').each(function(){
                $(this).attr('data-position', 0);
                $(this).attr('data-title', $('#name').val());
            });

        }
    }
    //select header, footer, content
    $('.position-select').each(function() {
        $(this).data('lastValue',$(this).val());
        positionchange($(this), 0);
    });

    $('.position-select').change(function() {
        positionchange($(this), 1);
        selectelement = $(this);
    });

    function positionchange(element, change){
        panel = 'header';
        if($(element).attr('id') == 'content-select'){
            panel = 'content';
        }else if($(element).attr('id') == 'footer-select'){
            panel = 'footer';
        }

        if($(element).val() == '0'){
            $(element).data('lastValue',$(element).val());
            $('.panel-'+panel+'_content').hide();
            $('.panel-'+panel+'_content-new').hide();
        }else if($(element).val() == 'createnew'){
            $('.panel-'+panel+'_content').hide();
            $('.panel-'+panel+'_content-new').hide();
            $('#position-name').parent().find('.text-danger').remove();
            //create new position
            if(change){
                pname = panel;
                $("#position-name").val(pname);
                $('#position-modal').modal();

                $('.panel-'+panel+'_content').hide();
                $('.panel-'+panel+'_content-new').hide();
            }
        }else{
            $(element).data('lastValue',$(element).val());
            $('.panel-'+panel+'_content').hide();
            if($('.panel-'+panel+'_content.'+$(element).val()).length) {
                $('.panel-'+panel+'_content.'+$(element).val()).show();
                $('.panel-'+panel+'_content-new').hide();
            }else{
                //add new
                $('.panel-'+panel+'_content-new').show();
            }
        }
    }
    //hook of position only
    if($('.select-hook-position').length) {
        $('.select-hook-position').each(function(){
            if($(this).val() != "0" && $(this).val() != "createnew") {
                hookbutton = $(this).closest('.row').find('.hook-button').first();
                $(hookbutton).show().find('a').attr('href', $(this).find(':selected').attr('data-url'));
            }
        });
        
        $('.select-hook-position').focus(function() {
           $(this).data('lastValue',$(this).val());
        });
        $('.select-hook-position').on("change", function(even) {
            if($(this).val() == 'createnew'){
                $('#position-name').parent().find('.text-danger').remove();
                pname = $(this).attr('data-hook') + " of " + $(this).attr('data-title');
                $("#position-name").val(pname);

                $('#position-modal').modal();
                $(this).val($(this).data('lastValue'));
                selectelement = $(this);
            } else if($(this).val() == "0") {
                $(this).closest('.row').find('.hook-button').first().hide();
            } else {
                hookbutton = $(this).closest('.row').find('.hook-button').first();
                $(hookbutton).find('a').attr('href', $(this).find(':selected').attr('data-url'));
                $(hookbutton).show();
            }
        });
    }

    $("#position-modal").on("hidden.bs.modal", function () {
        if($(selectelement).hasClass('position-select')) {
            $(selectelement).val($(selectelement).data('lastValue'));
            positionchange($(selectelement), 0);
        }
    });
    
    $('.btn-save-position').click(function(){
        if($("#position-name").val() != ""){
            ajaxleopostion();
        }else{
            $('#position-name').parent().append('<ul class="list-unstyled text-danger"><li>'+$("#position-name").data('empty')+'</li></ul>');
        }
    });

    $("#position-name").focus(function(){
        $('#position-name').parent().find('.text-danger').fadeOut(2000).remove();
    });

    function ajaxleopostion(){
        //selectelement
        $('<i class="loader --1"></i>').insertBefore($('#position-name'));
        if($(selectelement).hasClass('position-select')) {
            data = {
                "action": 'Position',
                "ajax": true,
                "type": 'position',
                "position_type": $(selectelement).attr('name'),
                "id_leoelements_profiles": $("#id_leoelements_profiles").val(),
                "position_name": $("#position-name").val(),
            };
        }else{
            data = {
                "action": 'Position',
                "ajax": true,
                "type": 'hook',
                "hook": $(selectelement).data('hook'),
                "position": $(selectelement).data('position'),
                "titlehook": $("#position-name").val(),
                "id_leoelements_profiles": $("#id_leoelements_profiles").val(),
            };
        }

        $.ajax({
            type: "GET",
            dataType: "Json",
            headers: {"cache-control": "no-cache"},
            url: $('#controller_url').val(),
            async: true,
            cache: false,
            data: data,
            success: function (data) {
                $('#position-modal .loader').remove();
                $('#position-name').parent().find('.text-danger').remove();
                if(data.error) {
                    $('#position-name').parent().append('<ul class="list-unstyled text-danger"><li>'+data.error+'</li></ul>');
                }else{
                    if(data.content_key) {
                        $(selectelement).append($('<option>', {
                            value: data.content_key,
                            text: data.title
                        }));
                        $(selectelement).val(data.content_key);
                    }else{
                        $(selectelement).append($('<option>', {
                            value: data.id,
                            text: data.title
                        }));
                        $(selectelement).val(data.id);
                    }

                    if($(selectelement).hasClass('position-select')) {
                        positionchange($(selectelement));
                    }else{
                        var selected = $(selectelement).find('option:selected');
                        selected.attr('data-url', data.url);
                        hookbutton = $(selectelement).closest('.row').find('.hook-button').first();
                        $(hookbutton).show();
                        $(hookbutton).find('a').attr('href', $(selectelement).find(':selected').attr('data-url'));
                    }
                    $('#position-modal').modal('hide');
                }

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#position-modal .loader').remove();
                alert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
            },
            complete: function () {
                $('#position-modal .loader').remove();
            }
        });
    }
});

function SetButonSaveToHeader() {
    var html_save_and_stay = 
    '<li>' +
        '<a id="page-header-desc-appagebuilder_shortcode-SaveAndStay" class="toolbar_btn  pointer" href="javascript:void(0);" title="Save and stay" onclick="TopSaveAndStay()">' +
            '<i class="process-icon-save"></i>' +
            '<div>Save and stay</div>' +
        '</a>' +
    '</li>';
    $('.toolbarBox .btn-toolbar ul').prepend(html_save_and_stay);
    
}

function TopSave(){
    if (typeof TopSave_Name !== 'undefined') {
        $("button[name$='"+TopSave_Name+"']").click();
    }
}

function TopSaveAndStay(){
    if (typeof TopSaveAndStay_Name !== 'undefined') {
        $("button[name$='"+TopSaveAndStay_Name+"']").click();
    }
}

/**
 * review  $('.nav-bar').on('click', '.menu-collapse', function() {
 */
function miniLeftMenu(parameters) {
    if( !$('body').hasClass('page-sidebar-closed')){
        $('body').toggleClass('page-sidebar-closed');
        $('body .main-menu').toggleClass('sidebar-closed');
        if ($('body').hasClass('page-sidebar-closed')) {
            $('nav.nav-bar ul.main-menu > li')
            .removeClass('ul-open open')
            .find('a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
        }
    }
}


