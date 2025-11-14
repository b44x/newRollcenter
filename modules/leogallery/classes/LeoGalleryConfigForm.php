<?php
/**
 * 2007-2022 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoGallery is module help you can displays amazing gallery with many features on product page
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2022 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

include_once dirname(__FILE__).'/LeoGalleryModel.php';

class LeoGalleryConfigForm extends LeoGalleryModel
{
    public $root;
    public $main_root;
    public $img_selector;
    public $selector;
    public $current_class;
    public $img_captions;
    public $img_captions_offset;
    
    
    public $thumbnails;
    public $animate_thumbs;
    public $show_thumbs;
    public $thumb_size;
    public $img_size;
    
    public $m_thumbnails;
    public $m_animate_thumbs;
    public $m_show_thumbs;
    public $m_thumb_size;
    public $m_img_size;
    
    public $fullscreen;
    public $zoom;
    public $closable;
    public $loop;
    public $slide_end_animatoin;
    public $hide_control_on_end;
    public $key_press;
    public $esc_key;
    public $controls;
    
    public $preload;
    public $mousewheel;
    public $download;
    
    public $autoplay;
    public $pause;
    public $progress;
    
    public $hash;
    public $counter;
    public $enable_drag;
    public $enable_swipe;
    
    public $thumbWidht;
    public $thumbHeight;
    
    public $bg_color;
    public $controls_color;
    public $controls_hcolor;
    
    public $toolbar_bg;
    public $prev_next_bg;
    public $prev_next_hbg;
    public $prev_next_color;
    public $prev_next_hcolor;
    
    public $thumb_bg;
    public $thumb_border;
    public $active_thumb_border;
    
    
    protected $defaultAttributeType = 'switch';
    
    public function rules()
    {
        return array(
            array(
                array(
                    'mousewheel',
                    'root',
                    'main_root',
                    'img_selector',
                    'selector',
                    'current_class',
                    'img_captions',
                    
                    'thumbnails',
                    'animate_thumbs',
                    'show_thumbs',
                    'thumb_size',
                    'img_size',
                    
                    'm_thumbnails',
                    'm_animate_thumbs',
                    'm_show_thumbs',
                    'm_thumb_size',
                    'm_img_size',
                    
                    'fullscreen',
                    'zoom',
                    'autoplay',
                    'progress',
                    'hash',
                    'closable',
                    'loop',
                    'esc_key',
                    'key_press',
                    'controls',
                    'slide_end_animatoin',
                    'hide_control_on_end',
                    'preload',
                    'download',
                    'counter',
                    'enable_drag',
                    'enable_swipe',
                    
                    'bg_color',
                    'controls_color',
                    'controls_hcolor',
                    'toolbar_bg',
                    'prev_next_bg',
                    'prev_next_hbg',
                    'prev_next_color',
                    'prev_next_hcolor',

                    'thumb_bg',
                    'thumb_border',
                    'active_thumb_border'
                ), 'safe'
            ),
            array(
                array('preload', 'pause'), 'isInt'
            ),
            array(
                array('root', 'img_captions_offset'), 'validateRequired',
                'message' => $this->l('"{label}" is required field'),
            ),
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'root' => $this->l('Thumbs root'),
            'main_root' => $this->l('Main image container'),
            'img_selector' => $this->l('Main image selector'),
            'selector' => $this->l('Selector'),
            'current_class' => $this->l('Current thumb css class'),
            'img_captions' => $this->l('Display images caption'),
            'img_captions_offset' => $this->l('Caption bottom offset, px'),
            'mousewheel' => $this->l('Mousewheel'),
            
            'thumbnails' => $this->l('Thumbnails on PC'),
            'animate_thumbs' => $this->l('Animate thumbnails on PC'),
            'show_thumbs' => $this->l('Show thumbnails by default on PC'),
            'thumb_size' => $this->l('Gallery thumbs size on PC'),
            'img_size' => $this->l('Gallery image size on PC'),
            
            'm_thumbnails' => $this->l('Thumbnails on mobile'),
            'm_animate_thumbs' => $this->l('Animate thumbnails on mobile'),
            'm_show_thumbs' => $this->l('Show thumbnails by default on mobile'),
            'm_thumb_size' => $this->l('Gallery thumbs size on mobile'),
            'm_img_size' => $this->l('Gallery image size on mobile'),
            
            'fullscreen' => $this->l('Enable fullscreen mode'),
            'zoom' => $this->l('Zoom'),
            'autoplay' => $this->l('Autoplay'),
            'pause' => $this->l('Delay'),
            'progress' => $this->l('Progress bar'),
            'hash' => $this->l('Hashed URL'),
            'closable' => $this->l('Clicks on dimmer to close gallery'),
            'loop' => $this->l('Loop'),
            'esc_key' => $this->l('"ESC" Key'),
            'key_press' => $this->l('Keyboard navigation'),
            'controls' => $this->l('Controls'),
            'slide_end_animatoin' => $this->l('SlideEnd animation'),
            'hide_control_on_end' => $this->l('Hide control on end'),
            'preload' => $this->l('Preload'),
            'download' => $this->l('Enable download'),
            'counter' => $this->l('Counter'),
            'enable_drag' => $this->l('Drag'),
            'enable_swipe' => $this->l('Swipe'),
            'thumb_size' => $this->l('Gallery thumbs size'),
            'bg_color' => $this->l('Background color'),
            'controls_color' => $this->l('Controls color'),
            'controls_hcolor' => $this->l('Controls hover color'),
            
            'toolbar_bg' => $this->l('Toolbar background color'),
            'prev_next_bg' => $this->l('Prev/Next buttons background color'),
            'prev_next_hbg' => $this->l('Prev/Next buttons hover background color'),
            'prev_next_color' => $this->l('Prev/Next buttons color'),
            'prev_next_hcolor' => $this->l('Prev/Next buttons hover color'),

            'thumb_bg' => $this->l('Thumbnails panel background color'),
            'thumb_border' => $this->l('Thumbnail border color'),
            'active_thumb_border' => $this->l('Active thumbnail border color'),
        );
    }
    
    public function attributeDefaults()
    {
        return array(
            'root' => '.images-container',
            'main_root' => '.product-cover',
            'current_class' => 'selected',
            'selector' => 'li',
            'img_selector' => 'img',
            'img_captions' => 1,
            'img_captions_offset' => 118,
            'mousewheel' => 1,
            
            'thumbnails' => 1,
            'animate_thumbs' => 1,
            'show_thumbs' => 1,
            'thumb_size' => $this->getProductThumbSize(),
            'img_size' => $this->getProductImgSize(),
            
            'm_thumbnails' => 1,
            'm_animate_thumbs' => 1,
            'm_show_thumbs' => 1,
            'm_thumb_size' => $this->getProductThumbSize(),
            'm_img_size' => $this->getProductImgSize(),
            
            'autoplay' => 1,
            'pause' => 3000,
            'progress' => 1,
            'fullscreen' => 1,
            'zoom' => 1,
            'hash' => 1,
            'closable' => 1,
            'loop' => 1,
            'esc_key' => 1,
            'key_press' => 1,
            'controls' => 1,
            'slide_end_animatoin' => 0,
            'hide_control_on_end' => 0,
            'preload' => 1,
            'download' => 0,
            'counter' => 1,
            'enable_drag' => 1,
            'enable_swipe' => 1,
            'bg_color' => '#000000',
            'controls_color' => '#999999',
            'controls_hcolor' => '#999999',
            'toolbar_bg' => 'rgba(0,0,0,0.45)',
            'prev_next_bg' => 'rgba(0,0,0,0.45)',
            'prev_next_hbg' => 'rgba(0,0,0,0.45)',
            'prev_next_color' => '#999999',
            'prev_next_hcolor' => '#FFFFFF',

            'thumb_bg' => '#0D0A0A',
            'thumb_border' => '#ffffff',
            'active_thumb_border' => '#a90707'
        );
    }
    
    public function attributeTypes()
    {
        return array(
            'main_root' => 'text',
            'current_class' => 'text',
            'img_selector' => 'text',
            'root' => 'text',
            'selector' => 'text',
            'img_captions' => 'switch',
            'img_captions_offset' => 'text',
            'preload' => 'text',
            'pause' => 'text',
            'thumb_size' => 'select',
            'm_thumb_size' => 'select',
            'img_size' => 'select',
            'm_img_size' => 'select',
            'bg_color' => 'color',
            'controls_color' => 'color',
            'controls_hcolor' => 'color',
            
            'toolbar_bg' => 'color',
            'prev_next_bg' => 'color',
            'prev_next_hbg' => 'color',
            'prev_next_color' => 'color',
            'prev_next_hcolor' => 'color',

            'thumb_bg' => 'color',
            'thumb_border' => 'color',
            'active_thumb_border' => 'color'
        );
    }
    
    public function attributeDescriptions()
    {
        return array(
            'root' => $this->l('Thumbs images container root CSS selector.'),
            'main_root' => $this->l('Main image container root CSS selector.'),
            'img_selector' => $this->l('Main image CSS selector.'),
            'mousewheel' => $this->l('Change slide on mousewheel'),
            
            'thumbnails' => $this->l('Enable thumbnails for the gallery'),
            'animate_thumbs' => $this->l('Enable thumbnail animation.'),
            'show_thumbs' => $this->l('Show thumbnails by default'),
            'thumb_size' => $this->l('Choose thumbnails size used on your product page'),
            'img_size' => $this->l('This option apply only if slider is enabled'),
            
            'm_thumbnails' => $this->l('Enable thumbnails for the gallery'),
            'm_animate_thumbs' => $this->l('Enable thumbnail animation.'),
            'm_show_thumbs' => $this->l('Show thumbnails by default'),
            'm_thumb_size' => $this->l('Choose thumbnails size used on your product page'),
            'm_img_size' => $this->l('This option apply only if slider is enabled'),
            
            'fullscreen' => $this->l('Enable/Disable fullscreen mode'),
            'zoom' => $this->l('Show zoom button'),
            'autoplay' => $this->l('Show autoplay button'),
            'pause' => $this->l('The time (in ms) between each auto transition.'),
            'progress' => $this->l('Enable autoplay progress bar.'),
            'hash' => $this->l('Enable hashed url for each gallery slide'),
            'closable' => $this->l('Allows clicks on dimmer to close gallery'),
            'loop' => $this->l('If false, will disable the ability to loop back to the beginning of the gallery when on the last element.'),
            'esc_key' => $this->l('Whether the gallery could be closed by pressing the "Esc" key'),
            'key_press' => $this->l('Enable keyboard navigation'),
            'controls' => $this->l('If false, prev/next buttons will not be displayed.'),
            'slide_end_animatoin' => $this->l('Enable slideEnd animation'),
            'hide_control_on_end' => $this->l('If true, prev/next button will be hidden on first/last image. false if slideEndAnimatoin or loop is enabled.'),
            'preload' => $this->l('Number of preload slides. will execute only after the current slide is fully loaded.'),
            'download' => $this->l('Enable download button'),
            'counter' => $this->l('Whether to show total number of images and index number of currently displayed image.'),
            'enable_drag' => $this->l('Enables desktop mouse drag support'),
            'enable_swipe' => $this->l('Enables swipe support'),
            
        );
    }
    
    public function getFormTitle()
    {
        return $this->l('Gallery settings');
    }
    
    public function getProductThumbSize()
    {
        return ImageType::getFormattedName('small');
    }
    
    public function getProductImgSize()
    {
        return ImageType::getFormattedName('large');
    }
    
    public function MThumbSizeSelectOptions()
    {
        return $this->thumbSizeSelectOptions();
    }
    
    public function thumbSizeSelectOptions()
    {
        $types = ImageType::getImagesTypes('products');
        $result = array();
        foreach ($types as $type) {
            $result[] = array(
                'id' => $type['name'],
                'name' => $type['name'] . ' (' . $type['width'] . 'x' . $type['height'] . ')'
            );
        }
        return $result;
    }
    
    public function mImgSizeSelectOptions()
    {
        return $this->imgSizeSelectOptions();
    }
    
    public function imgSizeSelectOptions()
    {
        $types = ImageType::getImagesTypes('products');
        $result = array(
            array(
                'id' => '_orig',
                'name' => $this->l('ORIGINAL')
            )
        );
        foreach ($types as $type) {
            $result[] = array(
                'id' => $type['name'],
                'name' => $type['name'] . ' (' . $type['width'] . 'x' . $type['height'] . ')'
            );
        }
        return $result;
    }
}
