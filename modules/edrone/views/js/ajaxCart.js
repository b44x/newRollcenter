/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

document.addEventListener("DOMContentLoaded", function (){
    // Stop execution if scripts were not successfully loaded, e.g when user has adblock installed
    if (typeof _edrone !== 'object' || (typeof _edrone === 'object' && typeof _edrone.init !== 'function')) {
        return false;
    }

    if (typeof(ajaxCart) === "object") {
        ajaxCart.newAdd = ajaxCart.add;
        ajaxCart.add = function(idProduct, idCombination, addedFromProductPage, callerElement, quantity, whishlist) {
            $("body").ajaxComplete(function(e, xhr, options) {
                $(e.currentTarget).unbind("ajaxComplete");

                const url = _edrone?.edrone_ajax_shop_url ? _edrone.edrone_ajax_shop_url : window.location.origin
                const request = new XMLHttpRequest();

                request.open("GET", edroneAddToCartController);

                request.onreadystatechange = function() {
                    if (this.readyState === 4) {
                        try {
                            _edrone.action_type = data.action_type
                            _edrone.product_category_ids = data.product_category_ids
                            _edrone.product_category_names = data.product_category_names
                            _edrone.product_ids = data.product_ids
                            _edrone.product_images = data.product_images
                            _edrone.product_skus = data.product_skus
                            _edrone.product_titles = data.product_titles
                            _edrone.action_type = encodeURIComponent(data.action_type)
                            _edrone.product_category_ids = encodeURIComponent(data.product_category_ids)
                            _edrone.product_category_names = encodeURIComponent(data.product_category_names)
                            _edrone.product_ids = encodeURIComponent(data.product_ids)
                            _edrone.product_images = encodeURIComponent(data.product_images)
                            _edrone.product_skus = encodeURIComponent(data.product_skus)
                            _edrone.product_titles = encodeURIComponent(data.product_titles)
                            _edrone.init();
                        } catch (e) {
                            console.log("Failed to get product data.")
                        }
                    }
                }
            });

            ajaxCart.newAdd(idProduct, idCombination, addedFromProductPage, callerElement, quantity, whishlist);
        }
    }
});