<?php
if (!function_exists('managerLogin')) {

    /*========================================================================*
     * Function: managerLogin
     *========================================================================*
     * Get template's code.
     * Parameters:
     *   - $tpl [string]
     *     Chunk name or document ID or HTML code
     * Returns:
     *   Snippet output
     *========================================================================*/
    function managerLogin($loginTpl, $loggedTpl, $errorTpl, $cssStyle, $language)
    {
        global $modx;

        ////////////////////////////////////////////////////////////////////////
        // Parameters management
        ////////////////////////////////////////////////////////////////////////

        // Language
        $defaultLang = 'en';
        $_lang = '';
        include MANAGER_LOGIN_BASE_PATH . 'lang/' . $defaultLang . '.inc.php';
        if (!empty($language) && ($language != $defaultLang)) {
            $langFile = MANAGER_LOGIN_BASE_PATH . 'lang/' . $language . '.inc.php';
            if (file_exists($langFile)) {
                include $langFile;
            }
        }

        // Templates
        if (empty($loginTpl)) {
            $loginTpl = file_get_contents(MANAGER_LOGIN_BASE_PATH . 'templates/defaultLogin.template.html');
            $modx->regClientCSS(file_get_contents(MANAGER_LOGIN_BASE_PATH . 'templates/defaultLogin.style.html'));
        }
        if (empty($loggedTpl)) {
            $loggedTpl = file_get_contents(MANAGER_LOGIN_BASE_PATH . 'templates/defaultLogged.template.html');
            $modx->regClientCSS(file_get_contents(MANAGER_LOGIN_BASE_PATH . 'templates/defaultLogged.style.html'));
        }
        if (empty($errorTpl)) {
            $loginTpl = file_get_contents(MANAGER_LOGIN_BASE_PATH . 'templates/defaultError.template.html');
        }

        ////////////////////////////////////////////////////////////////////////
        // Processing
        ////////////////////////////////////////////////////////////////////////

        $errors = array();

        $db = $modx->dbConfig['dbase'];
        $pre = $modx->dbConfig['table_prefix'];
        $max_attempts = 3;

        if (!($action = $_POST['action'])) {
            $action = $_GET['action'];
        }
        $username = $_POST['username'];
        $form_password = $_POST['password'];
        $rememberme = $_POST['rememberme'];

        $modx->db->connect();

        switch ($action) {

            case 'login':
                session_start();

                // invoke OnBeforeManagerLogin event
                $modx->invokeEvent("OnBeforeManagerLogin", array(
                        "username" => $username,
                        "userpassword" => $form_password,
                        "rememberme" => $rememberme)
                );

                $sql = "SELECT ATT.*, USR.*
                        FROM $db.{$pre}user_attributes ATT
                        INNER JOIN $db.{$pre}manager_users USR ON ATT.internalKey = USR.id
                        WHERE username = '$username';";
                $result = $modx->db->query($sql);

                // Was blocked, not anymore
                if ($usr_failedlogins >= $max_attemts && $usr_blockeduntil < time()) {
                    $sql = "UPDATE $db.{$pre}user_attributes
                            SET failedlogincount = '0',
                                blockeduntil = '" . (time() - 1) . "'
                            WHERE internalKey = '$usr_internalKey';";
                    $modx->db->query($sql);
                }

                // Username exists?
                if ($modx->db->getRecordCount($result) == 1) {
                    extract($modx->db->getRow($result), EXTR_PREFIX_ALL, 'usr');
                } else {
                    $errors[] = $_lang['err_user_notfound'];
                }

                // Still blocked?
                if ($usr_failedlogins >= $max_attempts && $usr_blockeduntil > time()) {
                    $errors[] = $_lang['err_user_failedlogins'];
                }

                // Blocked?
                if ($usr_blocked == '1') {
                    $errors[] = $_lang['err_user_blocked'];
                }

                // Still blocked?
                if ($usr_blockeduntil > time()) {
                    $errors[] = $_lang['err_user_blockeduntil'];
                }

                // Account expired?
                if ($usr_blockedafterd > 0 && $usr_blockedafter < time()) {
                    $errors[] = $_lang['err_user_blockedafter'];
                }

                // IP allowed?
                if ($allowed_ip && strpos($usr_allowed_ip, $_SERVER['REMOTE_ADDR']) === false) {
                    $errors[] = $_lang['err_user_ipnotallowed'];
                }

                // Weekday allowed?
                $today = getdate();
                if ($allowed_days && strpos($allowed_days, $today['wday'] + 1) === false) {
                    $errors[] = $_lang['err_user_daynotallowed'];
                }

                // invoke OnManagerAuthentication event
                $rt = $modx->invokeEvent("OnManagerAuthentication", array(
                        "userid" => $usr_internalKey,
                        "username" => $usr_username,
                        "userpassword" => $form_password,
                        "savedpassword" => $usr_password,
                        "rememberme" => $rememberme)
                );

                // check if plugin authenticated the user
                if (!$rt || (is_array($rt) && !in_array(true, $rt))) {
                    // Passwords match?
                    // Don't check unless there are no errors so far.
                    // Otherwise blocked users will still be able to check for valid passwords.
                    if (!$errors) {
                        $modx->loadExtension("ManagerAPI");
                        if ($usr_password != $modx->manager->genHash($form_password, $usr_internalKey)) {
                            $errors[] = $_lang['err_invalid_passwd'];
                        }
                    }
                }

                // If there were errors clear the session data
                if ($errors) {
                    session_destroy();
                    session_unset();
                } else {
                    // Otherwise set the session data
                    $_SESSION['usertype'] = 'manager';
                    $_SESSION['mgrShortname'] = $usr_username;
                    $_SESSION['mgrFullname'] = $usr_fullname;
                    $_SESSION['mgrEmail'] = $usr_email;
                    $_SESSION['mgrValidated'] = 1;
                    $_SESSION['mgrInternalKey'] = $usr_internalKey;
                    $_SESSION['mgrFailedlogins'] = $usr_failedlogins;
                    $_SESSION['mgrLastlogin'] = $usr_lastlogin;
                    $_SESSION['mgrLogincount'] = $usr_nrlogins;
                    $_SESSION['mgrRole'] = $usr_role;

                    // Role permissions
                    $sql = "SELECT * FROM $db.{$pre}user_roles where id=$usr_role;";
                    $result = $modx->db->query($sql);
                    $_SESSION['mgrPermissions'] = $modx->db->getRow($result);

                    // Document Group permissions
                    $groups = '';
                    $i = 0;
                    $sql = "SELECT access.documentgroup
                            FROM $db.{$pre}member_groups groups
                            INNER JOIN $db.{$pre}membergroup_access access ON access.membergroup = groups.user_group
                            WHERE groups.member = $usr_internalKey";
                    $result = $modx->db->query($sql);
                    while ($row = $modx->db->getRow($result)) {
                        $groups[$i++] = $row['documentgroup'];
                    }
                    $_SESSION['mgrDocgroups'] = $groups;
                }

                // invoke OnManagerLogin event
                $modx->invokeEvent("OnManagerLogin", array("userid" => $internalKey,
                        "username" => $username,
                        "userpassword" => $givenPassword,
                        "rememberme" => $rememberme)
                );

                if ($_SESSION['mgrValidated']) {
                    // check if we should redirect user to a web page
                    $tbl = $modx->getFullTableName('user_settings');
                    $id = $modx->db->getValue("SELECT setting_value FROM {$tbl}
                                               WHERE user = '{$usr_internalKey}'
                                               AND setting_name = 'manager_login_startup';");
                    if (isset($id) && $id > 0) {
                        $url = $modx->makeUrl($id, '', '', 'full');
                        $modx->sendRedirect($url);
                    }
                }

                break;

            case 'logout':
                $usr_internalKey = $modx->getLoginUserID();
                $username = $_SESSION['mgrShortname'];
                $_SESSION = array();
                if (isset($_COOKIE[session_name()])) {
                    setcookie(session_name(), '', time() - 42000, '/');
                }
                @session_destroy();
                $sessionID = md5(date('d-m-Y H:i:s'));
                session_id($sessionID);
                session_start();
                session_destroy();
                break;

            default:
                break;
        }

        ////////////////////////////////////////////////////////////////////////
        // Output
        ////////////////////////////////////////////////////////////////////////

        // Custom CSS ?
        if (!empty($cssStyle)) {
            $modx->regClientCSS($cssStyle);
        }

        // Logged or not ?
        if ($_SESSION['mgrValidated']) {
            $html = $loggedTpl;
            $actionVal = 'logout';

            $modx->setPlaceholder('ml.logged_msg', $_lang['logged_msg']);
            $modx->setPlaceholder('ml.logout_lbl', $_lang['logout_text']);
            $modx->setPlaceholder('ml.home_lbl', $_lang['home_text']);
        } else {
            $html = $loginTpl;
            $actionVal = 'login';

            for ($i = 0; $i < count($errors); $i++) {
                $errors[$i] = str_replace('[+ml.erroritem+]', htmlspecialchars($errors[$i]), $errorTpl);
            }
            $modx->setPlaceholder('ml.errors', implode("\n", $errors));

            $modx->setPlaceholder('ml.user_fldname', 'username');
            $modx->setPlaceholder('ml.user_lbl', $_lang['username']);
            $modx->setPlaceholder('ml.passwd_fldname', 'password');
            $modx->setPlaceholder('ml.passwd_lbl', $_lang['password']);
            $modx->setPlaceholder('ml.remember_fldname', 'rememberme');
            $modx->setPlaceholder('ml.remember_lbl', $_lang['remember_username']);
            $modx->setPlaceholder('ml.login_lbl', $_lang['login_button']);
        }

        // Common placholders
        $modx->setPlaceholder('ml.username', $username);
        $modx->setPlaceholder('ml.action_name', 'action');
        $modx->setPlaceholder('ml.action_val', $actionVal);

        // Return HTML template
        return $html;
    }

    /*========================================================================*
     * Function: managerLoginTemplate
     *========================================================================*
     * Get template's code.
     * Parameters:
     *   - $tpl [string]
     *     Chunk name or document ID or HTML code
     * Returns:
     *   Template's code
     *========================================================================*/
    function managerLoginTemplate($tpl)
    {
        global $modx;

        if ($modx->getChunk($tpl)) {
            $template = $modx->getChunk($tpl);
        } else if (is_numeric($tpl) && ($docInfo = $modx->getDocument($tpl))) {
            $template = $docInfo['content'];
        } else {
            $template = $tpl;
        }
        return $template;
    }

}

