/**
 *  @Website: leotheme.com - prestashop template provider
 *  @author Leotheme <leotheme@gmail.com>
 *  @copyright  Leotheme
 *  @description: 
 */
product_list_wrrap = $("#js-product-list .leo-product-ajax"),
le_pagging_count = 2,
leo_query_url = "", le_stop_ajax = 0;

(function ($) {
    $.LeoCustomAjax = function () {
        this.leoData = 'leoajax=1';
    };
    $.LeoCustomAjax.prototype = {
        processAjax: function () {
            if(typeof leo_allow_ajax !== "undefined" && !leo_allow_ajax){
                return;
            }
            var myElement = this;
            
            if ($(".leo-qty").length) //category_qty
                myElement.getCategoryList();
            
            if (opLeoElementsList.plist_load_multi_product_img && $(".leo-more-info").length) //product_list_image
                myElement.getProductListImage();
            else if ($(".leo-more-info").length)
                $(".leo-more-info").remove();
            
            if (opLeoElementsList.plist_load_more_product_img && opLeoElementsList.plist_load_more_product_img_option == 1 && $(".product-additional").length) // product_one_img
                myElement.getProductOneImage();
            else if ($(".product-additional").length)
                $(".product-additional").remove();
            
            if (opLeoElementsList.plist_load_more_product_img && opLeoElementsList.plist_load_more_product_img_option == 2 && $(".product-attribute-additional").length) // product_one_img
                myElement.getProductAttributeOneImage();
            else if ($(".product-attribute-additional").length)
                $(".product-attribute-additional").remove();
            
            if (opLeoElementsList.plist_load_more_product_img && opLeoElementsList.plist_load_more_product_img_option == 3 && $(".product-all-additional").length) //product_one_img
                myElement.getProductAllOneImage();
            else if ($(".product-all-additional").length)
                $(".product-all-additional").remove();
            
            if ($(".leo-more-cdown").length)
                myElement.getProductCdownInfo();
            
            if ($(".leo-more-color").length)
                myElement.getProductColorInfo();
            else if ($(".leo-more-color").length)
                $(".leo-more-color").remove();

            if($(".product-item-size").length)
                myElement.getSizeContent();

            if($(".product-item-attribute").length)
                myElement.getAttributeContent();

            if($(".product-item-manufacture").length)
                myElement.getManufactureName();

            if($(".leo-ajax-tabs").length)
                myElement.getTabContent();

            //find class ap-count-wishlist-compare
            if($('.ap-total-wishlist').length || $('.ap-total-compare').length)
            {
                myElement.getCountWishlistCompare();
            }
            
            if (myElement.leoData != "leoajax=1") {
                $.ajax({
                    type: 'POST',
                    headers: {"cache-control": "no-cache"},
                    url: opLeoElements.ajax + '&rand=' + new Date().getTime(),
                    async: true,
                    cache: false,
                    dataType: "json",
                    data: myElement.leoData,
                    success: function (jsonData) {
                        if (jsonData) {
                            if (jsonData.cat) {
                                for (i = 0; i < jsonData.cat.length; i++) {
                                    var str = jsonData.cat[i].total;
                                    var label = $(".leo-qty.leo-cat-" + jsonData.cat[i].id_category).data("str");
                                    if(typeof label != "undefined") {
                                        str += "<span>" + label + "</span>";
                                    }
                                    $(".leo-qty.leo-cat-" + jsonData.cat[i].id_category).html(str);
                                    $(".leo-qty.leo-cat-" + jsonData.cat[i].id_category).show();
                                }
                                
                                $('.leo-qty').each(function(){
                                        if($(this).html() == '')
                                        {
                                                $(this).html('0');
                                                $(this).show();
                                        }
                                })
                            }
                            if (jsonData.product_list_image) {
                                var listProduct = new Array();
                                for (i = 0; i < jsonData.product_list_image.length; i++) {
                                    listProduct[jsonData.product_list_image[i].id] = jsonData.product_list_image[i].content;
                                }

                                $(".leo-more-info").each(function () {
                                    $(this).html(listProduct[$(this).data("idproduct")]);
                                });
                                addEffectProducts();
                            }

                            if (jsonData.pro_cdown) {
                                var listProduct = new Array();
                                for (i = 0; i < jsonData.pro_cdown.length; i++) {
                                    listProduct[jsonData.pro_cdown[i].id] = jsonData.pro_cdown[i].content;
                                }

                                $(".leo-more-cdown").each(function () {
                                    $(this).html(listProduct[$(this).data("idproduct")]);
                                });
                            }

                            if (jsonData.pro_color) {
                                var listProduct = new Array();
                                for (i = 0; i < jsonData.pro_color.length; i++) {
                                    listProduct[jsonData.pro_color[i].id] = jsonData.pro_color[i].content;
                                }

                                $(".leo-more-color").each(function () {
                                    $(this).html(listProduct[$(this).data("idproduct")]);
                                });
                            }
                                                        
                            if (jsonData.product_one_img) {
                                var listProductImg = new Array();
                                var listProductName = new Array();
                                for (i = 0; i < jsonData.product_one_img.length; i++) {
                                    listProductImg[jsonData.product_one_img[i].id] = jsonData.product_one_img[i].content;
                                    listProductName[jsonData.product_one_img[i].id] = jsonData.product_one_img[i].name;
                                }
                                
                                iw = 360;
                                ih = 360;
                                if (typeof homeSize.width !== 'undefined') {
                                        iw = homeSize.width;
                                        ih = homeSize.height;
                                }else{
                                        iw = $('.product_img_link .img-fluid').first().attr('width');
                                        ih = $('.product_img_link .img-fluid').first().attr('height');
                                }
                                $(".product-additional").each(function () {
                                    if (listProductImg[$(this).data("idproduct")]) {
                                        var str_image = listProductImg[$(this).data("idproduct")];
                                        if ($(this).data("image-type")) {
                                            src_image = str_image.replace('home_default',$(this).data("image-type"));
                                        }else{
                                            src_image = str_image.replace('home_default', 'home_default');
                                        }
                                        var name_image = listProductName[$(this).data("idproduct")];
                                        $(this).html('<img loading="lazy"  class="img-fluid" title="'+name_image+'" alt="'+name_image+'" src="' + src_image + '" />');
                                    }
                                });
                                //addEffOneImg();
                            }
                                                        
                            if (jsonData.product_attribute_one_img) {
                                var listProductImg = new Array();
                                var listProductName = new Array();
                                for (i = 0; i < jsonData.product_attribute_one_img.length; i++) {
                                    listProductImg[jsonData.product_attribute_one_img[i].id] = jsonData.product_attribute_one_img[i].content;
                                    listProductName[jsonData.product_attribute_one_img[i].id] = jsonData.product_attribute_one_img[i].name;
                                }

                                iw = 360;
                                ih = 360;
                                if (typeof homeSize.width !== 'undefined') {
                                    iw = homeSize.width;
                                    ih = homeSize.height;
                                }else{
                                    iw = $('.product_img_link .img-fluid').first().attr('width');
                                    ih = $('.product_img_link .img-fluid').first().attr('height');
                                }
                                $(".product-attribute-additional").each(function () {
                                    if (listProductImg[$(this).closest('.js-product-miniature').data("id-product")]) {
                                        var str_image = listProductImg[$(this).closest('.js-product-miniature').data("id-product")];
                                        if ($(this).data("image-type")) {
                                            src_image = str_image.replace('home_default',$(this).data("image-type"));
                                        }else{
                                            src_image = str_image.replace('home_default', 'home_default');
                                        }
                                            var name_image = listProductName[$(this).closest('.js-product-miniature').data("id-product")];
                                        $(this).html('<img loading="lazy"  class="img-fluid" title="'+name_image+'" alt="'+name_image+'" src="' + src_image + '" width="'+iw+'" height="'+ih+'"/>');
                                    }
                                });
                                //addEffOneImg();
                            }
                            if (jsonData.product_attribute) {
                                if (typeof jsonData.product_attribute.attribute !== 'undefined') {
                                    $.each( jsonData.product_attribute.attribute, function( key, value ) {
                                        $('.product-attribute-'+key).html(value);
                                        $('.product-attribute-'+key).removeClass('product-item-attribute');
                                    });
                                }
                                if (typeof jsonData.product_attribute.size !== 'undefined') {
                                    $.each( jsonData.product_attribute.size, function( key, value ) {
                                        $('.product-size-'+key).html(value);
                                        $('.product-size-'+key).removeClass('product-item-size');
                                    });
                                }
                            }
                            if (jsonData.product_manufacture) {
                                $.each( jsonData.product_manufacture, function( key, value ) {
                                    $('.product-manufacture-'+key).html(value);
                                    $('.product-manufacture-'+key).removeClass('product-item-manufacture');
                                });
                            }
                            
                            if (jsonData.product_all_one_img) {
                                var listProductImg = new Array();
                                var listProductName = new Array();
                                for (i = 0; i < jsonData.product_all_one_img.length; i++) {
                                    listProductImg[jsonData.product_all_one_img[i].id] = jsonData.product_all_one_img[i].content;
                                    listProductName[jsonData.product_all_one_img[i].id] = jsonData.product_all_one_img[i].name;
                                }

                                iw = 360;
                                ih = 360;
                                if (typeof homeSize.width !== 'undefined') {
                                    iw = homeSize.width;
                                    ih = homeSize.height;
                                }else{
                                    iw = $('.product_img_link .img-fluid').first().attr('width');
                                    ih = $('.product_img_link .img-fluid').first().attr('height');
                                }
                                $(".product-all-additional").each(function () {
                                    if (listProductImg[$(this).closest('.js-product-miniature').data("id-product")]) {
                                        var str_image = listProductImg[$(this).closest('.js-product-miniature').data("id-product")];
                                        if ($(this).data("image-type")) {
                                            src_image = str_image.replace('home_default',$(this).data("image-type"));
                                        }else{
                                            src_image = str_image.replace('home_default', 'home_default');
                                        }
                                            var name_image = listProductName[$(this).closest('.js-product-miniature').data("id-product")];
                                        $(this).html('<img loading="lazy" class="img-fluid" title="'+name_image+'" alt="'+name_image+'" src="' + src_image + '" />');
                                    }
                                });
                                //addEffOneImg();
                            }
                            
                            //wishlist 
                            if (jsonData.wishlist_products)
                            {
                                $('.ap-total-wishlist').data('wishlist-total',jsonData.wishlist_products);
                                $('.ap-total-wishlist').text(jsonData.wishlist_products);
                            }
                            else
                            {
                                $('.ap-total-wishlist').data('wishlist-total',0);
                                $('.ap-total-wishlist').text('0');
                            }
                            
                            //compare
                            if (jsonData.compared_products)
                            {
                                $('.ap-total-compare').data('compare-total',jsonData.compared_products);
                                $('.ap-total-compare').text(jsonData.compared_products);
                            }
                            else
                            {
                                $('.ap-total-compare').data('compare-total',0);
                                $('.ap-total-compare').text(0);
                            }

                            if (jsonData.ajaxTab) {
                                callshowmore = callajaxcontent = 0;
                                $(".leo-ajax-tabs").addClass('loaded');
                                $.each( jsonData.ajaxTab, function( key, value ) {
                                    $("#"+key).html(value);
                                    if(value.indexOf('ApProductList') >= 0){
                                        callshowmore = 1;
                                    }
                                    if(value.indexOf('product_list') >= 0){
                                        callajaxcontent = 1;
                                    }
                                });
                                if(callshowmore){
                                    apshowmore();
                                }
                                if(callajaxcontent){
                                    if (typeof $.LeoCustomAjax !== "undefined" && $.isFunction($.LeoCustomAjax)) {
                                        var leoCustomAjax = new $.LeoCustomAjax();
                                        leoCustomAjax.processAjax();
                                    }
                                    
                                    //DONGND:: class function of leofeature
                                    callLeoFeature();
                                    
                                    //DONGND:: re call run animation
                                    activeAnimation();
                                }
                            }
                        }
                    },
                    error: function () {
                    }
                });
            }
        },
        
        //check get number product of wishlist compare
        getCountWishlistCompare: function()
        {
            this.leoData += '&wishlist_compare=1';
        },
        
        getCategoryList: function () {
            //get category id
            var leoCatList = "";
            $(".leo-qty").each(function () {
                if($(this).data("id")){
                    if (leoCatList)
                        leoCatList += "," + $(this).data("id");
                    else
                        leoCatList = $(this).data("id");
                }else{
                    if (leoCatList)
                        leoCatList += "," + $(this).attr("id");
                    else
                        leoCatList = $(this).attr("id");
                }
            });

            if (leoCatList) {
                leoCatList = leoCatList.replace(/leo-cat-/g, "");
                this.leoData += '&cat_list=' + leoCatList;
            }
            return false;
        },
        getProductListImage: function () {
            var leoProInfo = "";
            $(".leo-more-info").each(function () {
                if (!leoProInfo)
                    leoProInfo += $(this).data("idproduct");
                else
                    leoProInfo += "," + $(this).data("idproduct");
            });
            if (leoProInfo) {
                this.leoData += '&product_list_image=' + leoProInfo;
            }
            return false;
        },
        getProductCdownInfo: function () {
            var leoProCdown = "";
            $(".leo-more-cdown").each(function () {
                if (!leoProCdown)
                    leoProCdown += $(this).data("idproduct");
                else
                    leoProCdown += "," + $(this).data("idproduct");
            });
            if (leoProCdown) {
                this.leoData += '&pro_cdown=' + leoProCdown;
            }
            return false;
        },
        getProductColorInfo: function () {
            var leoProColor = "";
            $(".leo-more-color").each(function () {
                if (!leoProColor)
                    leoProColor += $(this).data("idproduct");
                else
                    leoProColor += "," + $(this).data("idproduct");
            });
            if (leoProColor) {
                this.leoData += '&pro_color=' + leoProColor;
            }
            return false;
        },
        getTabContent: function () {
            var tabshortcode = "";
            var tabshortcodekey = "";
            $(".leo-ajax-tabs").each(function () {
                if(!$(this).hasClass('loaded')){
                    if (!tabshortcode)
                        tabshortcode += $(this).data("shortcode");
                    else
                        tabshortcode += "@|@" + $(this).data("shortcode");
                    if (!tabshortcodekey)
                        tabshortcodekey += $(this).attr("id");
                    else
                        tabshortcodekey += "@|@" + $(this).attr("id");
                    if(!$(this).find('slick-loading').length){
                        $(this).html('<div class="slick-loading" style="display: block;"><div class="slick-list" style="height: 600px;"> </div></div>');
                    }
                }
            });
            if (tabshortcode) {
                this.leoData += '&tabshortcode=' + tabshortcode;
            }
            if (tabshortcode) {
                this.leoData += '&tabshortcodekey=' + tabshortcodekey;
                //get category
                if (tabshortcode && $('input:radio[name=ajaxtabcate]').length && $('input:radio[name=ajaxtabcate]:checked').val()) {
                    this.leoData += '&ajaxtabcate=' + $('input:radio[name=ajaxtabcate]:checked').val();
                }
            }
            return false;
        },
        getSizeContent: function () {
            //tranditional image
            var leoAdditional = "";
            $(".product-item-size").each(function () {
                if (!leoAdditional)
                    leoAdditional += $(this).data("idproduct");
                else
                    leoAdditional += "," + $(this).data("idproduct");
            });
            if (leoAdditional) {
                this.leoData += '&product_size=' + leoAdditional;
            }
            return false;
        },
        getAttributeContent: function () {
            //tranditional image
            var leoAdditional = "";
            $(".product-item-attribute").each(function () {
                if (!leoAdditional)
                    leoAdditional += $(this).data("idproduct");
                else
                    leoAdditional += "," + $(this).data("idproduct");
            });
            if (leoAdditional) {
                this.leoData += '&product_attribute=' + leoAdditional;
            }
            return false;
        },
        getManufactureName: function () {
            //tranditional image
            var leoAdditional = "";
            var testArray = [];
            $(".product-item-manufacture").each(function () {
                if (!leoAdditional){
                    leoAdditional += $(this).data("idmanufacturer");
                    testArray.push($(this).data("idmanufacturer"));
                }
                else{
                    if(testArray.indexOf($(this).data("idmanufacturer")) < 0){
                        leoAdditional += "," + $(this).data("idmanufacturer");
                        testArray.push($(this).data("idmanufacturer"));
                    }
                }
            });
            if (leoAdditional) {
                this.leoData += '&product_manufacture=' + leoAdditional;
            }
            return false;
        },
        getProductOneImage: function () {
            //tranditional image
            var leoAdditional = "";
            $(".product-additional").each(function () {
                if (!leoAdditional)
                    leoAdditional += $(this).data("idproduct");
                else
                    leoAdditional += "," + $(this).data("idproduct");
            });
            if (leoAdditional) {
                this.leoData += '&product_one_img=' + leoAdditional;
            }
            return false;
        },
        getProductAttributeOneImage: function () {
            //tranditional image
            var leoAdditionalattribute = "0-0";
            $(".product-attribute-additional").each(function () {
                console.log($(this).closest('.js-product-miniature').data("id-product"));
                console.log($(this).closest('.js-product-miniature').data("id-product-attribute"));
                leoAdditionalattribute += "," + $(this).closest('.js-product-miniature').data("id-product") + '-' + $(this).closest('.js-product-miniature').data("id-product-attribute");
            });
            console.log(leoAdditionalattribute);
            if (leoAdditionalattribute && leoAdditionalattribute != '0-0') {
                this.leoData += '&product_attribute_one_img=' + leoAdditionalattribute;
            }
            return false;
        },
        getProductAllOneImage: function () {
            //tranditional image
            var leoAdditional = "0";
            var image_product = "0";
            $(".product-all-additional").each(function () {
                leoAdditional += "," + $(this).closest('.js-product-miniature').data("id-product");
                image_product += "," + $(this).data("id-image");
            });
            if (leoAdditional) {
                this.leoData += '&product_all_one_img=' + leoAdditional + '&image_product=' + image_product;;
            }
            return false;
        },
    };
}(jQuery));

