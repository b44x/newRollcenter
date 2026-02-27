ArticleEditor.add('plugin', 'blocklink', {
    start: function() {
        this.app.toolbar.add('blocklink', {
            title: 'Block link',
            icon: '<i class="fa fa-link"></i>',
            command: 'blocklink.popup',
            blocks: {
                all: true,
                except: ['variable', 'figure', 'paragraph', 'image']
            }
        });
    },
    popup: function(params, button) {
        var instance = this.app.block.get();
        var $block = instance.getBlock();

        console.log($block.nodes[0].tagName);
        if ($block.nodes[0].tagName != "DIV") {
            console.log("DIV is only allowed");
            return;
        }

        // create
        var popup = this.app.popup.create('blocklink', {
            title: 'Block link',
            width: '400px',
            form: {
                target_url: { type: 'input', label: 'Target URL' }
            },
            footer: {
                save: { title: 'Save', command: 'blocklink.save', type: 'primary' },
                cancel: { title: 'Cancel', command: 'popup.close' }

            }
        });

        // data
        var target_url = $block.attr('data-target-url');

        // set form
        popup.setData({
            target_url: target_url
        });

        // open
        this.app.popup.open({ button: button });
    },
    save: function(popup) {
        this.app.popup.close();

        var data = popup.getData();
        var instance = this.app.block.get();
        var $block = instance.getBlock();

        if (data.target_url !== '') {
            $block.attr('data-target-url', data.target_url);
        }
        else {
            $block.removeAttr('data-target-url');
        }
    }
});