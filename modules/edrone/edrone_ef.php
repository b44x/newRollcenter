<?php
/**
 * @author    Edrone sp. z o.o <hello@edrone.me>
 * @copyright Edrone sp. z o.o
 * @license   https://edrone.me/integration-license/
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

define("NOTIFICATION_FILE_FOLDER",dirname(__FILE__).DIRECTORY_SEPARATOR.'tmp');
if(!file_exists(NOTIFICATION_FILE_FOLDER)){
    mkdir(NOTIFICATION_FILE_FOLDER);
}
define("NOTIFICATION_FILE_PATH",NOTIFICATION_FILE_FOLDER.DIRECTORY_SEPARATOR);

class NotificationTemp {

    public static function getNotification($userid){
        if(file_exists(NOTIFICATION_FILE_PATH.$userid.'.inc')){
            $data = @file(NOTIFICATION_FILE_PATH.$userid.'.inc');
            @unlink(NOTIFICATION_FILE_PATH.$userid.'.inc');
            return $data;
        }
        return false;
    }

    public static function genJsNotification($array){
        $tmp = '';
        foreach($array as $data){
            list($param_name, $param_value) = explode(':', $data, 2);
            $tmp .= '<script type="text/javascript">_edrone.'.trim($param_name).'="'.(trim($param_value)).'"</script>'.PHP_EOL;
        }
        return $tmp;
    }

    public static function setNotification($array,$userid){
        foreach($array as $key=>$value){
           $array[$key] = urlencode(trim($value));
        }
        file_put_contents(NOTIFICATION_FILE_PATH.$userid.'.inc', json_encode($array));
    }

}
