<?php

    /**
     * Core - Cron commandline tool
     *
     * @version   0.0.1
     * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link      http://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2015 Stuart McCulloch Anderson
     * @license   http://stuart.nx15.at/mit/2015 MIT
     */

    use Core\Core;

    parse_str(implode('&', array_slice($argv, 1)), $argv);
    foreach ($argv as $key => $value) {
        $key        = str_ireplace("--", "", $key);
        $_GET[$key] = $value;
    }

    require_once(dirname(__FILE__) . "/inc/Core.php");
    $fitbitApp = new Core();

    if ($fitbitApp->isUser($_GET['user'])) {
        $cooldown = $fitbitApp->getUserCooldown($_GET['user']);
        if (strtotime($cooldown) < strtotime(date("Y-m-d H:i:s"))) {
            if ($fitbitApp->supportedApi($_GET['get']) != $_GET['get']) {
                nxr("Forcing pull of " . $fitbitApp->supportedApi($_GET['get']) . " for " . $_GET['user']);
                $fitbitApp->getFitbitAPI($_GET['user'])->setForceSync(true);
                $fitbitApp->getFitbitAPI($_GET['user'])->pull($_GET['user'], $_GET['get']);
            } else {
                nxr("Unknown trigger " . $_GET['get'] . ". Supported calls are:");
                print_r($fitbitApp->supportedApi());
            }
        } else {
            $fitbitApp->getErrorRecording()->captureMessage("API limit reached", array('remote_api'), array(
                'level' => 'info',
                'extra' => array(
                    'api_req'      => $_GET['get'],
                    'user'         => $_GET['user'],
                    'cooldown'     => $cooldown,
                    'php_version'  => phpversion(),
                    'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1", true)
                )
            ));
            nxr("Can not process " . $fitbitApp->supportedApi($_GET['get']) . ". API limit reached for " . $_GET['user'] . ". Cooldown period ends " . $cooldown);
        }
    } else {
        $fitbitApp->getErrorRecording()->captureMessage("Unknown User", array('authentication'), array(
            'level' => 'info',
            'extra' => array(
                'api_req'      => $_GET['get'],
                'user'         => $_GET['user'],
                'php_version'  => phpversion(),
                'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1", true)
            )
        ));
        nxr("Can not process " . $fitbitApp->supportedApi($_GET['get']) . " since " . $_GET['user'] . " is no longer a user.");
    }
