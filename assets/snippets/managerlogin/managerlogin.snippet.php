<?php
define(MANAGER_LOGIN_PATH, MODX_BASE_PATH . 'assets/snippets/managerlogin/');

include MANAGER_LOGIN_PATH . 'inc/managerlogin.inc.php'

$loginTpl = isset($loginTpl) ? managerLoginTemplate($loginTpl) : '';
$loggedTpl = isset($loggedTpl) ? managerLoginTemplate($loggedTpl) : '';
$errorTpl = isset($errorTpl) ? managerLoginTemplate($errorTpl) : '';
$cssStyle = isset($cssStyle) ? $cssStyle : '';
$language = isset($language) ? $language : '';
return managerLogin($loginTpl, $loggedTpl, $errorTpl, $cssStyle, $language);
?>
