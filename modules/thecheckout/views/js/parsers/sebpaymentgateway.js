/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Software License Agreement
 * that is bundled with this package in the file LICENSE.txt.
 *
 *  @author    Peter Sliacky (Prestasmart)
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

checkoutPaymentParser.sebpaymentgateway = {
    after_load_callback: function () {
        // empty
    },

    container: function (element) {
        var paymentOptionId = element.attr('id').match(/payment-option-\d+/);

        if (paymentOptionId && 'undefined' !== typeof paymentOptionId[0]) {
            paymentOptionId = paymentOptionId[0];
            element.after('<div id="'+paymentOptionId+'-additional-information" class="sebpaymentgateway popup-notice js-additional-information definition-list additional-information ps-hidden" style="display: none;"><section><p>'+i18_popupPaymentNotice+'</p></section></div>')
        }
        payment.setPopupPaymentType(element);

        // element.find('input[name=payment-option]').addClass('binary'); // so that our 'pay' button in the popup disappears

        // Add CSS rule to hide payment form in payment methods list
        var cssEl = document.createElement('style'),sheet;
        document.head.appendChild(cssEl);
        cssEl.sheet.insertRule(`
            .js-additional-information.sebpaymentgateway {
              /* no rule here */
            }
        `);
    },

    popup_onopen_callback: function () {
        const el = document.querySelector('#sebpaymentgatewayPaymentElement > iframe');
        // re-initalize only when the element is not present yet
        if (!el && typeof SEBPAYMENTGATEWAY !== 'undefined' && typeof SEBPAYMENTGATEWAY.init === 'function') {
            SEBPAYMENTGATEWAY.init();
        }
        // make sure the submit button is enabled, sometimes ("something") disables it, and I'm not sure why
        setTimeout(function() {
            $('.popup-payment-button button[type=submit].disabled').removeClass('disabled');
        }, 2000);
    },
}