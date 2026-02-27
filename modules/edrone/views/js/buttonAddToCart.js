/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

document.addEventListener('DOMContentLoaded', function () {

    if (typeof prestashop !== 'undefined' && typeof prestashop.on === 'function') {

        prestashop.on("updateCart", function (e) {
            // In case adblock is active, suspend
            if (typeof _edrone !== 'object' || (typeof _edrone === 'object' && typeof _edrone.init !== 'function')) {
                return false;
            }

            if (e && e.reason && "add-to-cart" == e.reason.linkAction) {
                const idProduct = e.reason?.idProduct;
                const products = e.reason?.cart?.products;
                if (typeof products === 'object') {
                    const images = (e.reason.images, products[products.length - 1]);
                    _edrone.product_category_ids = _edrone.product_category_ids;
                    _edrone.product_category_names = _edrone.product_category_names;
                    _edrone.product_titles = images.name;
                    _edrone.product_urls = images.url;
                    _edrone.product_images = images.images[0].large.url;
                    _edrone.product_ids = idProduct;
                    _edrone.action_type = "add_to_cart";
                    _edrone.init()
                }
            }
        });
    } else {
        const originalOpen = XMLHttpRequest.prototype.open;

        XMLHttpRequest.prototype.open = function(method, url) {
            // Capture add to cart requests (not compatible with PrestaShop 1.6)
            try {
                const urlParams = new URLSearchParams(url);

                if (
                    typeof _edrone !== 'undefined' &&
                    method === 'POST' && (
                        urlParams.get('controller') === 'cart' ||
                        url.substring(url.lastIndexOf('/') + 1) === 'cart'
                    )
                ) {
                    this.onreadystatechange = () => {
                        if (this.readyState === 4) {
                            const response = JSON.parse(this.responseText);
                            const idProduct = response.id_product;
                            const products = response.cart?.products;
                            const addedProduct = products.map(product => {
                                if (product.id_product == idProduct) {
                                    return product;
                                }
                            });

                            _edrone.product_category_ids = _edrone.product_category_ids;
                            _edrone.product_category_names = _edrone.product_category_names;
                            _edrone.product_titles = addedProduct.name;
                            _edrone.product_urls = addedProduct.url;
                            _edrone.product_images = addedProduct.cover?.large.url;
                            _edrone.product_ids = idProduct;
                            _edrone.action_type = "add_to_cart";
                            _edrone.init();
                        }
                    }
                }
            } catch (error) {
                console.error("Error occured when intercepting HTTP request: ", error)
            }

            originalOpen.apply(this, arguments);
        }

    }
});