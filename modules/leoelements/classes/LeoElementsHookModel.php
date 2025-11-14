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

require_once(_PS_MODULE_DIR_.'leoelements/classes/LeoElementsProfilesModel.php');

class LeoElementsHookModel
{
    public $profile_data;
    public $profile_param;
    public $hook;

    public function create()
    {
        $this->profile_data = LeoElementsProfilesModel::getActiveProfile('index', 'model');
        $this->profile_param = json_decode($this->profile_data['params'], true);
        $this->fullwidth_index_hook = $this->fullwidthIndexHook();
        $this->fullwidth_other_hook = $this->fullwidthOtherHook();
        return $this;
    }

    public function fullwidthIndexHook()
    {
        return isset($this->profile_param['fullwidth_index_hook']) ? $this->profile_param['fullwidth_index_hook'] : LeoECSetting::getIndexHook(3);
    }

    public function fullwidthOtherHook()
    {
        return isset($this->profile_param['fullwidth_other_hook']) ? $this->profile_param['fullwidth_other_hook'] : LeoECSetting::getOtherHook(3);
    }

    public function fullwidthHook($hook_name, $page)
    {
        if ($page == 'index') {
            // validate module
            return isset($this->fullwidth_index_hook[$hook_name]) ? $this->fullwidth_index_hook[$hook_name] : 0;
        } else {
            # other page
            return isset($this->fullwidth_other_hook[$hook_name]) ? $this->fullwidth_other_hook[$hook_name] : 0;
        }
    }
}
