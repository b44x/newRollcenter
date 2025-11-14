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

class LeoGallerySliderConfigForm extends LeoGalleryModel
{
    public $enabled;
    
    public $mode;
    
    public $layout;
    public $vertical_height;
    public $thumb_width;
    
    public $thumb_size;
    public $img_size;
    public $thumb_item;
    
    public $all_images;
    
    public $controls;
    
    public $thumb_controls;
    
    public $thumb_controls_offset;
    
    public $gallery;
    
    public $control_bg;
    public $control_color;
    public $control_hbg;
    public $control_hcolor;
    
    public $thumb_border;
    public $thumb_border_color;
    
    public $active_thumb_border;
    public $active_thumb_border_color;
    
    //public $breakdowns;
    
    public function attributeTypes()
    {
        return array(
            'enabled' => 'switch',
            
            'layout' => 'select',
            
            'mode' => 'select',
            
            'thumb_size' => 'select',
            'img_size' => 'select',
            'all_images' => 'switch',
            'controls' => 'switch',
            
            'thumb_controls' => 'switch',
            'gallery' => 'switch',
            
            'control_bg' => 'color',
            'control_color' => 'color',
            'control_hbg' => 'color',
            'control_hcolor' => 'color',

            'thumb_border' => 'text',
            'thumb_border_color' => 'color',

            'active_thumb_border' => 'text',
            'active_thumb_border_color' => 'color',
            
            'breakdowns' => 'textarea'
        );
    }
    
    public function attributeDefaults()
    {
        return array(
            'enabled' => 1,
            
            'mode' => 'horizontal',
            
            'layout' => 'left',
            'vertical_height' => 500,
            'thumb_width' => 100,
            
            'thumb_size' => $this->getDefaultThumbSize(),
            'img_size' => $this->getDefaultImgSize(),
            
            'thumb_item' => 5,
            'all_images' => 0,
            
            'controls' => 1,
            'gallery' => 1,
            
            'thumb_controls_offset' => 1,
            
            'control_bg' => '',
            'control_color' => '#787878',
            'control_hbg' => '',
            'control_hcolor' => '#232323',

            'thumb_border' => '0',
            'thumb_border_color' => '',

            'active_thumb_border' => '3',
            'active_thumb_border_color' => '#2fb5d2',
        );
    }


    public function rules()
    {
        return array(
            array(
                array(
                    'enabled',
                    
                    'mode',
                    
                    'layout',
                    
                    'thumb_size',
                    'img_size',
                    'all_images',
                    
                    'controls',
                    
                    'thumb_controls',
                    
                    'gallery',
                       
                    'control_bg',
                    'control_color',
                    'control_hbg',
                    'control_hcolor',

                    'thumb_border',
                    'thumb_border_color',

                    'active_thumb_border',
                    'active_thumb_border_color',
                    
                    'breakdowns'
                ), 'safe'
            ),
            array(
                array(
                    'active_thumb_border',
                    'thumb_border',
                    'thumb_controls_offset',
                    'vertical_height',
                    'thumb_width',
                    'thumb_item'
                ), 'isInt'
            )
        );
    }
    
    public function layoutSelectOptions()
    {
        return array(
            array(
                'id' => 'left',
                'name' => $this->l('Thumbs on left side')
            ),
            array(
                'id' => 'right',
                'name' => $this->l('Thumbs on right side')
            ),
        );
    }
    
    public function modeSelectOptions()
    {
        return array(
            array(
                'id' => 'horizontal',
                'name' => $this->l('Horizontal')
            ),
            array(
                'id' => 'vertical',
                'name' => $this->l('Vertical')
            ),
        );
    }
    
    public function getDefaultThumbSize()
    {
        return ImageType::getFormattedName('home');
    }
    
    public function getDefaultImgSize()
    {
        return ImageType::getFormattedName('large');
    }
    
    public function thumbSizeSelectOptions()
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
    
    public function attributeLabels()
    {
        return array(
            'enabled' => $this->l('Enable'),
            
            'mode' => $this->l('Slider desktop mode'),
            
            'layout' => $this->l('Layout'),
            
            'thumb_size' => $this->l('Thumbnail size'),
            'img_size' => $this->l('Large image size'),
            
            'vertical_height' => $this->l('Slider height'),
            'thumb_width' => $this->l('Thumbnails width'),
            
            'controls' => $this->l('Controls'),
            
            'thumb_controls' => $this->l('Controls for thumbnails'),
            'thumb_item' => $this->l('Thumbnail items'),
            'all_images' => $this->l('Display all images instead of current combination images'),
            'thumb_controls_offset' => $this->l('Scroll items per click'),
            
            'gallery' => $this->l('Thumbnails'),
            
            'control_bg' => $this->l('Controls background color'),
            'control_color' => $this->l('Controls color'),
            'control_hbg' => $this->l('Controls hover background color'),
            'control_hcolor' => $this->l('Controls hover color'),

            'thumb_border' => $this->l('Thumbnails border width, px'),
            'thumb_border_color' => $this->l('Thumbnails border color'),

            'active_thumb_border' => $this->l('Active thumbnail border width, px'),
            'active_thumb_border_color' => $this->l('Active thumbnail border color')
        );
    }
    
    public function getFormTitle()
    {
        return $this->l('Slider settings');
    }
}