function addJSProduct(currentProduct) {
// http://demos.flesler.com/jquery/serialScroll/
    if (typeof $('.thumbs_list_' + currentProduct).serialScroll == 'function') { 
        $('.thumbs_list_' + currentProduct).serialScroll({
            items: 'li:visible',
            prev: '.view_scroll_left_' + currentProduct,
            next: '.view_scroll_right_' + currentProduct,
            axis: 'y',
            offset: 0,
            start: 0,
            stop: true,
            duration: 700,
            step: 1,
            lazy: true,
            lock: false,
            force: false,
            cycle: false,
            onBefore: function( e, elem, $pane, $items, pos ){
                //DONGND:: update status for button
                if( pos == 0 )
                {
                    $('.view_scroll_left_' + currentProduct).addClass('disable');                   
                }
                else if( pos == $items.length -1 )
                {
                    $('.view_scroll_right_' + currentProduct).addClass('disable');
                }
                else
                {
                    $('.view_scroll_left_' + currentProduct).removeClass('disable');
                    $('.view_scroll_right_' + currentProduct).removeClass('disable');
                }
            },
        });
        $('.thumbs_list_' + currentProduct).trigger('goto', 1);// SerialScroll Bug on goto 0 ?
        $('.thumbs_list_' + currentProduct).trigger('goto', 0);
    }   
}
function addEffectProducts(){
    
    if($(".leo-more-info").length){
        $(".leo-more-info").each(function() {
            addJSProduct($(this).data("idproduct"));
        });
        addEffectProduct();
    }
}

