<?php

if (!function_exists('IsModulesInPath')) {
    function IsModulesInPath($dirPath)
    {
        $explode = explode(DIRECTORY_SEPARATOR, $dirPath);
        return in_array('modules', $explode);
    }
}

if (!function_exists('getModulePath')) {
    function getModulePath($filepath_)
    {
        static $cache;
        if ($cache === null) {
            $cache = array();
        }

        if (!isset($cache[$filepath_])) {
            $filepath = findRealFilePath($filepath_);

            if (Tools::getValue('controller') &&
                stripos(Tools::getValue('controller'), 'PShow') !== false) {
                $controller = strtolower(Tools::getValue('controller'));
                $bestpath = false;
                for ($i = 0; $i <= strlen($controller); ++$i) {
                    $tmp = _PS_MODULE_DIR_ . substr($controller, 0, $i);
                    $bestpath = is_dir($tmp) ? $tmp : $bestpath;
                }
                $cache[$filepath_] = $bestpath . DIRECTORY_SEPARATOR;
                return $cache[$filepath_];
            }

            if (Tools::substr($filepath, -1, 1) == DIRECTORY_SEPARATOR) {
                $filepath = Tools::substr($filepath, 0, Tools::strlen($filepath) - 1);
            }

            $explode = explode(DIRECTORY_SEPARATOR, dirname($filepath));
            $stay = array_search('modules', $explode) + 1;
            if (!array_key_exists($stay, $explode)) {
                $cache[$filepath_] = $filepath . DIRECTORY_SEPARATOR;
                return $cache[$filepath_];
            }

            $newpath_ = array();
            for ($i = 0; $i <= $stay; ++$i) {
                $newpath_[] = $explode[$i];
            }
            $newpath = implode(DIRECTORY_SEPARATOR, $newpath_);

            $cache[$filepath_] = $newpath . DIRECTORY_SEPARATOR;
        }

        return $cache[$filepath_];
    }
}

if (!function_exists('findRealFilePath')) {
    function findRealFilePath($filepath)
    {
        $backtrace = debug_backtrace(0, 20);

        foreach ($backtrace as $call) {
            if (!array_key_exists('file', $call)) {
                continue;
            }
            if (stripos($call['file'], DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR) === false &&
                stripos($call['file'], DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) === false &&
                stripos($call['file'], DIRECTORY_SEPARATOR . 'config.php') === false &&
                stripos($call['file'], DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR) !== false) {
                return $call['file'];
            }
        }

        // this should not happen !
        return $filepath;
    }
}

if (!function_exists('getModuleName')) {
    function getModuleName($filepath_)
    {
        $module_path = getModulePath($filepath_);
        $module_path_arr = explode(DIRECTORY_SEPARATOR, $module_path);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && count($module_path_arr) <= 1) {
            $module_path_arr = explode(DIRECTORY_SEPARATOR, $module_path_arr[count($module_path_arr) - 1]);
        }

        return $module_path_arr[count($module_path_arr) - 2];
    }
}