window.productBlockInstructions = () => {
    alert(`Allowed tags:\n{{link}} - link to the product page\n{{name}} - product name\n{{price}} - product price\n{{cover}} - product cover image\n{{add_to_cart}} - add to cart button`)
};

window.pinProductBlockIds = () => {
    const productBlocks = document.querySelectorAll('[data-product-id]');
    productBlocks.forEach(block => {
        block.style.setProperty('position', 'relative');

        const productId = block.getAttribute('data-product-id');
        const idBlock = block.querySelector('[data-product-id-block]');
        if (productId) {
            let before;
            if (!idBlock) {
                before = document.createElement('section');
                before.setAttribute('data-temp-block', 'true');
                before.setAttribute('data-product-id-block', 'true');
                before.setAttribute('title', 'ID of the product');
                before.classList.add('block-before');
                before.classList.add('noneditable');
                before.style.setProperty('position', 'absolute');
                before.style.setProperty('top', '-2px');
                before.style.setProperty('right', '0');
                before.style.setProperty('transform', 'translate(0, 0)');
                before.style.setProperty('font-size', '11px');
                before.style.setProperty('padding', '0 3px');
                before.style.setProperty('background-color', 'rgba(0, 88, 251, .2)');
                before.style.setProperty('color', 'white');
                before.addEventListener('click', productBlockInstructions);
            } else {
                before = idBlock;
            }
            before.innerHTML = `<i class="fa fa-product-hunt"></i> #${productId}`;
            if (!idBlock) {
                block.insertBefore(before, block.firstChild);
            }
        } else if (idBlock) {
            idBlock.remove();
        }
    });
};

window.replaceCoverImages = () => {
    const images = document.querySelectorAll('img');

    const alternativeImageUrl = '/modules/pshoweditor/vendor/prestashow/presta-block-editor/product-image-placeholder.png';

    // Loop through all images and check if src is '{{cover}}'
    images.forEach(img => {
        if (img.getAttribute('src') === '%7B%7Bcover%7D%7D' || img.getAttribute('src') === '{{cover}}') {
            img.src = alternativeImageUrl;
            img.classList.add('replaced-cover-image');
        }
    });
};

window.addEventListener('DOMContentLoaded', () => {
    pinProductBlockIds();
    replaceCoverImages();
});