function addEffectProduct() {
    var speed = 800;
    var effect = "easeInOutQuad";

    //$(".products_block .carousel-inner .ajax_block_product:first-child").mouseenter(function() {
        //$(".products_block .carousel-inner").css("overflow", "inherit");
    //});
    //$(".carousel-inner").mouseleave(function() {
        //$(".carousel-inner").css("overflow", "hidden");
    //});

    $(".leo-more-info").each(function() {
        var leo_preview = this;
        $(leo_preview).find(".leo-hover-image").each(function() {
            $(this).mouseover(function() {
                var big_image = $(this).attr("rel");
                var imgElement = $(leo_preview).parent().find(".product-thumbnail img").first();
                if (!imgElement.length) {
                    imgElement = $(leo_preview).parent().find(".product_image img").first();
                }

                if (imgElement.length) {
                    $(imgElement).stop().animate({opacity: 0}, {duration: speed, easing: effect});
                    $(imgElement).first().attr("src", big_image);
                    $(imgElement).first().attr("data-rel", big_image);
                    $(imgElement).stop().animate({opacity: 1}, {duration: speed, easing: effect});
                }
                //DONGND:: change class when hover another image
                if (!$(this).hasClass('shown'))
                {
                    $(leo_preview).find('.shown').removeClass('shown');
                    $(this).parent().addClass('shown');
                }
            });
        });


        if (typeof fancybox == 'function') { 
            $('.thickbox-ajax-'+$(this).data("idproduct")).fancybox({
                helpers: {
                    overlay: {
                        locked: false
                    }
                },
                'hideOnContentClick': true,
                'transitionIn'  : 'elastic',
                'transitionOut' : 'elastic'
            });
        }
    });
}

function addEffOneImg() {
    var speed = 800;
    var effect = "easeInOutQuad";

    $(".product-additional").each(function() {
        if ($(this).find("img").length) {
            var leo_hover_image = $(this).parent().find("img").first();
            var leo_preview = $(this);
            $(this).parent().mouseenter(function() {
                $(this).find("img").first().stop().animate({opacity: 0}, {duration: speed, easing: effect});
                $(leo_preview).stop().animate({opacity: 1}, {duration: speed, easing: effect});
            });
            $(this).parent().mouseleave(function() {
                $(this).find("img").first().stop().animate({opacity: 1}, {duration: speed, easing: effect});
                $(leo_preview).stop().animate({opacity: 0}, {duration: speed, easing: effect});
            });
        }
    });
}
function log(message) {
    console.log(message);
}

function activeAnimation()
{
    $(".has-animation").each(function() {
        onScrollInit($(this));
    });
}

function onScrollInit(items) {
    items.each(function() {
        var osElement = $(this);
        var animation = $(osElement).data("animation");
        var osAnimationDelay = $(osElement).data("animation-delay");
        var osAnimationDuration = $(osElement).data("animation-duration");
        var osAnimationIterationCount = $(osElement).data("animation-iteration-count");
        var osAnimationInfinite = $(osElement).data("animation-infinite");
        if (osAnimationInfinite == 1)
        {
            var loop_animation = 'infinite';
        }
        else
        {
            var loop_animation = osAnimationIterationCount;
        }
        osElement.css({
            "-webkit-animation-delay": osAnimationDelay,
            "-moz-animation-delay": osAnimationDelay,
            "animation-delay": osAnimationDelay,
            "-webkit-animation-duration": osAnimationDuration,
            "-moz-animation-duration": osAnimationDuration,
            "animation-duration": osAnimationDuration,
            "-webkit-animation-iteration-count": loop_animation,
            "-moz-animation-iteration-count": loop_animation,
            "animation-iteration-count": loop_animation,
        });
        
        osElement.waypoint(function() {     
            if (osElement.hasClass('has-animation'))
            {                   
                osElement.addClass('animated '+ animation).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){                  
                    $(this).removeClass('has-animation animated ' +animation);                  
                });         
            }            
    
            this.destroy();
        }, {
            triggerOnce: true,
            offset: '100%'
        });
    });
}
/**
 * End block functions common for front-end
 */


/**
 * End block for module ap_gmap
 */
