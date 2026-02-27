/*
 *  @Website: apollotheme.com - prestashop template provider
 *  @author Apollotheme <apollotheme@gmail.com>
 *  @copyright Apollotheme
 *  @description: ApPageBuilder is module help you can build content for your shop
 */
/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */


$(function () {
    $(".show_menu1").click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        if ($(".group-nav").hasClass("active-menu")) {
            $(".group-nav").removeClass("active-menu");
            $(".bg-over-lay").removeClass("show-over-lay");
            $(".show_menu1").removeClass("active");
        } else {
            $(".group-nav").addClass("active-menu");
            $(".bg-over-lay").addClass("show-over-lay");
            $(".show_menu1").addClass("active");
        }
    });
    $(".closemenu").click(function (e) {
        e.stopPropagation();
        if (
            $(".group-nav").hasClass("active-menu") ||
            $(".bg-over-lay").hasClass("show-over-lay")
        ) {
            $(".group-nav").removeClass("active-menu");
            $(".bg-over-lay").removeClass("show-over-lay");
        }
        $("body").find(".show_menu1").removeClass("active");
    });
    //DONGND:: close menu when click out
    $(document).click(function (event) {
        if (!$(event.target).closest(".group-nav").length) {
            $("body").find(".group-nav").removeClass("active-menu");
            $("body").find(".bg-over-lay").removeClass("show-over-lay");
            $("body").find(".show_menu1").removeClass("active");
        } 
    });

    // dropdown menu when click menu

    // $(".group-nav .nav-item .dropdown-menu").slideUp();
    // $(".group-nav .nav-item.parent").click(function (e) {
    //     if (!e.target.closest(".dropdown-menu")) {
    //         $(".group-nav .nav-item .dropdown-menu").slideUp();
    //         if ($(this).hasClass("active-menu")) {
    //             $(this).children(".dropdown-menu").slideUp();
    //             $(this).removeClass("active-menu");
    //             console.log(1);
    //         } else {
    //             $(this).children(".dropdown-menu").slideDown();
    //             $(".group-nav .nav-item").removeClass("active-menu");
    //             $(this).addClass("active-menu");
    //             console.log(2);
    //         }
    //     }
    // });

});

$(function () {
    $(".block-category h1").detach().insertBefore('#wrapper .breadcrumb ol li:first-child');
    $(".contact-form h3").detach().insertBefore('#wrapper .breadcrumb ol li:first-child');
    $(".cms-id-4 .page-header > h1").detach().insertBefore('#wrapper .breadcrumb ol li:first-child');
});



$( document ).ajaxComplete(function() {
    $('.p-reference .product-reference').html($("#product-details .product-reference").clone());
    $('.p-reference .product-quantities').html($("#product-details .product-quantities").clone());
  });


$().ready(function () {
    $("#leo_search_block_top .title_block").click(function () {
        $(this).parent().toggleClass("active");
        setTimeout(function () {
            jQuery("#leo_search_block_top.active input.form-control").focus();
        }, 100);
    });
    $(document).keydown(function (e) {
        // ESCAPE key pressed
        if (e.keyCode == 27) {
            $("#leo_search_block_top").removeClass("active");
        }
    });
    $(document).click(function (event) {
        if (!$(event.target).closest("#leo_search_block_top").length) {
            $("#leo_search_block_top").removeClass("active");
        }
    });
});


$(function(){
    $('.show_newsletter').click(function(e) {
        e.preventDefault();
    	e.stopPropagation();
        if($('.group-newsletter').hasClass('active-popup')){
            $('.group-newsletter').removeClass('active-popup');
            $('.bg-layout-newsletter').addClass('show');
        }
        else{
             $('.group-newsletter').addClass('active-popup');
             $('.bg-layout-newsletter').addClass('show');
        }
    }); 
    $('.close_newsletter').click(function(e) {
    	e.stopPropagation();
        if($('.group-newsletter').hasClass('active-popup')){
            $('.group-newsletter').removeClass('active-popup');
        }
        if($('.bg-layout-newsletter').hasClass('show')){
            $('.bg-layout-newsletter').removeClass('show');
        }
    });
	
	//DONGND:: close menu when click out
	$(document).click(function(event) {
	  if (!$(event.target).closest(".wr-newsletter").length) {
	    $("body").find(".group-newsletter").removeClass("active-popup");
		$("body").find(".bg-layout-newsletter").removeClass("show");
	  }
	});
	
});



