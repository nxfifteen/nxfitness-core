<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

namespace Core\Babel;

require_once(dirname(__FILE__) . "/../../autoloader.php");

use Core\Core;
use Core\Rewards\RewardsSystem;
use couchClient;
use couchNotFoundException;
use DateInterval;
use DatePeriod;
use DateTime;
use djchen\OAuth2\Client\Provider\Fitbit;
use Exception;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException as IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken as AccessToken;
use SimpleXMLElement;

define("FITBIT_COM", "https://api.fitbit.com");

/**
 * ApiBabel
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-fitbit phpDocumentor wiki
 *            for ApiBabel.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class ApiBabel
{

    /**
     * @var Core
     */
    protected $AppClass;
    /**
     * @var RewardsSystem
     */
    protected $RewardsSystem;
    /**
     * @var Fitbit
     */
    protected $fitbitapi;
    /**
     * @var bool
     */
    protected $forceSync;

    /**
     * @var
     */
    private $holdingVar;

    /**
     * @var
     */
    private $activeUser;

    /**
     * @var
     */
    private $userAccessToken;

    /**
     * @param Core $fitbitApp
     * @param bool $personal
     */
    public function __construct($fitbitApp, $personal = false)
    {
        $this->setAppClass($fitbitApp);

        $personal = $personal ? "_personal" : "";

        $this->setLibrary(new Fitbit([
            'clientId' => $fitbitApp->getSetting("api_clientId" . $personal, null, false),
            'clientSecret' => $fitbitApp->getSetting("api_clientSecret" . $personal, null, false),
            'redirectUri' => $fitbitApp->getSetting("api_redirectUri" . $personal, null, false)
        ]));

        nxr(0, "clientId: " . $fitbitApp->getSetting("api_clientId" . $personal, null, false) . " used");

        $this->forceSync = false;

        if (!defined('IS_CRON_RUN')) {
            define('IS_CRON_RUN', false);
        }

    }

    /**
     * @param Core $AppClass
     */
    private function setAppClass($AppClass)
    {
        $this->AppClass = $AppClass;
    }

    /**
     * @param Fitbit $fitbitapi
     *
     * @todo Consider test case
     */
    public function setLibrary($fitbitapi)
    {
        $this->fitbitapi = $fitbitapi;
    }

    /**
     * @param mixed $userAccessToken
     *
     * @todo Consider test case
     */
    public function setUserAccessToken($userAccessToken)
    {
        $this->userAccessToken = $userAccessToken;
    }

    /**
     * @param string $user
     * @param string $trigger
     * @param bool $return
     *
     * @todo Consider test case
     * @return mixed|null|SimpleXMLElement|string
     */
    public function pull($user, $trigger, $return = false)
    {
        $this->setActiveUser($user);
        $xml = null;

        // Check we have a valid user
        if ($this->getAppClass()->isUser($user)) {
            nxr(1, "Checking $user for minecraft reward support");

            nxr(2, "Reward system ready");
            $this->RewardsSystem = new RewardsSystem($user);

            $userCoolDownTime = $this->getAppClass()->getUserCooldown($this->activeUser);
            if (strtotime($userCoolDownTime) >= date("U")) {
                nxr(0,
                    "User Cooldown in place. Cooldown will be lift at " . $userCoolDownTime . " please try again after that.");
                die();
            }

            // PULL - users profile
            if ($trigger == "all" || $trigger == "nomie_trackers") {
                $pull = $this->pullNomieTrackers();
                if ($this->isApiError($pull) && !IS_CRON_RUN) {
                    nxr(2, "Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                }
            }

            // Check this user has valid access to the Fitbit AIP
            if ($this->getAppClass()->valdidateOAuth($this->getAppClass()->getUserOAuthTokens($user, false))) {

                // If we've asked for a complete update then don't abide by cooldown times
                if ($trigger == "all") {
                    $this->forceSync = true;
                }

                // PULL - users profile
                if ($trigger == "all" || $trigger == "profile") {
                    $pull = $this->pullBabelProfile();
                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                        nxr(2, "Error profile: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                // PULL - Devices
                if ($trigger == "all" || $trigger == "devices") {
                    $pull = $this->pullBabelDevices();
                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                        nxr(2, "Error devices: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                // PULL - Badges
                if ($trigger == "all" || $trigger == "badges") {
                    $pull = $this->pullBabelBadges();
                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                        nxr(2, "Error badges: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                if ($trigger == "all" || $trigger == "leaderboard") {
                    $pull = $this->pullBabelLeaderboard();
                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                        nxr(2, "Error leaderboard: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                if ($trigger == "all" || $trigger == "foods" || $trigger == "goals_calories") {
                    $pull = $this->pullBabelCaloriesGoals();
                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                        nxr(2, "Error goals_calories: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                if ($trigger == "all" || $trigger == "activity_log") {
                    $pull = $this->pullBabelActivityLogs();
                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                        nxr(2, "Error activity_log: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                if ($trigger == "all" || $trigger == "goals") {
                    nxr(0, ' Downloading Goals');
                    $pull = $this->pullBabelUserGoals();
                    if ($this->isApiError($pull) && !IS_CRON_RUN) {
                        nxr(2, "Error goals: " . $this->getAppClass()->lookupErrorCode($pull));
                    }
                }

                // Set variables require bellow
                $currentDate = new DateTime ('now');
                $interval = DateInterval::createFromDateString('1 day');

                if ($trigger == "all" || $trigger == "heart") {
                    // Check we're allowed to pull these records here rather than at each loop
                    $isAllowed = $this->isAllowed("heart");
                    if (!is_numeric($isAllowed)) {
                        if ($this->isTriggerCooled("heart")) {
                            $period = new DatePeriod ($this->getLastCleanRun("heart"), $interval, $currentDate);
                            /** @var DateTime $dt */
                            foreach ($period as $dt) {
                                nxr(0, ' Downloading Heart Logs for ' . $dt->format("l jS M Y"));
                                $pull = $this->pullBabelHeartRateSeries($dt->format("Y-m-d"));
                                if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                    nxr(2, "Error Heart: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            if (!IS_CRON_RUN) {
                                nxr(2, "Error Heart: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }
                }

                if ($trigger == "all" || $trigger == "water" || $trigger == "foods") {
                    // Check we're allowed to pull these records here rather than at each loop
                    $isAllowed = $this->isAllowed("water");
                    if (!is_numeric($isAllowed)) {
                        if ($this->isTriggerCooled("water")) {
                            $period = new DatePeriod ($this->getLastCleanRun("water"), $interval, $currentDate);
                            /**
                             * @var DateTime $dt
                             */
                            foreach ($period as $dt) {
                                nxr(0, ' Downloading Water Logs for ' . $dt->format("l jS M Y"));
                                $pull = $this->pullBabelWater($dt->format("Y-m-d"));
                                if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                    nxr(2, "Error water: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            if (!IS_CRON_RUN) {
                                nxr(2, "Error water: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }
                }

                if ($trigger == "all" || $trigger == "sleep") {
                    $isAllowed = $this->isAllowed("sleep");
                    if (!is_numeric($isAllowed)) {
                        if ($this->isTriggerCooled("sleep")) {
                            $period = new DatePeriod ($this->getLastCleanRun("sleep"), $interval, $currentDate);
                            /**
                             * @var DateTime $dt
                             */
                            foreach ($period as $dt) {
                                nxr(0, ' Downloading Sleep Logs for ' . $dt->format("l jS M Y"));
                                $pull = $this->pullBabelSleep($dt->format("Y-m-d"));
                                if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                    nxr(2, "Error sleep: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            if (!IS_CRON_RUN) {
                                nxr(2, "Error sleep: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }
                }

                if ($trigger == "all" || $trigger == "body") {
                    $isAllowed = $this->isAllowed("body");
                    if (!is_numeric($isAllowed)) {
                        if ($this->isTriggerCooled("body")) {
                            $period = new DatePeriod ($this->getLastCleanRun("body"), $interval, $currentDate);
                            /**
                             * @var DateTime $dt
                             */
                            foreach ($period as $dt) {
                                nxr(0, ' Downloading Body Logs for ' . $dt->format("l jS M Y"));
                                $pull = $this->pullBabelBody($dt->format("Y-m-d"));
                                if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                    nxr(2, "Error body: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            if (!IS_CRON_RUN) {
                                nxr(2, "Error body: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }
                }

                if ($trigger == "all" || $trigger == "foods") {
                    $isAllowed = $this->isAllowed("foods");
                    if (!is_numeric($isAllowed)) {
                        if ($this->isTriggerCooled("foods")) {
                            $period = new DatePeriod ($this->getLastCleanRun("foods"), $interval, $currentDate);
                            /**
                             * @var DateTime $dt
                             */
                            foreach ($period as $dt) {
                                nxr(0, ' Downloading Foods Logs for ' . $dt->format("l jS M Y"));
                                $pull = $this->pullBabelMeals($dt->format("Y-m-d"));
                                if ($this->isApiError($pull) && !IS_CRON_RUN) {
                                    nxr(2, "Error foods: " . $this->getAppClass()->lookupErrorCode($pull));
                                }
                            }
                        } else {
                            if (!IS_CRON_RUN) {
                                nxr(2, "Error foods: " . $this->getAppClass()->lookupErrorCode(-143));
                            }
                        }
                    }
                }

                $timeSeries = [
                    "steps" => "300",
                    "distance" => "300",
                    "floors" => "300",
                    "elevation" => "300",
                    "minutesSedentary" => "1800",
                    "minutesLightlyActive" => "1800",
                    "minutesFairlyActive" => "1800",
                    "minutesVeryActive" => "1800",
                    "caloriesOut" => "1800"
                ];
                if ($trigger == "all" || $trigger == "activities") {
                    $isAllowed = $this->isAllowed("activities");
                    if (!is_numeric($isAllowed)) {
                        if ($this->isTriggerCooled("activities")) {
                            nxr(1, "Downloading Series Info");
                            foreach ($timeSeries as $activity => $timeout) {
                                $this->pullBabelTimeSeries($activity, true);
                            }
                            if (isset($this->holdingVar)) {
                                unset($this->holdingVar);
                            }
                            $this->setLastrun("activities", null, true);
                        }
                    }
                } else if (array_key_exists($trigger, $timeSeries)) {
                    $isAllowed = $this->isAllowed($trigger);
                    if (!is_numeric($isAllowed)) {
                        $this->pullBabelTimeSeries($trigger);
                    }
                }

                if ($trigger == "all") {
                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "users", [
                        "lastrun" => $currentDate->format("Y-m-d H:i:s")
                    ], ["fuid" => $this->getActiveUser()]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);
                }

            } else {
                nxr(0, "User has not yet authenticated with Fitbit");
            }

        }

        if ($return) {
            return $xml;
        } else {
            return true;
        }
    }

    /**
     * @param mixed $activeUser
     *
     * @todo Consider test case
     */
    public function setActiveUser($activeUser)
    {
        $this->activeUser = $activeUser;
    }

    /**
     * @return bool|array
     */
    private function pullNomieTrackers()
    {
        $isAllowed = $this->isAllowed("nomie_trackers");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("nomie_trackers")) {
                $nomie_user_key = $this->getAppClass()->getUserSetting($this->activeUser, "nomie_key", 'nomie');

                nxr(1, "Connecting to CouchDB");

                $nomie_username = $this->getAppClass()->getSetting("db_nomie_username", null, false);
                $nomie_password = $this->getAppClass()->getSetting("db_nomie_password", null, false);
                $nomie_protocol = $this->getAppClass()->getSetting("db_nomie_protocol", 'http', false);
                $nomie_host = $this->getAppClass()->getSetting("db_nomie_host", 'localhost', false);
                $nomie_port = $this->getAppClass()->getSetting("db_nomie_port", '5984', false);

                if (is_null($nomie_username)) {
                    nxr(2, "Nomie credentials missing");

                    return ["error" => "true", "code" => 105, "msg" => "Nomie is not setup correctly"];
                }

                $nomie_url = $nomie_protocol . '://' . $nomie_username . ':' . $nomie_password . '@' . $nomie_host . ':' . $nomie_port;

                try {
                    $couchClient = new couchClient ($nomie_url, $nomie_user_key . '_meta', [
                        "cookie_auth" => "true"
                    ]);
                } catch (Exception $e) {
                    nxr(4, $nomie_url);
                    $parts = parse_url($nomie_url);
                    nxr(0, print_r($parts, true));

                    if (!isset($nomie_username)) {
                        $was_username_set = "false";
                    } else {
                        $was_username_set = "true";
                    }
                    if (!isset($nomie_password)) {
                        $was_id_set = "false";
                    } else {
                        $was_id_set = "true";
                    }
                    if (!isset($nomie_host)) {
                        $was_host_set = "false";
                    } else {
                        $was_host_set = "true";
                    }
                    if (!isset($nomie_port)) {
                        $was_port_set = "false";
                    } else {
                        $was_port_set = "true";
                    }

                    $this->getAppClass()->getErrorRecording()->captureException($e, [
                        'extra' => [
                            'php_version' => phpversion(),
                            'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1", true),
                            'nomie_protocol' => $nomie_protocol,
                            'nomie_username' => $was_username_set,
                            'nomie_username_id' => $was_id_set,
                            'nomie_host' => $was_host_set,
                            'nomie_port' => $was_port_set,
                        ],
                    ]);

                    return "-144";
                }

                if (!$couchClient->databaseExists()) {
                    nxr(2, "Nomie Meta table missing");

                    return ["error" => "true", "code" => 105, "msg" => "Nomie is not setup correctly"];
                }

                try {
                    $trackerGroups = json_decode(json_encode($couchClient->getDoc('hyperStorage-groups')), true);
                } catch (couchNotFoundException $e) {
                    try {
                        $trackerGroups = json_decode(json_encode($couchClient->getDoc('groups')), true);
                    } catch (couchNotFoundException $e) {
                        $this->setLastrun("nomie_trackers", null, true);

                        return "-144";
                    }
                }

                if (is_array($trackerGroups) && array_key_exists("groups", $trackerGroups)) {
                    $trackerGroups = $trackerGroups['groups'];
                    if (is_array($trackerGroups) && array_key_exists("NxTracked",
                            $trackerGroups) && count($trackerGroups['NxTracked']) > 0
                    ) {
                        nxr(2, "Downloadnig NxTracked Group Trackers");
                        $trackerGroups = $trackerGroups['NxTracked'];
                    } else if (is_array($trackerGroups) && array_key_exists("NxCore",
                            $trackerGroups) && count($trackerGroups['NxCore']) > 0
                    ) {
                        nxr(2, "Downloadnig NxCore Group Trackers");
                        $trackerGroups = $trackerGroups['NxCore'];
                    } else if (is_array($trackerGroups) && array_key_exists("Main",
                            $trackerGroups) && count($trackerGroups['Main']) > 0
                    ) {
                        nxr(2, "Downloadnig Main Group Trackers");
                        $trackerGroups = $trackerGroups['Main'];
                    } else {
                        nxr(2, "Downloading All Trackers");
                        $trackerGroups = $trackerGroups['All'];
                    }

                    $couchClient->useDatabase($nomie_user_key . '_trackers');
                    if (!$couchClient->databaseExists()) {
                        nxr(2, "Nomie Tracker table missing");

                        return ["error" => "true", "code" => 105, "msg" => "Nomie is not setup correctly"];
                    }

                    $trackedTrackers = [];
                    $indexedTrackers = [];
                    $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
                    nxr(2, ".", true, false);
                    foreach ($trackerGroups as $tracker) {
                        nxr(0, ".", false, false);
                        try {
                            $doc = $couchClient->getDoc($tracker);
                        } catch (couchNotFoundException $e) {
                            $this->getAppClass()->getErrorRecording()->captureException($e, [
                                'extra' => [
                                    'php_version' => phpversion(),
                                    'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1", true)
                                ],
                            ]);
                        }

                        if (isset($doc) && is_object($doc)) {
                            array_push($trackedTrackers, $tracker);
                            $indexedTrackers[$tracker] = $doc->label;

                            $dbStorage = [
                                "fuid" => $this->activeUser,
                                "id" => $tracker,
                                "label" => $doc->label,
                                "icon" => trim(str_ireplace("  ", " ", $doc->icon)),
                                "color" => $doc->color,
                                "charge" => $doc->charge
                            ];

                            if (isset($doc->config->type)) {
                                $dbStorage['type'] = $doc->config->type;
                            }
                            if (isset($doc->config->math)) {
                                $dbStorage['math'] = $doc->config->math;
                            }
                            if (isset($doc->config->uom)) {
                                $dbStorage['uom'] = $doc->config->uom;
                            }

                            if (!$this->getAppClass()->getDatabase()->has($db_prefix . "nomie_trackers", [
                                "AND" => [
                                    "fuid" => $this->activeUser,
                                    "id" => $tracker
                                ]
                            ])
                            ) {
                                $this->getAppClass()->getDatabase()->insert($db_prefix . "nomie_trackers",
                                    $dbStorage);
                                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                    [
                                        "METHOD" => __METHOD__,
                                        "LINE" => __LINE__
                                    ]);
                            } else {
                                $this->getAppClass()->getDatabase()->update($db_prefix . "nomie_trackers",
                                    $dbStorage, [
                                        "AND" => [
                                            "fuid" => $this->activeUser,
                                            "id" => $tracker
                                        ]
                                    ]);
                                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                    [
                                        "METHOD" => __METHOD__,
                                        "LINE" => __LINE__
                                    ]);
                            }
                        }
                    }
                    nxr(1, "[DONE]", false);

                    $couchClient->useDatabase($nomie_user_key . '_events');
                    if (!$couchClient->databaseExists()) {
                        nxr(2, "Nomie Tracker table missing");

                        return ["error" => "true", "code" => 105, "msg" => "Nomie is not setup correctly"];
                    }
                    $trackerEvents = json_decode(json_encode($couchClient->getAllDocs()), true);
                    foreach ($trackerEvents['rows'] as $events) {
                        $event = explode("|", $events['id']);
                        $event[5] = date('Y-m-d H:i:s', $event[3] / 1000);

                        if (in_array($event[2], $trackedTrackers)) {
                            if (strlen($event[2]) > 30) {
                                $this->getAppClass()->getErrorRecording()->captureMessage("Observed event ID grater than DB supports",
                                    ['database'], [
                                        'level' => 'warning',
                                        'extra' => [
                                            'event_id' => $event[2],
                                            'string_length' => strlen($event[2]),
                                            'php_version' => phpversion(),
                                            'core_version' => $this->getAppClass()->getSetting("version",
                                                "0.0.0.1", true)
                                        ]
                                    ]);
                            }

                            if (!$this->getAppClass()->getDatabase()->has($db_prefix . "nomie_events", [
                                "AND" => [
                                    "fuid" => $this->activeUser,
                                    "id" => $event[2],
                                    "datestamp" => $event[5]
                                ]
                            ])
                            ) {
                                $document = json_decode(json_encode($couchClient->getDoc($events['id'])), true);

                                $event[6] = $document['value'];

                                $dbStorage = [
                                    "fuid" => $this->activeUser,
                                    "id" => $event[2],
                                    "datestamp" => $event[5],
                                    "value" => $event[6],
                                    "score" => $event[4],
                                ];

                                if (is_array($document['geo']) and count($document['geo']) == 2) {
                                    $dbStorage["geo_lat"] = $document['geo'][0];
                                    $dbStorage["geo_lon"] = $document['geo'][1];
                                    $event[7] = $document['geo'][0];
                                    $event[8] = $document['geo'][1];
                                }

                                $this->getAppClass()->getDatabase()->insert($db_prefix . "nomie_events",
                                    $dbStorage);
                                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                    [
                                        "METHOD" => __METHOD__,
                                        "LINE" => __LINE__
                                    ]);
                                nxr(3, "Stored event : " . $event[2] . " from " . $event[3]);
                            }

                            if (!is_null($this->RewardsSystem)) {
                                if (date('Y-m-d', $event[3] / 1000) == date('Y-m-d')) {
                                    $event[1] = $event[2];
                                    $event[2] = $indexedTrackers[$event[2]];
                                    $this->RewardsSystem->eventTrigger('Nomie', $event);
                                }
                            }
                        }
                    }
                }

                $this->setLastrun("nomie_trackers", null, true);

            } else {
                return "-143";
            }
        }

        return $isAllowed;
    }

    /**
     * @param string $trigger
     * @param bool $quiet
     *
     * @todo Consider test case
     * @return bool|string
     */
    public function isAllowed($trigger, $quiet = false)
    {
        if ($trigger == "profile") {
            return true;
        }

        $usrConfig = $this->getAppClass()->getUserSetting($this->getActiveUser(), 'scope_' . $trigger, true);
        if (!is_null($usrConfig) AND $usrConfig != 1) {
            if (!$quiet) {
                nxr(1, "Aborted $trigger disabled in user config");
            }

            return "-145";
        }

        $sysConfig = $this->getAppClass()->getSetting('scope_' . $trigger, true);
        if ($sysConfig != 1) {
            if (!$quiet) {
                nxr(1, "Aborted $trigger disabled in system config");
            }

            return "-146";
        }

        return true;
    }

    /**
     * @param string $trigger
     * @param bool $reset
     *
     * @todo Consider test case
     * @return bool
     */
    public function isTriggerCooled($trigger, $reset = false)
    {
        if ($this->forceSync) {
            return true;
        } else {
            $currentDate = new DateTime ('now');
            $coolDownTill = $this->getCoolDown($trigger, $reset);

            if ($coolDownTill->format("U") < $currentDate->format("U")) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $activity
     * @param bool $reset
     *
     * @return DateTime
     * @internal param $username
     */
    private function getCoolDown($activity, $reset = false)
    {
        if ($reset) {
            return new DateTime ("1970-01-01");
        }

        $username = $this->getActiveUser();

        //@TODO Add database error response
        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "runlog", [
            "AND" => [
                "user" => $username,
                "activity" => $activity
            ]
        ])
        ) {
            return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "runlog", "cooldown", [
                "AND" => [
                    "user" => $username,
                    "activity" => $activity
                ]
            ]));
        } else {
            return new DateTime ("1970-01-01");
        }
    }

    /**
     * @return mixed
     */
    private function getActiveUser()
    {
        return $this->activeUser;
    }

    /**
     * @return Core
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @param string $activity
     * @param null $cron_delay
     * @param bool $clean
     *
     * @internal param $username
     */
    private function setLastrun($activity, $cron_delay = null, $clean = false)
    {
        if (is_null($cron_delay)) {
            $cron_delay_holder = 'scope_' . $activity . '_timeout';
            $cron_delay = $this->getAppClass()->getSetting($cron_delay_holder, 5400);
        }

        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "runlog", [
            "AND" => [
                "user" => $this->getActiveUser(),
                "activity" => $activity
            ]
        ])
        ) {
            $fields = [
                "date" => date("Y-m-d H:i:s"),
                "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
            ];
            if ($clean) {
                $fields['lastrun'] = date("Y-m-d H:i:s");
            }

            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null,
                    false) . "runlog", $fields, [
                "AND" => [
                    "user" => $this->getActiveUser(),
                    "activity" => $activity
                ]
            ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
        } else {
            $fields = [
                "user" => $this->getActiveUser(),
                "activity" => $activity,
                "date" => date("Y-m-d H:i:s"),
                "cooldown" => date("Y-m-d H:i:s", time() + $cron_delay)
            ];
            if ($clean) {
                $fields['lastrun'] = date("Y-m-d H:i:s");
            }

            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null,
                    false) . "runlog", $fields);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
        }

        $cache_dir = dirname(__FILE__) . '/../../../' . 'cache' . DIRECTORY_SEPARATOR;
        $cache_files = scandir($cache_dir);
        foreach ($cache_files as $file) {
            if (file_exists($cache_dir . $file) && is_writable($cache_dir . $file) && substr($file, 0,
                    strlen($this->getActiveUser()) + 1) === "_" . $this->getActiveUser()
            ) {
                $cacheNames = $this->getAppClass()->getSettings()->getRelatedCacheNames($activity);
                if (count($cacheNames) > 0) {
                    foreach ($cacheNames as $cacheName) {
                        if (substr($file, 0,
                                strlen($this->getActiveUser()) + strlen($cacheName) + 2) === "_" . $this->getActiveUser() . "_" . $cacheName
                        ) {
                            if (file_exists($cache_dir . $file) && is_writable($cache_dir . $file)) {
                                nxr(2, "$file cache file was deleted");
                                unlink($cache_dir . $file);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string|int $xml
     *
     * @todo Consider test case
     * @return bool
     */
    public function isApiError($xml)
    {
        if (is_numeric($xml) AND $xml < 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed|null|SimpleXMLElement|string
     */
    private function pullBabelProfile()
    {
        $isAllowed = $this->isAllowed("profile");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("profile")) {
                $userProfile = $this->pullBabel('user/-/profile.json');
                $userProfile = $userProfile['user'];

                if (!isset($userProfile['height'])) {
                    $userProfile['height'] = null;
                }
                if (!isset($userProfile['strideLengthRunning'])) {
                    $userProfile['strideLengthRunning'] = null;
                }
                if (!isset($userProfile['strideLengthWalking'])) {
                    $userProfile['strideLengthWalking'] = null;
                }
                if (!isset($userProfile['country'])) {
                    $userProfile['country'] = null;
                }

                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null,
                        false) . "users", [
                    "avatar" => (String)$userProfile['avatar150'],
                    "country" => (String)$userProfile['country'],
                    "name" => (String)$userProfile['fullName'],
                    "gender" => (String)$userProfile['gender'],
                    "height" => (String)$userProfile['height'],
                    "seen" => (String)$userProfile['memberSince'],
                    "stride_running" => (String)$userProfile['strideLengthRunning'],
                    "stride_walking" => (String)$userProfile['strideLengthWalking']
                ], ["fuid" => $this->getActiveUser()]);
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                    [
                        "METHOD" => __METHOD__,
                        "LINE" => __LINE__
                    ]);

                if (!file_exists(dirname(__FILE__) . "/../../../images/avatars/" . $this->getActiveUser() . ".jpg")) {
                    file_put_contents(dirname(__FILE__) . "/../../../images/avatars/" . $this->getActiveUser() . ".jpg",
                        fopen((String)$userProfile['avatar150'], 'r'));
                }

                $this->setLastrun("profile", null, true);

                $subscriptions = $this->pullBabel('user/-/apiSubscriptions.json', true);
                if (count($subscriptions->apiSubscriptions) == 0) {
                    nxr(1, $this->getActiveUser() . " is not subscribed to the site");
                    $user_db_id = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                            null, false) . "users", 'uid', ["fuid" => $this->getActiveUser()]);
                    $this->pushBabelSubscription($user_db_id);
                    nxr(1, $this->getActiveUser() . " subscription confirmed with ID: $user_db_id");
                } else {
                    nxr(1, $this->getActiveUser() . " subscription is still valid");
                }

                return $userProfile;
            } else {
                return "-143";
            }
        } else {
            return $isAllowed;
        }
    }

    /**
     * @param string $path
     * @param bool $returnObject
     * @param bool $debugOutput
     * @param bool $supportFailures
     *
     * @todo Consider test case
     * @return mixed
     */
    public function pullBabel($path, $returnObject = false, $debugOutput = false, $supportFailures = false)
    {
        //$getRateRemaining = $this->getLibrary()->getRateRemaining();
        //if (is_numeric($getRateRemaining) && $getRateRemaining <= 2) {
        //    $restMinutes = round($this->getLibrary()->getRateReset() / 60, 0);
        //    nxr(1, "*** Rate limit reached. Please try again in about " . $restMinutes . " minutes ***");
        //
        //    $currentDate = new DateTime();
        //    $currentDate = $currentDate->modify("+" . ($restMinutes + 5) . " minutes");
        //    $this->getAppClass()->setUserCooldown($this->activeUser, $currentDate);
        //
        //    die();
        //} else if (is_numeric($getRateRemaining) && $getRateRemaining < 50) {
        //    nxr(1, "*** Down to your last " . $getRateRemaining . " calls ***");
        //}

        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->getAccessToken();

            $path = str_replace(FITBIT_COM . "/1/", "", $path);

            $request = $this->getLibrary()->getAuthenticatedRequest('GET', FITBIT_COM . "/1/" . $path,
                $accessToken);
            // Make the authenticated API request and get the response.
            $response = $this->getLibrary()->getParsedResponse($request);

            if ($returnObject) {
                $response = json_decode(json_encode($response), false);
            }

            if ($debugOutput) {
                nxr(0, print_r($request, true));
                nxr(0, print_r($response, true));
            }

            return $response;
        } catch (IdentityProviderException $e) {
            // Failed to get the access token or user details.

            if ($e->getCode() == 429) {
                nxr(1, "Rate limit reached. Please try again later");

                $currentDate = new DateTime();
                $currentDate = $currentDate->modify("+1 hours");
                $this->getAppClass()->setUserCooldown($this->activeUser, $currentDate->format("Y-m-d H:05:00"));

                $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);
                $this->getAppClass()->getDatabase()->insert($db_prefix . "inbox",
                    [
                        "fuid" => $this->activeUser,
                        "expires" => $currentDate->format("Y-m-d H:05:30"),
                        "ico" => "icon-cloud-download",
                        "icoColour" => "bg-danger",
                        "subject" => "Rate limit reached",
                        "body" => "Please try again after " . $currentDate->format("Y-m-d H:05:00"),
                        "bold" => "API Error"
                    ]
                );

                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

                die();
            } else {
                /*$this->getAppClass()->getErrorRecording()->captureException( $e, array(
						'level' => 'error',
						'extra' => array(
							'api_path'     => $path,
							'user'         => $this->activeUser,
							'php_version'  => phpversion(),
							'core_version' => $this->getAppClass()->getSetting( "version", "0.0.0.1", TRUE )
						),
					) );*/
                nxr(0, "Error " . $e->getCode() . ": " . $e->getMessage());
                nxr(0, $e->getFile() . " @" . $e->getLine());
                nxr(0, $e->getTraceAsString());
                if ($supportFailures) {
                    return $e->getCode();
                } else {
                    die();
                }
            }
        }
    }

    /**
     * @return AccessToken
     */
    private function getAccessToken()
    {
        if (is_null($this->userAccessToken)) {
            $user = $this->getActiveUser();

            $userArray = $this->getAppClass()->getUserOAuthTokens($user);
            if (is_array($userArray)) {
                $accessToken = new AccessToken([
                    'access_token' => $userArray['tkn_access'],
                    'refresh_token' => $userArray['tkn_refresh'],
                    'expires' => $userArray['tkn_expires']
                ]);

                if ($accessToken->hasExpired()) {
                    nxr(0, "This token as expired and needs refreshed");

                    $newAccessToken = $this->getLibrary()->getAccessToken('refresh_token', [
                        'refresh_token' => $accessToken->getRefreshToken()
                    ]);

                    $this->getAppClass()->setUserOAuthTokens($user, $newAccessToken);

                    // Purge old access token and store new access token to your data store.
                    return $newAccessToken;
                } else {
                    return $accessToken;
                }
            } else {
                nxr(0, 'User ' . $user . ' does not exist, unable to continue.');
                exit;
            }
        } else {
            return $this->userAccessToken;
        }
    }

    /**
     * @todo Consider test case
     * @return Fitbit
     */
    public function getLibrary()
    {
        return $this->fitbitapi;
    }

    /**
     * Add subscription
     *
     * @param string $id Subscription Id
     * @param string $path Subscription resource path (beginning with slash). Omit to subscribe to all user updates.
     * @param string $subscriberId
     *
     * @return mixed
     */
    private function pushBabelSubscription($id, $path = null, $subscriberId = null)
    {
        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->getAccessToken();

            $userHeaders = [
                "Accept-Header" => "en_GB",
                "Content-Type" => "application/x-www-form-urlencoded"
            ];
            if ($subscriberId) {
                $userHeaders['X-Fitbit-Subscriber-Id'] = $subscriberId;
            }

            if (isset($path)) {
                $path = '/' . $path;
            } else {
                $path = '';
            }

            $request = $this->getLibrary()->getAuthenticatedRequest(OAUTH_HTTP_METHOD_POST,
                FITBIT_COM . "/1/user/-" . $path . "/apiSubscriptions/" . $id . ".json", $accessToken,
                ["headers" => $userHeaders]);
            // Make the authenticated API request and get the response.

            $response = $this->getLibrary()->getResponse($request);
            $response = json_decode(json_encode($response), false);

            return $response;
        } catch (IdentityProviderException $e) {
            // Failed to get the access token or user details.
            $this->getAppClass()->getErrorRecording()->captureException($e, [
                'extra' => [
                    'php_version' => phpversion(),
                    'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1", true)
                ],
            ]);
            nxr(0, $e->getMessage());
            die();
        }
    }

    /**
     * Download information about devices associated with the users account. This is then stored in the database
     *
     * @return mixed|null|SimpleXMLElement|string
     */
    private function pullBabelDevices()
    {
        $isAllowed = $this->isAllowed("devices");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("devices")) {
                $userDevices = $this->pullBabel('user/-/devices.json', true);

                $trackers = [];
                foreach ($userDevices as $device) {
                    if (isset($device->id) and $device->id != "") {
                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "devices", ["AND" => ["id" => (String)$device->id]])
                        ) {
                            $current_battery = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "devices", "battery", ["id" => (String)$device->id]);

                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "devices", [
                                'lastSyncTime' => (String)$device->lastSyncTime,
                                'battery' => (String)$device->battery
                            ], ["id" => (String)$device->id]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                [
                                    "METHOD" => __METHOD__,
                                    "LINE" => __LINE__
                                ]);

                            if ($device->battery != $current_battery) {
                                $charged = 0;
                                if (
                                    ($current_battery == "Empty" && ($device->battery == "Low" || $device->battery == "Medium" || $device->battery == "High" || $device->battery == "Full"))
                                    || ($current_battery == "Low" && ($device->battery == "Medium" || $device->battery == "High" || $device->battery == "Full"))
                                    || ($current_battery == "Medium" && ($device->battery == "High" || $device->battery == "Full"))
                                ) {
                                    $charged = 1;
                                }

                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                        null, false) . "devices_charges", [
                                    'id' => (String)$device->id,
                                    'date' => (String)$device->lastSyncTime,
                                    'level' => (String)$device->battery,
                                    'charged' => $charged
                                ]);
                                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                    [
                                        "METHOD" => __METHOD__,
                                        "LINE" => __LINE__
                                    ]);
                            }
                        } else {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "devices", [
                                'id' => (String)$device->id,
                                'deviceVersion' => (String)$device->deviceVersion,
                                'type' => (String)$device->type,
                                'lastSyncTime' => (String)$device->lastSyncTime,
                                'battery' => (String)$device->battery
                            ]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                [
                                    "METHOD" => __METHOD__,
                                    "LINE" => __LINE__
                                ]);
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "devices_charges", [
                                'id' => (String)$device->id,
                                'date' => (String)$device->lastSyncTime,
                                'level' => (String)$device->battery
                            ]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                [
                                    "METHOD" => __METHOD__,
                                    "LINE" => __LINE__
                                ]);
                        }

                        if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "devices_user", [
                            "AND" => [
                                "user" => $this->getActiveUser(),
                                "device" => (String)$device->id
                            ]
                        ])
                        ) {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "devices_user", [
                                'user' => $this->getActiveUser(),
                                'device' => (String)$device->id
                            ]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                [
                                    "METHOD" => __METHOD__,
                                    "LINE" => __LINE__
                                ]);
                        }

                        if (!file_exists(dirname(__FILE__) . "/../../../images/devices/" . str_ireplace(" ", "",
                                $device->deviceVersion) . ".png")
                        ) {
                            $this->getAppClass()->getErrorRecording()->captureMessage("Missing Device Image",
                                ['static_files'], [
                                    'level' => 'warning',
                                    'extra' => [
                                        'type' => $device->type,
                                        'deviceVersion' => $device->deviceVersion,
                                        'expected_file' => str_ireplace(" ", "", $device->deviceVersion) . ".png",
                                        'php_version' => phpversion(),
                                        'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1",
                                            true)
                                    ]
                                ]);
                            nxr(1, "No device image for " . $device->type . " " . $device->deviceVersion);
                        }

                        if ($device->type == "TRACKER") {
                            array_push($trackers, $device->deviceVersion);
                        }

                    }
                }

                if (count($trackers) > 0) {
                    $supportedHeart = false;
                    $supportedFloors = false;

                    if (
                        in_array("Surge", $trackers) ||
                        in_array("Blaze", $trackers) ||
                        in_array("Alta HR", $trackers) ||
                        in_array("Charge HR", $trackers) ||
                        in_array("Charge2", $trackers)
                    ) {
                        $supportedHeart = true;
                        $supportedFloors = true;
                    } else if (
                        in_array("Charge", $trackers) ||
                        in_array("Alta", $trackers)
                    ) {
                        $supportedFloors = true;
                    }

                    if ($supportedHeart && $this->getAppClass()->getSetting("ownerFuid", null,
                            false) == $this->getActiveUser()
                    ) {
                        $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_heart", "1");
                    } else {
                        $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_heart", "0");
                    }

                    if ($supportedFloors) {
                        $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_floors", "1");
                        $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_elevation", "1");
                    } else {
                        $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_floors", "0");
                        $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_elevation", "0");
                    }

                    if (!is_null($this->getAppClass()->getSetting("nomie_key_" . $this->getActiveUser(), null,
                        false))
                    ) {
                        $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_nomie_trackers", "1");
                    } else {
                        $this->getAppClass()->setUserSetting($this->getActiveUser(), "scope_nomie_trackers", "0");
                    }
                }

                $this->setLastrun("devices", null, true);

                return $userDevices;
            } else {
                return "-143";
            }
        } else {
            return $isAllowed;
        }
    }

    /**
     * Download information of badges the user has aquired
     *
     * @return mixed|null|SimpleXMLElement|string
     * @internal param $user
     */
    private function pullBabelBadges()
    {
        $isAllowed = $this->isAllowed("badges");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("badges")) {
                $badgeFolder = dirname(__FILE__) . "/../../../images/badges/";
                if (file_exists($badgeFolder) AND is_writable($badgeFolder)) {

                    $userBadges = $this->pullBabel('user/' . $this->getActiveUser() . '/badges.json', true);

                    if (isset($userBadges)) {
                        foreach ($userBadges->badges as $badge) {

                            if (is_array($badge)) {
                                $badge = json_decode(json_encode($badge), false);
                            }

                            if ($badge->badgeType != "") {
                                /*
                                    * Check to make sure, some badges do not include unit values
                                    */
                                /*if ( isset( $badge->unit ) ) {
										$unit = (String) $badge->unit;
									} else {
										$unit = "";
									}*/

                                /*
									* If the badge is not already in the database insert it
									*/
                                if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                        null, false) . "bages", [
                                    "encodedId" => (String)$badge->encodedId
                                ])
                                ) {
                                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                            null, false) . "bages", [
                                        'encodedId' => (String)$badge->encodedId,
                                        'badgeType' => (String)$badge->badgeType,
                                        'value' => (String)$badge->value,
                                        'category' => (String)$badge->category,
                                        'description' => (String)$badge->description,
                                        'image' => basename((String)$badge->image50px),
                                        'badgeGradientEndColor' => (String)$badge->badgeGradientEndColor,
                                        'badgeGradientStartColor' => (String)$badge->badgeGradientStartColor,
                                        'earnedMessage' => (String)$badge->earnedMessage,
                                        'marketingDescription' => (String)$badge->marketingDescription,
                                        'name' => (String)$badge->name
                                    ]);
                                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                        [
                                            "METHOD" => __METHOD__,
                                            "LINE" => __LINE__
                                        ]);
                                }

                                if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                        null, false) . "bages_user", [
                                    "AND" => [
                                        "badgeid" => (String)$badge->encodedId,
                                        "fuid" => $this->getActiveUser()
                                    ]
                                ])
                                ) {
                                    nxr(0,
                                        " User " . $this->getActiveUser() . " has been awarded the " . $badge->name . " again");
                                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                            null, false) . "bages_user", [
                                        'dateTime' => (String)$badge->dateTime,
                                        'timesAchieved' => (String)$badge->timesAchieved
                                    ], [
                                        "AND" => [
                                            "badgeid" => (String)$badge->encodedId,
                                            "fuid" => $this->getActiveUser()
                                        ]
                                    ]);
                                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                        [
                                            "METHOD" => __METHOD__,
                                            "LINE" => __LINE__
                                        ]);
                                } else {
                                    nxr(0,
                                        " User " . $this->getActiveUser() . " has been awarded the " . $badge->name . ", " . $badge->timesAchieved . " times.");
                                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                            null, false) . "bages_user", [
                                        "badgeid" => (String)$badge->encodedId,
                                        "fuid" => $this->getActiveUser(),
                                        'dateTime' => (String)$badge->dateTime,
                                        'timesAchieved' => (String)$badge->timesAchieved
                                    ]);
                                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                        [
                                            "METHOD" => __METHOD__,
                                            "LINE" => __LINE__
                                        ]);
                                }

                                $imageFileName = basename((String)$badge->image50px);
                                if (!file_exists($badgeFolder . "/" . $imageFileName)) {
                                    file_put_contents($badgeFolder . "/" . $imageFileName,
                                        fopen((String)$badge->image50px, 'r'));
                                }

                                if (!file_exists($badgeFolder . "/75px")) {
                                    mkdir($badgeFolder . "/75px", 0755, true);
                                }
                                if (!file_exists($badgeFolder . "/75px/" . $imageFileName)) {
                                    file_put_contents($badgeFolder . "/75px/" . $imageFileName,
                                        fopen((String)$badge->image75px, 'r'));
                                }

                                if (!file_exists($badgeFolder . "/100px")) {
                                    mkdir($badgeFolder . "/100px", 0755, true);
                                }
                                if (!file_exists($badgeFolder . "/100px/" . $imageFileName)) {
                                    file_put_contents($badgeFolder . "/100px/" . $imageFileName,
                                        fopen((String)$badge->image100px, 'r'));
                                }

                                if (!file_exists($badgeFolder . "/125px")) {
                                    mkdir($badgeFolder . "/125px", 0755, true);
                                }
                                if (!file_exists($badgeFolder . "/125px/" . $imageFileName)) {
                                    file_put_contents($badgeFolder . "/125px/" . $imageFileName,
                                        fopen((String)$badge->image125px, 'r'));
                                }

                                if (!file_exists($badgeFolder . "/300px")) {
                                    mkdir($badgeFolder . "/300px", 0755, true);
                                }
                                if (!file_exists($badgeFolder . "/300px/" . $imageFileName)) {
                                    file_put_contents($badgeFolder . "/300px/" . $imageFileName,
                                        fopen((String)$badge->image300px, 'r'));
                                }

                                if (!is_null($this->RewardsSystem)) {
                                    $this->RewardsSystem->eventTrigger("FitbitBadgeAwarded", $badge);
                                }
                            }
                        }
                    }

                    $this->setLastrun("badges", null, true);

                    return $userBadges;
                } else {
                    if (!file_exists($badgeFolder)) {
                        nxr(0, "Missing: $badgeFolder");
                        $this->getAppClass()->getErrorRecording()->captureMessage("Missing badge folder",
                            ['file_system'], [
                                'level' => 'info',
                                'extra' => [
                                    'folder' => str_ireplace(dirname(__FILE__), "", $badgeFolder),
                                    'php_version' => phpversion(),
                                    'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1", true)
                                ]
                            ]);
                    } else if (!is_writable($badgeFolder)) {
                        nxr(0, "Unwritable: $badgeFolder");
                        if (get_current_user() == posix_getpwuid(fileowner($badgeFolder))['name']) {
                            $this->getAppClass()->getErrorRecording()->captureMessage("Unable to write too badge folder",
                                ['file_system'], [
                                    'level' => 'info',
                                    'extra' => [
                                        'folder' => str_ireplace(dirname(__FILE__), "", $badgeFolder),
                                        'permissions' => substr(sprintf('%o', fileperms($badgeFolder)), -4),
                                        'folder_owner' => posix_getpwuid(fileowner($badgeFolder))['name'],
                                        'folder_group' => posix_getpwuid(filegroup($badgeFolder))['name'],
                                        'runing_user' => get_current_user(),
                                        'php_version' => phpversion(),
                                        'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1",
                                            true)
                                    ]
                                ]);
                        }
                    }

                    return "-142";
                }
            } else {
                return "-143";
            }
        } else {
            return $isAllowed;
        }
    }

    /**
     * @return mixed|null|SimpleXMLElement|string
     * @internal param $user
     */
    private function pullBabelLeaderboard()
    {
        $isAllowed = $this->isAllowed("leaderboard");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("leaderboard")) {
                $userFriends = $this->pullBabel('user/-/friends/leaderboard.json', true);

                if (isset($userFriends)) {
                    $userFriends = $userFriends->friends;

                    if (count($userFriends) > 0) {
                        $youRank = 0;
                        $youDistance = 0;
                        $lastSteps = 0;
                        $storedLeaderboard = [];
                        foreach ($userFriends as $friend) {
                            $lifetime = floatval($friend->lifetime->steps);
                            $steps = floatval($friend->summary->steps);

                            if ($this->getActiveUser() == $this->getAppClass()->getSetting("ownerFuid", null,
                                    false)
                            ) {
                                if (!isset($allOwnersFriends)) {
                                    $allOwnersFriends = $friend->user->encodedId;
                                } else {
                                    $allOwnersFriends = $allOwnersFriends . "," . $friend->user->encodedId;
                                }
                            }

                            if ($friend->user->encodedId == $this->getActiveUser()) {
                                $displayName = "* YOU * are";
                                if ($steps == 0) {
                                    $youRank = count($userFriends);
                                } else {
                                    $youRank = (String)$friend->rank->steps;
                                }
                                $youDistance = ($lastSteps - $steps);
                                if ($youDistance < 0) {
                                    $youDistance = 0;
                                }
                            } else {
                                $displayName = $friend->user->displayName . " is";
                                $lastSteps = $steps;
                            }

                            nxr(2,
                                $displayName . " ranked " . $friend->rank->steps . " with " . number_format($steps) . " and " . number_format($lifetime) . " lifetime steps");

                            $friendId = $friend->user->encodedId;
                            $storedLeaderboard[$friendId] = [];
                            if (isset($friend->rank->steps) && !empty($friend->rank->steps)) {
                                $storedLeaderboard[$friendId]["rank"] = (String)$friend->rank->steps;
                            }
                            if (isset($friend->average->steps) && !empty($friend->average->steps)) {
                                $storedLeaderboard[$friendId]["stepsAvg"] = (String)$friend->average->steps;
                            }
                            if (isset($friend->lifetime->steps) && !empty($friend->lifetime->steps)) {
                                $storedLeaderboard[$friendId]["stepsLife"] = (String)$friend->lifetime->steps;
                            }
                            if (isset($friend->summary->steps) && !empty($friend->summary->steps)) {
                                $storedLeaderboard[$friendId]["stepsSum"] = (String)$friend->summary->steps;
                            }
                            if (isset($friend->user->avatar) && !empty($friend->user->avatar)) {
                                $storedLeaderboard[$friendId]["avatar"] = (String)$friend->user->avatar;
                            }
                            if (isset($friend->user->displayName) && !empty($friend->user->displayName)) {
                                $storedLeaderboard[$friendId]["displayName"] = (String)$friend->user->displayName;
                            }
                            if (isset($friend->user->gender) && !empty($friend->user->gender)) {
                                $storedLeaderboard[$friendId]["gender"] = (String)$friend->user->gender;
                            }
                            if (isset($friend->user->memberSince) && !empty($friend->user->memberSince)) {
                                $storedLeaderboard[$friendId]["memberSince"] = (String)$friend->user->memberSince;
                            }
                            if (isset($friend->user->age) && !empty($friend->user->age)) {
                                $storedLeaderboard[$friendId]["age"] = (String)$friend->user->age;
                            }
                            if (isset($friend->user->city) && !empty($friend->user->city)) {
                                $storedLeaderboard[$friendId]["city"] = (String)$friend->user->city;
                            }
                            if (isset($friend->user->country) && !empty($friend->user->country)) {
                                $storedLeaderboard[$friendId]["country"] = (String)$friend->user->country;
                            }

                        }

                        if ($this->getActiveUser() == $this->getAppClass()->getSetting("ownerFuid", null,
                                false) && isset($allOwnersFriends)
                        ) {
                            $this->getAppClass()->setSetting("owners_friends", $allOwnersFriends);
                        }

                        nxr(0,
                            "  * You are " . number_format($youDistance) . " steps away from the next rank and have " . count($userFriends) . " friends");

                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "users", [
                            'rank' => $youRank,
                            'friends' => count($userFriends),
                            'distance' => $youDistance
                        ], ["fuid" => $this->getActiveUser()]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);

                        if (count($storedLeaderboard) > 0) {
                            $this->getAppClass()->setUserSetting($this->getActiveUser(), "leaderboard",
                                json_encode($storedLeaderboard));
                        }

                    }
                }

                $this->setLastrun("leaderboard", null, true);

                return $userFriends;
            } else {
                return "-143";
            }
        } else {
            return $isAllowed;
        }

    }

    /**
     * @return mixed|null|SimpleXMLElement|string
     * @internal param $user
     */
    private function pullBabelCaloriesGoals()
    {
        $isAllowed = $this->isAllowed("goals_calories");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("goals_calories")) {
                $userCaloriesGoals = $this->pullBabel('user/-/foods/log/goal.json', true);

                if (isset($userCaloriesGoals) && isset($userCaloriesGoals->goals) && isset($userCaloriesGoals->foodPlan)) {
                    $fallback = false;

                    $usr_goals = $userCaloriesGoals->goals;

                    $usr_foodplan = $userCaloriesGoals->foodPlan;

                    if (empty($usr_goals->calories)) {
                        $usr_goals_calories = 0;
                        $fallback = true;
                    } else {
                        $usr_goals_calories = (int)$usr_goals->calories;
                    }

                    if (empty($usr_foodplan->intensity)) {
                        $usr_foodplan_intensity = "Unset";
                        $fallback = true;
                    } else {
                        $usr_foodplan_intensity = (string)$usr_foodplan->intensity;
                    }

                    $currentDate = new DateTime ('now');
                    if (empty($usr_foodplan->estimatedDate)) {
                        $usr_foodplan_estimatedDate = $currentDate->format("Y-m-d");
                        $fallback = true;
                    } else {
                        $usr_foodplan_estimatedDate = (string)$usr_foodplan->estimatedDate;
                    }

                    if (empty($usr_foodplan->personalized)) {
                        $usr_foodplan_personalized = "false";
                        $fallback = true;
                    } else {
                        $usr_foodplan_personalized = (string)$usr_foodplan->personalized;
                    }

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "food_goals", [
                        "AND" => [
                            "user" => $this->getActiveUser(),
                            "date" => $currentDate->format("Y-m-d")
                        ]
                    ])
                    ) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "food_goals", [
                            'calories' => $usr_goals_calories,
                            'intensity' => $usr_foodplan_intensity,
                            'estimatedDate' => $usr_foodplan_estimatedDate,
                            'personalized' => $usr_foodplan_personalized,
                        ], [
                            "AND" => [
                                "user" => $this->getActiveUser(),
                                "date" => $currentDate->format("Y-m-d")
                            ]
                        ]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "food_goals", [
                            'user' => $this->getActiveUser(),
                            'date' => $currentDate->format("Y-m-d"),
                            'calories' => $usr_goals_calories,
                            'intensity' => $usr_foodplan_intensity,
                            'estimatedDate' => $usr_foodplan_estimatedDate,
                            'personalized' => $usr_foodplan_personalized,
                        ]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    }

                    if ($fallback) {
                        $this->setLastrun("goals_calories");
                    } else {
                        $this->setLastrun("goals_calories", null, true);
                    }
                }

                return $userCaloriesGoals;
            } else {
                return "-143";
            }
        } else {
            return $isAllowed;
        }

    }

    /**
     * @return bool
     * @internal param $targetDate
     */
    private function pullBabelActivityLogs()
    {
        $isAllowed = $this->isAllowed("activity_log");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("activity_log")) {
                $targetDateTime = $this->getLastCleanRun("activity_log");

                nxr(0, ' Downloading activity logs from ' . $targetDateTime->format("Y-m-d"));

                $userActivityLog = $this->pullBabel('user/' . $this->getActiveUser() . '/activities/list.json?afterDate=' . $targetDateTime->format("Y-m-d") . '&sort=asc&limit=100&offset=0',
                    true);

                if (isset($userActivityLog) and is_object($userActivityLog)) {
                    $activityLog = $userActivityLog->activities;
                    if (isset($activityLog) && is_array($activityLog) && count($activityLog) > 0) {
                        foreach ($activityLog as $activity) {
                            $startTimeRaw = new DateTime ((String)$activity->startTime);
                            $startDate = $startTimeRaw->format("Y-m-d");
                            $startTime = $startTimeRaw->format("H:i:s");

                            if ((String)$activity->activityTypeId != "16010") {
                                $activityLevel = $activity->activityLevel;
                                $dbStorage = [
                                    "user" => $this->getActiveUser(),
                                    "logId" => (String)$activity->logId,
                                    "logType" => (String)$activity->logType,
                                    "activityName" => (String)$activity->activityName,
                                    "activityTypeId" => (String)$activity->activityTypeId,
                                    "activeDuration" => (String)$activity->activeDuration,
                                    "startDate" => $startDate,
                                    "startTime" => $startTime,
                                    "activityLevelSedentary" => $activityLevel[0]->minutes,
                                    "activityLevelLightly" => $activityLevel[1]->minutes,
                                    "activityLevelFairly" => $activityLevel[2]->minutes,
                                    "activityLevelVery" => $activityLevel[3]->minutes
                                ];

                                if (isset($activity->activityName)) {
                                    $dbStorage["activityName"] = (String)$activity->activityName;
                                }
                                if (isset($activity->distanceUnit)) {
                                    $dbStorage["distanceUnit"] = (String)$activity->distanceUnit;
                                }
                                if (isset($activity->distance)) {
                                    $dbStorage["distance"] = (String)$activity->distance;
                                }
                                if (isset($activity->speed)) {
                                    $dbStorage["speed"] = (String)$activity->speed;
                                }
                                if (isset($activity->pace)) {
                                    $dbStorage["pace"] = (String)$activity->pace;
                                }
                                if (isset($activity->steps)) {
                                    $dbStorage["steps"] = (String)$activity->steps;
                                }
                                if (isset($activity->calories)) {
                                    $dbStorage["calories"] = (String)$activity->calories;
                                }
                                if (isset($activity->caloriesLink)) {
                                    $dbStorage["caloriesLink"] = str_replace("https://api.fitbit.com/1/", "",
                                        (String)$activity->caloriesLink);
                                }
                                if (isset($activity->tcxLink)) {
                                    $dbStorage["tcxLink"] = str_replace("https://api.fitbit.com/1/", "",
                                        (String)$activity->tcxLink);
                                }
                                if (isset($activity->source) && isset($activity->source->name)) {
                                    $dbStorage["sourceName"] = (String)$activity->source->name;
                                }
                                if (isset($activity->source) && isset($activity->source->type)) {
                                    $dbStorage["sourceType"] = (String)$activity->source->type;
                                }

                                if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                        null, false) . "activity_log", [
                                    "AND" => [
                                        "user" => $this->getActiveUser(),
                                        "logId" => (String)$activity->logId
                                    ]
                                ])
                                ) {
                                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                            null, false) . "activity_log", $dbStorage);
                                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                        [
                                            "METHOD" => __METHOD__,
                                            "LINE" => __LINE__
                                        ]);
                                    nxr(2,
                                        "Activity " . (String)$activity->activityName . " on " . $startDate . " (" . (String)$activity->logId . ") add to the database.");
                                } else {
                                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                            null, false) . "activity_log", $dbStorage, [
                                        "AND" => [
                                            "user" => $this->getActiveUser(),
                                            "logId" => (String)$activity->logId
                                        ]
                                    ]);
                                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                        [
                                            "METHOD" => __METHOD__,
                                            "LINE" => __LINE__
                                        ]);
                                    nxr(2,
                                        "Activity " . (String)$activity->activityName . " on " . $startDate . " (" . (String)$activity->logId . ") updated in the database.");
                                }

                                if (isset($activity->tcxLink)) {
                                    $downloadTCX = true;

                                    if (isset($activity->logType) && $activity->logType == "auto_detected") {
                                        $downloadTCX = false;
                                    }

                                    if (isset($activity->source) && isset($activity->source->name) && isset($activity->source->type)) {
                                        if (($activity->source->type == "tracker" && $activity->source->name == "Surge") || ($activity->source->type == "app" && $activity->source->name == "Fitbit for Android")) {
                                            $downloadTCX = true;
                                        } else {
                                            $downloadTCX = false;
                                        }
                                    }

                                    if ($downloadTCX) {
                                        $this->pullBabelTCX($activity->tcxLink);
                                    }
                                }

                                if ($this->activeUser == $this->getAppClass()->getSetting("ownerFuid", null,
                                        false)
                                ) {
                                    $this->pullBabelHeartIntraday($activity);
                                }
                            }
                            $this->setLastCleanRun("activity_log", new DateTime ($startDate));

                            if (!is_null($this->RewardsSystem)) {
                                $this->RewardsSystem->eventTrigger("FitbitLoggedActivity", $activity);
                            }
                        }

                    } else {
                        nxr(2, "No recorded activities");
                        $this->setLastCleanRun("activity_log",
                            new DateTime ($userActivityLog->pagination->afterDate), 2);
                        $this->setLastrun("activity_log");
                    }
                } else {
                    $this->setLastCleanRun("activity_log",
                        new DateTime ((String)$targetDateTime->format("Y-m-d")), 7);
                    $this->setLastrun("activity_log");
                }

            } else {
                nxr(2, "Error activity log: " . $this->getAppClass()->lookupErrorCode(-143));
            }
        }

        return true;
    }

    /**
     * @param string $activity
     *
     * @return DateTime
     * @internal param $user
     */
    private function getLastCleanRun($activity)
    {
        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "runlog", [
            "AND" => [
                "user" => $this->getActiveUser(),
                "activity" => $activity
            ]
        ])
        ) {
            return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "runlog", "lastrun", [
                "AND" => [
                    "user" => $this->getActiveUser(),
                    "activity" => $activity
                ]
            ]));
        } else {
            return $this->getUserFirstSeen();
        }
    }

    /**
     * @return DateTime
     * @internal param $user
     */
    private function getUserFirstSeen()
    {
        return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                null, false) . "users", "seen", ["fuid" => $this->getActiveUser()]));
    }

    /**
     * @param string $tcxLink
     */
    private function pullBabelTCX($tcxLink)
    {
        nxr(3, "Downloading TCX File");
        if (!file_exists(dirname(__FILE__) . "/../../../tcx/" . basename($tcxLink))) {
            if (file_exists(dirname(__FILE__) . "/../../../tcx/") AND is_writable(dirname(__FILE__) . "/../../../tcx/")) {
                file_put_contents(dirname(__FILE__) . "/../../../tcx/" . basename($tcxLink), $this->pullBabel($tcxLink));
                nxr(4, "TCX files created: " . dirname(__FILE__) . "/../../../tcx/" . basename($tcxLink));
            } else {
                nxr(4, "Unable to write TCX files created");
            }
        } else {
            nxr(4, "TCX file present");
        }
    }

    /**
     * @param string $activity
     */
    private function pullBabelHeartIntraday($activity)
    {
        $isAllowed = $this->isAllowed("heart");
        if (!is_numeric($isAllowed)) {
            if ($this->activeUser == $this->getAppClass()->getSetting("ownerFuid", null, false)) {
                /** @var object $activity */
                $startTimeRaw = new DateTime ((String)$activity->startTime);
                $startDate = $startTimeRaw->format("Y-m-d");
                $startTime = $startTimeRaw->format("H:i");

                $endTimeRaw = new DateTime ((String)$activity->startTime);
                $endTimeRaw = $endTimeRaw->modify("+" . round($activity->activeDuration / 1000, 0) . " seconds");
                /** @var \DateTime $endTimeRaw */
                $endDate = $endTimeRaw->format("Y-m-d");
                $endTime = $endTimeRaw->format("H:i");

                if ($startDate == $endDate) {
                    nxr(3, "Activity Heart Rate on " . $startDate . " for " . $startTime . " till " . $endTime);

                    $hrUrl = "https://api.fitbit.com/1/user/-/activities/heart/date/" . $startDate . "/1d/1sec/time/" . $startTime . "/" . $endTime . ".json";
                    $heartRateValues = $this->pullBabel($hrUrl);

                    if (array_key_exists("activities-heart", $heartRateValues) &&
                        count($heartRateValues['activities-heart']) > 0 &&
                        array_key_exists("heartRateZones", $heartRateValues['activities-heart'][0]) &&
                        is_array($heartRateValues['activities-heart'][0]['heartRateZones'])
                    ) {
                        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "activity_log", [
                            "AND" => [
                                "user" => $this->getActiveUser(),
                                "logId" => (String)$activity->logId
                            ]
                        ])
                        ) {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "activity_log",
                                ["heartRateZones" => json_encode($heartRateValues['activities-heart'][0]['heartRateZones'])],
                                [
                                    "AND" => [
                                        "user" => $this->getActiveUser(),
                                        "logId" => (String)$activity->logId
                                    ]
                                ]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                [
                                    "METHOD" => __METHOD__,
                                    "LINE" => __LINE__
                                ]);
                            nxr(3, "Summary Information Added to Activity Log");
                        }
                    }

                    if (count($heartRateValues['activities-heart-intraday']['dataset']) > 0) {
                        $activitiesHeartIntraday = $heartRateValues['activities-heart-intraday']['dataset'];
                        $activitiesHeartIntraday = json_encode($activitiesHeartIntraday);

                        $dbStorage = [
                            "user" => $this->activeUser,
                            "logId" => $activity->logId,
                            "json" => $activitiesHeartIntraday
                        ];

                        if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "heart_activity", [
                            "AND" => [
                                "user" => $this->activeUser,
                                "logId" => $activity->logId
                            ]
                        ])
                        ) {
                            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "heart_activity", $dbStorage);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                [
                                    "METHOD" => __METHOD__,
                                    "LINE" => __LINE__
                                ]);
                        } else {
                            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "heart_activity", $dbStorage, [
                                "AND" => [
                                    "user" => $this->activeUser,
                                    "logId" => $activity->logId
                                ]
                            ]);
                            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                [
                                    "METHOD" => __METHOD__,
                                    "LINE" => __LINE__
                                ]);
                        }
                    }
                } else {
                    nxr(3,
                        "Activity Heart Rate Skipped. Unable to process across dates. Activity started on " . $startDate . " and ended on " . $endDate);
                }
            }
        }
    }

    /**
     * @param string $activity
     * @param null $date
     * @param int $delay
     *
     * @internal param $user
     */
    private function setLastCleanRun($activity, $date = null, $delay = 0)
    {
        if (is_null($date)) {
            $date = new DateTime("now");
            nxr(0, "Last run " . $date->format("Y-m-d H:i:s"));
        }
        if ($delay > 0) {
            $date->modify('-' . $delay . ' day');
        }

        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "runlog", [
            "AND" => [
                "user" => $this->getActiveUser(),
                "activity" => $activity
            ]
        ])
        ) {
            $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null,
                    false) . "runlog", [
                'date' => date("Y-m-d H:i:s"),
                'lastrun' => $date->format("Y-m-d H:i:s")
            ], ["AND" => ["user" => $this->getActiveUser(), "activity" => $activity]]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
        } else {
            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null,
                    false) . "runlog", [
                'user' => $this->getActiveUser(),
                'activity' => $activity,
                'date' => date("Y-m-d H:i:s"),
                'lastrun' => $date->format("Y-m-d H:i:s")
            ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
        }

        if ($delay == 0) {
            $this->setLastrun($activity, null, false);
        }
    }

    /**
     * @return mixed
     * @internal param $targetDate
     */
    private function pullBabelUserGoals()
    {
        $isAllowed = $this->isAllowed("goals");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("goals")) {
                $userGoals = $this->pullBabel('user/-/activities/goals/daily.json', true);

                if (isset($userGoals) && isset($userGoals->goals)) {
                    $currentDate = new DateTime();
                    $usr_goals = $userGoals->goals;
                    if (is_object($usr_goals)) {
                        $fallback = false;

                        if (!isset($usr_goals->caloriesOut) OR !isset($usr_goals->distance) OR !isset($usr_goals->floors) OR !isset($usr_goals->activeMinutes) OR !isset($usr_goals->steps) OR $usr_goals->caloriesOut == "" OR $usr_goals->distance == "" OR $usr_goals->floors == "" OR $usr_goals->activeMinutes == "" OR $usr_goals->steps == "") {
                            $this->getAppClass()->addCronJob($this->getActiveUser(), "goals");

                            if (!isset($usr_goals->caloriesOut) OR $usr_goals->caloriesOut == "") {
                                $usr_goals->caloriesOut = -1;
                            }

                            if (!isset($usr_goals->distance) OR $usr_goals->distance == "") {
                                $usr_goals->distance = -1;
                            }

                            if (!isset($usr_goals->floors) OR $usr_goals->floors == "") {
                                $usr_goals->floors = -1;
                            }

                            if (!isset($usr_goals->activeMinutes) OR $usr_goals->activeMinutes == "") {
                                $usr_goals->activeMinutes = -1;
                            }

                            if (!isset($usr_goals->steps) OR $usr_goals->steps == "") {
                                $usr_goals->steps = -1;
                            }

                            $fallback = true;
                        }

                        if ($usr_goals->steps > 1) {
                            $newGoal = $this->thisWeeksGoal("steps", $usr_goals->steps);
                            if ($newGoal > 0 && $usr_goals->steps != $newGoal) {
                                nxr(0,
                                    "  Returned steps target was " . $usr_goals->steps . " but I think it should be " . $newGoal);
                                $this->pushBabel('user/-/activities/goals/daily.json', ['steps' => $newGoal]);
                            } else if ($newGoal > 0) {
                                nxr(0,
                                    "  Returned steps target was " . $usr_goals->steps . " which is right for this week goal of " . $newGoal);
                            }

                            $this->getAppClass()->getUserSetting($this->getActiveUser(), "goal_steps", $newGoal);
                        }

                        if ($usr_goals->floors > 1) {
                            $newGoal = $this->thisWeeksGoal("floors", $usr_goals->floors);
                            if ($newGoal > 0 && $usr_goals->floors != $newGoal) {
                                nxr(0,
                                    "  Returned floor target was " . $usr_goals->floors . " but I think it should be " . $newGoal);
                                $this->pushBabel('user/-/activities/goals/daily.json', ['floors' => $newGoal]);
                            } else if ($newGoal > 0) {
                                nxr(0,
                                    "  Returned floor target was " . $usr_goals->floors . " which is right for this week goal of " . $newGoal);
                            }
                        }

                        $interval = DateInterval::createFromDateString('1 day');
                        $period = new DatePeriod ($this->getLastCleanRun("goals"), $interval, $currentDate);
                        /**
                         * @var DateTime $dt
                         */
                        foreach ($period as $dt) {
                            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "steps_goals", [
                                "AND" => [
                                    'user' => $this->getActiveUser(),
                                    'date' => $dt->format("Y-m-d")
                                ]
                            ])
                            ) {
                                $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                        null, false) . "steps_goals", [
                                    'caloriesOut' => (String)$usr_goals->caloriesOut,
                                    'distance' => (String)$usr_goals->distance,
                                    'floors' => (String)$usr_goals->floors,
                                    'activeMinutes' => (String)$usr_goals->activeMinutes,
                                    'steps' => (String)$usr_goals->steps,
                                    'syncd' => date("Y-m-d H:i:s")
                                ], [
                                    "AND" => [
                                        'user' => $this->getActiveUser(),
                                        'date' => $dt->format("Y-m-d")
                                    ]
                                ]);
                                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                    [
                                        "METHOD" => __METHOD__,
                                        "LINE" => __LINE__
                                    ]);
                            } else {
                                $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                        null, false) . "steps_goals", [
                                    'user' => $this->getActiveUser(),
                                    'date' => $dt->format("Y-m-d"),
                                    'caloriesOut' => (String)$usr_goals->caloriesOut,
                                    'distance' => (String)$usr_goals->distance,
                                    'floors' => (String)$usr_goals->floors,
                                    'activeMinutes' => (String)$usr_goals->activeMinutes,
                                    'steps' => (String)$usr_goals->steps,
                                    'syncd' => date("Y-m-d H:i:s")
                                ]);
                                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                    [
                                        "METHOD" => __METHOD__,
                                        "LINE" => __LINE__
                                    ]);
                            }
                        }

                        if (!$fallback) {
                            $this->setLastCleanRun("goals", $currentDate);
                        }
                        $this->setLastrun("goals");
                    }

                }

                return $userGoals;
            } else {
                return "-143";
            }
        } else {
            return $isAllowed;
        }

    }

    /**
     * @param string $string
     * @param int $current_goal
     *
     * @return float|int|string
     * @internal param $user
     */
    private function thisWeeksGoal($string, $current_goal = 0)
    {
        $lastMonday = date('Y-m-d', strtotime('last sunday'));
        $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));
        $plusTargetSteps = -1;

        if ($string == "steps") {
            $userPushLength = $this->getAppClass()->getUserSetting($this->getActiveUser(), "push_length",
                '50');
            $userPushStartString = $this->getAppClass()->getUserSetting($this->getActiveUser(), "push",
                '12-01 last sunday'); // Default to last Sunday in March
            $userPushStartDate = date("Y-m-d",
                strtotime(date("Y") . '-' . $userPushStartString)); // Default to last Sunday in March
            $userPushEndDate = date("Y-m-d",
                strtotime($userPushStartDate . ' +' . $userPushLength . ' day')); // Default to last Sunday in March

            $today = strtotime(date("Y-m-d"));
            if ($today >= strtotime($userPushStartDate) && $today <= strtotime($userPushEndDate)) {
                nxr(0, "Push is running");

                return $this->getAppClass()->getUserSetting($this->getActiveUser(), "push_steps", '10000');
            } else {
                $improvment = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_steps", 0);
                if ($improvment > 0) {
                    $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                            null, false) . "steps", 'steps',
                        [
                            "AND" => [
                                "user" => $this->getActiveUser(),
                                "date[>=]" => $oneWeek,
                                "date[<=]" => $lastMonday
                            ],
                            "ORDER" => ["date" => "DESC"],
                            "LIMIT" => 7
                        ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);

                    if (count($dbSteps) == 0) {
                        $plusTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                            "desire_steps_max", 1000);
                    } else {
                        $totalSteps = 0;
                        foreach ($dbSteps as $dbStep) {
                            $totalSteps = $totalSteps + $dbStep;
                        }
                        if ($totalSteps == 0) {
                            $totalSteps = 1;
                        }

                        $maxTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                            "desire_steps_max", 10000);
                        $minTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                            "desire_steps_min", ($maxTargetSteps * 0.66));
                        $LastWeeksSteps = round($totalSteps / count($dbSteps), 0);
                        $ProposedNextWeek = $LastWeeksSteps + round($LastWeeksSteps * ($improvment / 100), 0);

                        nxr(0,
                            "  * Min: " . $minTargetSteps . " Max: " . $maxTargetSteps . " LastWeeksSteps: " . $LastWeeksSteps . " ProposedNextWeek: " . $ProposedNextWeek);

                        if ($ProposedNextWeek >= $maxTargetSteps) {
                            $plusTargetSteps = $maxTargetSteps;
                        } else if ($ProposedNextWeek <= $minTargetSteps) {
                            $plusTargetSteps = $minTargetSteps;
                        } else {
                            $plusTargetSteps = $ProposedNextWeek;
                        }
                    }
                } else {
                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_steps_max", $current_goal);
                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_steps_min", $current_goal);
                }
            }
        } else if ($string == "floors") {
            $improvment = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_floors", 0);
            if ($improvment > 0) {
                $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "steps", 'floors',
                    [
                        "AND" => [
                            "user" => $this->getActiveUser(),
                            "date[>=]" => $oneWeek,
                            "date[<=]" => $lastMonday
                        ],
                        "ORDER" => ["date" => "DESC"],
                        "LIMIT" => 7
                    ]);
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                    [
                        "METHOD" => __METHOD__,
                        "LINE" => __LINE__
                    ]);

                if (count($dbSteps) == 0) {
                    $plusTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                        "desire_floors_max", 10);
                } else {
                    $totalSteps = 0;
                    foreach ($dbSteps as $dbStep) {
                        $totalSteps = $totalSteps + $dbStep;
                    }
                    if ($totalSteps == 0) {
                        $totalSteps = 1;
                    }

                    $maxTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                        "desire_floors_max", 10);
                    $minTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                        "desire_floors_min", ($maxTargetSteps * 0.66));
                    $LastWeeksSteps = round($totalSteps / count($dbSteps), 0);
                    $ProposedNextWeek = $LastWeeksSteps + round($LastWeeksSteps * ($improvment / 100), 0);

                    nxr(0,
                        "  * Min: " . $minTargetSteps . " Max: " . $maxTargetSteps . " LastWeeksSteps: " . $LastWeeksSteps . " ProposedNextWeek: " . $ProposedNextWeek);

                    if ($LastWeeksSteps >= $maxTargetSteps) {
                        $plusTargetSteps = $maxTargetSteps;
                    } else if ($LastWeeksSteps <= $minTargetSteps) {
                        $plusTargetSteps = $minTargetSteps;
                    } else {
                        $plusTargetSteps = $ProposedNextWeek;
                    }
                }
            } else {
                $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_floors_max", $current_goal);
                $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_floors_min", $current_goal);
            }
        } else if ($string == "activeMinutes") {
            $userPushLength = $this->getAppClass()->getUserSetting($this->getActiveUser(), "push_length",
                '50');
            $userPushStartString = $this->getAppClass()->getUserSetting($this->getActiveUser(), "push",
                '03-31 last sunday'); // Default to last Sunday in March
            $userPushStartDate = date("Y-m-d",
                strtotime(date("Y") . '-' . $userPushStartString)); // Default to last Sunday in March
            $userPushEndDate = date("Y-m-d",
                strtotime($userPushStartDate . ' +' . $userPushLength . ' day')); // Default to last Sunday in March

            $today = strtotime(date("Y-m-d"));
            if ($today >= strtotime($userPushStartDate) && $today <= strtotime($userPushEndDate)) {
                nxr(0, "Push is running");

                return $this->getAppClass()->getUserSetting($this->getActiveUser(), "push_activity", '30');
            } else {
                $improvment = $this->getAppClass()->getUserSetting($this->getActiveUser(), "desire_active", 0);
                if ($improvment > 0) {
                    $dbActiveMinutes = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                            null, false) . "activity", [
                        'veryactive',
                        'fairlyactive'
                    ],
                        [
                            "AND" => [
                                "user" => $this->getActiveUser(),
                                "date[>=]" => $oneWeek,
                                "date[<=]" => $lastMonday
                            ],
                            "ORDER" => ["date" => "DESC"],
                            "LIMIT" => 7
                        ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);

                    if (count($dbActiveMinutes) == 0) {
                        $plusTargetSteps = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                            "desire_active_max", 30);
                    } else {
                        $totalMinutes = 0;
                        foreach ($dbActiveMinutes as $dbStep) {
                            $totalMinutes = $totalMinutes + $dbStep['veryactive'] + $dbStep['fairlyactive'];
                        }
                        if ($totalMinutes == 0) {
                            $totalMinutes = 1;
                        }

                        $maxTargetActive = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                            "desire_active_max", 30);
                        $minTargetActive = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                            "desire_active_min", ($maxTargetActive * 0.66));
                        $LastWeeksActive = round($totalMinutes / count($dbActiveMinutes), 0);
                        $ProposedNextWeek = $LastWeeksActive + round($LastWeeksActive * ($improvment / 100), 0);

                        nxr(4,
                            "* Min: " . $minTargetActive . " Max: " . $maxTargetActive . " LastWeeksSteps: " . $LastWeeksActive . " ProposedNextWeek: " . $ProposedNextWeek);

                        if ($ProposedNextWeek >= $maxTargetActive) {
                            $plusTargetSteps = $maxTargetActive;
                        } else if ($ProposedNextWeek <= $minTargetActive) {
                            $plusTargetSteps = $minTargetActive;
                        } else {
                            $plusTargetSteps = $ProposedNextWeek;
                        }
                    }
                } else {
                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_active_max",
                        $current_goal);
                    $this->getAppClass()->setUserSetting($this->getActiveUser(), "desire_active_min",
                        $current_goal);
                }
            }
        }

        return $plusTargetSteps;
    }

    /**
     * @param string $path
     * @param string|array $pushObject
     * @param bool $returnObject
     *
     * @return mixed
     */
    private function pushBabel($path, $pushObject, $returnObject = false)
    {
        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->getAccessToken();

            if (is_array($pushObject)) {
                $pushObject = http_build_query($pushObject);
            }

            $request = $this->getLibrary()->getAuthenticatedRequest(OAUTH_HTTP_METHOD_POST,
                FITBIT_COM . "/1/" . $path, $accessToken,
                [
                    "headers" =>
                        [
                            "Accept-Header" => "en_GB",
                            "Content-Type" => "application/x-www-form-urlencoded"
                        ],
                    "body" => $pushObject
                ]);
            // Make the authenticated API request and get the response.

            $response = $this->getLibrary()->getResponse($request);

            if ($returnObject) {
                $response = json_decode(json_encode($response), false);
            }

            return $response;
        } catch (IdentityProviderException $e) {
            // Failed to get the access token or user details.
            $this->getAppClass()->getErrorRecording()->captureException($e, [
                'extra' => [
                    'php_version' => phpversion(),
                    'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1", true)
                ],
            ]);
            nxr(0, $e->getMessage());
            die();
        }
    }

    /**
     * @param string $lastCleanRun
     *
     * @return bool|mixed|string
     */
    private function pullBabelHeartRateSeries($lastCleanRun)
    {
        // Check we're allowed to pull these records here rather than at each loop
        $isAllowed = $this->isAllowed("heart");
        if (!is_numeric($isAllowed)) {
            if ($this->isTriggerCooled("heart")) {
                $lastCleanRun = new DateTime ($lastCleanRun);
                $userHeartRateLog = $this->pullBabel('user/' . $this->getActiveUser() . '/activities/heart/date/' . $lastCleanRun->format('Y-m-d') . '/1d.json',
                    true, false, true);
                if (isset($userHeartRateLog) and is_numeric($userHeartRateLog)) {
                    return "-" . $userHeartRateLog;
                }

                if (isset($userHeartRateLog)) {
                    $className = "activities-heart";
                    $activities = $userHeartRateLog->$className;
                    if (is_array($activities) && count($activities) > 0) {
                        foreach ($activities as $activity) {
                            $lastDateReturned = $activity->dateTime;
                            if (array_key_exists("restingHeartRate", $activity->value)) {
                                $databaseArray = [
                                    'user' => $this->getActiveUser(),
                                    'date' => (String)$activity->dateTime,
                                    'resting' => (String)$activity->value->restingHeartRate
                                ];
                                foreach ($activity->value->heartRateZones as $heartRateZone) {
                                    if (array_key_exists("minutes", $heartRateZone)) {
                                        nxr(2,
                                            $activity->dateTime . " you spent " . $heartRateZone->minutes . " in " . $heartRateZone->name . " zone");
                                        $key = str_replace(" ", "",
                                            strtolower($heartRateZone->name));
                                        $databaseArray[$key] = (String)$heartRateZone->minutes;
                                        $databaseArray[$key . '_cals'] = (String)$heartRateZone->caloriesOut;
                                    } else {
                                        nxr(2,
                                            $activity->dateTime . " does have time spent in '" . $heartRateZone->name . "' zone");
                                    }

                                }

                                if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix",
                                        null, false) . "heartAverage",
                                    [
                                        "AND" => [
                                            'user' => $this->getActiveUser(),
                                            'date' => (String)$activity->dateTime
                                        ]
                                    ])
                                ) {
                                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                            null, false) . "heartAverage", $databaseArray);
                                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                        [
                                            "METHOD" => __METHOD__,
                                            "LINE" => __LINE__
                                        ]);
                                } else {
                                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                            null, false) . "heartAverage", $databaseArray,
                                        [
                                            "AND" => [
                                                'user' => $this->getActiveUser(),
                                                'date' => (String)$activity->dateTime
                                            ]
                                        ]);
                                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                                        [
                                            "METHOD" => __METHOD__,
                                            "LINE" => __LINE__
                                        ]);
                                }
                            } else {
                                nxr(2, $activity->dateTime . " does have a resting heart rate");
                            }
                        }
                    }

                    if (isset($lastDateReturned)) {
                        $this->setLastCleanRun("heart", new DateTime ($lastDateReturned));
                    }
                }

                return $userHeartRateLog;
            } else {
                return "-143";
            }
        } else {
            return $isAllowed;
        }
    }

    /**
     * @param string $targetDate
     *
     * @return mixed
     */
    private function pullBabelWater($targetDate)
    {
        $targetDateTime = new DateTime ($targetDate);
        $userWaterLog = $this->pullBabel('user/-/foods/log/water/date/' . $targetDateTime->format('Y-m-d') . '.json',
            true);

        if (isset($userWaterLog)) {
            if (isset($userWaterLog->summary->water)) {

                if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                        false) . "water", [
                    "AND" => [
                        'user' => $this->getActiveUser(),
                        'date' => $targetDate
                    ]
                ])
                ) {
                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "water", [
                        'id' => $targetDateTime->format("U"),
                        'liquid' => (String)$userWaterLog->summary->water
                    ], ["AND" => ['user' => $this->getActiveUser(), 'date' => $targetDate]]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);
                } else {
                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "water", [
                        'user' => $this->getActiveUser(),
                        'date' => $targetDate,
                        'id' => $targetDateTime->format("U"),
                        'liquid' => (String)$userWaterLog->summary->water
                    ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);
                }

                if (!is_null($this->RewardsSystem)) {
                    $this->RewardsSystem->eventTrigger("RecordedWater", $userWaterLog);
                }

                $this->setLastCleanRun("water", $targetDateTime);
            }
        }

        return $userWaterLog;
    }

    /**
     * @param string $targetDate
     *
     * @return mixed|null|SimpleXMLElement|string
     */
    private function pullBabelSleep($targetDate)
    {
        $targetDateTime = new DateTime ($targetDate);
        $userSleepLog = $this->pullBabel('user/' . $this->getActiveUser() . '/sleep/date/' . $targetDateTime->format('Y-m-d') . '.json',
            true);

        if (isset($userSleepLog) and is_object($userSleepLog) and is_array($userSleepLog->sleep) and count($userSleepLog->sleep) > 0) {
            $loggedSleep = $userSleepLog->sleep[0];
            if ($loggedSleep->logId != 0) {
                if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                        false) . "sleep", ["logId" => (String)$loggedSleep->logId])
                ) {
                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "sleep", [
                        "logId" => (String)$loggedSleep->logId,
                        'awakeningsCount' => (String)$loggedSleep->awakeningsCount,
                        'duration' => (String)$loggedSleep->duration,
                        'efficiency' => (String)$loggedSleep->efficiency,
                        'isMainSleep' => (String)$loggedSleep->isMainSleep,
                        'minutesAfterWakeup' => (String)$loggedSleep->minutesAfterWakeup,
                        'minutesAsleep' => (String)$loggedSleep->minutesAsleep,
                        'minutesAwake' => (String)$loggedSleep->minutesAwake,
                        'minutesToFallAsleep' => (String)$loggedSleep->minutesToFallAsleep,
                        'startTime' => (String)$loggedSleep->startTime,
                        'timeInBed' => (String)$loggedSleep->timeInBed
                    ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);
                }

                if (!$this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                        false) . "sleep_user", [
                    "AND" => [
                        'user' => $this->getActiveUser(),
                        'sleeplog' => (String)$loggedSleep->logId
                    ]
                ])
                ) {
                    $insertData = [
                        'user' => $this->getActiveUser(),
                        'sleeplog' => (String)$loggedSleep->logId,
                        'totalMinutesAsleep' => (String)$userSleepLog->summary->totalMinutesAsleep,
                        'totalSleepRecords' => (String)$userSleepLog->summary->totalSleepRecords,
                        'totalTimeInBed' => (String)$userSleepLog->summary->totalTimeInBed
                    ];

                    if (isset($userSleepLog->summary->stages)) {
                        if (isset($userSleepLog->summary->stages->deep)) {
                            $insertData['deep'] = $userSleepLog->summary->stages->deep;
                        }
                        if (isset($userSleepLog->summary->stages->light)) {
                            $insertData['light'] = $userSleepLog->summary->stages->light;
                        }
                        if (isset($userSleepLog->summary->stages->rem)) {
                            $insertData['rem'] = $userSleepLog->summary->stages->rem;
                        }
                        if (isset($userSleepLog->summary->stages->wake)) {
                            $insertData['wake'] = $userSleepLog->summary->stages->wake;
                        }
                    }

                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "sleep_user", $insertData);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);
                }

                $this->setLastCleanRun("sleep", new DateTime ($targetDate));

                nxr(2, "Sleeplog " . $loggedSleep->logId . " recorded");
            }
        } else {
            $this->setLastCleanRun("sleep", new DateTime ($targetDate), 7);
            $this->setLastrun("sleep");
        }

        return $userSleepLog;

    }

    /**
     * @param string $targetDate
     *
     * @return mixed
     */
    private function pullBabelBody($targetDate)
    {
        $targetDateTime = new DateTime ($targetDate);
        $userBodyLog = $this->pullBabel('user/' . $this->getActiveUser() . '/body/date/' . $targetDateTime->format('Y-m-d') . '.json',
            true);

        if (isset($userBodyLog)) {
            $fallback = false;
            $currentDate = new DateTime ();
            if ($currentDate->format("Y-m-d") == $targetDate and ($userBodyLog->body->weight == "0" OR $userBodyLog->body->fat == "0" OR
                    $userBodyLog->body->bmi == "0" OR (isset($userBodyLog->goals) AND ((isset($userBodyLog->goals->weight) AND $userBodyLog->goals->weight == "0") OR
                            (isset($userBodyLog->goals->fat) AND $userBodyLog->goals->fat == "0"))))
            ) {
                $this->getAppClass()->addCronJob($this->getActiveUser(), "body");
                $fallback = true;
            }

            $insertToDB = false;
            if (!isset($userBodyLog->body->weight) or $userBodyLog->body->weight == "0") {
                nxr(0, '  Weight unrecorded, reverting to previous record');
                $weight = $this->getDBCurrentBody($this->getActiveUser(), "weight");
                $fallback = true;
            } else {
                $weight = (float)$userBodyLog->body->weight;
                $insertToDB = true;
            }

            if (!isset($userBodyLog->body->fat) or $userBodyLog->body->fat == "0") {
                nxr(0, '  Body Fat unrecorded, reverting to previous record');
                $fat = $this->getDBCurrentBody($this->getActiveUser(), "fat");
                $fallback = true;
            } else {
                $fat = (float)$userBodyLog->body->fat;
                $insertToDB = true;
            }

            if ($insertToDB) {
                if (!isset($userBodyLog->goals->weight) or $userBodyLog->goals->weight == "0") {
                    nxr(0, '  Weight Goal unset, reverting to previous record');
                    $goalsweight = $this->getDBCurrentBody($this->getActiveUser(), "weightGoal");
                } else {
                    $goalsweight = (float)$userBodyLog->goals->weight;
                }

                if (!isset($userBodyLog->goals->fat) or $userBodyLog->goals->fat == "0") {
                    nxr(0, '  Body Fat Goal unset, reverting to previous record');
                    $goalsfat = $this->getDBCurrentBody($this->getActiveUser(), "fatGoal");
                } else {
                    $goalsfat = (float)$userBodyLog->goals->fat;
                }

                $user_height = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "users", "height", ["fuid" => $this->getActiveUser()]);
                if (is_numeric($user_height) AND $user_height > 0) {
                    $user_height = $user_height / 100;
                    $bmi = round($weight / ($user_height * $user_height), 2);
                } else {
                    $bmi = "0.0";
                }

                $db_insetArray = [
                    "weight" => $weight,
                    "weightGoal" => $goalsweight,
                    "fat" => $fat,
                    "fatGoal" => $goalsfat,
                    "bmi" => $bmi
                ];

                $lastWeight = $this->getDBCurrentBody($this->getActiveUser(), "weight");
                $lastFat = $this->getDBCurrentBody($this->getActiveUser(), "fat");
                if ($lastWeight != $weight) {
                    $db_insetArray['weightAvg'] = round(($weight - $lastWeight) / 10, 1,
                            PHP_ROUND_HALF_UP) + $lastWeight;
                } else {
                    $db_insetArray['weightAvg'] = $this->getDBCurrentBody($this->getActiveUser(), "weightAvg");
                }
                if ($lastFat != $fat) {
                    $db_insetArray['fatAvg'] = round(($fat - $lastFat) / 10, 1, PHP_ROUND_HALF_UP) + $lastFat;
                } else {
                    $db_insetArray['fatAvg'] = $this->getDBCurrentBody($this->getActiveUser(), "fatAvg");
                }

                if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                        false) . "body", [
                    "AND" => [
                        'user' => $this->getActiveUser(),
                        'date' => $targetDate
                    ]
                ])
                ) {
                    $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "body", $db_insetArray, [
                        "AND" => [
                            'user' => $this->getActiveUser(),
                            'date' => $targetDate
                        ]
                    ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);
                } else {
                    $db_insetArray['user'] = $this->getActiveUser();
                    $db_insetArray['date'] = $targetDate;
                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "body", $db_insetArray);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);
                }

                if ($currentDate->format("Y-m-d") == $targetDate && !is_null($this->RewardsSystem)) {
                    $this->RewardsSystem->eventTrigger("BodyWeight", [$weight, $goalsweight, $lastWeight]);
                    $this->RewardsSystem->eventTrigger("BodyFat", [$fat, $goalsfat, $lastFat]);
                }

                if (!$fallback) {
                    $this->setLastCleanRun("body", new DateTime ($targetDate));
                }
            } else {
                $currentDate = new DateTime();
                $daysSinceReading = (strtotime($currentDate->format("Y-m-d")) - strtotime($targetDateTime->format('Y-m-d'))) / (60 * 60 * 24);
                nxr(3,
                    "No recorded data for " . $targetDateTime->format('Y-m-d') . " " . $daysSinceReading . " days ago");
                if ($daysSinceReading > 7) {
                    $this->setLastCleanRun("body", new DateTime ($targetDate));
                }
            }
        }

        return $userBodyLog;

    }

    /**
     * @param string $user
     * @param string $string
     *
     * @todo Consider test case
     * @return bool|int
     */
    public function getDBCurrentBody($user, $string)
    {
        if (!$user) {
            return "No default user selected";
        }

        $return = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null,
                false) . "body", $string, [
            "user" => $user,
            "ORDER" => ["date" => "DESC"]
        ]);

        if (!is_numeric($return)) {
            return 0;
        } else {
            return $return;
        }
    }

    /**
     * @param string $targetDate
     *
     * @return mixed
     */
    private function pullBabelMeals($targetDate)
    {
        $targetDateTime = new DateTime ($targetDate);
        $userFoodLog = $this->pullBabel('user/' . $this->getActiveUser() . '/foods/log/date/' . $targetDateTime->format('Y-m-d') . '.json',
            true);

        if (isset($userFoodLog)) {
            if (count($userFoodLog->foods) > 0) {
                foreach ($userFoodLog->foods as $meal) {
                    nxr(2, "Logging meal " . $meal->loggedFood->name);

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "food", [
                        "AND" => [
                            'user' => $this->getActiveUser(),
                            'date' => $targetDate,
                            'meal' => (String)$meal->loggedFood->name
                        ]
                    ])
                    ) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "food", [
                            'calories' => (String)$meal->nutritionalValues->calories,
                            'carbs' => (String)$meal->nutritionalValues->carbs,
                            'fat' => (String)$meal->nutritionalValues->fat,
                            'fiber' => (String)$meal->nutritionalValues->fiber,
                            'protein' => (String)$meal->nutritionalValues->protein,
                            'sodium' => (String)$meal->nutritionalValues->sodium
                        ], [
                            "AND" => [
                                'user' => $this->getActiveUser(),
                                'date' => $targetDate,
                                'meal' => (String)$meal->loggedFood->name
                            ]
                        ]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    } else {
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "food", [
                            'user' => $this->getActiveUser(),
                            'date' => $targetDate,
                            'meal' => (String)$meal->loggedFood->name,
                            'calories' => (String)$meal->nutritionalValues->calories,
                            'carbs' => (String)$meal->nutritionalValues->carbs,
                            'fat' => (String)$meal->nutritionalValues->fat,
                            'fiber' => (String)$meal->nutritionalValues->fiber,
                            'protein' => (String)$meal->nutritionalValues->protein,
                            'sodium' => (String)$meal->nutritionalValues->sodium
                        ]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    }

                    if (!is_null($this->RewardsSystem)) {
                        $this->RewardsSystem->eventTrigger("RecordedMeal", $meal);
                    }

                    $this->setLastCleanRun("foods", $targetDateTime);
                }
            } else {
                $currentDate = new DateTime();
                $daysSinceReading = (strtotime($currentDate->format("Y-m-d")) - strtotime($targetDateTime->format('Y-m-d'))) / (60 * 60 * 24);
                nxr(3,
                    "No recorded data for " . $targetDateTime->format('Y-m-d') . " " . $daysSinceReading . " days ago");
                if ($daysSinceReading > 7) {
                    $this->setLastCleanRun("foods", $targetDateTime);
                }
            }
        }

        return $userFoodLog;
    }

    /**
     * @param string $trigger
     * @param bool $force
     *
     * @return string|bool
     */
    private function pullBabelTimeSeries($trigger, $force = false)
    {
        if ($force || $this->isTriggerCooled($trigger)) {
            $currentDate = new DateTime();

            $lastrun = $this->getLastCleanRun($trigger);
            $daysSince = (strtotime($currentDate->format("Y-m-d")) - strtotime($lastrun->format("l jS M Y"))) / (60 * 60 * 24);

            nxr(2, "Last download: $daysSince days ago. ");

            $allRecords = false;
            if ($daysSince < 8) {
                $daysSince = "7d";
            } else if ($daysSince < 30) {
                $daysSince = "30d";
            } else if ($daysSince < 90) {
                $daysSince = "3m";
            } else if ($daysSince < 180) {
                $daysSince = "6m";
            } else if ($daysSince < 364) {
                $daysSince = "1y";
            } else {
                $allRecords = true;
                $daysSince = "1y";
                $lastrun->add(new DateInterval('P360D'));
            }

            if ($allRecords) {
                nxr(2, "Requesting $trigger data for $daysSince days");
                $this->pullBabelTimeSeriesByTrigger($trigger, $daysSince, $lastrun);
            } else {
                nxr(2, "Requesting $trigger data for $daysSince days");
                $this->pullBabelTimeSeriesByTrigger($trigger, $daysSince);
                $this->setLastrun($trigger);
            }
        } else {
            if (!IS_CRON_RUN) {
                nxr(3, "Error " . $trigger . ": " . $this->getAppClass()->lookupErrorCode(-143));
            }
        }

        return true;
    }

    /**
     * @param string $trigger
     * @param string $daysSince
     * @param DateTime|null $lastrun
     *
     * @return string|bool
     */
    private function pullBabelTimeSeriesByTrigger($trigger, $daysSince, $lastrun = null)
    {
        switch ($trigger) {
            case "steps":
            case "distance":
            case "floors":
            case "elevation":
            case "caloriesOut":
                $this->pullBabelTimeSeriesForSteps($trigger, $daysSince, $lastrun);
                break;
            case "minutesVeryActive":
            case "minutesSedentary":
            case "minutesLightlyActive":
            case "minutesFairlyActive":
                $this->pullBabelTimeSeriesForActivity($trigger, $daysSince, $lastrun);
                break;
        }

        return true;
    }

    /**
     * @param string $trigger
     * @param string $daysSince
     * @param DateTime|null $lastrun
     *
     * @return string|bool
     */
    private function pullBabelTimeSeriesForSteps($trigger, $daysSince, $lastrun = null)
    {
        if (!is_null($lastrun)) {
            $currentDate = $lastrun;
        } else {
            $currentDate = new DateTime ('now');
        }

        nxr(0,
            '   Get ' . $this->getAppClass()->supportedApi($trigger) . ' records from ' . $currentDate->format("Y-m-d"));
        $userTimeSeries = $this->getTimeSeries($trigger, $currentDate, $daysSince);

        if (isset($userTimeSeries) and is_array($userTimeSeries)) {
            $FirstSeen = $this->getUserFirstSeen()->format("Y-m-d");

            foreach ($userTimeSeries as $steps) {
                if (strtotime($steps->dateTime) >= strtotime($FirstSeen)) {
                    if ($steps->value == 0) {
                        $currentDate = new DateTime();
                        $daysSinceReading = (strtotime($currentDate->format("Y-m-d")) - strtotime($steps->dateTime)) / (60 * 60 * 24);
                        nxr(4, "No recorded data for " . $steps->dateTime . " " . $daysSinceReading . " days ago");
                        if ($daysSinceReading > 180) {
                            $this->setLastCleanRun($trigger, new DateTime ($steps->dateTime));
                        }
                    } else {
                        nxr(4,
                            $this->getAppClass()->supportedApi($trigger) . " record for " . $steps->dateTime . " is " . $steps->value);
                    }

                    if ($steps->value > 0) {
                        $this->setLastCleanRun($trigger, new DateTime ($steps->dateTime));
                    }

                    $dbValues = [
                        $trigger => (String)$steps->value,
                        'syncd' => $currentDate->format('Y-m-d H:m:s')
                    ];

                    if ($trigger == "steps" || $trigger == "floors" || $trigger == "distance") {
                        $steps_goals = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "steps_goals",
                            [$trigger, "date"], [
                                "AND" => [
                                    "user" => $this->getActiveUser(),
                                    "date" => $steps->dateTime
                                ]
                            ]);

                        if (!is_numeric($steps_goals[$trigger])) {
                            $steps_goals = $this->getAppClass()->getUserSetting($this->getActiveUser(),
                                "goal_" . $trigger);
                        } else {
                            $steps_goals = $steps_goals[$trigger];
                        }

                        if ($steps->value >= $steps_goals) {
                            $dbValues[$trigger . '_g'] = 1;
                            if ($trigger == "steps") {
                                $this->checkGoalStreak($steps->dateTime, $trigger, true);
                            }
                        } else if ($steps->value < $steps_goals && strtotime($currentDate->format("Y-m-d")) > strtotime($steps->dateTime)) {
                            $dbValues[$trigger . '_g'] = 0;
                            if ($trigger == "steps") {
                                $this->checkGoalStreak($steps->dateTime, $trigger, false);
                            }
                        }
                    }

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "steps", [
                        "AND" => [
                            'user' => $this->getActiveUser(),
                            'date' => (String)$steps->dateTime
                        ]
                    ])
                    ) {
                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "steps", $dbValues, [
                            "AND" => [
                                'user' => $this->getActiveUser(),
                                'date' => (String)$steps->dateTime
                            ]
                        ]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    } else {
                        $dbValues['user'] = $this->getActiveUser();
                        $dbValues['date'] = (String)$steps->dateTime;
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "steps", $dbValues);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    }

                    if (!is_null($this->RewardsSystem)) {
                        $this->RewardsSystem->eventTrigger("FitbitTracker", [$steps->dateTime, $trigger, $steps->value]);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Launch TimeSeries requests
     * Allowed types are:
     *            'caloriesIn', 'water'
     *            'caloriesOut', 'steps', 'distance', 'floors', 'elevation'
     *            'minutesSedentary', 'minutesLightlyActive', 'minutesFairlyActive', 'minutesVeryActive',
     *            'activityCalories',
     *            'tracker_caloriesOut', 'tracker_steps', 'tracker_distance', 'tracker_floors', 'tracker_elevation'
     *            'startTime', 'timeInBed', 'minutesAsleep', 'minutesAwake', 'awakeningsCount',
     *            'minutesToFallAsleep', 'minutesAfterWakeup',
     *            'efficiency'
     *            'weight', 'bmi', 'fat'
     *
     * @param string $type
     * @param string|DateTime $baseDate DateTime or 'today', to_period
     * @param string|DateTime $to_period DateTime or '1d, 7d, 30d, 1w, 1m, 3m, 6m, 1y, max'
     *
     * @todo Consider test case
     * @return array|boolean
     */
    public function getTimeSeries($type, $baseDate, $to_period)
    {
        switch ($type) {
            case 'caloriesIn':
                $path = '/foods/log/caloriesIn';
                break;
            case 'water':
                $path = '/foods/log/water';
                break;

            case 'caloriesOut':
                $path = '/activities/calories';
                break;
            case 'steps':
                $path = '/activities/steps';
                break;
            case 'distance':
                $path = '/activities/distance';
                break;
            case 'floors':
                $path = '/activities/floors';
                break;
            case 'elevation':
                $path = '/activities/elevation';
                break;
            case 'minutesSedentary':
                $path = '/activities/minutesSedentary';
                break;
            case 'minutesLightlyActive':
                $path = '/activities/minutesLightlyActive';
                break;
            case 'minutesFairlyActive':
                $path = '/activities/minutesFairlyActive';
                break;
            case 'minutesVeryActive':
                $path = '/activities/minutesVeryActive';
                break;
            case 'activityCalories':
                $path = '/activities/activityCalories';
                break;

            case 'tracker_caloriesOut':
                $path = '/activities/log/tracker/calories';
                break;
            case 'tracker_steps':
                $path = '/activities/log/tracker/steps';
                break;
            case 'tracker_distance':
                $path = '/activities/log/tracker/distance';
                break;
            case 'tracker_floors':
                $path = '/activities/log/tracker/floors';
                break;
            case 'tracker_elevation':
                $path = '/activities/log/tracker/elevation';
                break;

            case 'startTime':
                $path = '/sleep/startTime';
                break;
            case 'timeInBed':
                $path = '/sleep/timeInBed';
                break;
            case 'minutesAsleep':
                $path = '/sleep/minutesAsleep';
                break;
            case 'awakeningsCount':
                $path = '/sleep/awakeningsCount';
                break;
            case 'minutesAwake':
                $path = '/sleep/minutesAwake';
                break;
            case 'minutesToFallAsleep':
                $path = '/sleep/minutesToFallAsleep';
                break;
            case 'minutesAfterWakeup':
                $path = '/sleep/minutesAfterWakeup';
                break;
            case 'efficiency':
                $path = '/sleep/efficiency';
                break;

            case 'weight':
                $path = '/body/weight';
                break;
            case 'bmi':
                $path = '/body/bmi';
                break;
            case 'fat':
                $path = '/body/fat';
                break;

            default:
                return false;
        }

        $response = $this->pullBabel('user/' . $this->getActiveUser() . $path . '/date/' . (is_string($baseDate) ? $baseDate : $baseDate->format('Y-m-d')) . "/" . (is_string($to_period) ? $to_period : $to_period->format('Y-m-d')) . '.json',
            true);

        switch ($type) {
            case 'caloriesOut':
                $objectKey = "activities-calories";
                break;
            default:
                $objectKey = "activities-" . $type;
                break;
        }

        $response = $response->$objectKey;

        return $response;
    }

    /**
     * @param string $dateTime
     * @param string $goal
     * @param boolean $value
     */
    private function checkGoalStreak($dateTime, $goal, $value)
    {
        //$todaysDate = new DateTime ( 'now' );
        $dateTime = new DateTime ($dateTime);

        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        if ($this->getAppClass()->getDatabase()->has($db_prefix . "streak_goal", [
            "AND" => [
                "fuid" => $this->getActiveUser(),
                "goal" => $goal,
                "end_date" => null
            ]
        ])
        ) {
            $streak = true;
            $streak_start = $this->getAppClass()->getDatabase()->get($db_prefix . "streak_goal", "start_date",
                [
                    "AND" => [
                        "fuid" => $this->getActiveUser(),
                        "goal" => $goal,
                        "end_date" => null
                    ]
                ]);
        } else {
            $streak = false;
            $streak_start = $dateTime->format("Y-m-d");
        }

        if (strtotime($dateTime->format("Y-m-d")) >= strtotime($streak_start)) {
            if ($streak) {
                if ($value) {
                    $dateTimeStart = new DateTime ($streak_start);
                    $days_between = $dateTimeStart->diff($dateTime)->format("%a");
                    $days_between = (int)$days_between + 1;

                    $this->getAppClass()->getDatabase()->update($db_prefix . "streak_goal", [
                        "length" => $days_between
                    ],
                        [
                            "AND" => [
                                "fuid" => $this->getActiveUser(),
                                "goal" => $goal,
                                "start_date" => $streak_start
                            ]
                        ]
                    );
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);

                    if (strtotime($dateTime->format("Y-m-d")) >= strtotime($streak_start)) {
                        if (!is_null($this->RewardsSystem)) {
                            $this->RewardsSystem->eventTrigger('FitbitStreak', [$goal, $days_between, $streak_start]);
                        }
                    }

                    nxr(5, "Steak started on $streak_start on will continue");
                } else {
                    $dateTimeEnd = $dateTime;
                    $dateTimeEnd->add(DateInterval::createFromDateString('yesterday'));
                    $streak_end = $dateTimeEnd->format('Y-m-d');

                    $days_between = $dateTime->diff($dateTimeEnd)->format("%a");
                    $days_between = 1 + (int)$days_between;

                    $this->getAppClass()->getDatabase()->update($db_prefix . "streak_goal", [
                        "end_date" => $streak_end,
                        "length" => $days_between
                    ],
                        [
                            "AND" => [
                                "fuid" => $this->getActiveUser(),
                                "goal" => $goal,
                                "start_date" => $streak_start
                            ]
                        ]
                    );
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                        "METHOD" => __METHOD__,
                        "LINE" => __LINE__
                    ]);

                    if (!is_null($this->RewardsSystem)) {
                        $this->RewardsSystem->eventTrigger('FitbitStreak', [$goal, $days_between, $streak_start]);
                    }

                    nxr(5, "Steak started on $streak_start, but as ended on " . $streak_end);
                }
            } else {
                if ($value) {
                    if (!$this->getAppClass()->getDatabase()->has($db_prefix . "streak_goal", [
                        "AND" => [
                            "fuid" => $this->getActiveUser(),
                            "goal" => $goal,
                            "start_date" => $dateTime->format("Y-m-d")
                        ]
                    ])
                    ) {
                        $this->getAppClass()->getDatabase()->insert($db_prefix . "streak_goal", [
                            "fuid" => $this->getActiveUser(),
                            "goal" => $goal,
                            "start_date" => $dateTime->format("Y-m-d"),
                            "end_date" => null,
                            "length" => 1
                        ]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    }

                    if (strtotime($dateTime->format("Y-m-d")) >= strtotime($streak_start)) {
                        if (!is_null($this->RewardsSystem)) {
                            $this->RewardsSystem->eventTrigger('FitbitStreak', [$goal, 1, $streak_start]);
                        }
                    }

                    nxr(5, "No running streak, but a new one has started");
                }
            }
        }

    }

    /**
     * @param string $trigger
     * @param string $daysSince
     * @param DateTime|null $lastrun
     *
     * @return bool
     */
    private function pullBabelTimeSeriesForActivity($trigger, $daysSince, $lastrun = null)
    {

        switch ($trigger) {
            case "minutesVeryActive":
                $databaseColumn = "veryactive";
                break;
            case "minutesSedentary":
                $databaseColumn = "sedentary";
                break;
            case "minutesLightlyActive":
                $databaseColumn = "lightlyactive";
                break;
            case "minutesFairlyActive":
                $databaseColumn = "fairlyactive";
                break;
            default:
                return false;
        }

        if (!is_null($lastrun)) {
            $currentDate = $lastrun;
        } else {
            $currentDate = new DateTime ('now');
        }

        nxr(0,
            '   Get ' . $this->getAppClass()->supportedApi($trigger) . ' records ' . $currentDate->format("Y-m-d"));
        $userTimeSeries = $this->getTimeSeries($trigger, $currentDate, $daysSince);

        if (isset($userTimeSeries) and is_array($userTimeSeries)) {
            if (!isset($this->holdingVar) OR !array_key_exists("type",
                    $this->holdingVar) OR !array_key_exists("data",
                    $this->holdingVar) OR $this->holdingVar["type"] != "activities/goals/daily.json" OR $this->holdingVar["data"] == ""
            ) {
                if (isset($this->holdingVar)) {
                    unset($this->holdingVar);
                }
                $this->holdingVar = ["type" => "activities/goals/daily.json", "data" => ""];
                $this->holdingVar["data"] = $this->pullBabel('user/-/activities/goals/daily.json', true);

                if ($trigger == "minutesVeryActive") {
                    $newGoal = $this->thisWeeksGoal("activeMinutes",
                        $this->holdingVar["data"]->goals->activeMinutes);
                    if ($newGoal > 0 && $this->holdingVar["data"]->goals->activeMinutes != $newGoal) {
                        nxr(4,
                            "Returned activity target was " . $this->holdingVar["data"]->goals->activeMinutes . " but I think it should be " . $newGoal);
                        $this->pushBabel('user/-/activities/goals/daily.json', ['activeMinutes' => $newGoal]);
                    } else if ($newGoal > 0) {
                        nxr(4,
                            "Returned activity target was " . $this->holdingVar["data"]->goals->activeMinutes . " which is right for this week goal of " . $newGoal);
                    }
                }
            }

            $FirstSeen = $this->getUserFirstSeen()->format("Y-m-d");
            $todaysDate = new DateTime ('now');
            foreach ($userTimeSeries as $series) {
                if (strtotime($series->dateTime) >= strtotime($FirstSeen)) {
                    nxr(4,
                        $this->getAppClass()->supportedApi($trigger) . " " . $series->dateTime . " is " . $series->value);

                    if ($series->value > 0) {
                        $this->setLastCleanRun($trigger, new DateTime ($series->dateTime));
                    }

                    if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "activity", [
                        "AND" => [
                            'user' => $this->getActiveUser(),
                            'date' => (String)$series->dateTime
                        ]
                    ])
                    ) {
                        $dbStorage = [
                            $databaseColumn => (String)$series->value,
                            'syncd' => $currentDate->format('Y-m-d H:m:s')
                        ];

                        if ($currentDate->format("Y-m-d") == $series->dateTime) {
                            $dbStorage['target'] = (String)$this->holdingVar["data"]->goals->activeMinutes;
                        }

                        $this->getAppClass()->getDatabase()->update($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "activity", $dbStorage, [
                            "AND" => [
                                'user' => $this->getActiveUser(),
                                'date' => (String)$series->dateTime
                            ]
                        ]);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    } else {
                        $dbStorage = [
                            'user' => $this->getActiveUser(),
                            'date' => (String)$series->dateTime,
                            $databaseColumn => (String)$series->value,
                            'syncd' => $currentDate->format('Y-m-d H:m:s')
                        ];
                        if ($currentDate->format("Y-m-d") == $series->dateTime) {
                            $dbStorage['target'] = (String)$this->holdingVar["data"]->goals->activeMinutes;
                        }
                        $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix",
                                null, false) . "activity", $dbStorage);
                        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                            [
                                "METHOD" => __METHOD__,
                                "LINE" => __LINE__
                            ]);
                    }

                    if ($databaseColumn == "veryactive" && strtotime($series->dateTime) < strtotime($todaysDate->format('Y-m-d'))) {
                        if (!is_null($this->RewardsSystem)) {
                            $this->RewardsSystem->eventTrigger("FitbitVeryActive", $series->value);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @todo     Consider test case
     * @return bool
     * @internal param bool $forceSync
     */
    public function getForceSync()
    {
        return $this->forceSync;
    }

    /**
     * @param boolean $forceSync
     *
     * @todo Consider test case
     */
    public function setForceSync($forceSync)
    {
        $this->forceSync = $forceSync;
    }

    /**
     * @param string $_nx_fb_usr
     *
     * @todo Consider test case
     * @return bool
     */
    public function validateOAuth($_nx_fb_usr)
    {
        $this->setActiveUser($_nx_fb_usr);

        try {
            // Try to get an access token using the authorization code grant.
            $accessToken = $this->getAccessToken();

            $request = $this->getLibrary()->getResourceOwner($accessToken);
            if ($request->getId() == $_nx_fb_usr) {
                return true;
            } else {
                nxr(1, "User miss match " . $request->getId() . " should equal " . $_nx_fb_usr);

                return false;
            }

        } catch (IdentityProviderException $e) {
            // Failed to get the access token or user details.
            $this->getAppClass()->getErrorRecording()->captureException($e, [
                'extra' => [
                    'php_version' => phpversion(),
                    'core_version' => $this->getAppClass()->getSetting("version", "0.0.0.1", true)
                ],
            ]);
            nxr(1, "User validation test failed: " . print_r($e->getMessage(), true));
            if ($e->getCode() == 400) {
                $this->getAppClass()->delUserOAuthTokens($_nx_fb_usr);
            }

            return false;
        }
    }

    /**
     * @param mixed $newUserProfile
     *
     * @todo Consider test case
     * @return bool
     */
    public function createNewUser($newUserProfile)
    {
        if ($this->getAppClass()->isUser($newUserProfile->encodedId)) {
            nxr(0, "User already present");

            return false;
        } else {

            $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null,
                    false) . "users", [
                'fuid' => $newUserProfile->encodedId,
                'group' => 'user',
                'api' => $newUserProfile->encodedId,
                'name' => $newUserProfile->fullName,
                'dob' => $newUserProfile->dateOfBirth,
                'avatar' => $newUserProfile->avatar150,
                'seen' => $newUserProfile->memberSince,
                'lastrun' => $newUserProfile->memberSince,
                'gender' => $newUserProfile->gender,
                'height' => $newUserProfile->height,
                'stride_running' => $newUserProfile->strideLengthRunning,
                'stride_walking' => $newUserProfile->strideLengthWalking,
                'country' => $newUserProfile->country,
                'tkn_access' => $this->getAccessToken()->getToken(),
                'tkn_refresh' => $this->getAccessToken()->getRefreshToken(),
                'tkn_expires' => $this->getAccessToken()->getExpires(),
                'rank' => 0,
                'friends' => 0,
                'distance' => 0,
            ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            return true;
        }
    }

}