function synSize(name) {
    var obj = $("#" + name);
    var div = $(obj).closest(".gmap-cover");
    var gmap = $(div).find(".gmap");
    $(obj).height($(gmap).height());
    //console.log($(gmap).height());
}
function apshowmore(){
    /**
     * Start block for module ap_product_list
     */
    $(".btn-show-more").click(function() {
        var page = parseInt($(this).data('page'));
        var use_animation = parseInt($(this).data('use-animation'));
        var btn = $(this);
        var config = $(this).closest(".ApProductList").find(".apconfig").val();

        // FIX 1.7
        btn.data('reset-text', btn.html() );
        btn.html(  btn.data('loading-text') );
        
        $.ajax({
            headers: {"cache-control": "no-cache"},
            url: prestashop.urls.base_url + 'modules/appagebuilder/apajax.php',
            async: true,
            cache: false,
            dataType: "Json",
            data: {"config": config, "p": page, "use_animation": use_animation},
            success: function(response) {
                var boxCover = $(btn).closest(".box-show-more");
                if(!response.is_more) {
                    $(boxCover).removeClass("open").fadeOut();
                }
                if(response.html) {
                    $(boxCover).prev().append(response.html);
                }
                $(btn).data("page", (page + 1));
                
                if (typeof $.LeoCustomAjax !== "undefined" && $.isFunction($.LeoCustomAjax)) {
                    var leoCustomAjax = new $.LeoCustomAjax();
                    leoCustomAjax.processAjax();
                }
                
                // class function of leofeature
                callLeoFeature();
                
                // re call run animation
                activeAnimation();
                
                //run swipe image after load image
                btn.parents('.ApProductList').find('.product-list-images-mobile').each(function(){
                    if ($(this).children().length > 1 && !$(this).hasClass('slick-slider')) {
                        $(this).slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: true,
                            dots: true,
                            infinite: false,
                            rtl: $('body').hasClass('lang-rtl') ? true : false,
                        });
                    }
                });
            }
        }).always(function () {
            // FIX 1.7
            btn.html(  btn.data('reset-text') );
        });
    });
    /**
     * End block for module ap_product_list
     */
    /**
     * Start block for module ap_image
     */     
    /**
     * End block for module ap_image
     */
}
function callLeoFeature()
{
    if(typeof (leoBtCart) != 'undefined')
    {       
        leoBtCart();
    }
    if(typeof (leoSelectAttr) != 'undefined')
    {
        leoSelectAttr();        
    }
    if(typeof (LeoWishlistButtonAction) != 'undefined')
    {   
        LeoWishlistButtonAction();      
    }
    if(typeof (LeoCompareButtonAction) != 'undefined')
    {       
        LeoCompareButtonAction();
    }
    if(typeof (actionQuickViewLoading) != 'undefined')
    {
        actionQuickViewLoading();
    }
}

$(document).ready(function() {
    var leoCustomAjax = new $.LeoCustomAjax();
    leoCustomAjax.processAjax();

    prestashop.on('updateProductList', function() {
        // FIX BUG : FILTER PRODUCT NOT SHOW MORE IMAGE
        if (typeof $.LeoCustomAjax !== "undefined" && $.isFunction($.LeoCustomAjax)) {
            var leoCustomAjax = new $.LeoCustomAjax();
            leoCustomAjax.processAjax();
        }
    });
    var langRtl = $('body').hasClass('lang-rtl');
    if($('.product-list-images-mobile').length) {
            $('.product-list-images-mobile').each(function(){
                if ($(this).children().length > 1) {
                    $(this).slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: true,
                        dots: true,
                        infinite: false, 
                        rtl: langRtl ? true : false,
                    });
                }
            });
        
        //turn off swipe mode of owl-carousel when slide active on mobile
        $( document ).ajaxComplete(function( event, xhr, settings ) {
            if (settings.url.indexOf('apajax') > 0) {
                $('.product-list-images-mobile').each(function(){
                    if($('.product-list-images-mobile').hasClass('slick-slider')){
                        function offSlideCarousel(selector){
                            selector.parents('.owl-item').on("touchstart mousedown", function(e) {
                                // Prevent carousel swipe
                                e.stopPropagation();
                            });
                        }
                        if (window.addEventListener) {
                            window.addEventListener("load", offSlideCarousel($(this)), false);
                        }
                        else if (window.attachEvent){
                            window.attachEvent("onload", offSlideCarousel($(this)));
                        }
                        else {
                            window.onload = offSlideCarousel($(this));
                        }
                    }
                    
                });
            }
            //fix swipe after ajax in category
            if (settings.url.indexOf('from-xhr') > 0 || settings.url.indexOf('amazzingfilter/ajax') > 0) {
                $('.product-list-images-mobile').each(function(){
                    if ($(this).children().length > 1) {
                        $(this).slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: true,
                            dots: true,
                            infinite: false,
                            rtl: langRtl ? true : false,
                        });
                    }
                });
            }
        });
    }
    
    // REPLACE URL IN BLOCKLANGUAGES
    if(typeof approfile_multilang_url != "undefined") {
         $.each(approfile_multilang_url, function(index, profile){
            var url_search = prestashop.urls.base_url + profile.iso_code;
            var url_change = prestashop.urls.base_url + profile.iso_code + '/' + profile.friendly_url + '.html';
            
            //console.log(url_change);
                        
			//DONGND:: update for module blockgrouptop and default
			if ($('#leo_block_top').length)
			{
				var parent_o = $('#leo_block_top .language-selector');
			}
			else
			{
				var parent_o = $('.language-selector-wrapper');
			}
			
			parent_o.find('li a').each(function(){
				
				var lang_href = $(this).attr('href');
				
				if(lang_href.indexOf(url_search) > -1 )
				{
					//window.console.log('--' + url_change);
					$(this).attr('href', url_change);
					//window.console.log(url_change);
				}
			});
			
        });
    }
	//DONGND:: update for module blockgrouptop and default
	if ($('#leo_block_top').length)
	{
		var parent_o_currency = $('#leo_block_top .currency-selector');
	}
	else
	{
		var parent_o_currency = $('.currency-selector');
	}
    
    // REPLACE URL IN BLOCKLANGUAGES
	parent_o_currency.find('li a').each(function(){
		
        var url_link = $(this).attr('href');
        var id_currency = getParamFromURL("id_currency", url_link);
        var SubmitCurrency = getParamFromURL("SubmitCurrency", url_link);
        
        var current_url = window.location.href;
		//DONGND:: fix for only product page, url has #
		if (prestashop.page.page_name == 'product')
		{			
			var current_url = prestashop.urls.current_url;		
		}
        var current_url = removeParamFromURL('SubmitCurrency',current_url);
        var current_url = removeParamFromURL('id_currency',current_url);
        
        if(current_url.indexOf('?') == -1){
            var new_url = current_url + '?SubmitCurrency=' + SubmitCurrency + "&id_currency=" +id_currency;
            $(this).attr('href', new_url);
        }
        else{
            var new_url = current_url + '&SubmitCurrency=' + SubmitCurrency + "&id_currency=" +id_currency;
            $(this).attr('href', new_url);
        }
    });
});

var data_col_ajax = '';
if ($('body').attr('id') == 'category') {
    $( document ).ajaxComplete(function( event, xhr, settings ) {
        if (settings.url.indexOf('amazzingfilter/ajax') > 0) {
            $('.grid-select[data-col="'+data_col_ajax+'"]').click();
        }
    })
}


