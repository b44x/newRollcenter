
function smarty_block_ifConsentGranted_init($params, $content, $smarty, &$repeat)
{
    if (function_exists('smarty_block_ifConsentGranted')) {
        return smarty_block_ifConsentGranted($params, $content, $smarty, $repeat);
    }
    return $content;
}
smartyRegisterFunction($smarty, 'block', 'ifConsentGranted', 'smarty_block_ifConsentGranted_init');
