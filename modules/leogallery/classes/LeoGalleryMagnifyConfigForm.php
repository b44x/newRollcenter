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

class LeoGalleryMagnifyConfigForm extends LeoGalleryModel
{
    public $enable;
    public $type;
    public $width;
    public $height;
    public $fader;
    public $fader_opacity;
    
    public function attributeTypes()
    {
        return array(
            'enable' => 'switch',
            'type' => 'select',
            'width' => 'text',
            'height' => 'text',
            'fader' => 'switch',
            'fader_opacity' => 'text'
        );
    }
    
    public function attributeDefaults()
    {
        return array(
            'enable' => 0,
            'type' => 'lens',
            'width' => 200,
            'height' => 200,
            'fader' => 0,
            'fader_opacity' => 20
        );
    }
    
    public function rules()
    {
        return array(
            array(
                array(
                    'enable',
                    'fader',
                    'type'
                ), 'safe'
            ),
            array(
                array(
                    'width',
                    'height',
                    'fader_opacity'
                ), 'isInt'
            )
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'enable' => $this->l('Enable magnifier'),
            'type' => $this->l('Magnifier type'),
            'width' => $this->l('Magnifier width, px'),
            'height' => $this->l('Magnifier height, px'),
            'fader' => $this->l('Fade image on hover'),
            'fader_opacity' => $this->l('Fader opacity, %')
        );
    }
    
    public function typeSelectOptions()
    {
        return array(
            array(
                'id' => 'lens',
                'name' => $this->l('Lens')
            ),
            array(
                'id' => 'out',
                'name' => $this->l('Out-of-image zoom')
            ),
            array(
                'id' => 'in',
                'name' => $this->l('Inner-image zoom')
            )
        );
    }
    
    public function attributeDescriptions()
    {
        return array(
            'fader_opacity' => $this->l('From 0 to 100')
        );
    }
    
    public function getFormTitle()
    {
        return $this->l('Magnier settings');
    }
}