function leproductlistaction() {
    $('.readmore').click(function(){
        $(this).closest('.readmore-wrap').toggleClass('more');
        if($(this).closest('.readmore-wrap').hasClass('more')) {
            $(this).html($(this).data('less'));
        }else{
            $(this).html($(this).data('more'));
        }
    });
    $('.grid-select').click(function(){
        var t = $(this).data("col");
        data_col_ajax = t;
        $(".grid-select").removeClass("active");
        $(this).addClass("active");
        //list
        if(t=='list') {
            $('#js-product-list .product_list').removeClass('grid').addClass('list');
        }else{
            $('#js-product-list .product_list').removeClass('list').addClass('grid');
            c = windowToClass();
            s = c+'[0-9]';
            r = new RegExp(s, "g");
            $('.ajax_block_product').each(function(){
                ci = $(this).attr('class');
                if(ci.indexOf(c+'12') >= 0) {
                    $(this).attr('class', ci.replace(c+'12', c+t));
                }else if(ci.indexOf(c+'2-4') >= 0) {
                    $(this).attr('class', ci.replace(c+'2-4', c+t));
                }else{
                    $(this).attr('class', ci.replace(r, c+t));
                }
            });
        }
        resetLastFirstClass();
        lesetCookie(c+'product',t,1);
    });
}
function resetLastFirstClass(){
    if($('.grid-select.active').length){
        t = $('.grid-select.active').data('col');
        if(t) {
            if(t=='2-4') {
                v = 5;
            }else{
                v = 12/t;
            }

            width = window.innerWidth;
            total = $('#js-product-list .ajax_block_product').length;
            //desktop 
            if(width>=992) {
                $('#js-product-list .ajax_block_product').removeClass('first-in-line').removeClass('last-in-line').removeClass('last-line');
                $('#js-product-list .ajax_block_product').each(function(i){
                    j = i + 1;
                    
                    if(j%v == 0) {
                        $(this).addClass('last-in-line');
                    }else if(j%v == 1) {
                        $(this).addClass('first-in-line');
                    }
                    if(total%v == 0){
                        if(j>(total-v)) {
                            $(this).addClass('last-line');   
                        }    
                    }else{
                        vt = total%v;
                        if(j>(total-vt)) {
                            $(this).addClass('last-line');   
                        }
                    }
                });
            } else if(width>=768) {
                $('#js-product-list .ajax_block_product').removeClass('last-item-of-tablet-line').removeClass('last-tablet-line');
                $('#js-product-list .ajax_block_product').each(function(i){
                    j = i + 1;
                    if(j%v == 0) {
                        $(this).addClass('last-item-of-tablet-line');
                    }else if(j%v == 1) {
                        $(this).addClass('first-item-of-tablet-line');
                    }
                    if(total%v == 0){
                        if(j>(total-v)) {
                            $(this).addClass('last-tablet-line');   
                        }    
                    }else{
                        vt = total%v;
                        if(j>(total-vt)) {
                            $(this).addClass('last-tablet-line');   
                        }
                    }
                });
            } else {
                $('#js-product-list .ajax_block_product').removeClass('last-item-of-mobile-line').removeClass('first-item-of-mobile-line').removeClass('last-mobile-line');
                $('#js-product-list .ajax_block_product').each(function(i){
                    j = i + 1;
                    if(j%v == 0) {
                        $(this).addClass('last-item-of-mobile-line');
                    }else if(j%v == 1) {
                        $(this).addClass('first-item-of-mobile-line');
                    }
                    if(total%v == 0){
                        if(j>(total-v)) {
                            $(this).addClass('last-mobile-line');   
                        }    
                    }else{
                        vt = total%v;
                        if(j>(total-vt)) {
                            $(this).addClass('last-mobile-line');   
                        }
                    }
                });
            }
        }
    }
}
function windowToClass() {
    width = window.innerWidth;
    if(width>=1200) return 'col-xl-';
    if(width>=992) return 'col-lg-';
    if(width>=768) return 'col-md-';
    if(width>=576) return 'col-sm-';
    if(width>=480) return 'col-xs-';
    return 'col-sp-';
} 

function infiniteButton() {
    if($("#pagination_summary").hasClass('done')) {
        $('.btn-leloadmorep').hide();
    } else {
        $('.btn-leloadmorep').show();
    }
    $('.btn-leloadmorep').on('click', function(e) {
        if (window.location.search) {
            leo_query_url = '&page='
        } else {
            leo_query_url = '?page='
        }
        e.preventDefault();
        $('.pagination').addClass('loading');
        $.post(window.location.href + leo_query_url + le_pagging_count, function(response) {
            var $result = $(response).find('article');
            if(!$result.length) {
                le_stop_ajax = 1;
            }
            $result.each(function(index, article) {
                setTimeout(function() {
                    classdiv = $(':first-child' , product_list_wrrap).attr('class');
                    divwrap = $( '<div class="'+classdiv+'"></div>' );
                    $(divwrap).append(article);
                    $(product_list_wrrap).append(divwrap);
                }, index * 100)
            });
            //pagination_summary
            $summary = $(response).find('#pagination_summary');
            $("#pagination_summary").html($summary.html());
            $('.pagination').removeClass('loading');
            if (le_stop_ajax || $summary.hasClass('done')) {
                $('.btn-leloadmorep').hide();
            }
        })
        le_pagging_count++;
        resetLastFirstClass();
    })
};
function infiniteScroll() {
    if (window.location.search) {
        leo_query_url = '&page='
    } else {
        leo_query_url = '?page='
    }
    setscroll();
    function setscroll() {
        if(!le_stop_ajax) {
            $(window).scroll(function addScroll() {
                if ($(this).height() + $(this).scrollTop() >= product_list_wrrap[0].scrollHeight) {
                    ajax();
                    $(window).off('scroll', addScroll);
                    le_pagging_count++;
                }
            });
        }
    }

    function ajax() {
        $('.pagination').addClass('loading');
        $.post(window.location.href + leo_query_url + le_pagging_count, function(response) {
            var $result = $(response).find('article');
            if(!$result.length) {
                le_stop_ajax = 1;
            }
            $result.each(function(index, article) {
                setTimeout(function() {
                    classdiv = $(':first-child' , product_list_wrrap).attr('class');
                    divwrap = $( '<div class="'+classdiv+'"></div>' );
                    $(divwrap).append(article);
                    $(product_list_wrrap).append(divwrap);
                }, index * 100)
            });
            setTimeout(function() {
                resetLastFirstClass();
            }, 6000);
            //pagination_summary
            $summary = $(response).find('#pagination_summary');
            $("#pagination_summary").html($summary.html());
            $('.pagination').removeClass('loading');
            if (le_stop_ajax || $summary.hasClass('done')) {
                le_stop_ajax = 1;
            }
        }).always(function()
        {
            if(!le_stop_ajax) {
                setscroll();
            }
        });
    }
}
$().ready(function(){
    floatHeader();
    if($("body").hasClass("keep-header") && $(window).width() > 990){
        $("#header").addClass( "navbar-fixed-top" );
        var hideheight =  $("#header").height()+120;
        $("#page").css( "padding-top", $("#header").height() );
        setTimeout(function(){
        $("#page").css( "padding-top", $("#header").height() );
        },200);
    }

    floatFooter();
    backtotop();

    // filter button
    if($('body').attr('id') == 'category' && $('.filter-toggle-button').length){
        $('body').append('<div class="overlay-filter"></div>');
        if ($('#left-column #search_filters_wrapper').length){
            var column = '#left-column';
        }else if($('#right-column #search_filters_wrapper').length){
            var column = '#right-column';
        }
        $(document).on('click', '.filter-toggle-button', function(e){
            e.preventDefault();
            if($('#horizontal_filters').length){
                $('#horizontal_filters').slideToggle();
            }else{
                $(column).toggleClass('active-filter');
                $('.overlay-filter').addClass('active');
            }
        });
        if(screen.width < 576 && $('#left-column').hasClass('filter-toggle')){
            $(document).on('click', '#search_filter_toggler', function(e){
                e.preventDefault();
                $(column).toggleClass('active-filter');
                $('.overlay-filter').addClass('active');
            })
            $(document).on('click', '#search_filters_wrapper', function(e){
                $('.overlay-filter').trigger('click');
            });
        }
        $(document).on('click', column+'.filter-toggle .close', function(e){
            e.preventDefault();
            $(column).removeClass('active-filter');
            $('.overlay-filter').removeClass('active');
            if($('#left-column').hasClass('filter-toggle') && screen.width < 576){
                $('#content-wrapper').removeClass('hidden-sm-down');
                $('#search_filters_wrapper').removeClass('hidden-sm-down');
            }
        })
        $(document).on('click', '.overlay-filter', function(e){
            e.preventDefault();
            $(column).removeClass('active-filter');
            $('.overlay-filter').removeClass('active');
            if($('#left-column').hasClass('filter-toggle') && screen.width < 576){
                $('#content-wrapper').removeClass('hidden-sm-down');
                $('#search_filters_wrapper').removeClass('hidden-sm-down');
            }
        })
    }

    // add loading
    $('.pagination').append('<div class="loading-icon"></div>');
    
    if($('#js-product-list').length){
        //move search to top
        if(screen.width > 576) {
            movefacedsearchtotop();
        }
        leproductlistaction();
        setDefaultListGrid();
        prestashop.on("updateProductList", () => {
            product_list_wrrap = $("#js-product-list .leo-product-ajax");
            le_pagging_count = 2;
            le_stop_ajax = 0;
            leproductlistaction();
            resetLastFirstClass();
            if($('.pagination').hasClass('scroll') ) {
                infiniteScroll();
            }
            if($('.pagination').hasClass('loadmore')) {
                infiniteButton();
            }
        });
        if($('.pagination').hasClass('scroll') ) {
            infiniteScroll();
        }
        if($('.pagination').hasClass('loadmore')) {
            infiniteButton();
        }
    }
    if(typeof (products_list_functions) != 'undefined')
    {
        for (var i = 0; i < products_list_functions.length; i++) {
            products_list_functions[i]();
        }
    }
      
      // update for order page - tab adress, when change adress, block adress change class selected
    $('.address-item .radio-block').click(function(){
        if (!$(this).parents('.address-item').hasClass('selected'))
        {
            $('.address-item.selected').removeClass('selected');
            $(this).parents('.address-item').addClass('selected');
        }
    })

    // loading quickview
    actionQuickViewLoading();

    prestashop.on('updateProductList', function() {
        actionQuickViewLoading();
    }); 

    // Run slick and zoom if not use leogallery module
    if(!use_leo_gallery){
        //check page product only
        if(prestashop.page.page_name == 'product'){
            innitSlickandZoom();
        }
                    
        prestashop.on('updatedProduct', function () {
            
            if ($('.quickview.modal .product-thumb-images').length)
            {
                // run slick slider for product thumb - quickview
                initSlickProductQuickView();        
            }
            else if ($('.product-detail .product-thumb-images').length)
            {
                // re-call setup slick when change attribute at product page
                innitSlickandZoom();
            }
        });
        
        // display modal by config
        if (typeof $("#content").data('templatemodal') != 'undefined')
        {
            if (!$("#content").data('templatemodal'))
            {
                $('div[data-target="#product-modal"]').hide();
            }
        }
    }
});
$( window ).resize(function() {
    // Run slick and zoom if not use leogallery module
    if(!use_leo_gallery){
        // fix zoom, only work at product page
        if (prestashop.page.page_name == 'product')
            restartElevateZoom();
            
        // fix lost slider of modal when resize
        if ($('#product-modal .product-images').hasClass('slick-initialized') && $('#product-modal .product-images').height() == 0)
        {       
            // setup slide for product thumb (modal)
            $('#product-modal .product-images').slick('unslick');
            $('#product-modal .product-images').hide();
            initSlickProductModal();
        }
    }
});

