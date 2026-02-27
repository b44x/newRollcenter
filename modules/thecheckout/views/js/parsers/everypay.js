/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Software License Agreement
 * that is bundled with this package in the file LICENSE.txt.
 *
 *  @author    Peter Sliacky (Prestasmart)
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

tc_confirmOrderValidations['everypay'] = function () {
    if (
        $('[data-payment-module=everypay] input[name=payment-option]:checked').length &&
        $('.everypay_banklinks_list [name=banklinkChoice]:visible').length &&
        $('.everypay_banklinks_list [name=banklinkChoice]:checked').length == 0
    ) {
        var paymentErrorMsg = $('#thecheckout-payment .dynamic-content > .inner-wrapper > .error-msg');
        $('.payment-validation-details').remove();
        paymentErrorMsg.addClass('everypay').append('<span class="payment-validation-details"> (channel)</span>')
        paymentErrorMsg.show();
        scrollToElement(paymentErrorMsg);
        return false;
    } else {
        return true;
    }
}

checkoutPaymentParser.everypay = {
  all_hooks_content: function (content) {
   
  },

  container: function (element) {
    element.find('input[name=payment-option]').removeClass('binary'); // we will trigger payment manually (see form: below)

    // Add CSS rule to hide payment form in payment methods list
    var cssEl = document.createElement('style'),sheet;
    document.head.appendChild(cssEl);
    cssEl.sheet.insertRule(`
      label#banklinkPayButton {
        display: none!important;
      }
    `);

    $(document).off('change.removeError').on('change.removeError', '[name=banklinkChoice]', function() { 
      $('#thecheckout-payment .error-msg.everypay').hide(); console.log('hidden now'); 
    });

  },

  form: function (element) {

    element.find('form').addClass('payment-form').attr('action', 'javascript: EVERYPAY?.startBanklinkPayment();');
    // element.find('form').addClass('ppp').on('submit', function(event) { 
    //   event.preventDefault(); 
    //   console.log('submit event triggered');
    //   EVERYPAY?.startBanklinkPayment(); 
    // });

  },

  additionalInformation: function (element) {

  }

}

 