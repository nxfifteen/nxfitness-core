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

namespace Core\UX;

require_once(dirname(__FILE__) . "/../../autoloader.php");

use Core\Config;
use Core\Core;
use DateTime;
use Medoo\Medoo;

/**
 * Class NxFitAdmin
 */
class NxFitAdmin
{

    /**
     * @var Medoo
     */
    protected $database;

    /**
     * @var string
     */
    protected $activeUser;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Config
     */
    protected $apiSettings;

    /**
     * @var Core
     */
    protected $nxFit;

    /**
     * @var Config
     */
    protected $dbUserProfile;

    /**
     * Create the admin UX class, parent class to Code with additonal UX methods
     */
    public function __construct()
    {
        if (isset($_SESSION) && array_key_exists("admin_config",
                $_SESSION) && is_array($_SESSION['admin_config']) && count($_SESSION['admin_config']) > 0
        ) {
            $this->setConfig($_SESSION['admin_config']);
        } else {
            if (isset($config)) {
                $_SESSION['admin_config'] = $config;
                $this->setConfig($_SESSION['admin_config']);
            }
        }

        $this->nxFit = new Core();

        $this->setDatabase(new medoo([
            'database_type' => 'mysql',
            'database_name' => $this->getApiSetting("db_name"),
            'server' => $this->getApiSetting("db_server"),
            'username' => $this->getApiSetting("db_username"),
            'password' => $this->getApiSetting("db_password"),
            'charset' => 'utf8'
        ]));

        $this->getApiSettingClass()->setDatabase($this->getDatabase());

        if (!isset($_COOKIE['_nx_fb_usr']) || !isset($_COOKIE['_nx_fb_key'])) {
            header("Location: " . $this->getConfig('url') . $this->getConfig('/admin') . "/login");
        } else if (isset($_COOKIE['_nx_fb_key']) AND $_COOKIE['_nx_fb_key'] != hash("sha256",
                $this->getApiSetting("salt") . $_COOKIE['_nx_fb_usr'] . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR'])
        ) {
            header("Location: " . $this->getConfig('url') . $this->getConfig('/admin') . "/login");
        }

        $this->setActiveUser($_COOKIE['_nx_fb_usr']);

    }

    /**
     * @param string $key
     * @param null $default
     * @param bool $query_db
     *
     * @return string
     */
    public function getApiSetting($key = "", $default = null, $query_db = true)
    {
        return $this->getApiSettingClass()->get($key, $default, $query_db);
    }

    /**
     * @return Config
     */
    public function getApiSettingClass()
    {
        return $this->getNxFit()->getSettings();
    }

    /**
     * @return Core
     */
    public function getNxFit()
    {
        return $this->nxFit;
    }

    /**
     * @return medoo
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param medoo $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getConfig($key = "")
    {
        if (!is_string($key) && $key == "") {
            return $this->config;
        } else {
            return $this->config[$key];
        }
    }

    /**
     * @param array $config
     *

     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return float
     */
    public function getSyncStatus()
    {
        if (!array_key_exists("SyncProgress",
                $_SESSION) || !is_numeric($_SESSION['SyncProgress']) || $_SESSION['SyncProgress'] < 0 || $_SESSION['SyncProgress'] > 100 ||
            !array_key_exists("SyncProgressScopes", $_SESSION) || !is_array($_SESSION['SyncProgressScopes'])
        ) {
            $timeToday = strtotime(date("Y-m-d H:i:s"));
            $timeFirstSeen = strtotime($this->getUserProfile()['seen'] . ' 00:00:00');

            $totalProgress = 0;
            $allowed_triggers = [];
            foreach ($this->getNxFit()->supportedApi() as $key => $name) {
                if ($this->getApiSetting('scope_' . $key) && $this->getNxFit()->getUserSetting($_COOKIE['_nx_fb_usr'],
                        'scope_' . $key) && $key != "all"
                ) {
                    $allowed_triggers[$key]['name'] = $this->getNxFit()->supportedApi($key);

                    /** @var \DateTime $oldestScope */
                    $oldestScope = $this->getOldestScope($key);
                    $timeLastRun = strtotime($oldestScope->format("Y-m-d H:i:s"));

                    $differenceLastRun = $timeLastRun - $timeToday;
                    $differenceFirstSeen = $timeFirstSeen - $timeToday;
                    $precentageCompleted = round((100 - ($differenceLastRun / $differenceFirstSeen) * 100), 1);
                    if ($precentageCompleted < 0) {
                        $precentageCompleted = 0;
                    }
                    if ($precentageCompleted > 100) {
                        $precentageCompleted = 100;
                    }

                    $allowed_triggers[$key]['precentage'] = $precentageCompleted;
                    $totalProgress += $precentageCompleted;
                }
            }

            $_SESSION['SyncProgressScopes'] = $allowed_triggers;
            $_SESSION['SyncProgress'] = round(($totalProgress / (100 * count($allowed_triggers))) * 100, 1);

        }

        return $_SESSION['SyncProgress'];
    }

    /**
     * @return Config
     */
    public function getUserProfile()
    {
        if (!isset($this->dbUserProfile)) {
            $userProfile = $this->getDatabase()->get($this->getApiSetting("db_prefix", null,
                    false) . "users", [
                'name',
                'avatar',
                'city',
                'country',
                'height',
                'stride_walking',
                'stride_running',
                'gender',
                'rank',
                'friends',
                'distance',
                'seen',
                'lastrun',
                'cooldown'
            ], ["fuid" => $this->getActiveUser()]);
            $this->dbUserProfile = $userProfile;
        }

        return $this->dbUserProfile;
    }

    /**
     * @return string
     */
    public function getActiveUser()
    {
        return $this->activeUser;
    }

    /**
     * @param string $activeUser
     *

     */
    public function setActiveUser($activeUser)
    {
        $this->activeUser = $activeUser;
    }

    /**
     * @param null $scope
     *
     * @return DateTime
     */
    public function getOldestScope($scope = null)
    {
        if (is_null($scope)) {
            if ($this->getDatabase()->has($this->getApiSetting("db_prefix", null, false) . "runlog",
                ["user" => $this->getActiveUser()])
            ) {
                return new DateTime ($this->getDatabase()->get($this->getApiSetting("db_prefix", null,
                        false) . "runlog", "lastrun", [
                    "user" => $this->getActiveUser(),
                    "ORDER" => ["lastrun" => "ASC"]
                ]));
            }
        } else {
            if ($this->getDatabase()->has($this->getApiSetting("db_prefix", null, false) . "runlog", [
                "AND" => [
                    "user" => $this->getActiveUser(),
                    "activity" => $scope
                ]
            ])
            ) {
                $returnTime = new DateTime ($this->getDatabase()->get($this->getApiSetting("db_prefix", null,
                        false) . "runlog", "lastrun", [
                    "AND" => [
                        "user" => $this->getActiveUser(),
                        "activity" => $scope
                    ],
                    "ORDER" => ["lastrun" => "ASC"]
                ]));

                return $returnTime;
            }
        }

        return new DateTime ("1970-01-01");
    }

    /**
     * @return string
     */
    public function getLocalWeatherImage()
    {
        $usrProfile = $this->getUserProfile();
        $usrCity = $usrProfile['city'];
        $usrCountry = $usrProfile['country'];

        if (isset($usrCity) && (!is_string($usrCity) || $usrCity == "")) {
            unset($usrCity);
        } else {
            $usrCity = str_ireplace(" ", "", str_ireplace(".", "", $usrCity));
        }
        if (isset($usrCountry) && (!is_string($usrCountry) || $usrCountry == "")) {
            unset($usrCountry);
        } else {
            $usrCountry = str_ireplace(" ", "", str_ireplace(".", "", $usrCountry));
        }

        $imagePath = CORE_UX . "img/local/";

        if (isset($usrCity) && isset($usrCountry) && file_exists($imagePath . strtolower($usrCountry) . "/" . strtolower($usrCity) . ".jpg")) {
            return "img/local/" . strtolower($usrCountry) . "/" . strtolower($usrCity) . ".jpg";
        }

        if (isset($usrCity) && file_exists($imagePath . strtolower($usrCity) . ".jpg")) {
            if (isset($usrCountry)) {
                nxr(1,
                    "+** No Location Image for " . strtolower($usrCountry) . "/" . strtolower($usrCity) . ".jpg");
            }

            return "img/local/" . strtolower($usrCity) . ".jpg";
        }

        if (isset($usrCountry) && file_exists($imagePath . strtolower($usrCountry) . ".jpg")) {
            if (isset($usrCity)) {
                nxr(1,
                    "+** No Location Image for " . strtolower($usrCountry) . "/" . strtolower($usrCity) . ".jpg");
            }

            return "img/local/" . strtolower($usrCountry) . ".jpg";
        }

        return "img/local/default.jpg";
    }

    /**
     * @return string
     */
    public function getLocalWeatherCode()
    {
        $usrProfile = $this->getUserProfile();
        $usrCity = $usrProfile['city'];
        $usrCountry = $usrProfile['country'];

        if (isset($usrCity) && isset($usrCountry)) {
            return "$usrCity, $usrCountry";
        }

        return "London, GB";
    }

    /**
     * @return string|array
     */
    public function getUserTheme()
    {
        //$themes = array('blue','default','green','orange','purple');
        //return $themes[array_rand($themes, 1)];

        return "default";
    }

    /**
     * @param string $trigger
     *
     * @return DateTime
     */
    public function getScopeCoolDown($trigger)
    {
        if ($this->getDatabase()->has($this->getApiSetting("db_prefix", null, false) . "runlog", [
            "AND" => [
                "user" => $this->getActiveUser(),
                "activity" => $trigger
            ]
        ])
        ) {
            return new DateTime ($this->getDatabase()->get($this->getApiSetting("db_prefix", null,
                    false) . "runlog", "cooldown", [
                "AND" => [
                    "user" => $this->getActiveUser(),
                    "activity" => $trigger
                ]
            ]));
        } else {
            return new DateTime ("1970-01-01");
        }
    }

    /**
     * @param string $string
     * @param array $array
     *
     * @return string
     */
    public function getThemeWidgets($string, $array)
    {
        unset($string);
        unset($array);

        return "";
    }
}