function processFloatFooter(footerAdd, scroolAction){
    if(footerAdd){
        $("#footer").addClass("fixed-bottom");
        setTimeout(function(){
          $("#wrapper").css( "padding-bottom", $("#footer").height() );
        },200);
    }else{
        $("#footer").removeClass( "fixed-bottom").removeClass("close-link");
        $("#wrapper").css( "padding-bottom", '');
    }

    if(scroolAction){
        $(window).off("scroll", scroolFooterHandler);
        $(window).scroll(scroolFooterHandler);
    }else{
        $(window).off("scroll", scroolFooterHandler);
    }

}
function floatFooter(){
    if (!$("body").hasClass("keep-footer") || $(window).width() <= 990){
        return;
    }

    $(window).resize(function(){
        if ($(window).width() <= 990)
        {
          processFloatFooter(0,0);
        }
        else if ($(window).width() > 990)
        {
          if ($("body").hasClass("keep-footer"))
            processFloatFooter(1,1);
        }
    });
    processFloatFooter(1,1);
}

var scroolFooterHandler = function(){
    var footerScrollTimer;
    if(footerScrollTimer) {
      window.clearTimeout(footerScrollTimer);
    }
    footerScrollTimer = window.setTimeout(function() {
      var pos = $(window).scrollTop();
      if (!$("body").hasClass("keep-footer")) return;
      if($(window).width() > 990){
        var pos = $(window).scrollTop();
        if($('body').hasClass('keep-header')){
            hideheight = $('#wrapper').height()-$('#footer').height()-$('#header').height();
        }else{
            hideheight = $('#wrapper').height()-$('#footer').height();
        }
        
        if(pos >= hideheight ){
            $("#wrapper").css( "padding-bottom", '');
            $("#footer").removeClass("close-link").removeClass("fixed-bottom");
        }else {
            //restore footer float
            $("#wrapper").css( "padding-bottom", $("#footer").height() );

            $("#footer").addClass("fixed-bottom");
            if($("#footer").data("close-link") == 1) {
                $("#footer").addClass("close-link")
            }
        }
      }
    }, 100);
};

function processFloatHeader(headerAdd, scroolAction){
    if ($('.ac_results').length)
    {
        $('.ac_results').hide();
    }
    
  if(headerAdd){
    $("#header").addClass( "navbar-fixed-top" );
    var hideheight =  $("#header").height()+120;
    $("#page").css( "padding-top", $("#header").height() );
    setTimeout(function(){
      $("#page").css( "padding-top", $("#header").height() );
    },200);
  }else{
    $("#header").removeClass( "navbar-fixed-top" );
    $("#page").css( "padding-top", '');
  }

  var pos = $(window).scrollTop();
  if( scroolAction && pos >= hideheight ){
    $(".header-nav").addClass('hide-bar');
    $(".hide-bar").css( "margin-top", - $(".header-nav").height() );
    $("#header").addClass("mini-navbar");
  }else {
    $(".header-nav").removeClass('hide-bar');
    $(".header-nav").css( "margin-top", 0 );
    $("#header").removeClass("mini-navbar");
  }
}

//Float Menu
function floatHeader(){
  if (!$("body").hasClass("keep-header") || $(window).width() <= 990){
    return;
  }
  
  $(window).resize(function(){
    if ($(window).width() <= 990)
    {
      processFloatHeader(0,0);
    }
    else if ($(window).width() > 990)
    {
      if ($("body").hasClass("keep-header"))
        processFloatHeader(1,1);
    }
  });
  var headerScrollTimer;

  $(window).scroll(function() {
    if(headerScrollTimer) {
      window.clearTimeout(headerScrollTimer);
    }

    headerScrollTimer = window.setTimeout(function() {
      if (!$("body").hasClass("keep-header")) return;
      if($(window).width() > 990){
        processFloatHeader(1,1);
      }
    }, 100);
  });
}
function backtotop(){
  $("#back-top").hide();
  $(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
      $('#back-top').fadeIn();
    } else {
      $('#back-top').fadeOut();
    }
  });

  // scroll body to 0px on click
  $('#back-top a').click(function () {
    $('body,html').animate({
      scrollTop: 0
    }, 800);
    return false;
  });
}
function movefacedsearchtotop(){
    if($('#horizontal_filters').length) {
        if(('#left-column #search_filters_wrapper').length) {
            search_filters_wrapper = $("#search_filters_wrapper").clone(1);
            $("#search_filters_wrapper").remove();
            $("#horizontal_filters").append(search_filters_wrapper);
        }    
    }
}
function setDefaultListGrid()
{
    c = windowToClass();
    var t = legetCookie(c+'product');
    if(t==""){
        if($("#btn_view_product").data('mode') == 'list'){
            cv = "view-list";
        }else{
            t = $("#btn_view_product").data(c.substr(0,c.length-1));
            cv = 'view-'+t;
        }
    }else{
        cv = coltoview(t);
    }
    $('.grid-select.'+cv).trigger('click');
}
function coltoview(t){
    c = 'view-3';
    switch(t) {
        case '12':
            c = 'view-1';
            break;
        case '6':
            c = 'view-2';
            break;
        case '4':
            c = 'view-3';
            break;
        case '3':
            c = 'view-4';
            break;
        case '5':
            c = 'view-2-4';
            break;
        case 'list':
            c = 'view-list';
            break;
        default:
        // code block
    }
    return c
}
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

