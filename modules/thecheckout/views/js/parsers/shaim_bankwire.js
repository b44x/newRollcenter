/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Software License Agreement
 * bundled with this package in the file LICENSE.txt.
 *
 *  @author    Peter Sliacky (Prestasmart)
 *  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

checkoutPaymentParser.shaim_bankwire = {
    all_hooks_content: function (content) {

    },

    additionalInformation: function (element) {
        var paymentOption = element.attr('id').match(/payment-option-\d+/)[0];
        var feeEl = element.find('#shaim_bankwire_sleva_clean');
        var fee = 0;
        if (feeEl.length) {
            fee = (-1) * feeEl.val();
        }
        element.last().append('<div class="payment-option-fee hidden" id="' + paymentOption + '-fee">' + fee + '</div>');
    }
}
