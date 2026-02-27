ArticleEditor.add('plugin', 'pshow-icons', {
    start: function() {
        this.app.toolbar.add('pshow-icons', {
            title: 'Icons',
            icon: '<i class="fa fa-star"></i>',
            command: 'pshow-icons.popup'
        });
    },
    popup: function(params, button) {
        var icons = [
            'fa-adjust', 'fa-adn', 'fa-align-center', 'fa-align-justify',
            'fa-align-left', 'fa-align-right', 'fa-ambulance', 'fa-anchor',
            'fa-android', 'fa-angellist', 'fa-angle-double-down', 'fa-angle-double-left',
            'fa-angle-double-right', 'fa-angle-double-up', 'fa-angle-down', 'fa-angle-left',
            'fa-angle-right', 'fa-angle-up', 'fa-apple', 'fa-archive',
            'fa-area-chart', 'fa-arrow-circle-down', 'fa-arrow-circle-left', 'fa-arrow-circle-right',
            'fa-arrow-circle-up', 'fa-arrow-down', 'fa-arrow-left', 'fa-arrow-right',
            'fa-arrow-up', 'fa-arrows', 'fa-arrows-alt', 'fa-arrows-h',
            'fa-arrows-v', 'fa-asterisk', 'fa-at', 'fa-backward',
            'fa-ban', 'fa-bar-chart', 'fa-barcode', 'fa-bars',
            'fa-bed', 'fa-beer', 'fa-behance', 'fa-behance-square',
            'fa-bell', 'fa-bell-o', 'fa-bell-slash', 'fa-bell-slash-o',
            'fa-bicycle', 'fa-binoculars', 'fa-birthday-cake', 'fa-bitbucket',
            'fa-bitbucket-square', 'fa-bold', 'fa-bolt', 'fa-bomb',
            'fa-book', 'fa-bookmark', 'fa-bookmark-o', 'fa-briefcase',
            'fa-btc', 'fa-bug', 'fa-building', 'fa-building-o',
            'fa-bullhorn', 'fa-bullseye', 'fa-bus', 'fa-calculator',
            'fa-calendar', 'fa-calendar-o', 'fa-camera', 'fa-camera-retro',
            'fa-car', 'fa-caret-down', 'fa-caret-left', 'fa-caret-right',
            'fa-caret-up', 'fa-cart-arrow-down', 'fa-cart-plus', 'fa-cc',
            'fa-cc-amex', 'fa-cc-discover', 'fa-cc-mastercard', 'fa-cc-paypal',
            'fa-cc-stripe', 'fa-cc-visa', 'fa-certificate', 'fa-chain-broken',
            'fa-check', 'fa-check-circle', 'fa-check-circle-o', 'fa-check-square',
            'fa-check-square-o', 'fa-chevron-circle-down', 'fa-chevron-circle-left', 'fa-chevron-circle-right',
            'fa-chevron-circle-up', 'fa-chevron-down', 'fa-chevron-left', 'fa-chevron-right',
            'fa-chevron-up', 'fa-child', 'fa-circle', 'fa-circle-o',
            'fa-circle-o-notch', 'fa-circle-thin', 'fa-clipboard', 'fa-clock-o',
            'fa-cloud', 'fa-cloud-download', 'fa-cloud-upload', 'fa-code',
            'fa-code-fork', 'fa-coffee', 'fa-cog', 'fa-cogs',
            'fa-columns', 'fa-comment', 'fa-comment-o', 'fa-comments',
            'fa-comments-o', 'fa-compass', 'fa-compress', 'fa-copy',
            'fa-copyright', 'fa-credit-card', 'fa-crop', 'fa-crosshairs',
            'fa-css3', 'fa-cube', 'fa-cubes', 'fa-cutlery',
            'fa-database', 'fa-desktop', 'fa-diamond', 'fa-download',
            'fa-dropbox', 'fa-edit', 'fa-eject', 'fa-ellipsis-h',
            'fa-ellipsis-v', 'fa-envelope', 'fa-envelope-o', 'fa-envelope-square',
            'fa-eraser', 'fa-exchange', 'fa-exclamation', 'fa-exclamation-circle',
            'fa-exclamation-triangle', 'fa-expand', 'fa-external-link', 'fa-external-link-square',
            'fa-eye', 'fa-eye-slash', 'fa-eyedropper', 'fa-facebook',
            'fa-facebook-square', 'fa-fast-backward', 'fa-fast-forward', 'fa-fax',
            'fa-female', 'fa-fighter-jet', 'fa-file', 'fa-file-archive-o',
            'fa-file-audio-o', 'fa-file-code-o', 'fa-file-excel-o', 'fa-file-image-o',
            'fa-file-o', 'fa-file-pdf-o', 'fa-file-powerpoint-o', 'fa-file-text',
            'fa-file-text-o', 'fa-file-video-o', 'fa-file-word-o', 'fa-files-o',
            'fa-film', 'fa-filter', 'fa-fire', 'fa-fire-extinguisher',
            'fa-flag', 'fa-flag-checkered', 'fa-flag-o', 'fa-flask',
            'fa-flickr', 'fa-floppy-o', 'fa-folder', 'fa-folder-o',
            'fa-folder-open', 'fa-folder-open-o', 'fa-font', 'fa-forward',
            'fa-foursquare', 'fa-frown-o', 'fa-futbol-o', 'fa-gamepad',
            'fa-gavel', 'fa-gbp', 'fa-gift', 'fa-git',
            'fa-git-square', 'fa-github', 'fa-github-alt', 'fa-github-square',
            'fa-glass', 'fa-globe', 'fa-google', 'fa-google-plus',
            'fa-google-plus-square', 'fa-graduation-cap', 'fa-gratipay', 'fa-h-square',
            'fa-hand-o-down', 'fa-hand-o-left', 'fa-hand-o-right', 'fa-hand-o-up',
            'fa-hdd-o', 'fa-header', 'fa-headphones', 'fa-heart',
            'fa-heart-o', 'fa-history', 'fa-home', 'fa-hospital-o',
            'fa-html5', 'fa-image', 'fa-inbox', 'fa-indent',
            'fa-info', 'fa-info-circle', 'fa-inr', 'fa-instagram',
            'fa-ioxhost', 'fa-italic', 'fa-joomla', 'fa-jpy',
            'fa-jsfiddle', 'fa-key', 'fa-keyboard-o', 'fa-krw',
            'fa-language', 'fa-laptop', 'fa-lastfm', 'fa-lastfm-square',
            'fa-leaf', 'fa-lemon-o', 'fa-level-down', 'fa-level-up',
            'fa-life-ring', 'fa-lightbulb-o', 'fa-line-chart', 'fa-link',
            'fa-linkedin', 'fa-linkedin-square', 'fa-linux', 'fa-list',
            'fa-list-alt', 'fa-list-ol', 'fa-list-ul', 'fa-location-arrow',
            'fa-lock', 'fa-long-arrow-down', 'fa-long-arrow-left', 'fa-long-arrow-right',
            'fa-long-arrow-up', 'fa-magic', 'fa-magnet', 'fa-male',
            'fa-map-marker', 'fa-maxcdn', 'fa-meanpath', 'fa-medkit',
            'fa-meh-o', 'fa-microphone', 'fa-microphone-slash', 'fa-minus',
            'fa-minus-circle', 'fa-minus-square', 'fa-minus-square-o', 'fa-mobile',
            'fa-money', 'fa-moon-o', 'fa-motorcycle', 'fa-music',
            'fa-newspaper-o', 'fa-openid', 'fa-outdent', 'fa-pagelines',
            'fa-paint-brush', 'fa-paper-plane', 'fa-paper-plane-o', 'fa-paperclip',
            'fa-paragraph', 'fa-pause', 'fa-paw', 'fa-paypal',
            'fa-pencil', 'fa-pencil-square', 'fa-pencil-square-o', 'fa-phone',
            'fa-phone-square', 'fa-picture-o', 'fa-pie-chart', 'fa-pied-piper',
            'fa-pied-piper-alt', 'fa-pinterest', 'fa-pinterest-square', 'fa-plane',
            'fa-play', 'fa-play-circle', 'fa-play-circle-o', 'fa-plug',
            'fa-plus', 'fa-plus-circle', 'fa-plus-square', 'fa-plus-square-o',
            'fa-power-off', 'fa-print', 'fa-puzzle-piece', 'fa-qq',
            'fa-qrcode', 'fa-question', 'fa-question-circle', 'fa-quote-left',
            'fa-quote-right', 'fa-random', 'fa-rebel', 'fa-recycle',
            'fa-reddit', 'fa-reddit-square', 'fa-refresh', 'fa-renren',
            'fa-repeat', 'fa-reply', 'fa-reply-all', 'fa-retweet',
            'fa-road', 'fa-rocket', 'fa-rss', 'fa-rss-square',
            'fa-rub', 'fa-scissors', 'fa-search', 'fa-search-minus',
            'fa-search-plus', 'fa-sellsy', 'fa-server', 'fa-share',
            'fa-share-alt', 'fa-share-alt-square', 'fa-share-square', 'fa-share-square-o',
            'fa-shield', 'fa-ship', 'fa-shirtsinbulk', 'fa-shopping-cart',
            'fa-sign-in', 'fa-sign-out', 'fa-signal', 'fa-simplybuilt',
            'fa-sitemap', 'fa-skyatlas', 'fa-skype', 'fa-slack',
            'fa-sliders', 'fa-slideshare', 'fa-smile-o', 'fa-sort',
            'fa-sort-alpha-asc', 'fa-sort-alpha-desc', 'fa-sort-amount-asc', 'fa-sort-amount-desc',
            'fa-sort-asc', 'fa-sort-desc', 'fa-sort-numeric-asc', 'fa-sort-numeric-desc',
            'fa-soundcloud', 'fa-space-shuttle', 'fa-spinner', 'fa-spoon',
            'fa-spotify', 'fa-square', 'fa-square-o', 'fa-stack-exchange',
            'fa-stack-overflow', 'fa-star', 'fa-star-half', 'fa-star-half-o',
            'fa-star-o', 'fa-steam', 'fa-steam-square', 'fa-step-backward',
            'fa-step-forward', 'fa-stethoscope', 'fa-stop', 'fa-street-view',
            'fa-strikethrough', 'fa-stumbleupon', 'fa-stumbleupon-circle', 'fa-subscript',
            'fa-subway', 'fa-suitcase', 'fa-sun-o', 'fa-superscript',
            'fa-table', 'fa-tablet', 'fa-tachometer', 'fa-tag',
            'fa-tags', 'fa-tasks', 'fa-taxi', 'fa-tencent-weibo',
            'fa-terminal', 'fa-text-height', 'fa-text-width', 'fa-th',
            'fa-th-large', 'fa-th-list', 'fa-thumbs-down', 'fa-thumbs-o-down',
            'fa-thumbs-o-up', 'fa-thumbs-up', 'fa-ticket', 'fa-times',
            'fa-times-circle', 'fa-times-circle-o', 'fa-tint', 'fa-toggle-off',
            'fa-toggle-on', 'fa-train', 'fa-transgender', 'fa-transgender-alt',
            'fa-trash', 'fa-trash-o', 'fa-tree', 'fa-trello',
            'fa-trophy', 'fa-truck', 'fa-try', 'fa-tty',
            'fa-tumblr', 'fa-tumblr-square', 'fa-twitch', 'fa-twitter',
            'fa-twitter-square', 'fa-umbrella', 'fa-underline', 'fa-undo',
            'fa-university', 'fa-unlock', 'fa-unlock-alt', 'fa-upload',
            'fa-usd', 'fa-user', 'fa-user-md', 'fa-user-plus',
            'fa-user-secret', 'fa-user-times', 'fa-users', 'fa-venus',
            'fa-venus-double', 'fa-venus-mars', 'fa-viacoin', 'fa-video-camera',
            'fa-vimeo-square', 'fa-vine', 'fa-vk', 'fa-volume-down',
            'fa-volume-off', 'fa-volume-up', 'fa-weibo', 'fa-weixin',
            'fa-whatsapp', 'fa-wheelchair', 'fa-wifi', 'fa-windows',
            'fa-wordpress', 'fa-wrench', 'fa-xing', 'fa-xing-square',
            'fa-yahoo', 'fa-yelp', 'fa-youtube', 'fa-youtube-play',
            'fa-youtube-square'
        ];

        // Create popup
        var popup = this.app.popup.create('pshow-icons', {
            title: '',
            width: '600px',
            height: '400px'
        });

        var $body = popup.getBody();

        $body.css('padding', '15px');
        
        // Create search input
        var $search = this.dom('<input type="text" placeholder="Search icons..." style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px;">');
        
        // Create icons container
        var $container = this.dom('<div style="display: flex; flex-wrap: wrap; gap: 10px; max-height: 300px; overflow-y: auto; ">');
        
        // Add icons
        var iconElements = [];
        icons.forEach(function(icon) {
            var $icon = this.dom(`<div class="icon-item" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; text-align: center; width: 80px;">
                <i class="fa ${icon}" style="font-size: 20px;"></i>
                <div style="font-size: 10px; margin-top: 5px;">${icon.substring(3).replace(/-/g, ' ')}</div>
            </div>`);
            
            $icon.on('click', this.insert.bind(this, icon));
            $container.append($icon);
            iconElements.push({
                element: $icon,
                name: icon
            });
        }.bind(this));

        // Search functionality
        $search.on('input', function(e) {
            var searchTerm = e.target.value.toLowerCase();
            
            iconElements.forEach(function(item) {
                if (item.name.toLowerCase().includes(searchTerm)) {
                    item.element.css('display', 'block');
                } else {
                    item.element.css('display', 'none');
                }
            });
        }.bind(this));

        // Append elements
        $body.append($search);
        $body.append($container);

        // Open popup
        this.app.popup.open({ button: button });
    },
    insert: function(icon, e) {
        e.preventDefault();
        
        var $icon = this.dom(`<i class="fa ${icon}"></i>`);
        this.app.insertion.insertNode($icon);

        this.app.popup.close();
    }
});