// Zoom config
var options_modal_product_page = {
    speed: 300,
    dots: false,
    infinite: false,
    slidesToShow: 4,
    slidesToScroll: 1,
    vertical: true,
    verticalSwiping: true,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1,
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 400,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
};

// create option for slick slider of quickview
var options_quickview = {
    speed: 300,
    dots: false,
    infinite: false,
    slidesToShow: 4,
    slidesToScroll: 1,
    vertical: true,
    verticalSwiping: true,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 1,
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1
        }
      }
    ]
};
function innitSlickandZoom(){
    if($("#main").hasClass('product-image-thumbs')){
        
        // setup slide for product thumb (main)
        $('.product-detail .product-thumb-images').imagesLoaded( function(){ 
                    
            if (typeof check_loaded_main_product != 'undefined')
            {
                clearInterval(check_loaded_main_product);
            }
            
            check_loaded_main_product = setInterval(function(){
                if($('.product-detail .product-thumb-images').height() > 0)
                {   
                    
                    $('.product-detail .product-thumb-images').fadeIn();
                    
                    clearInterval(check_loaded_main_product);
                    postion = $("#content").data("templateview");
                    // add config for over 1200 extra large
                    numberimage = $("#content").data("numberimage");
                    numberimage1200 = $("#content").data("numberimage1200");
                    numberimage992  = $("#content").data("numberimage992");
                    numberimage768  = $("#content").data("numberimage768");
                    numberimage576  = $("#content").data("numberimage576");
                    numberimage480  = $("#content").data("numberimage480");
                    numberimage360  = $("#content").data("numberimage360");

                    if(postion !== 'undefined'){
                        initSlickProductThumb(postion, numberimage, numberimage1200, numberimage992, numberimage768, numberimage576, numberimage480, numberimage360);
                    }
                    
                }
            }, 300);
        });
        
        // setup slide for product thumb (modal)
        initSlickProductModal();
        
    }
    //call action zoom
    applyElevateZoom();
}

function restartElevateZoom(){  
    $(".zoomContainer").remove();
    applyElevateZoom();
}

