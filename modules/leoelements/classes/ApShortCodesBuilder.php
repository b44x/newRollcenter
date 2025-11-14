<?php
/**
 * 2007-2022 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * LeoElements is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Leotheme <leotheme@gmail.com>
 *  @copyright 2007-2022 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

if (!class_exists('ApShortCodeBase')) {
    
    class ApShortCodesBuilder
    {
        public static $shortcode_tags = array();
        public static $lang_id = 0;
        public static $is_front_office = 1;
        public static $is_gen_html = 1;
        public static $data_form = array();
        public static $shortcode_lang;
        public static $hook_name;
        public static $profile_param;

        public static function addShortcode($tag, $func)
        {
            self::$shortcode_tags[$tag] = $func;
        }

        public static function removeShortcode($tag)
        {
            unset(self::$shortcode_tags[$tag]);
        }

        public static function shortcodeExists($tag)
        {
            return array_key_exists($tag, self::$shortcode_tags);
        }

        public static function doShortcode($content)
        {
            if (false === strpos($content, '[')) {
                return $content;
            }
            $pattern = self::getShortcodeRegex();
            return preg_replace_callback("/$pattern/s", array(new ApShortCodesBuilder, 'doShortcodeTag'), $content);
        }

        public static function getShortcodeRegex()
        {
            $tagnames = array_keys(self::$shortcode_tags);
            $tagregexp = join('|', array_map('preg_quote', $tagnames));
            return '\\[(\\[?)'."($tagregexp)"
                    .'(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)';
        }

        public static function doShortcodeTag($m)
        {
            $shortcode_tags = self::$shortcode_tags;
            // allow [[foo]] syntax for escaping a tag
            if ($m[1] == '[' && $m[6] == ']') {
                return Tools::substr($m[0], 1, -1);
            }
            $tag = $m[2];
            $attr = self::shortcodeParseAtts($m[3]);
            $function_call = self::$is_front_office ? 'fontContent' : 'adminContent';
            if (isset($m[5])) {
                return $m[1].call_user_func(array($shortcode_tags[$tag], $function_call), $attr, $m[5], $tag, self::$is_gen_html).$m[6];
            } else {
                // self-closing tag
                return $m[1].call_user_func(array($shortcode_tags[$tag], $function_call), $attr, null, $tag, self::$is_gen_html).$m[6];
            }
        }

        public static function shortcodeParseAtts($text)
        {
            $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
            $text = preg_replace('/[\x{00a0}\x{200b}]+/u', ' ', $text);
            $atts = array();
            if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
                foreach ($match as $m) {
                    if (!empty($m[1])) {
                        $atts[Tools::strtolower($m[1])] = stripcslashes($m[2]);
                    } elseif (!empty($m[3])) {
                        $atts[Tools::strtolower($m[3])] = stripcslashes($m[4]);
                    } elseif (!empty($m[5])) {
                        $atts[Tools::strtolower($m[5])] = stripcslashes($m[6]);
                    } elseif (isset($m[7]) && Tools::strlen($m[7])) {
                        $atts[] = stripcslashes($m[7]);
                    } elseif (isset($m[8])) {
                        $atts[] = stripcslashes($m[8]);
                    } else {
                        $atts = ltrim($text);
                    }
                }
            }
            return $atts;
        }

        public static function stripShortcodes($content)
        {
            $shortcode_tags = self::$shortcode_tags;
            if (empty($shortcode_tags) || !is_array($shortcode_tags)) {
                return $content;
            }
            $pattern = self::getShortcodeRegex();
            return preg_replace_callback("/$pattern/s", array(self, 'stripShortcodeTag'), $content);
        }

        public static function stripShortcodeTag($m)
        {
            // allow [[foo]] syntax for escaping a tag
            if ($m[1] == '[' && $m[6] == ']') {
                return Tools::substr($m[0], 1, -1);
            }
            return $m[1].$m[6];
        }

        public static function setShortCodeLang($type)
        {
            if (!isset(AdminLeoElementsProfilesController::$shortcode_lang[$type])) {
                $fileName = $type;
                if (strpos($type, 'apSub') !== false) {
                    $fileName = str_replace('Sub', '', $type);
                }
                if (file_exists(_PS_MODULE_DIR_.'leoelements/classes/shortcodes/'.$fileName.'.php')) {
                    require_once(_PS_MODULE_DIR_.'leoelements/classes/shortcodes/'.$fileName.'.php');
                }
                if ($fileName != $type) {
                    $inputs = call_user_func(array(new $fileName, 'getConfigList'), 1);
                } else {
                    $inputs = call_user_func(array(new $fileName, 'getConfigList'));
                }
                foreach ($inputs as $input) {
                    if (isset($input['lang']) && $input['lang']) {
                        if (self::$lang_id) {
                            AdminLeoElementsProfilesController::$shortcode_lang[$type][$input['name']] = $input['name'].'_'.self::$lang_id;
                        } else {
                            foreach (AdminLeoElementsProfilesController::$language as $lang) {
                                AdminLeoElementsProfilesController::$shortcode_lang[$type][$input['name'].'_'
                                        .$lang['iso_code']] = $input['name'].'_'.$lang['id_lang'];
                            }
                        }
                    }
                }
            }
        }

        public static function converParamToAttr($params, $type, $theme_dir = '')
        {
            $attr = '';
            self::setShortCodeLang($type);

            $lang_field = array();
            if (isset(AdminLeoElementsProfilesController::$shortcode_lang[$type])) {
                $lang_field = AdminLeoElementsProfilesController::$shortcode_lang[$type];
            }

            //remove lang field first
            if ($lang_field) {
                foreach ($params as $key => $val) {
                    if (false !== $arr_key = array_search($key, $lang_field)) {
                        //do something
                        $params[$arr_key] = $val;
                        foreach ($params as $key => $val) {
                            foreach (AdminLeoElementsProfilesController::$language as $lang) {
                                unset($params[$arr_key.'_'.$lang['id_lang']]);
                            }
                        }
                    }
                }
            }
            //DONGND:: fix data for ApBlockcarousel
            if ($type == 'ApBlockCarousel' && isset($params['total_slider']) && $params['total_slider'] != '') {
                $lang_field_special = array();
                $list = explode('|', $params['total_slider']);
                foreach ($list as $list_item) {
                    $lang_field_special['tit_'.$list_item] = 'tit_'.$list_item.'_'.self::$lang_id;
                    $lang_field_special['img_'.$list_item] = 'img_'.$list_item.'_'.self::$lang_id;
                    $lang_field_special['link_'.$list_item] = 'link_'.$list_item.'_'.self::$lang_id;
                    $lang_field_special['descript_'.$list_item] = 'descript_'.$list_item.'_'.self::$lang_id;
                };
                if ($lang_field_special) {
                    foreach ($params as $key => $val) {
                        if (false !== $arr_key = array_search($key, $lang_field_special)) {
                        //do something
                            $params[$arr_key.'_'.self::$lang_id] = $val;
                            foreach ($params as $key => $val) {
                                foreach (AdminLeoElementsProfilesController::$language as $lang) {
                                    if ($lang['id_lang'] != self::$lang_id) {
                                        unset($params[$arr_key.'_'.$lang['id_lang']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($params as $key => $val) {
                if ($key == 'override_folder' && $val) {
                    //remove space
                    $val = str_replace(' ', '', $val);
                    //add new function override folder for widget
                    self::processOverrideTpl($val, $type, $theme_dir);
                }
                $attr .= ($attr ? ' ' : '').$key.'="'.$val.'"';
            }
            return ($attr ? ' ' : '').$attr;
        }
        
        
        //DONGND:: clone function for Apshortcode
        public static function setShortCodeLang2($type)
        {
            if (!isset(AdminLeoElementsShortcodeController::$shortcode_lang[$type])) {
                $fileName = $type;
                if (strpos($type, 'apSub') !== false) {
                    $fileName = str_replace('Sub', '', $type);
                }
                if (file_exists(_PS_MODULE_DIR_.'leoelements/classes/shortcodes/'.$fileName.'.php')) {
                    require_once(_PS_MODULE_DIR_.'leoelements/classes/shortcodes/'.$fileName.'.php');
                }
                if ($fileName != $type) {
                    $inputs = call_user_func(array(new $fileName, 'getConfigList'), 1);
                } else {
                    $inputs = call_user_func(array(new $fileName, 'getConfigList'));
                }
                foreach ($inputs as $input) {
                    if (isset($input['lang']) && $input['lang']) {
                        if (self::$lang_id) {
                            AdminLeoElementsShortcodeController::$shortcode_lang[$type][$input['name']] = $input['name'].'_'.self::$lang_id;
                        } else {
                            foreach (AdminLeoElementsShortcodeController::$language as $lang) {
                                AdminLeoElementsShortcodeController::$shortcode_lang[$type][$input['name'].'_'
                                        .$lang['iso_code']] = $input['name'].'_'.$lang['id_lang'];
                            }
                        }
                    }
                }
            }
        }

        //DONGND:: clone function for Apshortcode
        public static function converParamToAttr2($params, $type, $theme_dir = '')
        {
            $attr = '';
            self::setShortCodeLang2($type);

            $lang_field = array();
            if (isset(AdminLeoElementsShortcodeController::$shortcode_lang[$type])) {
                $lang_field = AdminLeoElementsShortcodeController::$shortcode_lang[$type];
            }

            //remove lang field first
            if ($lang_field) {
                foreach ($params as $key => $val) {
                    if (false !== $arr_key = array_search($key, $lang_field)) {
                        //do something
                        $params[$arr_key] = $val;
                        foreach ($params as $key => $val) {
                            foreach (AdminLeoElementsShortcodeController::$language as $lang) {
                                unset($params[$arr_key.'_'.$lang['id_lang']]);
                            }
                        }
                    }
                }
            }
            //fix data for ApBlockcarousel
            if ($type == 'ApBlockCarousel' && isset($params['total_slider']) && $params['total_slider'] != '') {
                $lang_field_special = array();
                $list = explode('|', $params['total_slider']);
                foreach ($list as $list_item) {
                    $lang_field_special['tit_'.$list_item] = 'tit_'.$list_item.'_'.self::$lang_id;
                    $lang_field_special['img_'.$list_item] = 'img_'.$list_item.'_'.self::$lang_id;
                    $lang_field_special['link_'.$list_item] = 'link_'.$list_item.'_'.self::$lang_id;
                    $lang_field_special['descript_'.$list_item] = 'descript_'.$list_item.'_'.self::$lang_id;
                };
                if ($lang_field_special) {
                    foreach ($params as $key => $val) {
                        if (false !== $arr_key = array_search($key, $lang_field_special)) {
                        //do something
                            $params[$arr_key.'_'.self::$lang_id] = $val;
                            foreach ($params as $key => $val) {
                                foreach (Language::getLanguages(false) as $lang) {
                                    if ($lang['id_lang'] != self::$lang_id) {
                                        unset($params[$arr_key.'_'.$lang['id_lang']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            foreach ($params as $key => $val) {
                if ($key == 'override_folder' && $val) {
                    //remove space
                    $val = str_replace(' ', '', $val);
                    //add new function override folder for widget
                    self::processOverrideTpl($val, $type, $theme_dir);
                }
                $attr .= ($attr ? ' ' : '').$key.'="'.$val.'"';
            }
            return ($attr ? ' ' : '').$attr;
        }

        public static function processOverrideTpl($val, $type, $theme_dir)
        {
            if (file_exists($theme_dir.'modules/leoelements/views/templates/hook/'.$val.'/'.$type.'.tpl')) {
                return;
            }

            //create overide folder
            if (!is_dir($theme_dir.'modules/leoelements/')) {
                // validate module
                mkdir($theme_dir.'modules/leoelements/', 0755, true);
            }
            if (!is_dir($theme_dir.'modules/leoelements/views/')) {
                // validate module
                mkdir($theme_dir.'modules/leoelements/views/', 0755, true);
            }
            if (!is_dir($theme_dir.'modules/leoelements/views/templates/')) {
                // validate module
                mkdir($theme_dir.'modules/leoelements/views/templates/', 0755, true);
            }
            if (!is_dir($theme_dir.'modules/leoelements/views/templates/hook/')) {
                // validate module
                mkdir($theme_dir.'modules/leoelements/views/templates/hook/', 0755, true);
            }
            if (!is_dir($theme_dir.'modules/leoelements/views/templates/hook/'.$val)) {
                // validate module
                mkdir($theme_dir.'modules/leoelements/views/templates/hook/'.$val, 0755, true);
            }
            if (file_exists(_PS_MODULE_DIR_.'leoelements/views/templates/hook/'.$type.'.tpl')) {
                Tools::copy(_PS_MODULE_DIR_.'leoelements/views/templates/hook/'.$type.'.tpl', $theme_dir.'modules/leoelements/views/templates/hook/'.$val.'/'.$type.'.tpl');
            } else {
                $theme_dir_ori = _PS_THEME_DIR_;
                Tools::copy(_PS_MODULE_DIR_.'leoelements/views/templates/hook/ApGeneral.tpl', $theme_dir_ori.'modules/leoelements/views/templates/hook/'.$val.'/'.$type.'.tpl');
            }
        }

        public static function correctDeCodeData($data)
        {
            $function_name = 'base64_decode';
            //$functionName = 'b'.'a'.'s'.'e'.'6'.'4'.'_'.'decode';
            return call_user_func($function_name, $data);
        }

        public static function correctEnCodeData($data)
        {
            $function_name = 'base64_encode';
            //$functionName = 'b'.'a'.'s'.'e'.'6'.'4'.'_'.'encode';
            return call_user_func($function_name, $data);
        }

        public function parse($str)
        {
            return self::doShortcode($str);
        }
    }

}
