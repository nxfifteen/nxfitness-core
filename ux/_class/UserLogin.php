<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @subpackage  UX
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

require_once(dirname(__FILE__) . "/../../lib/autoloader.php");

use Core\Core;

header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

$fitbitApp = new Core();
if (array_key_exists("fuid", $_POST)) {
    $valid = $fitbitApp->isUserValid($_POST['fuid'],
        hash("sha256", $fitbitApp->getSetting("salt") . $_POST['password']));
    if ($valid == -1 and array_key_exists("password", $_POST) and array_key_exists("password2",
            $_POST) and $_POST['password'] == $_POST['password2']
    ) {
        $newUserArray = ['password' => hash("sha256", $fitbitApp->getSetting("salt") . $_POST['password'])];
        if (array_key_exists("email", $_POST) and $_POST['email'] != "") {
            $newUserArray['eml'] = $_POST['email'];
        }
        $fitbitApp->getDatabase()->update($fitbitApp->getSetting("db_prefix", null, false) . "users", $newUserArray,
            ['fuid' => $_POST['fuid']]);

        //nxr(0, "DEBUG(205):" . __LINE__);
        setcookie('_nx_fb_usr', $_POST['fuid'], false, '/', $_SERVER['SERVER_NAME']);
        setcookie('_nx_fb_key', gen_cookie_hash($fitbitApp, $_POST['fuid']), false, '/', $_SERVER['SERVER_NAME']);

        header("Location: " . $_SESSION['admin_config']['http/admin'] . "/");
    } else if ($valid == -1 and array_key_exists("password", $_POST) and array_key_exists("password2",
            $_POST) and $_POST['password'] != $_POST['password2']
    ) {
        header("Location: " . $_SESSION['admin_config']['http/admin'] . "/views/pages/register.php?usr=" . $_POST['fuid'] . "&eml=" . $_POST['eml'] . "&err=Passwords Dont Match");
    } else {
        if ($valid != -1 and is_string($valid)) {
            if (isset($_POST['remember'])) {
                /* Set cookie to last 1 year */
                //nxr(0, "DEBUG(205):" . __LINE__);
                setcookie('_nx_fb_usr', $valid, time() + 60 * 60 * 24 * 365, '/', $_SERVER['SERVER_NAME']);
                setcookie('_nx_fb_key', gen_cookie_hash($fitbitApp, $valid), time() + 60 * 60 * 24 * 365, '/',
                    $_SERVER['SERVER_NAME']);
            } else {
                /* Cookie expires when browser closes */
                //nxr(0, "DEBUG(205):" . __LINE__);
                setcookie('_nx_fb_usr', $valid, false, '/', $_SERVER['SERVER_NAME']);
                setcookie('_nx_fb_key', gen_cookie_hash($fitbitApp, $valid), false, '/', $_SERVER['SERVER_NAME']);
            }
            header("Location: " . $_SESSION['admin_config']['http/admin'] . "/");
        } else if ($valid == -1) {
            header("Location: " . $_SESSION['admin_config']['http/admin'] . "/views/pages/register.php?usr=" . $_POST['fuid']);
        } else {
            header("Location: " . $_SESSION['admin_config']['http/admin'] . "/views/pages/login.php?usr=" . $valid . "&err=Username/Password Invalid");
        }
    }
} else {
    header("Location: " . $_SESSION['admin_config']['http/admin'] . "/login");
}

/**
 * @param Core $fitbitApp
 * @param          $fuid
 *
 * @return string
 * @internal param array $_POST
 */
function gen_cookie_hash($fitbitApp, $fuid)
{
    return hash("sha256",
        $fitbitApp->getSetting("salt") . $fuid . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR']);
}