function applyElevateZoom(){
    var zt = $("#content").data('templatezoomtype');
    if(zt != 'in' && ($(window).width() <= 991 || zt == 'none'))
    {
        // remove elevateZoom on mobile
        if($('#main').hasClass('product-image-gallery'))
        {
            if ($('img.js-thumb').data('elevateZoom'))
            {
                var ezApi = $('img.js-thumb').data('elevateZoom');
                ezApi.changeState('disable');
                $('img.js-thumb').unbind("touchmove");
            }
            $("#zoom_product").bind("click", function(e) {
                if ( $.isFunction($.fn.lightGallery) ) {
                    var $cl = $(this).lightGallery({
                        dynamic: true,
                        dynamicEl: getthumblightGallery()
                    });
                    $cl.on('onCloseAfter.lg', function(event, index, fromTouch, fromThumb) {
                        if ($(this).data('lightGallery'))
                            $(this).data('lightGallery').destroy(true);
                    });
                }else{
                    $.fancybox(getthumbgallery());
                }
            });
        }
        else
        {
            if ($("#zoom_product").data('elevateZoom'))
            {
                var ezApi = $("#zoom_product").data('elevateZoom');
                ezApi.changeState('disable');
                $("#zoom_product").unbind("touchmove");
            }
            $(document).on("click", "#zoom_product", function(e) {
                            setTimeout(function(){      // DELAY 0,1s to fix twice click
                //$(this).unbind('click', arguments.callee);            
                if ( $.isFunction($.fn.lightGallery) ) {
                    var $cl = $(this).lightGallery({
                        dynamic: true,
                        dynamicEl: getthumblightGallery()
                    });
                                    $cl.on('onCloseAfter.lg', function(event, index, fromTouch, fromThumb) {
                                        if ($(this).data('lightGallery')) {
                                            $(this).data('lightGallery').destroy(true);
                                            window.console.log('abc');
                                        }
                                    });
                }else{
                    var ez = $('#zoom_product').data('elevateZoom');
                    $.fancybox(getthumbgallery());
                }               
                            }, 100);
            });
        }
        return false;
    }
      
    //check if that is gallery, zoom all thumb
    // fix zoom, create config
    
    var zoom_cursor;
    var zoom_type;
    var scroll_zoom = false;
    var lens_FadeIn = 200;
    var lens_FadeOut = 200;
    var zoomWindow_FadeIn = 200;
    var zoomWindow_FadeOut = 200;
    var zoom_tint = false;
    var zoomWindow_Width = 400;
    var zoomWindow_Height = 400;
    var zoomWindow_Position = 1;
    
    if (zt == 'in')
    {
        zoom_cursor = 'crosshair';
        zoom_type = 'inner';
        lens_FadeIn = false;
        lens_FadeOut = false;       
    }
    else
    {
        zoom_cursor = 'default';
        zoom_type = 'window';
        zoom_tint = true;
        zoomWindow_Width = $("#content").data('zoomwindowwidth');
        zoomWindow_Height = $("#content").data('zoomwindowheight');
        
        if ($("#content").data('zoomposition') == 'right')
        {           
            // update position of zoom window with ar language
            if (prestashop.language.is_rtl == 1)
            {
                zoomWindow_Position = 11;
            }
            else
            {
                zoomWindow_Position = 1;
            }
        }
        if ($("#content").data('zoomposition') == 'left')
        {
            // update position of zoom window with ar language
            if (prestashop.language.is_rtl == 1)
            {
                zoomWindow_Position = 1;
            }
            else
            {
                zoomWindow_Position = 11;
            }
        }
        if ($("#content").data('zoomposition') == 'top')
        {
            zoomWindow_Position = 13;
        }
        if ($("#content").data('zoomposition') == 'bottom')
        {
            zoomWindow_Position = 7;
        }
        
        if (zt == 'in_scrooll')
        {
            // scroll to zoom does not work on IE
            var ua = window.navigator.userAgent;
            var old_ie = ua.indexOf('MSIE ');
            var new_ie = ua.indexOf('Trident/');
            if (old_ie > 0 || new_ie > 0) // If Internet Explorer, return version number
            {
                scroll_zoom = false;
            }
            else  // If another browser, return 0
            {
                scroll_zoom = true;
            }
            
        }
    };
    
    if($('#main').hasClass('product-image-gallery'))
    {
        lens_FadeIn = false;
        lens_FadeOut = false;
        zoomWindow_FadeIn = false;
        zoomWindow_FadeOut = false;
    }
    
    var zoom_config = {
        responsive  : true,
        cursor: zoom_cursor,
        scrollZoom: scroll_zoom,
        scrollZoomIncrement: 0.1,
        zoomLevel: 1,
        zoomType: zoom_type,
        gallery: 'thumb-gallery',
        lensFadeIn: lens_FadeIn,
        lensFadeOut: lens_FadeOut,
        zoomWindowFadeIn: zoomWindow_FadeIn,
        zoomWindowFadeOut: zoomWindow_FadeOut,
        zoomWindowWidth: zoomWindow_Width,
        zoomWindowHeight: zoomWindow_Height,
        borderColour: '#888',
        borderSize: 2,
        zoomWindowOffetx: 0,
        zoomWindowOffety: 0,
        zoomWindowPosition: zoomWindow_Position,
        tint: zoom_tint,
    };
    
    if($('#main').hasClass('product-image-gallery'))
    {
        $('img.js-thumb').each(function(){
            var parent_e = $(this).parent();
            $(this).attr('src', parent_e.data('image'));
            $(this).data('type-zoom', parent_e.data('zoom-image'));
        });
        
        if($.fn.elevateZoom !== undefined)
        {
            $('img.js-thumb').elevateZoom(zoom_config);
            // fix click a thumb replace all image and add fancybox
            $('img.js-thumb').bind("click", function(e) {
                if ( $.isFunction($.fn.lightGallery) ) {
                    var $cl = $(this).lightGallery({
                        dynamic: true,
                        dynamicEl: getthumblightGallery()
                    });
                    $cl.on('onCloseAfter.lg', function(event, index, fromTouch, fromThumb) {
                        if ($(this).data('lightGallery'))
                            $(this).data('lightGallery').destroy(true);
                    });
                }else{
                    var ez =   $(this).data('elevateZoom');
                    $.fancybox(ez.getGalleryList());
                }
                return false;
            });
        }
    }
    else
    {
        if($.fn.elevateZoom !== undefined)
        {
            
            $("#zoom_product").elevateZoom(zoom_config);            
            $("#zoom_product").bind("click", function(e) {
                if ( $.isFunction($.fn.lightGallery) ) {
                    var $cl = $(this).lightGallery({
                        dynamic: true,
                        dynamicEl: getthumblightGallery()
                    });
                    $cl.on('onCloseAfter.lg', function(event, index, fromTouch, fromThumb) {
                        if ($(this).data('lightGallery'))
                            $(this).data('lightGallery').destroy(true);
                    });
                }else{
                    var ez = $('#zoom_product').data('elevateZoom');
                    $.fancybox(ez.getGalleryList());
                }
                
                return false;
            });
            
        }
        
    }
}
function getthumblightGallery(){
    var gallerylist = [];
    $("#thumb-gallery a").each(function() {
        var a = "";
        $(this).data("zoom-image") ? a = $(this).data("zoom-image") : $(this).data("image") && (a = $(this).data("image"));
        gallerylist.push({
            src: "" + $(this).data("image") + "",
            thumb: "" + a + "",
            title: $(this).find("img").attr("title")
        })
    });
    return gallerylist
}
function getthumbgallery(){
    gallerylist = [];
    $("#thumb-gallery a").each(function() {
        var a = "";
        $(this).data("zoom-image") ? a = $(this).data("zoom-image") : $(this).data("image") && (a = $(this).data("image"));
        gallerylist.push({
            href: "" + a + "",
            title: $(this).find("img").attr("title")
        })
    });
    return gallerylist
}
function initSlickProductThumb(postion, numberimage, numberimage1200, numberimage992, numberimage768, numberimage576, numberimage480 , numberimage360){
    var vertical = true;
    var verticalSwiping = true;
    // update for rtl
    var slick_rtl = false;

    if(postion == "bottom"){
        vertical = false;
        verticalSwiping = false;
    } 

    if(postion == 'none'){
        vertical = false;
        verticalSwiping = false;
        numberimage = numberimage1200 = numberimage992 = numberimage768 = numberimage576 = numberimage480 = numberimage360 = 1;
    }
    
    // update for rtl
    if (!vertical && prestashop.language.is_rtl == 1)
    {
        slick_rtl = true;
    }

  var slider = $('#thumb-gallery');

  slider.slick({
    speed: 300,
    dots: false,
    infinite: false,
    slidesToShow: numberimage,
    vertical: vertical,
    verticalSwiping: verticalSwiping,
    slidesToScroll: 1,
    rtl: slick_rtl,
    responsive: [
      {
        breakpoint: 1200,
              settings: {
                    slidesToShow: numberimage1200,
                    slidesToScroll: 1,
              }
            },
    {
        breakpoint: 992,
        settings: {
          slidesToShow: numberimage992,
          slidesToScroll: 1,
        }
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: numberimage768,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 576,
        settings: {
          slidesToShow: numberimage576,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: numberimage480,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 360,
        settings: {
          slidesToShow: numberimage360,
          slidesToScroll: 1
        }
      }
    ]
  });
  $("#thumb-gallery").show();

  if(postion == 'none')
  {
    var slickInstance = slider[0];
    var slides = $(slickInstance.slick.$slides);
    var positionStart = findPosition(slides);
    var slideCount = slickInstance.slick.slideCount;
    
    // update slick for case without thubms
    if ((positionStart + 1) == slideCount){
        $('.arrows-product-fake .slick-next').addClass('slick-disabled');
    }else if(positionStart == 0){
        $('.arrows-product-fake .slick-prev').addClass('slick-disabled');
    }
    
    // active image first load
    slider.slick('slickGoTo', positionStart);

    $('.arrows-product-fake .slick-next').on( "click", function() {
        if (!$(this).hasClass('slick-disabled'))
        {
            $('.arrows-product-fake .slick-prev').removeClass('slick-disabled');
            var positionCurrent = findPosition(slides);
            if ((positionCurrent + 1) < slideCount) {
                $(slides[positionCurrent]).removeClass('active');
                $(slides[positionCurrent + 1]).addClass('active');
                $(slides[positionCurrent + 1]).find('img').trigger("click");
                slider.slick('slickNext');
                if((positionCurrent + 1) == (slideCount - 1)){
                    $(this).addClass('slick-disabled');
                }
            }
        }
      
    });

    $('.arrows-product-fake .slick-prev').on( "click", function() {
        if (!$(this).hasClass('slick-disabled'))
        {
            $('.arrows-product-fake .slick-next').removeClass('slick-disabled');
            var positionCurrent = findPosition(slides);
            if ((positionCurrent) > 0) {
                $(slides[positionCurrent]).removeClass('active');
                $(slides[positionCurrent - 1]).addClass('active');
                $(slides[positionCurrent - 1]).find('img').trigger("click");
                slider.slick('slickPrev');
                if((positionCurrent - 1) == 0){
                    $(this).addClass('slick-disabled');
                }
            }
        }
    });
  }
}

function findPosition(slides){
  var position;
  for (var i = 0; i < slides.length; i++) {
      if ($(slides[i]).hasClass('active')) {
      position = $(slides[i]).data('slick-index');
      return position;
    }
  }
}

// loading quickview
function actionQuickViewLoading()
{
  $('.quick-view').click(function(){
    if (!$(this).hasClass('active'))
    {
      $(this).addClass('active');
      $(this).find('.leo-quickview-bt-loading').css({'display':'block'});
      $(this).find('.leo-quickview-bt-content').hide();
        if (typeof check_active_quickview != 'undefined')
        {
            clearInterval(check_active_quickview);
        }
     
      check_active_quickview = setInterval(function(){
        if($('.quickview.modal').length)
        {           
          $('.quickview.modal').on('hide.bs.modal', function (e) {
            $('.quick-view.active').find('.leo-quickview-bt-loading').hide();
            $('.quick-view.active').find('.leo-quickview-bt-content').show();
            $('.quick-view.active').removeClass('active');
          });
          clearInterval(check_active_quickview);
            
            // run slick for product thumb - quickview
            if(!use_leo_gallery){
                initSlickProductQuickView();
            }
        }
        
      }, 300);

    }
  })
}

// build slick slider for quickview
function initSlickProductQuickView(){
    if(!use_leo_gallery){
        $('.quickview.modal .product-thumb-images').imagesLoaded( function(){ 
            if (typeof check_loaded_thumb_quickview != 'undefined')
            {
                clearInterval(check_loaded_thumb_quickview);
            }
            check_loaded_thumb_quickview = setInterval(function(){
                if($('.quickview.modal .product-thumb-images').height() > 0)
                {   
                    $('.quickview.modal .product-thumb-images').fadeIn();
                    
                    clearInterval(check_loaded_thumb_quickview);
                    $('.quickview.modal .product-thumb-images').slick(options_quickview);
                }
            }, 300);
        });
    }
}

// build slick slider for modal - product page
function initSlickProductModal(){
    $('#product-modal .product-images').imagesLoaded( function(){   
        if (typeof check_loaded_thumb_modal != 'undefined')
        {
            clearInterval(check_loaded_thumb_modal);
        }
        check_loaded_thumb_modal = setInterval(function(){
            if($('#product-modal .product-images').height() > 0)
            {   
                $('#product-modal .product-images').fadeIn();
                
                clearInterval(check_loaded_thumb_modal);
                $('#product-modal .product-images').slick(options_modal_product_page);
            }
        }, 300);
    });
}

function removeParamFromURL(key, sourceURL) {

    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

    
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        if(params_arr.length > 0){
            rtn = rtn + "?" + params_arr.join("&");
        }
    }
    return rtn;
}

function getParamFromURL(key, sourceURL) {

    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

    if (queryString !== "") {
        params_arr = queryString.split("&");
        
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                return params_arr[i].split("=")[1];
            }
        }
    }
    return false;
}