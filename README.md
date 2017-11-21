# THIS PROJECT IS DEPRECATED

ManagerLogin is not maintained anymore. It maybe does not work in Evolution 1.1 anymore. Please fork it and bring it back to life, if you need it.

# ManagerLogin

Login to the manager through the frontend.

Author: Adam Crownoble (contact: adam@obledesign.com)

Added template and language parameters by enidan (contact: lariflakneur@gmail.com)

Version: 2.0.1

## Parameters (all optional):
 
Name | Description | Default
---- | ----------- | -------
language | String defining which language file to use for default templates and error messages. | `en`
loginTpl | Template to use when no user is logged within the manager. Can be either a chunk name, a document ID or HTML code. | `$defaultLoginTpl` in `templates.inc.php`
loggedTpl | Template to use when a user is logged within the manager. Can be either a chunk name, a document ID or HTML code. | `$defaultLoggedTpl` in `templates.inc.php`
errorTpl | Template to use for each error message to display. Can be either a chunk name or a document ID or directly HTML code. | `$defaultErrorTpl` in `templates.inc.php`
cssStyle | CSS style to add within the document <head> tag. Can be either a filename or directly CSS code inside a style tag. | -

Warning: When using directly HTML or CSS code for the parameters, it may not work because of MODx parser behaviour. For example, '=' is not supported.

## Placeholders

See default templates in templates.inc.php file for example usage.

Placeholder | Description
----------- | -----------
[+ml.erroritem+] | Used in `&errorTpl` error message text.
[+ml.errors+] | Used in `&loginTpl` templated error messages.
[+ml.user_fldname+] | Used in `&loginTpl` name of username form field.
[+ml.user_lbl+] | Used in `&loginTpl` text label for username form field.
[+ml.passwd_fldname+] | Used in `&loginTpl` name of password form field.
[+ml.passwd_lbl+] | Used in `&loginTpl` text label for password form field.
[+ml.remember_fldname+] | Used in `&loginTpl` name of rememberme form checkbox field.
[+ml.remember_lbl+] | Used in `&loginTpl` label for rememberme form checkbox field.
[+ml.login_lbl+] | Used in `&loginTpl` text for login button.
[+ml.logged_msg+] | Used in `&loggedTpl` message displayed when a user is logged.
[+ml.logout_lbl+] | Used in `&loggedTpl` text for logout link.
[+ml.home_lbl+] | Used in `&loggedTpl` text for homepage link.
[+ml.username+] | Used in `&loginTpl` and `&loggedTpl` user name
[+ml.action_name+] | Used in `&loginTpl` and `&loggedTpl` name of action parameter (hidden form field or URL argument).
[+ml.action_val+] | Used in `&loginTpl` and `&loggedTpl` value of action parameter (hidden form field or URL argument).
