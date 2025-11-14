/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */
$(document).ready(function(){
    if($("#lecolor").length) {
        $('.leocolor').each(function() {
            var input = this;
            $(input).attr('readonly', 'readonly');
            if($(input).val()) {
                $(input).css('backgroundColor', '#' + $(input).val());
            }
            $(input).ColorPicker({
                onChange: function(hsb, hex, rgb) {
                    $(input).css('backgroundColor', '#' + hex);
                    $(input).val('#' + hex);
                    if($(input).attr('id') !== undefined) {
                        name = $(input).attr("id").replaceAll('_', '-');
                        if($("#style"+$(input).attr("id")).length) {
                            style = ':root {--'+name+':'+$(input).val()+'}';
                            $("#style"+$(input).attr("id")).html(style);
                        } else {
                            style = '<style type="text/css" id="style'+$(input).attr("id")+'">';
                            style += ':root {--'+name+':'+$(input).val()+'}';
                            style += '</style>';
                            $("head").append(style);
                        }
                    }
                }
            });
        });

        $(".clear-bg").click(function() {
            var $parent = $(this).parent();
            var $input = $(".leocolor", $parent);
            
            if ($input.val('')) {
                $input.attr('style', '');
                $("#style"+$input.attr("id")).remove();
            }
            $input.val('');

            return false;
        });

    }
    $('.panel-font').change(function(){
        name = $(this).attr('name').replaceAll('_', '-');
        o = $("option:selected", $(this));
        v = $(o).html();
        l = $(o).data('load');
        family = $(o).data('family');
        var style = '';
        if((typeof l == 'undefined' || l != 1) && typeof family != 'undefined'){
            ft = $(o).data('type');
            fstyle = $(o).data('style');
            fweight = $(o).data('weight');
            file = $(o).data('file').split(",");
            font_url = $("#font_url").val();

            if(ft==1){
                for (var i = 0; i < file.length; i++) {
                    if(file[i].split('.')[1] == 'woff2'){
                        var font_face = new FontFace(family, 'url('+font_url+file[i]+')');
                        font_face.style = fstyle;
                        font_face.weight = fweight;
                        font_face.load();
                    }
                }
            }else if (ft==2){
                WebFont.load({
                    google: {
                        families: [family+':'+fweight]
                    }
                });
            }
            if($("#font"+name).length) {
                style = ':root {--'+name+':"'+family+'", sans-serif;}';
                $("#font"+name).html(style);
            }else{
                style = '<style type="text/css" id="font'+name+'">';
                style += ':root {--'+name+':"'+family+'", sans-serif;}';
                style += '</style>';
                $("head").append(style);
            }
            
            $(o).data('load',1)
        }else{
            if($("#font"+name).length) {
                style += ':root {--'+name+':"'+(typeof family != 'undefined' ? family : $(this).val())+'", sans-serif;}';
                $("#font"+name).html(style);
            }else{
                style = '<style type="text/css" id="font'+name+'">';
                style += ':root {--'+name+':"'+(typeof family != 'undefined' ? family : $(this).val())+'", sans-serif;}';
                style += '</style>';
                $("head").append(style);
            }
        }
    });
    // Toggle Paneltool Tab
    var heightColorTab = 0;
    $('.paneltool-tab').each(function(){
        var height = $(this).closest('.group-input').find('.paneltool-content').height();
        if($(this).closest('.group-input').attr('id') == 'lecolor'){
            heightColorTab = height;
        }else{
            $(this).data('height', height);
            $(this).closest('.group-input').find('.paneltool-content').css('height', '0px'); // hide tab
        }
        
    });
    var maxHeightColorSubTab = 0;
    $('.paneltool-subtab').each(function(){
        var height = $(this).closest('.group-input').find('.paneltool-subcontent').height();
        if(height > maxHeightColorSubTab) {
            maxHeightColorSubTab = height;
        }
        heightColorTab = heightColorTab - height;
        $(this).data('height', height);
        $(this).closest('.group-input').find('.paneltool-subcontent').css('height', '0px'); // hide subtab
    });

    $('#lecolor .paneltool-content').css('height', heightColorTab); // height default of Color tab
    $('.paneltool-tab').on('click', function(){
        if($(this).closest('.group-input').attr('id') != 'lecolor'){
            var group_input = $(this).closest('.group-input');
            $(this).closest('.group-input').find('.paneltool-content').animate({height: group_input.hasClass('active') ? '0px' : $(this).data('height')}, 'fast');
            group_input.toggleClass('active');
        }
    });
    maxHeightColorTab = heightColorTab;
    $('.paneltool-subtab').on('click', function(){
        var group_input = $(this).closest('.group-input');
        var height = $(this).data('height');
        if(!group_input.hasClass('active')){
            maxHeightColorTab += height;
            $('#lecolor .paneltool-content').css('height', maxHeightColorTab);
        }
        $(this).closest('.group-input').find('.paneltool-subcontent').animate({height: group_input.hasClass('active') ? '0px' : height}, 'fast');
        if(group_input.hasClass('active')){
            maxHeightColorTab -= height;
            $('#lecolor .paneltool-content').css('height', maxHeightColorTab);
        }
        group_input.toggleClass('active');
    });
    // End Toggle Paneltool Tab

    $('#panelTab a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
    var expiresThemConfigDay = 1;
    $('#panelTab a:first').tab('show');
    $(".bg-config").hide();
    var $MAINCONTAINER = $("html");

    $('.accordion-group select.input-setting').change(function() {
        var input = this;
        if ($(input).data('selector')) {
            var ex = $(input).data('attrs') == 'font-size' ? 'px' : "";
            $($MAINCONTAINER).find($(input).data('selector')).css($(input).data('attrs'), $(input).val() + ex);
        }
    });
    $(".paneltool .panelbutton").click(function() {
        $(this).parent().toggleClass("active");
    });	
	
    /* float footer */
    $('.enable_ffooter').click(function(){
        if(!$(this).hasClass('current')){
            var configName = $('#leo-paneltool').data('cname')+'_enable_ffooter';
            $('.enable_ffooter').removeClass('current');
            $(this).addClass('current');
            if($(this).data('value')){
                $('body').addClass('keep-footer');
                lesetCookie(configName,1,1);
                if(typeof floatFooter == 'function')
                    floatFooter();
            }
            else{
                $('body').removeClass('keep-footer');
                lesetCookie(configName,0,1);
                if(typeof processFloatFooter == 'function')
                    processFloatFooter(0,0);
            }
        }
    });


    /* float header */
    $('.enable_fheader').click(function(){
        if(!$(this).hasClass('current')){
            var configName = $('#leo-paneltool').data('cname')+'_enable_fheader';
            $('.enable_fheader').removeClass('current');
            $(this).addClass('current');
            if($(this).data('value')){
                $('body').addClass('keep-header');
                lesetCookie(configName,1,1);
                if(typeof floatHeader == 'function')
                    floatHeader();
            }
            else{
                $('body').removeClass('keep-header');
                lesetCookie(configName,0,1);
                if(typeof processFloatHeader == 'function')
                    processFloatHeader(0,0);
            }
        }
    });
    /* header style */
    var currentHeaderStyle = $('.leo-dynamic-update-header.current-header').data('header-style');
    $('.leo-dynamic-update-header').click(function(){
        if(!$(this).hasClass('current-header'))
        {
            $('.leo-dynamic-update-header').removeClass('current-header');
            $(this).addClass('current-header');

            var selectedHeader = $(this).data('header-style');
            $('body').removeClass(currentHeaderStyle);
            $('body').addClass(selectedHeader);
            currentHeaderStyle = selectedHeader;
            var configName = $('#leo-paneltool').data('cname')+'_header_style';
            lesetCookie(configName,selectedHeader,1);
        }
    });
    var currentSideBarStyle = $('.leo-dynamic-update-side.current-sidebar').data('sidebar');
    var sideBarStyleList = [];
    $('.leo-dynamic-update-side').each(function(i){
        sideBarStyleList[i] = $(this).data('sidebar');
    });
    $('.leo-dynamic-update-side').click(function(){
        if(!$(this).hasClass('current-sidebar'))
        {
            $('.leo-dynamic-update-side').removeClass('current-sidebar');
            $(this).addClass('current-sidebar');

            var selectedHeader = $(this).data('sidebar');
            $.each(sideBarStyleList, function( index, value ) {
                $('body').removeClass(value);
            });
            $('body').addClass(selectedHeader);
            currentSideBarStyle = selectedHeader;
            getBodyClassByMenu();

            var configName = $('#leo-paneltool').data('cname')+'_sidebarmenu';
            lesetCookie(configName,selectedHeader,1);
        }
    });
    
    var currentLayoutMode = $('.leo-dynamic-update-layout.current-layout-mod').data('layout-mod');
    $('.leo-dynamic-update-layout').click(function(){

        if(!$(this).hasClass('current-layout-mod'))
        {
            $('.leo-dynamic-update-layout').removeClass('current-layout-mod');
            $(this).addClass('current-layout-mod');

            var selectedLayout = $(this).data('layout-mod');
            $('body').removeClass(currentLayoutMode);
            $('body').addClass(selectedLayout);
            currentLayoutMode = selectedLayout;
			
			getBodyClassByMenu();
			
            var configName = $('#leo-paneltool').data('cname')+'_layout_mode';
            lesetCookie(configName,selectedLayout,1);
        }
    });
	
	function getBodyClassByMenu(){
        if($('body').hasClass('sidebar-hide') || $('body').hasClass('header-hide-topmenu'))
           $('body').removeClass('double-menu'); 
        else
            if(!$('body').hasClass('double-menu')) $('body').addClass('double-menu'); 
    }

	//DONGND:: click out to close paneltool
	$(document).click(function (e) {
		e.stopPropagation();		
		var container = $(".paneltool.active");	
		//DONGND:: fix click colorpicker close panel
		var container_colorpicker = $('.colorpicker');		
		//check if the clicked area is in container or not
		if (container.length && container.has(e.target).length === 0 && container_colorpicker.length && container_colorpicker.has(e.target).length === 0 && !$(e.target).hasClass('panelbutton') && !$(e.target).hasClass('fa-cog') && !$(e.target).hasClass('fa-times')) {			
			container.toggleClass("active");			
		}
		
	})
});