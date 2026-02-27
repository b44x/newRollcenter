/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

function sendInformation(email, subscriberStatus, invoker) {
    if (typeof _edrone !== 'object' || (typeof _edrone === 'object' && typeof _edrone.init !== 'function')) {
        return false;
    }

    const wasSentFromFooter = invoker.closest('footer');
    const request = new XMLHttpRequest();
    const url = _edrone.edrone_ajax_shop_url ? _edrone.edrone_ajax_shop_url : window.location.origin

    request.open('GET', edroneSessionController + '?email=' + email + '&status=' + subscriberStatus, true);

    request.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status >= 200 && this.status < 400) {
                const data = JSON.parse(this.responseText);

                try {
                    _edrone.action_type = 'subscribe'
                    _edrone.customer_tags = wasSentFromFooter ? 'Footer' : 'subscribe'
                    _edrone.subscription_status_type_id = encodeURIComponent(data.subscription_status_type_id)
                    _edrone.email = data.email ? data.email : email;
                    _edrone.subscription_status_reason = encodeURIComponent(data.subscription_status_reason)
                    _edrone.event_date = encodeURIComponent(data.event_date)
                    _edrone.event_id = encodeURIComponent(data.event_id)
                    _edrone.app_id = encodeURIComponent(data.app_id)
                    _edrone.signature = encodeURIComponent(data.signature)
                    _edrone.init();
                } catch (e) {
                    console.log('Failed to subscribe user: ')
                    console.log(e)
                }
            }
        }
    }

    request.send();
}

function sendAdditionalSubscribeTrace() {
    const fieldsToRemove = [
        'product_titles',
        'product_ids',
        'product_images',
        'product_skus' ,
        'product_urls',
        'product_category_ids',
        'product_category_names',
        'product_counts',
        'product_brand_ids',
        'product_brand_names',
        'order_currency',
        'order_payment_value',
        'coupon',
        'city',
        'base_payment_value',
        'base_currency',
        'order_id'
    ];

    if (
        (_edrone.action_type === 'order' && _edrone.send_additional_newsletter_trace) ||
        (typeof edroneSendRegisterTrace !== 'undefined' && typeof edroneTag !== 'undefined')
    ) {
        fieldsToRemove.forEach(field => delete _edrone[field]);
        const newsletterTag = typeof edroneTag !== 'undefined' ? edroneTag : 'Subscribe';

        if (
            typeof prestashop === 'object' &&
            (prestashop?.customer.birthday && prestashop?.customer.birthday !== '0000-00-00')
        ) {
            _edrone.birth_date = prestashop.customer.birthday;
        }

        _edrone.customer_tags = _edrone.action_type === 'order' ? 'Order' : newsletterTag;
        _edrone.action_type = 'subscribe';

        return _edrone.init();
    }
}

document.addEventListener('DOMContentLoaded', function (event) {
    document.body.addEventListener('click', function _func(mouseEvent) {
        const regex = new RegExp('([nN]ewsletter)');
        // Form exists in user data
        if (
            mouseEvent.target.name === 'submitNewsletter' ||
            (regex.test(mouseEvent.target.name) && ['submit', 'button'].includes(mouseEvent.target.type))
        ) {
            document.body.removeEventListener('click', _func);

            const emailUserInformation = mouseEvent.target.closest('form')?.querySelector('input[name$="email"]');
            const subscriberStatus = mouseEvent.target.closest('form')?.querySelector('input[name$="newsletter"]');

            // If newsletter agreement checkbox is not detected, check if we submitted newsletter form
            if (emailUserInformation) {
                sendInformation(emailUserInformation.value, ((subscriberStatus && subscriberStatus.checked) || mouseEvent.target.name === 'submitNewsletter') ? 1 : 0, mouseEvent.target);
            }

        }
    });
});
