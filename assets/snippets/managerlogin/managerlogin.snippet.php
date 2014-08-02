<?php
define('MANAGER_LOGIN_PATH', str_replace(MODX_BASE_PATH, '', str_replace('\\', '/', realpath(dirname(__FILE__)))) . '/');
define('MANAGER_LOGIN_BASE_PATH', MODX_BASE_PATH . MTV_PATH);

include MANAGER_LOGIN_BASE_PATH . 'inc/managerlogin.inc.php'

$loginTpl = isset($loginTpl) ? managerLoginTemplate($loginTpl) : '';
$loggedTpl = isset($loggedTpl) ? managerLoginTemplate($loggedTpl) : '';
$errorTpl = isset($errorTpl) ? managerLoginTemplate($errorTpl) : '';
$cssStyle = isset($cssStyle) ? $cssStyle : '';
$language = isset($language) ? $language : '';
return managerLogin($loginTpl, $loggedTpl, $errorTpl, $cssStyle, $language);
?>
