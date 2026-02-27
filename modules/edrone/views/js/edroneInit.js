/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

(function() {
    const isOrderConfirmationPage = (document.querySelector('body#order-confirmation') || document.querySelector('body.page-order-confirmation'));

    _edrone.platform = "prestashop"
    _edrone.action_type = "other"

    // Send order by backend when user has adblock installed
    try {
        if (isOrderConfirmationPage && typeof _edrone.init !== 'function') {
            setTimeout(() => {
                if (typeof _edrone.init !== 'function' && (sendOrderByBackendController && edroneOrderId && edroneCustomerId)) {
                    const xhr = new XMLHttpRequest();

                    xhr.open('GET', sendOrderByBackendController + '?order_id=' + edroneOrderId + '&customer_id=' + edroneCustomerId);
                    xhr.onreadystatechange = function() {
                        if (this.readyState === 4) {
                            if (this.status >= 200 && this.status < 400) {
                                return true
                            }

                            console.error("Sending order not completed. Status code: " + this.status);
                            return false;
                        }
                    }

                    xhr.send();
                }
            }, 2000) // Fallback if for some reason edrone wasn't loaded in time to avoid duplicate events
        }
    } catch (e) {
        console.log("Error when sending order by server: ", e);
    }


    window._edrone_send_handler = function() {
        _edrone.first_run = false;
        const urlBase = _edrone.edrone_ajax_shop_url ? _edrone.edrone_ajax_shop_url : window.location.origin
        let request = new XMLHttpRequest();
        let category_id = null;

        if (document.querySelector('body#product') || document.querySelector('body.page-product')) {
            _edrone.action_type = "product_view"
        }
        else if(document.querySelector('body#index') || document.querySelector('body.page-index')) {
            _edrone.action_type = 'homepage_view';
        }
        else if (document.querySelector("body#category")) {
            _edrone.action_type = "category_view";

            document.querySelector("body#category").classList.forEach(c => {
                let re = /(category-id-)\d+/g;
                if (c.match(re)) {
                    category_id = parseInt(c.split("category-id-")[1]);
                }
            });
        }
        else if (isOrderConfirmationPage && !parseInt(edroneIsSSOrder)) {
            _edrone.action_type = 'order'
        }

        const url = edroneSessionController + (category_id > 0 ? '?category_id=' + category_id : '');

        request.open('GET', url, true);
        request.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status >= 200 && this.status < 400) {
                    try {
                        var data = JSON.parse(this.responseText);
                    } catch (e) {
                        console.error(e);
                        return false;
                    }

                    window._edrone.sender_type = "browser";
                    window._edrone.app_id = encodeURIComponent(data.app_id);
                    window._edrone.email = data.email; // URL encoded email is being mark as invalid in our system
                    window._edrone.first_name = encodeURIComponent(data.first_name);
                    window._edrone.last_name = encodeURIComponent(data.last_name);
                    window._edrone.country = encodeURIComponent(data.country);
                    window._edrone.version = data.version ? encodeURIComponent(data.version) : 'Unknown version';
                    window._edrone.platform_version = encodeURIComponent(data.platform_version);
                    // window._edrone.shop_lang = encodeURIComponent(data.shop_lang);

                    // Avoid including redundant data in all requests
                    if (edroneIsSSOrder == false && window._edrone.action_type == 'order') { // If order is not handled on backend, send order
                        if (data.order_id) {
                            window._edrone.order_id = encodeURIComponent(data.order_id);
                        }
                        if (data.base_currency) {
                            window._edrone.base_currency = encodeURIComponent(data.base_currency);
                        }
                        if (data.base_payment_value) {
                            window._edrone.base_payment_value = data.base_payment_value;
                        }
                        if (data.city) {
                            window._edrone.city = data.city;
                        }
                        if (data.coupon) {
                            window._edrone.coupon = encodeURIComponent(data.coupon);
                        }
                        if (data.order_payment_value) {
                            window._edrone.order_payment_value = encodeURIComponent(data.order_payment_value);
                        }
                        if (data.order_currency) {
                            window._edrone.order_currency = encodeURIComponent(data.order_currency);
                        }
                        if (data.product_counts) {
                            window._edrone.product_counts = encodeURIComponent(data.product_counts);
                        }
                    }

                    if (!['other', 'homepage_view'].includes(window._edrone.action_type)) {
                        if (data.product_category_ids) {
                            window._edrone.product_category_ids = encodeURIComponent(data.product_category_ids);
                        }
                        if (data.product_category_names) {
                            window._edrone.product_category_names = encodeURIComponent(data.product_category_names);
                        }
                    }

                    if (
                        ['add_to_cart', 'product_view'].includes(window._edrone.action_type) ||
                        (window._edrone.action_type === 'order' && edroneIsSSOrder == false)
                    ) {
                        if (data.product_titles) {
                            window._edrone.product_titles = encodeURIComponent(data.product_titles);
                        }

                        if (data.product_ids) {
                            window._edrone.product_ids = encodeURIComponent(data.product_ids);
                        }

                        if (data.product_images) {
                            window._edrone.product_images = encodeURIComponent(data.product_images);
                        }

                        if (data.product_skus) {
                            window._edrone.product_skus = data.product_skus;
                        }

                        if (data.product_urls) {
                            window._edrone.product_urls = encodeURIComponent(data.product_urls);
                        }

                        if (window._edrone.action_type !== 'order' || edroneIsSSOrder != false) {
                            if (window._edrone.base_currency) {
                                delete window._edrone.base_currency;
                            }

                            if (window._edrone.base_payment_value) {
                                delete window._edrone.base_payment_value;
                            }
                        }
                    }

                    if (typeof window._edrone.init === 'function') {
                        window._edrone.init();

                        if (typeof sendAdditionalSubscribeTrace === 'function') {
                            sendAdditionalSubscribeTrace();
                        }
                    }
                }
            }
        };
        request.send();
        request = null;
    }

})();
