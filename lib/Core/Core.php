<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

namespace Core;

require_once(dirname(__FILE__) . "/../autoloader.php");
require_once(dirname(__FILE__) . "/../../config.def.dist.php");

use Core\Analytics\ErrorRecording;
use Core\Babel\ApiBabel;
use Core\Deploy\Upgrade;
use DateTime;
use League\OAuth2\Client\Token\AccessToken as AccessToken;
use Medoo\Medoo;

date_default_timezone_set('Europe/London');
error_reporting(E_ALL);

/**
 * Main app class
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-NxFitbit phpDocumentor
 *            wiki for Core.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class Core
{

    /**
     * @var Medoo
     */
    protected $database;
    /**
     * @var ApiBabel
     */
    protected $fitbitapi;
    /**
     * @var Config
     */
    protected $settings;
    /**
     * @var ErrorRecording
     */
    protected $errorRecording;

    /**
     * @todo Consider test case
     */
    public function __construct()
    {
        $this->setSettings(new Config());

        $this->setDatabase(new medoo([
            'database_type' => 'mysql',
            'database_name' => $this->getSetting("db_name"),
            'server' => $this->getSetting("db_server"),
            'username' => $this->getSetting("db_username"),
            'password' => $this->getSetting("db_password"),
            'charset' => 'utf8'
        ]));

        $this->getSettings()->setDatabase($this->getDatabase());

        $installedVersion = $this->getSetting("version", "0.0.0.1", true);
        if ($installedVersion != APP_VERSION) {
            nxr(0, "Installed version $installedVersion and should be " . APP_VERSION);
            $dataReturnClass = new Upgrade($this);

            echo "Upgrading from " . $dataReturnClass->getInstallVersion() . " to " . $dataReturnClass->getInstallingVersion() . ". ";
            echo $dataReturnClass->getNumUpdates() . " updates outstanding\n";

            if ($dataReturnClass->getNumUpdates() > 0) {
                $dataReturnClass->runUpdates();
            }

            unset($dataReturnClass);
            nxr(0, "Update completed, please re-run the command");
            die();
        }

        $this->errorRecording = new ErrorRecording($this);

    }

    /**
     * @param Config $settings
     */
    private function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param Medoo $database
     */
    private function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * Cron job / queue management
     */

    /**
     * Get settings from config class
     *
     * @param string $key
     * @param null $default
     * @param bool $query_db
     *
     * @todo Consider test case
     * @return string
     */
    public function getSetting($key, $default = null, $query_db = true)
    {
        return $this->getSettings()->get($key, $default, $query_db);
    }

    /**
     * @todo Consider test case
     * @return Config
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @todo Consider test case
     * @return Medoo
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Get settings from config class
     *
     * @param string $fuid
     * @param string $key
     * @param null $default
     * @param bool $query_db
     *
     * @todo Consider test case
     * @return string
     */
    public function getUserSetting($fuid, $key, $default = null, $query_db = true)
    {
        return $this->getSettings()->getUser($fuid, $key, $default, $query_db);
    }

    /**
     * Users
     */

    /**
     * Add new cron jobs to queue
     *
     * @param string $user_fitbit_id
     * @param string $trigger
     * @param bool $force
     *
     * @todo Consider test case
     */
    public function addCronJob($user_fitbit_id, $trigger, $force = false)
    {
        if ($force || $this->getSetting('scope_' . $trigger . '_cron', false)) {
            if (!$this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "queue", [
                "AND" => [
                    "user" => $user_fitbit_id,
                    "trigger" => $trigger
                ]
            ])
            ) {
                $this->getDatabase()->insert($this->getSetting("db_prefix", null, false) . "queue", [
                    "user" => $user_fitbit_id,
                    "trigger" => $trigger,
                    "date" => date("Y-m-d H:i:s")
                ]);
                $this->getErrorRecording()->postDatabaseQuery($this->getDatabase(), [
                    "METHOD" => __METHOD__,
                    "LINE" => __LINE__
                ]);
            } else {
                nxr(0, "Cron job already present");
            }
        } else {
            nxr(0, "I am not allowed to queue $trigger");
        }
    }

    /**
     * @todo Consider test case
     * @return ErrorRecording
     */
    public function getErrorRecording()
    {
        return $this->errorRecording;
    }

    /**
     * Settings and configuration
     */

    /**
     * Delete cron jobs from queue
     *
     * @param string $user_fitbit_id
     * @param string $trigger
     *
     * @todo Consider test case
     */
    public function delCronJob($user_fitbit_id, $trigger)
    {
        if ($this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "queue", [
            "AND" => [
                "user" => $user_fitbit_id,
                "trigger" => $trigger
            ]
        ])
        ) {
            if (!$this->getDatabase()->delete($this->getSetting("db_prefix", null, false) . "queue", [
                "AND" => [
                    "user" => $user_fitbit_id,
                    "trigger" => $trigger
                ]
            ])
            ) {
                $this->getErrorRecording()->postDatabaseQuery($this->getDatabase(), [
                    "METHOD" => __METHOD__,
                    "LINE" => __LINE__
                ]);
                nxr(0, "Failed to delete $trigger Cron job");
            }
        } else {
            $this->getErrorRecording()->postDatabaseQuery($this->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
            nxr(0, "Failed to delete $trigger Cron job");
        }
    }

    /**
     * Get list of pending cron jobs from database
     *
     * @todo Consider test case
     * @return array|bool
     */
    public function getCronJobs()
    {
        return $this->getDatabase()->select($this->getSetting("db_prefix", null, false) . "queue", "*",
            ["ORDER" => ["date" => "ASC"]]);
    }

    /**
     * @param bool $reset
     * @param string $userFitbitId
     *
     * @todo Consider test case
     * @return ApiBabel
     */
    public function getFitbitAPI($userFitbitId = "", $reset = false)
    {
        if (is_null($this->fitbitapi) || $reset) {
            if ($userFitbitId == $this->getSetting("ownerFuid", null, false)) {
                $this->fitbitapi = new ApiBabel($this, true);
            } else {
                $this->fitbitapi = new ApiBabel($this, false);
            }
        }

        return $this->fitbitapi;
    }

    /**
     * @param ApiBabel $fitbitapi
     *
     * @todo Consider test case
     */
    public function setFitbitapi($fitbitapi)
    {
        $this->fitbitapi = $fitbitapi;
    }

    /**
     * Database functions
     */

    /**
     * @param string $user_fitbit_id
     * @param string|DateTime $datetime
     *
     * @todo Consider test case
     * @return array|int
     */
    public function setUserCooldown($user_fitbit_id, $datetime)
    {
        if ($this->isUser($user_fitbit_id)) {
            if (is_string($datetime)) {
                $datetime = new DateTime ($datetime);
            }

            return $this->getDatabase()->update($this->getSetting("db_prefix", null, false) . "users", [
                'cooldown' => $datetime->format("Y-m-d H:i:s")
            ], ["AND" => ['fuid' => $user_fitbit_id]]);
        } else {
            return 0;
        }
    }

    /**
     * @param string $user_fitbit_id
     *
     * @todo Consider test case
     * @return bool
     */
    public function isUser($user_fitbit_id)
    {
        if ($this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "users",
            ["fuid" => $user_fitbit_id])
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $user_fitbit_id
     *
     * @todo Consider test case
     * @return int|array
     */
    public function getUserCooldown($user_fitbit_id)
    {
        if ($this->isUser($user_fitbit_id)) {
            return $this->getDatabase()->get($this->getSetting("db_prefix", null, false) . "users", "cooldown",
                ["fuid" => $user_fitbit_id]);
        } else {
            return 0;
        }
    }

    /**
     * @param string $user_fitbit_id
     * @param AccessToken $accessToken
     *
     * @todo Consider test case
     */
    public function setUserOAuthTokens($user_fitbit_id, $accessToken)
    {
        $this->getDatabase()->update(
            $this->getSetting("db_prefix", false) . "users",
            [
                'tkn_access' => $accessToken->getToken(),
                'tkn_refresh' => $accessToken->getRefreshToken(),
                'tkn_expires' => $accessToken->getExpires()
            ], ["fuid" => $user_fitbit_id]);
    }

    /**
     * @param string $user_fitbit_id
     *
     * @todo Consider test case
     */
    public function delUserOAuthTokens($user_fitbit_id)
    {
        $this->getDatabase()->update($this->getSetting("db_prefix", false) . "users",
            [
                'tkn_access' => '',
                'tkn_refresh' => '',
                'tkn_expires' => 0
            ], ["fuid" => $user_fitbit_id]);
    }

    /**
     * @param string $user_fitbit_id
     * @param string $user_fitbit_password
     *
     * @todo Consider test case
     * @return bool
     */
    public function isUserValid($user_fitbit_id, $user_fitbit_password)
    {
        if (strpos($user_fitbit_id, '@') !== false) {
            $user_fitbit_id = $this->isUserValidEml($user_fitbit_id);
        }

        if ($this->isUser($user_fitbit_id)) {
            if ($this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "users", [
                "AND" => [
                    "fuid" => $user_fitbit_id,
                    "password" => $user_fitbit_password
                ]
            ])
            ) {
                return $user_fitbit_id;
            } else if ($this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "users", [
                "AND" => [
                    "fuid" => $user_fitbit_id,
                    "password" => ''
                ]
            ])
            ) {
                return -1;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $user_fitbit_id
     *
     * @todo Consider test case
     * @return bool
     */
    public function isUserValidEml($user_fitbit_id)
    {
        if ($this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "users",
            ["eml" => $user_fitbit_id])
        ) {
            $user_fuid = $this->getDatabase()->get($this->getSetting("db_prefix", null, false) . "users", "fuid",
                ["eml" => $user_fitbit_id]);

            return $user_fuid;
        } else {
            return $user_fitbit_id;
        }
    }

    /**
     * @param string|int $errCode
     * @param null $user
     *
     * @todo Consider test case
     * @return string
     */
    public function lookupErrorCode($errCode, $user = null)
    {
        switch ($errCode) {
            case "-146":
                return "Disabled in user config.";
            case "-145":
                return "Disabled in system config.";
            case "-144":
                return "Username missmatch.";
            case "-143":
                return "API cool down in effect.";
            case "-142":
                return "Unable to create required directory.";
            case "429":
                if (!is_null($user)) {
                    $hour = date("H") + 1;
                    $this->getDatabase()->update($this->getSetting("db_prefix", null, false) . "users", [
                        'cooldown' => date("Y-m-d " . $hour . ":01:00"),
                    ], ['fuid' => $user]);
                }

                return "Either you hit the rate limiting quota for the client or for the viewer";
            default:
                return $errCode;
        }
    }

    /**
     * Set value in database/config class
     *
     * @param string $key
     * @param string $value
     * @param bool $query_db
     *
     * @todo Consider test case
     * @return bool
     */
    public function setSetting($key, $value, $query_db = true)
    {
        return $this->getSettings()->set($key, $value, $query_db);
    }

    /**
     * Get settings from config class
     *
     * @param string $fuid
     * @param string $key
     * @param string $value
     *
     * @todo Consider test case
     * @return string
     */
    public function setUserSetting($fuid, $key, $value)
    {
        return $this->getSettings()->setUser($fuid, $key, $value);
    }

    /**
     * Helper function to check for supported API calls
     *
     * @param null $key
     *
     * @todo Consider test case
     * @return array|null|string
     */
    public function supportedApi($key = null)
    {
        $database_array = [
            'all' => 'Everything',
            'floors' => 'Floors Climed',
            'foods' => 'Calorie Intake',
            'badges' => 'Badges',
            'sleep' => 'Sleep Records',
            'body' => 'Weight & Body Fat Records',
            'goals' => 'Personal Goals',
            'water' => 'Water Intake',
            'activities' => 'Pedomitor & Activities',
            'leaderboard' => 'Friends',
            'devices' => 'Device Status',
            'caloriesOut' => 'Calories Out',
            'goals_calories' => 'Calorie Goals',
            'minutesVeryActive' => 'Minutes Very Active',
            'minutesFairlyActive' => 'Minutes Fairly Active',
            'minutesLightlyActive' => 'Minutes Lightly Active',
            'minutesSedentary' => 'Minutes Sedentary',
            'elevation' => 'Elevation',
            'distance' => 'Distance Traveled',
            'steps' => 'Steps Taken',
            'profile' => 'User Profile',
            'heart' => 'Heart Rates',
            'activity_log' => 'Activities',
            'nomie_trackers' => "Nomie Trackers"
        ];
        ksort($database_array);

        if (is_null($key)) {
            return $database_array;
        } else {
            if (array_key_exists($key, $database_array)) {
                return $database_array[$key];
            } else {
                return $key;
            }
        }
    }

    /**
     * @param string $_nx_fb_usr
     *
     * @todo Consider test case
     * @return bool
     */
    public function isUserOAuthAuthorised($_nx_fb_usr)
    {
        if (array_key_exists("userIsOAuth_" . $_nx_fb_usr,
                $_SESSION) && is_bool($_SESSION['userIsOAuth_' . $_nx_fb_usr]) && $_SESSION['userIsOAuth_' . $_nx_fb_usr] !== false
        ) {
            return $_SESSION['userIsOAuth_' . $_nx_fb_usr];
        } else {
            if ($this->valdidateOAuth($this->getUserOAuthTokens($_nx_fb_usr, false))) {
                $_SESSION['userIsOAuth_' . $_nx_fb_usr] = true;

                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param array|bool $userArray
     *
     * @todo Consider test case
     * @return bool
     */
    public function valdidateOAuth($userArray)
    {
        if ($userArray['tkn_access'] == "" || $userArray['tkn_refresh'] == "" || $userArray['tkn_expires'] == "") {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $user_fitbit_id
     * @param bool $validate
     *
     * @todo Consider test case
     * @return bool
     */
    public function getUserOAuthTokens($user_fitbit_id, $validate = true)
    {
        $userArray = $this->getDatabase()->get($this->getSetting("db_prefix", null, false) . "users", [
            'tkn_access',
            'tkn_refresh',
            'tkn_expires'
        ], ["fuid" => $user_fitbit_id]);
        if (is_array($userArray)) {
            if ($validate && $this->valdidateOAuth($userArray)) {
                return $userArray;
            } else if (!$validate) {
                return $userArray;
            }
        }

        return false;
    }

}
