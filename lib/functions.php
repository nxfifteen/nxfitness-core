<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 * Copyright (c) 2017. Stuart McCulloch Anderson
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

/**
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.DevelopmentCodeFragment)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
if (!function_exists("nxr")) {
    /**
     * NXR is a helper function. Past strings are recorded in a text file
     * and when run from a command line output is displayed on screen as
     * well
     *
     * @param integer $indentation Log line indenation
     * @param string|array|object $msg String input to be displayed in logs files
     * @param bool $includeDate If true appends datetime stamp
     * @param bool $newline If true adds a new line character
     * @param bool $echoLine Print a new line or not
     */
    function nxr($indentation, $msg, $includeDate = true, $newline = true, $echoLine = true)
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = print_r($msg, true);
        }

        for ($counter = 0; $counter < $indentation; $counter++) {
            $msg = " " . $msg;
        }

        if ($includeDate) {
            $msg = date("Y-m-d H:i:s") . ": " . $msg;
        }
        if ($newline) {
            $msg = $msg . "\n";
        }

        if (is_writable(dirname(__FILE__) . "/../fitbit.log")) {
            $logFileName = fopen(dirname(__FILE__) . "/../fitbit.log", "a");
            fwrite($logFileName, $msg);
            fclose($logFileName);
        }

        if ((!defined('TEST_SUITE') || TEST_SUITE == false) && $echoLine !== false && (!defined('IS_CRON_RUN') || !IS_CRON_RUN) && php_sapi_name() == "cli") {
            echo $msg;
        }
    }
}

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
if (!function_exists("nxr_destroy_session")) {

    function nxr_destroy_session()
    {
        // Unset all of the session variables.
        unset($_SESSION);

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }
}

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
if (!function_exists("msgApi")) {
    /**
     * @param string $api
     * @param string $title
     * @param string $text
     * @param array $options
     */
    function msgApi($api, $title, $text, $options)
    {
        if (!is_null($api)) {
            $urlBase = "https://joinjoaomgcd.appspot.com/_ah/api/messaging/v1/sendPush";

            $icon = "";
            $sound = "";

            if (array_key_exists("icon", $options)) {
                $icon = "&icon=" . urlencode($options['icon']);
            }

            if (array_key_exists("sound", $options)) {
                $sound = "&sound=" . urlencode($options['sound']);
            }

            $url = $urlBase . "?deviceId=group.windows10&text=" . urlencode($text) . "&title=" . urlencode($title) . $icon . $sound . "&apikey=$api";
            $response = json_decode(file_get_contents($url), true);
            if ($response['success'] != "true") {
                nxr(0, "Join Operation failed, invalid response received: $response");
            }

            $url = $urlBase . "?deviceId=group.phone&text=" . urlencode($text) . "&title=" . urlencode($title) . $icon . $sound . "&apikey=$api";
            $response = json_decode(file_get_contents($url), true);
            if ($response['success'] != "true") {
                nxr(0, "Join Operation failed, invalid response received: $response");
            }
        }
    }
}

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
if (!function_exists("nomieRecord")) {
    /**
     * @param string $api
     * @param string $event
     * @param string $autoRemote
     */
    function nomieRecord($api, $event, $autoRemote)
    {
        if (!is_null($api)) {
            nxr(0, "Nomie API");
            $url = "https://api.nomie.io/v2/push/$api/$event";
            $response = file_get_contents($url);
            nxr(1, $response);
        }

        if (!is_null($autoRemote)) {
            nxr(0, "AutoRemote API");
            $url = "https://autoremotejoaomgcd.appspot.com/sendmessage?key=$autoRemote&message=nomie";
            $response = file_get_contents($url);
            nxr(1, $response);
        }
    }
}

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
if (!function_exists("round_up")) {
    function round_up($number, $precision = 2)
    {
        $fig = (int)str_pad('1', $precision, '0');
        return (ceil($number * $fig) / $fig);
    }
}

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
if (!function_exists("round_down")) {
    function round_down($number, $precision = 2)
    {
        $fig = (int)str_pad('1', $precision, '0');
        return (floor($number * $fig) / $fig);
    }
}