$(function () {
    var offset = $('.box_product11').offset();
    var top = offset.top;
    var left = offset.left;


    $('.scroll_product').click(function (e) { 
        e.preventDefault();
        $("html, body").animate(
            { scrollTop: top }, 1000);
    });
    
});

$(document).ready(function () {
    $(".custom-video .custom-video_thumb i").click(function () {
        $.fancybox({
            content: $(".custom-video .video").html(),
            helpers: {
                overlay: {
                    locked: false,
                },
            },
        });
    });
});




document.addEventListener('DOMContentLoaded', function () {
  try {
    const productDetail = document.querySelector('.product-detail') || document;

    const inputA = productDetail.querySelector('#dp_szerokosc_a, [id*="dp_szerokosc_a"]') ||
                   productDetail.querySelector('input[aria-label*="Szerokość A"], input[placeholder*="Szerokość A"]');
    const inputB = productDetail.querySelector('#dp_szerokosc_b, [id*="dp_szerokosc_b"]') ||
                   productDetail.querySelector('input[aria-label*="Szerokość B"], input[placeholder*="Szerokość B"]');
    const inputH = productDetail.querySelector('#dp_wys_l_max_175, #dp_wys_s_max_175, #dp_wys_l_max_160, [id*="dp_wys_"]') ||
                   productDetail.querySelector('input[aria-label*="Wysokość"], input[placeholder*="Wysokość"]');

    function nearestCommonAncestor(a, b) {
      if (!a || !b) return null;
      const setA = new Set();
      let n = a;
      while (n) { setA.add(n); n = n.parentElement; }
      let m = b;
      while (m) { if (setA.has(m)) return m; m = m.parentElement; }
      return null;
    }

    const rootAB = nearestCommonAncestor(
      inputA?.closest('.dp_field_container') || inputA,
      inputB?.closest('.dp_field_container') || inputB
    ) || productDetail.querySelector('.product-variants') || productDetail;

    let dimensionsWrapper = rootAB.querySelector('.dp_dimensions');
    if (!dimensionsWrapper) {
      dimensionsWrapper = document.createElement('div');
      dimensionsWrapper.className = 'dp_dimensions';
      const anchor = (inputA && inputA.closest('.dp_field_container')) || inputA || rootAB.firstElementChild;
      if (anchor && anchor.parentElement) {
        anchor.parentElement.insertBefore(dimensionsWrapper, anchor);
      } else {
        rootAB.appendChild(dimensionsWrapper);
      }
    }

    function moveWithLabel(inputEl, className) {
      if (!inputEl) return null;
      const fieldWrapper = document.createElement('div');
      fieldWrapper.className = className + ' dp-ensure-label';

      // znajdź label po for/id lub najbliższy w kontenerze
      let labelEl = null;
      const id = inputEl.id || '';
      if (id) labelEl = productDetail.querySelector(`label[for="${id}"], label[for*="${id}"]`);
      if (!labelEl) {
        const localLabel = inputEl.closest('.dp_field_container')?.querySelector('label') ||
                           inputEl.parentElement?.querySelector('label');
        if (localLabel) labelEl = localLabel;
      }

      if (labelEl) fieldWrapper.appendChild(labelEl);
      fieldWrapper.appendChild(inputEl);

      dimensionsWrapper.appendChild(fieldWrapper);
      return fieldWrapper;
    }

    moveWithLabel(inputA, 'dp_field_szerokosc_a');
    moveWithLabel(inputB, 'dp_field_szerokosc_b');
    moveWithLabel(inputH, 'dp_field_wysokosc');

    dimensionsWrapper.querySelectorAll('label').forEach(lbl => {
      lbl.style.display = 'block';
      lbl.style.visibility = 'visible';
      lbl.style.opacity = '1';
    });

    dimensionsWrapper.querySelectorAll('input, select').forEach(el => {
      el.style.maxWidth = '100%';
      el.style.width = '100%';
    });

  } catch (e) {
    console.warn('Dimensions autowrap failed:', e);
  }
});

