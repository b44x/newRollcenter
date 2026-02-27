/* globals PShowBEBaseUri, PShowBEConfig, PShowEditorImageUploadControllerURL, PShowEditorImageListControllerURL, PShowThemeCssUri */

import './plugin-icons';
import './block-link';
import './product-block';
import './my-fontcolor';

window.PShowBlockEditor = function (selector, settings) {
    settings = settings || PShowBEConfig;
    if (typeof PShowBEBaseUri !== 'undefined') {
        settings.css = PShowBEBaseUri + 'dist/';
    }

    function convertDivsToP(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const divs = doc.querySelectorAll('div');
        divs.forEach(div => {
            if (
                Array.from(div.childNodes).some(
                    node => node.nodeType === Node.TEXT_NODE 
                    && node.textContent.trim() !== ''
                ) 
                && !div.querySelector('div')
            ) {
                const p = doc.createElement('p');
                p.innerHTML = div.innerHTML;
                
                // Preserve original classes and add pse-p
                const originalClasses = div.className;
                p.className = originalClasses ? `${originalClasses} pse-p` : 'pse-p';
                
                p.style.cssText = div.style.cssText;
                if (div.getAttribute('data-target-url')) {
                    p.setAttribute('data-target-url', div.getAttribute('data-target-url'));
                }
                div.parentNode.replaceChild(p, div);
            }
        });
        return doc.body.innerHTML;
    }

    setTimeout(() => {
        let toolbarTopOffset = document.getElementById('header_infos')?.offsetHeight || 0;
        toolbarTopOffset += document.getElementsByClassName('header-toolbar')[0]?.offsetHeight || 0;
        toolbarTopOffset += document.getElementsByClassName('page-head')[0]?.offsetHeight || 0;

        let tinyMceInterval = {};
        const textarea = document.querySelectorAll(selector);
        let editor = {};

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === "attributes") {
                    if (mutation.target.disabled) {
                        editor[mutation.target.id].disable();
                        return;
                    }
                    editor[mutation.target.id].enable();
                }
            });
        });

        for (let i = 0; i < textarea.length; i++) {
            const col = textarea[i].closest('.col-lg-9');

            if (col) {
                col.classList.add('col-lg-11');
                col.classList.remove('col-lg-9');

                const colLangSelector = col.nextElementSibling;
                if (colLangSelector) {
                    colLangSelector.classList.add('col-lg-1');
                    colLangSelector.classList.remove('col-lg-2');
                }
            }

            if (!textarea[i].id) {
                textarea[i].id = 'pse-editor-' + (Math.random().toString(36).substring(2));
            }

            // remove tinymce
            textarea[i].classList.remove('autoload_rte');

            // MIGRATIONS
            // .grid    ->  .pse-grid
            textarea[i].value = textarea[i].value.replace(/([^-])grid/g, '$1pse-grid');
            // <table>  ->  <table class="pse-table">
            textarea[i].value = textarea[i].value.replace(/<table>/g, '<table class="pse-table">');
            // .valign- ->  .pse-valign-
            textarea[i].value = textarea[i].value.replace(/([^-])valign-([a-z]+)/g, '$1pse-valign-$2');
            // .align-  ->  .pse-align-
            textarea[i].value = textarea[i].value.replace(/([^-v])align-([a-z]+)/g, '$1pse-align-$2');

            const valueDOM = new DOMParser().parseFromString(textarea[i].value, "text/html");
            const valueOnlyDOM = valueDOM?.body?.firstChild;
            if (valueOnlyDOM && typeof valueOnlyDOM.querySelector === 'function') {
                const contentDiv = valueOnlyDOM.querySelector('div[class="app__tabs__content__inner js__app__tabs__content pse-div"]');
                if (contentDiv && contentDiv?.innerHTML) {
                    textarea[i].value = contentDiv.innerHTML;
                }
            }

            // <div (...) class="pse-div">(some text)</div> -> <p ... class="pse-p">(some text)</p>
            textarea[i].value = convertDivsToP(textarea[i].value);

            editor[textarea[i].id] = ArticleEditor('#' + textarea[i].id, {
                image: {
                    width: true,
                    types: ['image/png', 'image/jpeg'],
                    upload: PShowEditorImageUploadControllerURL,
                    select: PShowEditorImageListControllerURL,
                },
                topbar: {
                    undoredo: true
                },
                toolbar: {
                    stickyTopOffset: toolbarTopOffset,
                },
                snippets: PShowThemeCssUri + 'pshow-block-editor-snippets.json',
                custom: {
                    css: [
                        PShowBEBaseUri + 'dist/front.css',
                        'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
                        PShowThemeCssUri + 'pshow-block-editor.css',
                    ],
                    js: [
                        PShowBEBaseUri + 'dist/admin.js',
                    ]
                },
                subscribe: {
                    'editor.before.change': function(event) {
                        $('form[name="product"] .cancel-button').prop('disabled', false).removeClass('disabled');
                        $('form[name="product"] .product-form-save-button').prop('disabled', false).removeClass('disabled');
                        
                        // remove data-temp-block tags
                        const dom = new DOMParser().parseFromString(event.params.html, 'text/html');
                        const tempBlocks = dom.querySelectorAll('[data-temp-block="true"]');
                        if (tempBlocks) {
                            tempBlocks.forEach(block => {
                                block.remove();
                            });
                        }

                        // restore cover images
                        const replacedImages = dom.querySelectorAll('.replaced-cover-image');
                        replacedImages.forEach(img => {
                            img.src = '{{cover}}';
                            img.classList.remove('replaced-cover-image');
                        });

                        event.params.html = dom.body.outerHTML;

                        const iframe = editor[textarea[i].id].editor.$editor.nodes[0];
                        if (iframe) {
                            iframe.contentWindow.pinProductBlockIds();
                            iframe.contentWindow.replaceCoverImages();
                        }
                    }
                },
                plugins: [
                    'selector',
                    'imageposition',
                    'imageresize',
                    'removeformat',
                    'specialchars',
                    'underline',
                    // 'definedlinks',
                    'counter',
                    'blockcode',
                    'reorder',
                    'fontsize',
                    'fontfamily',
                    'my-fontcolor',
                    'pshow-icons',
                    'blocklink',
                    'product-block',
                ],
                path: {
                    title: 'Editor',
                },
                grid: {
                    classname: 'pse-grid',
                },
                table: {
                    template: '<table class="pse-table"><tr><td></td><td></td></tr><tr><td></td><td></td></tr></table>'
                },
                align: {
                    'left': 'pse-align-left',
                    'center': 'pse-align-center',
                    'right': 'pse-align-right',
                    'justify': 'pse-align-justify'
                },
                valign: {
                    top: 'pse-valign-top',
                    middle: 'pse-valign-middle',
                    bottom: 'pse-valign-bottom'
                },
                codemirror: {
                    lineNumbers: true
                },
                classes: {
                    'p': 'pse-p',
                    'h1': 'pse-h1',
                    'h2': 'pse-h2',
                    'h3': 'pse-h3',
                    'h4': 'pse-h4',
                    'h5': 'pse-h5',
                    'h6': 'pse-h6',
                    'blockquote': 'pse-blockquote',
                    'pre': 'pse-pre',
                    'code': 'pse-code',
                    'table': 'pse-table',
                    'th': 'pse-th',
                    'td': 'pse-td',
                    'tr': 'pse-tr',
                    'ul': 'pse-ul',
                    'ol': 'pse-ol',
                    'li': 'pse-li',
                    'img': 'pse-img',
                    'a': 'pse-a',
                    'span': 'pse-span',
                    'div': 'pse-div',
                    'iframe': 'pse-iframe',
                    'figure': 'pse-figure',
                    'figcaption': 'pse-figcaption',
                    'section': 'pse-section',
                    'hr': 'pse-hr',
                    'b': 'pse-b',
                    'i': 'pse-i',
                    'u': 'pse-u',
                    's': 'pse-s',
                    'del': 'pse-del',
                    'sub': 'pse-sub',
                    'sup': 'pse-sup',
                    'em': 'pse-em',
                    'strong': 'pse-strong',
                    'small': 'pse-small',
                    'abbr': 'pse-abbr'
                },
                // definedlinks: {
                //     items: [
                //         { "name": "Select...", "url": false },
                //         { "name": "Google", "url": "http://google.com" },
                //         { "name": "Home", "url": "/" },
                //         { "name": "About", "url": "/about/" },
                //         { "name": "Contact", "url": "/contact/" }
                //     ]
                // },
                ...settings,
            });

            observer.observe(textarea[i], {
                attributes: true,
            });

            // watch for changes made by jquery
            let originalVal = $.fn.val;
            $.fn.val = function (value) {
                if (value !== undefined && this[0]?.id === textarea[i]?.id) {
                    editor[textarea[i].id].editor.setContent({html: value});
                }
                return originalVal.apply(this, arguments);
            };

            // remove tinymce
            if (typeof tinymce !== "undefined") {
                tinymce.remove(`#${textarea[i].id}`);
                textarea[i].style.display = 'none';
            }
            tinyMceInterval[textarea[i].id] = setInterval(() => {
                if (typeof tinymce === "undefined") {
                    return;
                }
                tinymce.remove(`#${textarea[i].id}`);
                textarea[i].style.display = 'none';
                clearInterval(tinyMceInterval[textarea[i].id]);
            }, 2000);
        }
    }, 1);
};

window.addEventListener('load', function () {
    PShowBlockEditor('[data-pshow-block-editor]');
    if (PShowBEConfig?.editors_id) {
        for (const id of PShowBEConfig.editors_id) {
            PShowBlockEditor(id);
        }
    }

    const head = document.head;
    const link = document.createElement('link');
    link.href = 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
    link.rel = 'stylesheet';
    head.appendChild(link);
});
