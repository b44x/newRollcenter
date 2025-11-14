<?php
/**
* This file will override class ProductController. Do not modify this file if you want to upgrade the module in future
* 
* 
*  @author    LeoTheme <leotheme@gmail.com>
*  @copyright LeoTheme
*  @license   http://leotheme.com - prestashop template provider
*/
class ProductController extends ProductControllerCore
{
    /*
    * module: leofeature
    * date: 2025-03-27 14:42:27
    * version: 2.2.2
    */
    public function initContent()
    {
        if(Module::isInstalled('leofeature') && Module::isEnabled('leofeature'))
        {
            require_once(_PS_MODULE_DIR_ . 'leofeature/classes/ProductReview.php');
            $leofeature = Module::getInstanceByName('leofeature');
            if (Configuration::get('LEOFEATURE_ENABLE_PRODUCT_REVIEWS')) {
                $id_product = $this->product->id;
                $averageRating = ProductReview::getAverageGrade((int) $id_product);
                
                $this->product->productComments = [
                    'averageRating' => $averageRating['grade']?$averageRating['grade']:Configuration::get('LEOFEATURE_PRODUCT_REVIEWS_DREVIEW'),
                    'nbComments' => (int) (ProductReview::getReviewNumber((int) $id_product))?(int) (ProductReview::getReviewNumber((int) $id_product)):Configuration::get('LEOFEATURE_PRODUCT_REVIEWS_DNREVIEW'),
                ];
            }
        }
        parent::initContent();
    }
}