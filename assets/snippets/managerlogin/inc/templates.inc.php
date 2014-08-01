<?php
// Error template
$defaultErrorTpl = '<p class="error">[+ml.erroritem+]</p>';

// Login template
$defaultLoginTpl = '<div id="login_form">' . "\n"
    . '[+ml.errors+]' . "\n"
    . '<form method="post" action="[~[*id*]~]">' . "\n"
    . '  <input type="hidden" name="[+ml.action_name+]" value="[+ml.action_val+]" />' . "\n"
    . '  <div class="field_box">' . "\n"
    . '    <label id="username_label" for="username">[+ml.user_lbl+]</label>' . "\n"
    . '    <input id="username" class="text" type="text" name="[+ml.user_fldname+]" value="[+ml.username+]" />' . "\n"
    . '  </div>' . "\n"
    . '  <div class="field_box">' . "\n"
    . '    <label id="password_label" for="password">[+ml.passwd_lbl+]</label>' . "\n"
    . '    <input id="password" class="password" type="password" name="[+ml.passwd_fldname+]" value="" />' . "\n"
    . '  </div>' . "\n"
    . '  <div class="field_box">' . "\n"
    . '    <input id="rememberme" class="checkbox" type="checkbox" name="[+ml.remember_fldname+]" value="1" />' . "\n"
    . '    <label id="rememberme_label" for="rememberme">[+ml.remember_lbl+]</label>' . "\n"
    . '  </div>' . "\n"
    . '  <div class="submit_box">' . "\n"
    . '    <button type="submit">[+ml.login_lbl+]</button>' . "\n"
    . '  </div>' . "\n"
    . '</form>' . "\n"
    . '</div>' . "\n";
$defaultLoginCss = '<style type="text/css">' . "\n"
    . '#login_form {' . "\n"
    . '  width:150px;' . "\n"
    . '}' . "\n"
    . '#login_form label {' . "\n"
    . '  display:block;' . "\n"
    . '  font-weight:bold;' . "\n"
    . '}' . "\n"
    . '#login_form input.text, #login_form input.password {' . "\n"
    . '  width:150px;' . "\n"
    . '}' . "\n"
    . '#login_form input.checkbox {' . "\n"
    . '  float:left;' . "\n"
    . '}' . "\n"
    . '#login_form div.field_box {' . "\n"
    . '  margin:10px 0;' . "\n"
    . '}' . "\n"
    . '#login_form div.submit_box {' . "\n"
    . '  text-align:right;' . "\n"
    . '}' . "\n"
    . '#login_form p.error {' . "\n"
    . '  text-align:center;' . "\n"
    . '  color:#950000;' . "\n"
    . '  font-weight:bold;' . "\n"
    . '}' . "\n"
    . '</style>' . "\n";

// Logged template
$defaultLoggedTpl = '<p id="logged_in">' . "\n"
    . '<strong>[+ml.logged_msg+]</strong><br />' . "\n"
    . '<a href="[~[*id*]~]&amp;[+ml.action_name+]=[+ml.action_val+]">[+ml.logout_lbl+]</a>'
    . ' | <a href="[~[(site_start)]~]">[+ml.home_lbl+]</a>' . "\n"
    . '</p>' . "\n";
$defaultLoggedCss = '<style type="text/css">' . "\n"
    . '#logged_in {' . "\n"
    . '  text-align:center;' . "\n"
    . '}' . "\n"
    . '</style>' . "\n";
?>
