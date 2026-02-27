(document.readyState === 'loading'
  ? document.addEventListener('DOMContentLoaded', onDOMContentLoaded)
  : onDOMContentLoaded()
);

function onDOMContentLoaded() {
    initLinkBlocks();
    initProductBlocks();
    initModals();
}

function initLinkBlocks() {
    const blockLinks = document.querySelectorAll('[data-target-url]');
    blockLinks.forEach(blockLink => {
        try {
            const targetUrl = blockLink.getAttribute('data-target-url');

            const newBlockLink = document.createElement('a');
            [...blockLink.attributes].forEach(attr => {
                newBlockLink.setAttribute(attr.name, attr.value);
            });
            while (blockLink.firstChild) {
                newBlockLink.appendChild(blockLink.firstChild);
            }
            newBlockLink.href = targetUrl;
            newBlockLink.classList.add('pse-a-dynamic');

            blockLink.parentNode.replaceChild(newBlockLink, blockLink);
        } catch (e) {
            console.error(e);
        }
    });
};

function initProductBlocks() {
    const productDataCache = new Map();
    const pendingRequests = new Map();

    const requestProductData = async (productId) => {
        // cache hit
        if (productDataCache.has(productId)) {
            return productDataCache.get(productId);
        }

        // If there's already a pending request for this product ID, 
        // return that promise instead of creating a new request
        if (pendingRequests.has(productId)) {
            return pendingRequests.get(productId);
        }

        // Create a new request and store its promise
        const requestPromise = (async () => {
            const response = await fetch(`${PShowFEProductApiUrl}?pid=${productId}`);
            const data = await response.json();
            productDataCache.set(productId, data);
            pendingRequests.delete(productId); // Remove from pending once complete
            return data;
        })();

        pendingRequests.set(productId, requestPromise);
        return requestPromise;
    };

    const allowedTags = ['name', 'price', 'link', 'cover', 'add_to_cart'];

    const productBlocks = document.querySelectorAll('[data-product-id]');
    productBlocks.forEach(async block => {
        if (block.tagName.toLowerCase() === 'input') return; // skip inputs
        
        const blockClone = block.cloneNode(true);

        const productId = blockClone.getAttribute('data-product-id');
        const productData = await requestProductData(productId);

        productData["add_to_cart"] = PShowFEProductAddToCartButton;
        productData["add_to_cart"] = productData["add_to_cart"]
        .replace('<button', `<button data-add-to-cart-product-id="${productId}"`);

        blockClone.innerHTML = allowedTags.reduce((acc, tag) => {
            return acc.replace(`{{${tag}}}`, productData[tag])
                .replace(`%7B%7B${tag}%7D%7D`, productData[tag]);
        }, blockClone.innerHTML);

        const targetUrl = blockClone.getAttribute('data-target-url');
        if (targetUrl) {
            blockClone.setAttribute(
                'data-target-url',
                targetUrl.replace('%7B%7Blink%7D%7D', productData.link)
                    .replace('{{link}}', productData.link)
            );
        }
        const href = blockClone.getAttribute('href');
        if (href) {
            blockClone.setAttribute(
                'href',
                href.replace('%7B%7Blink%7D%7D', productData.link)
                    .replace('{{link}}', productData.link)
            );
        }
        blockClone.querySelectorAll('[data-target-url]').forEach(link => {
            link.setAttribute(
                'data-target-url',
                link.getAttribute('data-target-url')
                    ?.replace('%7B%7Blink%7D%7D', productData.link)
                    .replace('{{link}}', productData.link)
            );
        });

        const parent = block.parentElement;
        parent.replaceChild(blockClone, block);
    });

    document.body.addEventListener('click', (e) => {
        const button = e.target.closest('button[data-add-to-cart-product-id]');
        if (button) {
            e.stopPropagation();
            e.preventDefault();

            const productId = button.getAttribute('data-add-to-cart-product-id');
            const productAttributeId = 0;

            let data = "qty=1&id_product=" + productId ;
            data += "&id_product_attribute=" + productAttributeId;
            data += "&id_customization=0&add=1&action=update&token=" + PShowFEProductAddToCartToken;

            fetch(PShowFEProductAddToCartUrl, {
                method: 'POST',
                body: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
            })
                .then(() => {
                    prestashop.emit("updateCart", {
                        reason: {
                            idProduct: productId,
                            idProductAttribute: productAttributeId,
                            idCustomization: 0,
                            linkAction: "add-to-cart",
                            cart: data.cart
                        }, resp: e
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        }
    });

};

function initModals() {

    if (typeof PSEImageModal === 'undefined' || !PSEImageModal) {
        return;
    }

    const createModal = (body, footer) => {
        const modal = document.createElement('div');
        modal.classList.add('pse-modal');

        const modalContent = document.createElement('div');
        modalContent.classList.add('pse-modal-content');
        modal.appendChild(modalContent);

        const modalCloseBtn = document.createElement('button');
        modalCloseBtn.classList.add('pse-modal-close');
        modalContent.appendChild(modalCloseBtn);

        const modalContentBody = document.createElement('div');
        modalContentBody.classList.add('pse-modal-body');
        modalContentBody.innerHTML = body;
        modalContent.appendChild(modalContentBody);

        if (typeof footer !== 'undefined' && footer) {
            const modalContentFooter = document.createElement('div');
            modalContentFooter.classList.add('pse-modal-footer');
            modalContentFooter.innerHTML = footer;
            modalContent.appendChild(modalContentFooter);
        }

        document.body.appendChild(modal);

        modal.classList.add('pse-modal-open');

        modalCloseBtn.addEventListener('click', () => {
            modal.remove();
        });
    }

    $(document).on('click', '.pse-figure', function () {
        const imageSrc = this.querySelector('img').getAttribute('src');
        const figureCaption = this.querySelector('figcaption')?.innerHTML;
        createModal(
            `<img src="${imageSrc.replace('.thumbnail.', '.')}" alt="${figureCaption || ""}"/>`,
            figureCaption
        );
    });

};

