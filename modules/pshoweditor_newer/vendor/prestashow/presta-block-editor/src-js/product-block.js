ArticleEditor.add('plugin', 'product-block', {
    start: function () {
        this.app.toolbar.add('product-block', {
            title: 'Product block',
            icon: '<i class="fa fa-product-hunt"></i>',
            command: 'product-block.popup',
            blocks: {
                all: true,
                except: ['variable', 'figure', 'paragraph', 'image']
            }
        });
    },
    popup: function (params, button) {
        var instance = this.app.block.get();
        var $block = instance.getBlock();

        console.log($block.nodes[0].tagName);
        if ($block.nodes[0].tagName != "DIV") {
            console.log("DIV is only allowed");
            return;
        }

        // create
        var popup = this.app.popup.create('product-block', {
            title: 'Product block',
            width: '400px',
            form: {
                product_id: { type: 'input', label: 'Product ID' }
            },
            footer: {
                save: { title: 'Save', command: 'product-block.save', type: 'primary' },
                cancel: { title: 'Cancel', command: 'popup.close' }

            }
        });

        // data
        var product_id = $block.attr('data-product-id');

        // set form
        popup.setData({
            product_id: product_id
        });

        // open
        this.app.popup.open({ button: button });
    },
    save: function (popup) {
        this.app.popup.close();

        var data = popup.getData();
        var instance = this.app.block.get();
        var $block = instance.getBlock();

        if (data.product_id !== '') {
            $block.attr('data-product-id', data.product_id);
        }
        else {
            $block.removeAttr('data-product-id');
        }
    }
});