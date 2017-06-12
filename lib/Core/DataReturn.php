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

use Core\Analytics\UserAnalytics;
use DateInterval;
use DatePeriod;
use DateTime;

/**
 * DataReturn
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-DataReturn phpDocumentor
 *            wiki for dataReturn.
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/DataReturn DataReturn Wiki.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class DataReturn
{

    /**
     * @var Core
     */
    protected $AppClass;

    /**
     * @var String
     */
    protected $UserID;
    protected $forCache;
    /**
     * @var String
     */
    protected $paramDate;
    /**
     * @var String
     */
    protected $paramPeriod;
    /**
     * @var UserAnalytics
     */
    protected $tracking;

    /**
     * @param string $userFid
     *
     * @todo Consider test case
     */
    public function __construct($userFid)
    {
        $this->setAppClass(new Core());
        $this->setUserID($userFid);
        $this->setForCache(true);

        if (is_array($_SERVER) && array_key_exists("SERVER_NAME", $_SERVER)) {
            $this->setTracking(new UserAnalytics($this->getAppClass()->getSetting("trackingId"),
                $this->getAppClass()->getSetting("trackingPath")));
        }
    }

    /**
     * @param Core $paramClass
     */
    private function setAppClass($paramClass)
    {
        $this->AppClass = $paramClass;
    }

    /**
     * @return Core
     */
    private function getAppClass()
    {
        return $this->AppClass;
    }

    /**
     * @todo Consider test case
     * @return bool
     */
    public function isUser()
    {
        return $this->getAppClass()->isUser((String)$this->getUserID());
    }

    /**
     * @todo Consider test case
     * @return String
     */
    public function getUserID()
    {
        return $this->UserID;
    }

    /**
     * @param String $UserID
     *
     * @todo Consider test case
     */
    public function setUserID($UserID)
    {
        $this->UserID = $UserID;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordAboutMe()
    {
        $dbSteps = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps",
            ['steps'],
            ["user" => $this->getUserID()]
        );
        $dbFloors = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps",
            ['floors'],
            ["user" => $this->getUserID()]
        );
        $dbDistance = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps",
            ['distance'],
            ["user" => $this->getUserID()]
        );

        $yearThis = date("Y");
        $yearLast = date("Y") - 1;
        $dbStepsYearThis = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps",
            ['steps'],
            ["AND" => ["user" => $this->getUserID(), "date[~]" => $yearThis . "%"]]
        );

        $dbStepsYearLast = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps",
            ['steps'],
            ["AND" => ["user" => $this->getUserID(), "date[~]" => $yearLast . "%"]]
        );

        return [
            "steps" => round($dbSteps, 0),
            "floors" => round($dbFloors, 0),
            "distance" => round($dbDistance, 0),
            "stepsThisYear" => round($dbStepsYearThis, 0),
            "stepsLastYear" => round($dbStepsYearLast, 0),
        ];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordGeoSecure()
    {
        if (
            (array_key_exists('_nx_fb_usr', $_COOKIE) && $_COOKIE['_nx_fb_usr'] != $_GET['user']) || !array_key_exists('_nx_fb_usr', $_COOKIE)
        ) {
            return array();
        } else {
            return json_decode($this->getAppClass()->getUserSetting($this->getUserID(), "geo_private", array()), true);
        }
    }

    /**
     * @todo Consider test case
     * @return bool
     */
    public function returnUserRecordActivity()
    {
        $userActivity = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null,
                false) . "activity",
            ['sedentary', 'lightlyactive', 'fairlyactive', 'veryactive'],
            $this->dbWhere());

        $userActivity['total'] = $userActivity['sedentary'] + $userActivity['lightlyactive'] + $userActivity['fairlyactive'] + $userActivity['veryactive'];

        return $userActivity;
    }

    /**
     * @param int $limit
     * @param string $tableName
     *
     * @todo Consider test case
     * @return array
     */
    public function dbWhere($limit = 1, $tableName = '')
    {
        if ($limit < 1) {
            $limit = 1;
        }
        if ($tableName != "") {
            $tableName = $tableName . ".";
        }

        if ($this->getParamPeriod() == "single") {
            return [
                "AND" => [
                    $tableName . "user" => $this->getUserID(),
                    $tableName . "date" => $this->getParamDate()
                ]
            ];
        } else if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
            $days = $this->getParamPeriod();
            $days = str_ireplace("last", "", $days);
            $then = date('Y-m-d', strtotime($this->getParamDate() . " -" . $days . " day"));

            return [
                "AND" => [
                    $tableName . "user" => $this->getUserID(),
                    $tableName . "date[<=]" => $this->getParamDate(),
                    $tableName . "date[>=]" => $then
                ],
                "ORDER" => [$tableName . "date" => "DESC"],
                "LIMIT" => $days
            ];
        } else {
            return [
                $tableName . "user" => $this->getUserID(),
                "ORDER" => $tableName . "date DESC",
                "LIMIT" => $limit
            ];
        }
    }

    /**
     * @todo Consider test case
     * @return String
     */
    public function getParamPeriod()
    {
        if (is_null($this->paramPeriod)) {
            $this->paramPeriod = "single";
        } else if ($this->paramPeriod == "all") {
            $dbUserFirstSeen = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "users", 'seen', ["fuid" => $this->getUserID()]);
            $now = time(); // or your date as well
            $your_date = strtotime($dbUserFirstSeen);
            $datediff = $now - $your_date;
            $this->paramPeriod = "last" . floor($datediff / (60 * 60 * 24));
        }

        return $this->paramPeriod;
    }

    /**
     * @todo Consider test case
     *
     * @param String $paramPeriod
     */
    public function setParamPeriod($paramPeriod)
    {
        $this->paramPeriod = $paramPeriod;
    }

    /**
     * @todo Consider test case
     * @return String
     */
    public function getParamDate()
    {
        if (is_null($this->paramDate) || $this->paramDate == "latest") {
            $this->paramDate = date('Y-m-d');
        }

        return $this->paramDate;
    }

    /**
     * @todo Consider test case
     *
     * @param String $paramDate
     */
    public function setParamDate($paramDate)
    {
        $this->paramDate = $paramDate;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordActivityHistory()
    {
        if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
            $days = $this->getParamPeriod();
            $sqlLimit = str_ireplace("last", "", $days);
        } else {
            $sqlLimit = 1;
        }

        // AND `startDate` >= '2016-06-24' AND `startDate` <= '2016-06-26'

        $sqlQueryString = "SELECT `activityName` as `name`,`logId`,`startDate`,`startTime`,`calories`,`activeDuration` as `duration`,`steps`, "
            . "`activityLevelSedentary` as `sedentary`, `activityLevelLightly` as `lightly`, `activityLevelFairly` as `fairly`, `activityLevelVery` as `very`, `sourceType`, `sourceName` "
            . "FROM `" . $this->getAppClass()->getSetting("db_prefix", null, false) . "activity_log` "
            . "WHERE `user` = '" . $this->getUserID() . "' AND `sourceType` IS NOT NULL ";

        if (is_array($_GET) && array_key_exists("start", $_GET) && array_key_exists("end", $_GET)) {
            $sqlQueryString .= "AND `startDate` >= '" . $_GET['start'] . "' AND `startDate` <= '" . $_GET['end'] . "' ";
        } else if (is_array($_GET) && array_key_exists("start", $_GET)) {
            $sqlQueryString .= "AND `startDate` >= '" . $_GET['start'] . "' ";
        } else if (is_array($_GET) && array_key_exists("end", $_GET)) {
            $sqlQueryString .= "AND `startDate` <= '" . $_GET['end'] . "' ";
        }

        $sqlQueryString .= "ORDER BY `startDate` DESC, `startTime` DESC LIMIT " . $sqlLimit;

        $userActivity = $this->getAppClass()->getDatabase()->query($sqlQueryString)->fetchAll();
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $daysStats = [];
        $returnArray = [];
        foreach ($userActivity as $record) {
            $record['source'] = ["type" => $record['sourceType'], "name" => $record['sourceName']];
            $record['activityLevel'] = [
                "sedentary" => $record['sedentary'],
                "lightly" => $record['lightly'],
                "fairly" => $record['fairly'],
                "very" => $record['very']
            ];

            $startTime = new DateTime($record['startDate'] . " " . $record['startTime']);
            $recKey = $startTime->format("F, Y");
            if (!array_key_exists($recKey, $returnArray) || !is_array($returnArray[$recKey])) {
                $returnArray[$recKey] = [];
            }

            if (substr($record['name'], 0, 6) === "Skiing") {
                $record['name'] = "Skiing";
            } else if (substr($record['name'], 0, 7) === "Sit-ups" || substr($record['name'], 0,
                    12) === "Calisthenics"
            ) {
                $record['name'] = "Calisthenics (pushups, sit-ups, squats)";
            } else {
                $record['name'] = str_ireplace(" (MyFitnessPal)", "", $record['name']);
            }
            $endTime = date("U", strtotime($record['startDate'] . " " . $record['startTime']));
            $endTime = $endTime + ($record['duration'] / 1000);
            $record['endTime'] = date("F dS \@H:i", $endTime);
            $record['duration'] = round(($record['duration'] / 1000) / 60, 0, PHP_ROUND_HALF_UP);
            $record['startTime'] = date("F dS \@H:i", strtotime($record['startDate'] . " " . $record['startTime']));

            if ($record['calories'] == 0 || $record['calories'] == 0) {
                $record['calPerMinute'] = 0;
            } else {
                $record['calPerMinute'] = round($record['calories'] / $record['duration'], 1);
            }

            if (strpos(strtolower($record['name']), 'calisthenics') !== false || strpos(strtolower($record['name']),
                    'strength') !== false
            ) {
                $record['colour'] = "teal";
            } else if (strpos(strtolower($record['name']), 'run') !== false || strpos(strtolower($record['name']),
                    'walk') !== false
            ) {
                $record['colour'] = "green";
            } else if (strpos(strtolower($record['name']), 'skiing') !== false) {
                $record['colour'] = "purple";
            } else {
                $record['colour'] = "bricky";
            }

            $record['calories'] = number_format($record['calories'], 0);
            $record['steps'] = number_format($record['steps'], 0);

            if (!array_key_exists($record['startDate'], $daysStats)) {
                $daysStats[$record['startDate']] = [];

                $db_steps = $this->getAppClass()->getSetting("db_prefix", null, false) . "steps";
                $db_activity = $this->getAppClass()->getSetting("db_prefix", null, false) . "activity";
                $dbDaysStatsDb = $this->getAppClass()->getDatabase()->query(
                    "SELECT `$db_steps`.`caloriesOut`,`$db_steps`.`steps`,`$db_activity`.`fairlyactive`,`$db_activity`.`veryactive`"
                    . " FROM `$db_steps`"
                    . " JOIN `$db_activity` ON (`$db_steps`.`date` = `$db_activity`.`date`) AND (`$db_steps`.`user` = `$db_activity`.`user`)"
                    . " WHERE `$db_activity`.`user` = '" . $this->getUserID() . "' AND `$db_activity`.`date` = '" . $record['startDate'] . "'"
                    . " ORDER BY `$db_activity`.`date` DESC");
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                    [
                        "METHOD" => __METHOD__,
                        "LINE" => __LINE__
                    ]);

                foreach ($dbDaysStatsDb as $dbValue) {
                    $daysStats[$record['startDate']]['active'] = number_format($dbValue['fairlyactive'] + $dbValue['veryactive'],
                        0);
                    $daysStats[$record['startDate']]['caloriesOut'] = number_format($dbValue['caloriesOut'], 0);
                    $daysStats[$record['startDate']]['steps'] = number_format($dbValue['steps'], 0);
                }
            }

            $record['stats'] = $daysStats[$record['startDate']];

            unset($record['startDate']);
            unset($record[0]);
            unset($record[1]);
            unset($record[2]);
            unset($record[3]);
            unset($record[4]);
            unset($record[5]);
            unset($record[6]);
            unset($record[7]);
            unset($record[8]);
            unset($record[9]);
            unset($record[10]);
            unset($record[11]);
            unset($record[12]);
            unset($record['sourceType']);
            unset($record['sourceName']);
            unset($record['sedentary']);
            unset($record['lightly']);
            unset($record['fairly']);
            unset($record['very']);

            ksort($record);

            $tcxFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tcx' . DIRECTORY_SEPARATOR . $record['logId'] . '.tcx';
            if (!file_exists($tcxFile)) {
                $record['gpx'] = "none";
            } else {
                $createGPXFile = false;
                if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $record['logId'] . '.gpx')) {
                    if (filemtime(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $record['logId'] . '.gpx') < strtotime('-2 hours')) {
                        $createGPXFile = true;
                    } else {
                        $record['gpx'] = $this->getAppClass()->getSetting("http/") . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $record['logId'] . '.gpx';
                    }
                } else {
                    if (is_writable(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache')) {
                        $createGPXFile = true;
                    } else {
                        $record['gpx'] = "none";
                    }
                }

                if ($createGPXFile) {
                    $record['gpx'] = $this->returnUserRecordActivityTCX($record['logId'], $record['name'] . ": " . $record['startTime']);
                    $record['gpx'] = $record['gpx']['return']['gpx'];
                }

                if (file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $record['logId'] . '_laps.json')) {
                    $str = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $record['logId'] . '_laps.json');
                    $jsonMeta = json_decode($str, true); // decode the JSON into an associative array
                    $record['visibility'] = $jsonMeta['meta']['visibility'];
                } else {
                    $record['visibility'] = "unknown";
                }

                if (
                    (array_key_exists('_nx_fb_usr', $_COOKIE) && $_COOKIE['_nx_fb_usr'] != $_GET['user']) ||
                    (!array_key_exists('_nx_fb_usr', $_COOKIE) && $record['visibility'] != "public")
                ) {
                    $record['gpx'] = "none";
                }
            }

            array_push($returnArray[$recKey], $record);
        }

        return $returnArray;
    }

    /**
     * @param null $tcxFileName
     * @param null $tcxTrackName
     *
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordActivityTCX($tcxFileName = null, $tcxTrackName = null)
    {
        if (is_null($tcxFileName)) {
            if (array_key_exists("tcx", $_GET)) {
                $tcxFileName = $_GET['tcx'];
            }
        }

        if (!is_null($tcxFileName)) {
            if (is_null($tcxTrackName)) {
                $tcxTrackName = $tcxFileName . " Fitbit Track";
            }

            $tcxFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tcx' . DIRECTORY_SEPARATOR . $tcxFileName . '.tcx';

            if (file_exists($tcxFile)) {
                $items = simplexml_load_file($tcxFile);
                if (!is_object($items)) {
                    $items = simplexml_load_file($tcxFile);
                    if (!is_object($items)) {
                        $items = simplexml_load_file($tcxFile);
                        if (!is_object($items)) {
                            return [
                                "error" => "Failed to read $tcxFileName TCX file",
                                "return" => [
                                    "Id" => "Failed to read $tcxFileName TCX file",
                                    "TotalTimeSeconds" => 0,
                                    "DistanceMeters" => 0,
                                    "Calories" => 0,
                                    "Intensity" => 0,
                                    "LatitudeDegrees" => "56.462018",
                                    "LongitudeDegrees" => "-2.970721",
                                    "gpx" => "none"
                                ]
                            ];
                        }
                    }
                } else if (!isset($items->Activities->Activity->Lap)) {
                    return [
                        "error" => "TCX Files contains no GPS Points",
                        "return" => [
                            "Id" => "No GPS in TCX file",
                            "TotalTimeSeconds" => 0,
                            "DistanceMeters" => 0,
                            "Calories" => 0,
                            "Intensity" => 0,
                            "LatitudeDegrees" => "56.462018",
                            "LongitudeDegrees" => "-2.970721",
                            "gpx" => "none"
                        ]
                    ];
                }

                /** @lang XML */
                $gpx = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                $gpx .= "<gpx creator=\"NxFit - http://nxfifteen.me.uk\" ";
                $gpx .= "\n   xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd\"";
                $gpx .= "\n   xmlns=\"http://www.topografix.com/GPX/1/1\"";
                $gpx .= "\n   xmlns:gpxtpx=\"http://www.garmin.com/xmlschemas/TrackPointExtension/v1\"";
                $gpx .= "\n   xmlns:gpxx=\"http://www.garmin.com/xmlschemas/GpxExtensions/v3\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">";
                $gpx .= "\n <metadata>";
                $gpx .= "\n  <name>" . $tcxTrackName . "</name>";
                $gpx .= "\n  <link href=\"http://nxfifteen.me.uk/\"><text>" . $tcxFileName . " Fitbit Track</text></link>";
                $gpx .= "\n  <time>" . $items->Activities->Activity->Id . "</time>";
                $gpx .= "\n </metadata>";
                $gpx .= "\n <trk>";
                $gpx .= "\n  <name>" . $tcxTrackName . "</name>";

                $gpx .= "\n  <trkseg>";

                $gpxMeta = [];
                $gpxMeta['meta'] = [];
                $gpxMeta['laps'] = [];

                $startLatitudeVery = 0;
                $startLongitudeVery = 0;
                $endLatitude = 0;
                $endLongitude = 0;

                foreach ($items->Activities->Activity->Lap as $Laps) {
                    $trackCount = 0;
                    $totalHeartRate = 0;
                    $startLatitude = 0;
                    $startLongitude = 0;
                    foreach ($Laps->Track->Trackpoint as $trkpt) {
                        $trackCount++;
                        $gpx .= "\n   <trkpt lat=\"" . $trkpt->Position->LatitudeDegrees . "\" lon=\"" . $trkpt->Position->LongitudeDegrees . "\">";
                        $gpx .= "\n    <time>" . $trkpt->Time . "</time>";
                        if (isset($trkpt->AltitudeMeters)) {
                            $gpx .= "\n    <ele>" . $trkpt->AltitudeMeters . "</ele>";
                        } else {
                            $gpx .= "\n    <ele>0</ele>";
                        }
                        $gpx .= "\n    <extensions>";
                        $gpx .= "\n     <gpxtpx:TrackPointExtension>";
                        $gpx .= "\n      <gpxtpx:hr>" . $trkpt->HeartRateBpm->Value . "</gpxtpx:hr>";
                        $totalHeartRate = $totalHeartRate + $trkpt->HeartRateBpm->Value;
                        $gpx .= "\n     </gpxtpx:TrackPointExtension>";
                        $gpx .= "\n    </extensions>";
                        $gpx .= "\n   </trkpt>";

                        if ($startLatitude == 0) {
                            $startLatitude = (Float)$trkpt->Position->LatitudeDegrees;
                        }
                        if ($startLongitude == 0) {
                            $startLongitude = (Float)$trkpt->Position->LongitudeDegrees;
                        }

                        if ($startLatitudeVery == 0) {
                            $startLatitudeVery = (Float)$trkpt->Position->LatitudeDegrees;
                        }
                        if ($startLongitudeVery == 0) {
                            $startLongitudeVery = (Float)$trkpt->Position->LongitudeDegrees;
                        }

                        $endLatitude = (Float)$trkpt->Position->LatitudeDegrees;
                        $endLongitude = (Float)$trkpt->Position->LongitudeDegrees;
                    }

                    /** @var \SimpleXMLElement $Laps */
                    $attributes = json_decode(json_encode($Laps->attributes()), true);
                    /** @noinspection PhpUndefinedFieldInspection */
                    $gpxMeta['laps'][] = [
                        "startTime" => $attributes['@attributes']['StartTime'],
                        "elapsedTime" => (Float)$Laps->TotalTimeSeconds,
                        "startPoint" => [$startLatitude, $startLongitude],
                        "endPoint" => [$endLatitude, $endLongitude],
                        "calories" => (Integer)$Laps->Calories,
                        "distance" => (Float)$Laps->DistanceMeters,
                        "AverageHeartRateBpm" => number_format(($totalHeartRate / $trackCount), 2),
                        "intensity" => (String)$Laps->Intensity,
                    ];
                }

                $gpx .= "\n  </trkseg>";
                $gpx .= "\n </trk>";
                $gpx .= "\n</gpx>";

                $fh = fopen(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '.gpx', 'w');
                fwrite($fh, $gpx);
                fclose($fh);

                $gpxMeta['meta']['visibility'] = "public";
                $geo_private = json_decode($this->getAppClass()->getUserSetting($this->getUserID(), "geo_private", array()), true);

                if (is_array($geo_private) && count($geo_private) > 0) {
                    foreach ($geo_private as $geoSpot) {
                        if ($gpxMeta['meta']['visibility'] == "public") {
                            $gpxMeta['meta']['distance_start'] = round($this->haversineGreatCircleDistance($geoSpot['lat'], $geoSpot['lon'], $startLatitudeVery, $startLongitudeVery, "M"), 2);
                            $gpxMeta['meta']['distance_end'] = round($this->haversineGreatCircleDistance($geoSpot['lat'], $geoSpot['lon'], $endLatitude, $endLongitude, "M"), 2);
                            if ($gpxMeta['meta']['distance_start'] < $geoSpot['radious'] || $gpxMeta['meta']['distance_end'] < $geoSpot['radious']) {
                                $gpxMeta['meta']['visibility'] = "private";
                            }
                        }
                    }
                }

                $fh = fopen(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '_laps.json',
                    'w');
                fwrite($fh, json_encode($gpxMeta));
                fclose($fh);

                if (!file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '.gpx')) {
                    $gpxFileName = "none";
                } else {
                    $gpxFileName = $this->getAppClass()->getSetting("http/") . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $tcxFileName . '.gpx';
                }

                $trackPoint = $items->Activities->Activity->Lap->Track->Trackpoint;

                return [
                    "error" => "",
                    "return" => [
                        "Id" => (String)$items->Activities->Activity->Id,
                        "TotalTimeSeconds" => (String)$items->Activities->Activity->Lap->TotalTimeSeconds,
                        "DistanceMeters" => (String)$items->Activities->Activity->Lap->DistanceMeters,
                        "Calories" => (String)$items->Activities->Activity->Lap->Calories,
                        "Intensity" => (String)$items->Activities->Activity->Lap->Intensity,
                        "LatitudeDegrees" => (String)$trackPoint[0]->Position->LatitudeDegrees,
                        "LongitudeDegrees" => (String)$trackPoint[0]->Position->LongitudeDegrees,
                        "gpx" => $gpxFileName
                    ]
                ];
            } else {
                return [
                    "error" => "TCX file for $tcxFileName is missing",
                    "return" => [
                        "Id" => "TCX file for $tcxFileName is missing",
                        "TotalTimeSeconds" => 0,
                        "DistanceMeters" => 0,
                        "Calories" => 0,
                        "Intensity" => 0,
                        "LatitudeDegrees" => "56.462018",
                        "LongitudeDegrees" => "-2.970721",
                        "gpx" => "none"
                    ]
                ];
            }
        } else {
            return [
                "error" => "You must set an activity id",
                "return" => [
                    "Id" => "You must set an activity id",
                    "TotalTimeSeconds" => 0,
                    "DistanceMeters" => 0,
                    "Calories" => 0,
                    "Intensity" => 0,
                    "LatitudeDegrees" => "56.462018",
                    "LongitudeDegrees" => "-2.970721",
                    "gpx" => "none"
                ]
            ];
        }
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     *
     * @param double $lat1
     * @param double $lon1
     * @param double $lat2
     * @param double $lon2
     * @param string $unit
     *
     * @return float Distance between points in [m] (same as earthRadius)
     * @internal param float $latitudeFrom Latitude of start point in [deg decimal]
     * @internal param float $longitudeFrom Longitude of start point in [deg decimal]
     * @internal param float $latitudeTo Latitude of target point in [deg decimal]
     * @internal param float $longitudeTo Longitude of target point in [deg decimal]
     * @internal param float $earthRadius Mean earth radius in [m]
     */
    private function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "M") {
            return ($miles * 1609.34);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordBadges()
    {
        $userBadges = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                null, false) . "bages_user", [
            "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                false) . "bages" => ["badgeid" => "encodedId"]
        ],
            [
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages.category',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages.value',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages.image',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages.badgeGradientEndColor',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages.badgeGradientStartColor',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages.earnedMessage',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages.marketingdescription',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages.name',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages_user.dateTime',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'bages_user.timesAchieved',
            ], [
                $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "bages_user.fuid" => $this->getUserID(),
                "ORDER" => [
                    $this->getAppClass()->getSetting("db_prefix", null, false) . "bages.value ASC",
                    $this->getAppClass()->getSetting("db_prefix", null, false) . "bages_user.dateTime ASC"
                ]
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $badges = [];
        foreach ($userBadges as $userBadge) {
            $badge_key = $userBadge['category'];
            if (!array_key_exists($badge_key, $badges)) {
                $badges[$badge_key] = [];
            }

            array_push($badges[$badge_key], $userBadge);
        }

        return ["images" => "images/badges/", "badges" => $badges];
    }

    /**
     * @todo Consider test case
     * @return array|bool
     */
    public function returnUserRecordBody()
    {
        $return = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "body",
            [
                'date',
                'weight',
                'weightGoal',
                'fat',
                'fatGoal',
                'bmi',
                'calf',
                'bicep',
                'chest',
                'forearm',
                'hips',
                'neck',
                'thigh',
                'waist'
            ],
            $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        return $return;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordChallenger()
    {
        return $this->returnUserRecordPush();
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordPush()
    {
        $userPushLength = $this->getAppClass()->getUserSetting($this->getUserID(), "push_length", '50');
        $userPushStartString = $this->getAppClass()->getUserSetting($this->getUserID(), "push",
            '03-31 last sunday'); // Default to last Sunday in March
        $userPushStartDate = date("Y-m-d",
            strtotime(date("Y") . '-' . $userPushStartString)); // Default to last Sunday in March
        $userPushEndDate = date("Y-m-d",
            strtotime($userPushStartDate . ' +' . $userPushLength . ' day')); // Default to last Sunday in March

        $userPushTrgSteps = $this->getAppClass()->getUserSetting($this->getUserID(), "push_steps", '10000');
        $userPushTrgDistance = $this->getAppClass()->getUserSetting($this->getUserID(), "push_distance", '5');
        $userPushTrgUnit = $this->getAppClass()->getUserSetting($this->getUserID(), "push_unit", 'km');
        $userPushTrgActivity = $this->getAppClass()->getUserSetting($this->getUserID(), "push_activity", '30');

        $userPushPassMark = $this->getAppClass()->getUserSetting($this->getUserID(), "push_passmark", '95');

        $dbPush = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "push",
            ['startDate', 'endDate', 'score', 'steps', 'distance', 'veryactive'],
            ["user" => $this->getUserID()]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        if (!$dbPush) {
            $dbPush = [];
        }

        if (count($dbPush) > 0) {
            foreach ($dbPush as $index => $push) {
                $dbPush[$index]['score'] = round($dbPush[$index]['score'], 0, PHP_ROUND_HALF_UP);
                if ($push['score'] >= 98) {
                    $dbPush[$index]['pass'] = 2;
                } else if ($push['score'] >= $userPushPassMark) {
                    $dbPush[$index]['pass'] = 1;
                } else {
                    $dbPush[$index]['pass'] = 0;
                }
            }
        }

        $today = strtotime(date("Y-m-d"));
        if ($today >= strtotime($userPushStartDate) && $today <= strtotime($userPushEndDate)) {
            $currentPush = [];

            $days = 0;
            $daysPast = 0;
            $dbPushRec = $this->calculatePushDays($userPushStartDate, $userPushEndDate,
                new DateTime($userPushStartDate));
            foreach ($dbPushRec['events'] as $dayRecord) {
                if ($dayRecord['className'] == "label-pass") {
                    $days += 1;
                    $daysPast += 1;
                } else if ($dayRecord['className'] == "label-today") {
                    $currentPush['distance'] = $dayRecord['distance'];
                    $currentPush['active'] = $dayRecord['active'];
                    $currentPush['steps'] = $dayRecord['steps'];
                } else {
                    $days += 1;
                }
            }

            $currentPush['steps_g'] = $this->getAppClass()->getUserSetting($this->getUserID(), "push_steps",
                '10000');
            $currentPush['distance_g'] = $this->getAppClass()->getUserSetting($this->getUserID(), "push_distance",
                '5');
            $currentPush['active_g'] = $this->getAppClass()->getUserSetting($this->getUserID(), "push_activity",
                '30');

            $currentPush['day'] = $days;
            $currentPush['day_past'] = $daysPast;
            $currentPush['score'] = ($currentPush['day_past'] / $currentPush['day']) * 100;

            return [
                'pushActive' => 'active',
                'pushLength' => $userPushLength,
                'scores' => $dbPush,
                'current' => $currentPush,
                'goals' => [
                    'Activity' => $userPushTrgActivity,
                    'Steps' => $userPushTrgSteps,
                    'Distance' => $userPushTrgDistance,
                    'Unit' => $userPushTrgUnit
                ],
                'next' => [
                    'startDate' => $userPushStartDate,
                    'startDateF' => date("jS F, Y", strtotime($userPushStartDate)),
                    'endDate' => $userPushEndDate,
                    'endDateF' => date("jS F, Y", strtotime($userPushEndDate))
                ]
            ];
        } else if ($today > strtotime($userPushStartDate)) {
            $plusOnePushStartDate = date("Y-m-d",
                strtotime((date("Y") + 1) . '-' . $userPushStartString)); // Default to last Sunday in March
            $plusOnePushEndDate = date("Y-m-d",
                strtotime($plusOnePushStartDate . ' +' . $userPushLength . ' day')); // Default to last Sunday in March

            return [
                'pushActive' => 'past',
                'pushLength' => $userPushLength,
                'showDate' => $userPushStartDate,
                'scores' => $dbPush,
                'goals' => [
                    'Activity' => $userPushTrgActivity,
                    'Steps' => $userPushTrgSteps,
                    'Distance' => $userPushTrgDistance,
                    'Unit' => $userPushTrgUnit
                ],
                'next' => [
                    'startDate' => $plusOnePushStartDate,
                    'startDateF' => date("jS F, Y", strtotime($plusOnePushStartDate)),
                    'endDate' => $plusOnePushEndDate,
                    'endDateF' => date("jS F, Y", strtotime($plusOnePushEndDate))
                ],
                'last' => [
                    'startDate' => $userPushStartDate,
                    'startDateF' => date("jS F, Y", strtotime($userPushStartDate)),
                    'endDate' => $userPushEndDate,
                    'endDateF' => date("jS F, Y", strtotime($userPushEndDate))
                ]
            ];
        } else if ($today < strtotime($userPushStartDate)) {

            $nimusOnePushStartDate = date("Y-m-d",
                strtotime((date("Y") - 1) . '-' . $userPushStartString)); // Default to last Sunday in March
            $nimusOnePushEndDate = date("Y-m-d",
                strtotime($nimusOnePushStartDate . ' +' . $userPushLength . ' day')); // Default to last Sunday in March

            return [
                'pushActive' => 'future',
                'pushLength' => $userPushLength,
                'showDate' => $nimusOnePushStartDate,
                'scores' => $dbPush,
                'goals' => [
                    'Activity' => $userPushTrgActivity,
                    'Steps' => $userPushTrgSteps,
                    'Distance' => $userPushTrgDistance,
                    'Unit' => $userPushTrgUnit
                ],
                'next' => [
                    'startDate' => $userPushStartDate,
                    'startDateF' => date("jS F, Y", strtotime($userPushStartDate)),
                    'endDate' => $userPushEndDate,
                    'endDateF' => date("jS F, Y", strtotime($userPushEndDate))
                ],
                'last' => [
                    'startDate' => $nimusOnePushStartDate,
                    'startDateF' => date("jS F, Y", strtotime($nimusOnePushStartDate)),
                    'endDate' => $nimusOnePushEndDate,
                    'endDateF' => date("jS F, Y", strtotime($nimusOnePushEndDate))
                ]
            ];
        }

        return [];
    }

    /**
     * @param string $userPushStartDate
     * @param string $userPushEndDate
     * @param DateTime $range_start
     *
     * @return array
     */
    private function calculatePushDays($userPushStartDate, $userPushEndDate, $range_start)
    {
        $userPushTrgSteps = $this->getAppClass()->getUserSetting($this->getUserID(), "push_steps", '10000');
        $userPushTrgDistance = $this->getAppClass()->getUserSetting($this->getUserID(), "push_distance", '5');
        $userPushTrgUnit = $this->getAppClass()->getUserSetting($this->getUserID(), "push_unit", 'km');
        $userPushTrgActivity = $this->getAppClass()->getUserSetting($this->getUserID(), "push_activity", '30');

        $db_steps = $this->getAppClass()->getSetting("db_prefix", null, false) . "steps";
        $db_activity = $this->getAppClass()->getSetting("db_prefix", null, false) . "activity";

        $dbEvents = $this->getAppClass()->getDatabase()->query(
            "SELECT `$db_steps`.`date`,`$db_steps`.`distance`,`$db_steps`.`steps`,`$db_activity`.`fairlyactive`,`$db_activity`.`veryactive`"
            . " FROM `$db_steps`"
            . " JOIN `$db_activity` ON (`$db_steps`.`date` = `$db_activity`.`date`) AND (`$db_steps`.`user` = `$db_activity`.`user`)"
            . " WHERE `$db_steps`.`user` = '" . $this->getUserID() . "' AND `$db_steps`.`date` <= '" . $userPushEndDate . "' AND `$db_steps`.`date` >= '$userPushStartDate' "
            . " ORDER BY `$db_steps`.`date` DESC");
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $days = 0;
        $score = 0;
        $scoreDistance = 0;
        $scoreVeryactive = 0;
        $scoreSteps = 0;
        $startDateCovered = false;
        $calenderEvents = [];
        foreach ($dbEvents as $dbEvent) {
            if (strtotime($dbEvent['date']) >= strtotime($userPushStartDate) && strtotime($dbEvent['date']) <= strtotime($userPushEndDate)) {
                $days += 1;

                if ($userPushTrgUnit == "km") {
                    $dbEvent['distance'] = $dbEvent['distance'] * 1.609344;
                }
                if (strtotime($dbEvent['date']) == strtotime($userPushStartDate)) {
                    $startDateCovered = true;
                }

                $dbEvent['veryactive'] = $dbEvent['fairlyactive'] + $dbEvent['veryactive'];

                $scoreVeryactive += $dbEvent['veryactive'];
                $scoreDistance += $dbEvent['distance'];
                $scoreSteps += $dbEvent['steps'];

                if (strtotime($dbEvent['date']) == strtotime(date("Y-m-d"))) {
                    if ($days > 0) {
                        $score = round(($score / $days) * 100, 2);
                    } else {
                        $score = 0;
                    }
                    array_push($calenderEvents, [
                        "title" => $dbEvent['steps'] . " steps"
                            . "\n" . $dbEvent['veryactive'] . " min"
                            . "\n" . number_format($dbEvent['distance'], 2) . $userPushTrgUnit,
                        "start" => $dbEvent['date'],
                        'className' => 'label-today',
                        'distance' => round($dbEvent['distance'], 2),
                        'active' => $dbEvent['veryactive'],
                        'steps' => $dbEvent['steps']
                    ]);
                } else if ($dbEvent['steps'] >= $userPushTrgSteps) {
                    $score = $score + 1;
                    array_push($calenderEvents, [
                        "title" => "Past!\nSteps: " . number_format($dbEvent['steps'], 0),
                        "start" => $dbEvent['date'],
                        'className' => 'label-pass'
                    ]);
                } else if ($dbEvent['veryactive'] >= $userPushTrgActivity) {
                    $score = $score + 1;
                    array_push($calenderEvents, [
                        "title" => "Past!\nActivity: " . $dbEvent['veryactive'] . " min",
                        "start" => $dbEvent['date'],
                        'className' => 'label-pass'
                    ]);
                } else if ($dbEvent['distance'] >= $userPushTrgDistance) {
                    $score = $score + 1;
                    array_push($calenderEvents, [
                        "title" => "Past!\nDistance: " . number_format($dbEvent['distance'],
                                2) . $userPushTrgUnit,
                        "start" => $dbEvent['date'],
                        'className' => 'label-pass'
                    ]);
                } else {
                    array_push($calenderEvents, [
                        "title" => "Steps: " . number_format($dbEvent['steps'], 0)
                            . "\nDistance: " . number_format($dbEvent['distance'], 2) . $userPushTrgUnit
                            . "\nActivity: " . $dbEvent['veryactive'] . " min",
                        "start" => $dbEvent['date'],
                        'className' => 'label-failed'
                    ]);
                }

            }
        }

        if (!$startDateCovered) {
            array_push($calenderEvents, [
                "title" => "Push " . $range_start->format("Y") . "\nStart!",
                "start" => $userPushStartDate,
                'className' => 'label-nopush'
            ]);
        }

        if ($days > 0) {
            $score = round(($score / $days) * 100, 2);
        } else {
            $score = 0;
        }

        return [
            'score' => $score,
            'veryactive' => $scoreVeryactive,
            'steps' => $scoreSteps,
            'distance' => $scoreDistance,
            'events' => $calenderEvents
        ];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordPushCalendar()
    {
        // Short-circuit if the client did not give us a date range.
        if (!isset($_GET['start']) || !isset($_GET['end'])) {
            return ["error" => "true", "code" => 105, "msg" => "No start or end date given"];
        }

        $range_start = new DateTime($_GET['start']);
        $range_end = new DateTime($_GET['end']);

        $userPushLength = $this->getAppClass()->getUserSetting($this->getUserID(), "push_length", '50');
        $userPushStartDate = $this->getAppClass()->getUserSetting($this->getUserID(), "push",
            '03-31 last sunday'); // Default to last Sunday in March
        $userPushStartDate = date("Y-m-d",
            strtotime($range_end->format("Y") . '-' . $userPushStartDate)); // Default to last Sunday in March
        $userPushEndDate = date("Y-m-d",
            strtotime($userPushStartDate . ' +' . $userPushLength . ' day')); // Default to last Sunday in March

        $calenderEvents = [];
        if (strtotime($userPushEndDate) <= strtotime(date("Y-m-d"))) {
            $dbPush = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "push",
                'dayData',
                [
                    "AND" => [
                        "user" => $this->getUserID(),
                        "startDate" => $userPushStartDate,
                        "endDate" => $userPushEndDate
                    ],
                    "LIMIT" => 1
                ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            if (!$dbPush) {
                $calenderEvents = $this->calculatePushDays($userPushStartDate, $userPushEndDate, $range_start);
                if (!array_key_exists("debug", $_GET) or $_GET['debug'] != "true") {
                    $this->getAppClass()->getDatabase()->insert($this->getAppClass()->getSetting("db_prefix", null,
                            false) . "push", [
                        'user' => $this->getUserID(),
                        'startDate' => $userPushStartDate,
                        'endDate' => $userPushEndDate,
                        'score' => $calenderEvents['score'],
                        'veryactive' => $calenderEvents['veryactive'],
                        'steps' => $calenderEvents['steps'],
                        'distance' => $calenderEvents['distance'],
                        'dayData' => json_encode($calenderEvents['events'])
                    ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);
                }
            } else {
                $calenderEvents['events'] = json_decode($dbPush[0]);
            }
        } else {
            $calenderEvents = $this->calculatePushDays($userPushStartDate, $userPushEndDate, $range_start);
        }

        return ['sole' => true, 'return' => $calenderEvents['events']];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordConky()
    {
        $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps",
            ['distance', 'floors', 'steps'],
            $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        if (count($dbSteps) > 0) {
            $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "steps_goals",
                ['distance', 'floors', 'steps'],
                $this->dbWhere());
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
            if (count($dbGoals) == 0) {
                // If todays goals are missing download the most recent goals
                $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "steps_goals",
                    ['distance', 'floors', 'steps'],
                    ["user" => $this->getUserID(), "ORDER" => ["date" => "DESC"]]);
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                    [
                        "METHOD" => __METHOD__,
                        "LINE" => __LINE__
                    ]);
            }

            $dbActiveMinutes = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "activity",
                [
                    'target',
                    'fairlyactive',
                    'veryactive'
                ],
                [
                    "AND" => [
                        "user" => $this->getUserID(),
                        "date" => date("Y-m-d")
                    ],
                    "ORDER" => ["date" => "ASC"]
                ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            $dbActiveMinutes = array_pop($dbActiveMinutes);
            $dbGoals[0]['activity'] = (String)round($dbActiveMinutes['target'], 2);
            $dbActiveMinutes = $dbActiveMinutes['fairlyactive'] + $dbActiveMinutes['veryactive'];

            $dbSteps[0]['activity'] = $dbActiveMinutes;

            $dbSteps[0]['activity_p'] = ($dbActiveMinutes / $dbGoals[0]['activity']) * 100;
            $dbSteps[0]['steps_p'] = ($dbSteps[0]['steps'] / $dbGoals[0]['steps']) * 100;
            $dbSteps[0]['floors_p'] = ($dbSteps[0]['floors'] / $dbGoals[0]['floors']) * 100;
            $dbSteps[0]['distance_p'] = ($dbSteps[0]['distance'] / $dbGoals[0]['distance']) * 100;

            if ($dbSteps[0]['activity_p'] > 100) {
                $dbSteps[0]['activity_p'] = 100;
            }
            if ($dbSteps[0]['steps_p'] > 100) {
                $dbSteps[0]['steps_p'] = 100;
            }
            if ($dbSteps[0]['floors_p'] > 100) {
                $dbSteps[0]['floors_p'] = 100;
            }
            if ($dbSteps[0]['distance_p'] > 100) {
                $dbSteps[0]['distance_p'] = 100;
            }

            $dbSteps[0]['distance'] = (String)round($dbSteps[0]['distance'], 2);
            $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);

            $challange = $this->returnUserRecordPush();
            $challange = [
                "active" => $challange['pushActive'],
                "startDateF" => $challange['next']['startDateF'],
                "endDateF" => $challange['next']['endDateF'],
                "activity" => ($challange['current']['active'] / $challange['current']['active_g']) * 100,
                "distance" => ($challange['current']['distance'] / $challange['current']['distance_g']) * 100,
                "steps" => ($challange['current']['steps'] / $challange['current']['steps_g']) * 100
            ];

            if ($challange['activity'] > 100) {
                $challange['activity'] = 100;
            }
            if ($challange['distance'] > 100) {
                $challange['distance'] = 100;
            }
            if ($challange['steps'] > 100) {
                $challange['steps'] = 100;
            }

            $journeys = $this->returnUserRecordJourneysState();
            $journeys = array_pop($journeys);
            $journeys = [
                "name" => $journeys['name'],
                "blurb" => $journeys['blurb'],
            ];

            if (!is_null($this->getTracking())) {
                $this->getTracking()->track("JSON Get", $this->getUserID(), "Steps");
                $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");
            }

            return [
                'recorded' => $dbSteps[0],
                'goal' => $dbGoals[0],
                'challange' => $challange,
                'journeys' => $journeys
            ];
        } else {
            return ["error" => "true", "code" => 104, "msg" => "No results for given date"];
        }
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordJourneysState()
    {
        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "journeys_travellers", ["fuid" => $this->getUserID()])
        ) {
            $dbJourneys = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "journeys_travellers", [
                "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "journeys" => ["jid" => "jid"]
            ],
                [
                    $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys.jid',
                    $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys.name',
                    $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys.blurb',
                    $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_travellers.start_date',
                ], [
                    $this->getAppClass()->getSetting("db_prefix", null,
                        false) . "journeys_travellers.fuid" => $this->getUserID()
                ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            $journeys = [];
            foreach ($dbJourneys as $dbJourney) {
                $user_miles_travelled = $this->getUserMilesSince($dbJourney['start_date']);

                $dbLegs = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "journeys_legs", [
                    "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                        false) . "journeys" => ["jid" => "jid"]
                ],
                    [
                        $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_legs.lid',
                        $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_legs.name',
                    ], [
                        "AND" => [
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . "journeys.jid" => $dbJourney['jid'],
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . "journeys_legs.start_mile[<=]" => $user_miles_travelled,
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . "journeys_legs.end_mile[>=]" => $user_miles_travelled
                        ]
                    ]);
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                    [
                        "METHOD" => __METHOD__,
                        "LINE" => __LINE__
                    ]);

                $legs = [];
                foreach ($dbLegs as $dbLeg) {
                    // Get all narative items the user has completed
                    $dbNarratives = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                            null, false) . "journeys_narrative", [
                        "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                            false) . "journeys_legs" => ["lid" => "lid"]
                    ],
                        [
                            $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_narrative.nid',
                            $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_narrative.miles',
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . 'journeys_narrative.subtitle',
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . 'journeys_narrative.narrative',
                        ], [
                            "AND" => [
                                $this->getAppClass()->getSetting("db_prefix", null,
                                    false) . "journeys_narrative.lid" => $dbLeg['lid'],
                                $this->getAppClass()->getSetting("db_prefix", null,
                                    false) . "journeys_narrative.miles[<=]" => $user_miles_travelled
                            ],
                            "LIMIT" => 1,
                            "ORDER" => [
                                $this->getAppClass()->getSetting("db_prefix", null,
                                    false) . "journeys_narrative.miles" => "DESC"
                            ]
                        ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);

                    $prevNarrativeMiles = 0;
                    foreach ($dbNarratives as $dbNarrative) {
                        $narrativeArray = [
                            "lid" => $dbLeg['lid'] . "/" . $dbNarrative['nid'],
                            "legs_names" => $dbLeg['name'],
                            "miles" => $dbNarrative['miles'],
                            "miles_travelled" => $dbNarrative['miles'] - $prevNarrativeMiles,
                            "miles_off" => 0,
                            "subtitle" => $dbNarrative['subtitle'],
                            "narrative" => $dbNarrative['narrative']
                        ];
                        $prevNarrativeMiles = $dbNarrative['miles'];

                        if ($dbNarrative['miles'] > $user_miles_travelled) {
                            $narrativeArray["miles_off"] = number_format($dbNarrative['miles'] - $user_miles_travelled,
                                2);
                        }

                        $legs['last'] = $narrativeArray;
                    }

                    $dbNarrativeNext = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                            null, false) . "journeys_narrative", [
                        "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                            false) . "journeys_legs" => ["lid" => "lid"]
                    ],
                        [
                            $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_narrative.nid',
                            $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_narrative.miles',
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . 'journeys_narrative.subtitle',
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . 'journeys_narrative.narrative',
                        ], [
                            "AND" => [
                                $this->getAppClass()->getSetting("db_prefix", null,
                                    false) . "journeys_narrative.lid" => $dbLeg['lid'],
                                $this->getAppClass()->getSetting("db_prefix", null,
                                    false) . "journeys_narrative.miles[>=]" => $user_miles_travelled
                            ],
                            "LIMIT" => 1,
                            "ORDER" => [
                                $this->getAppClass()->getSetting("db_prefix", null,
                                    false) . "journeys_narrative.miles" => "ASC"
                            ]
                        ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);

                    foreach ($dbNarrativeNext as $dbNarrative) {
                        $narrativeArray = [
                            "lid" => $dbLeg['lid'] . "/" . $dbNarrative['nid'],
                            "legs_names" => $dbLeg['name'],
                            "miles" => $dbNarrative['miles'],
                            "miles_travelled" => $dbNarrative['miles'] - $prevNarrativeMiles,
                            "miles_off" => 0,
                            "subtitle" => $dbNarrative['subtitle'],
                            "narrative" => $dbNarrative['narrative']
                        ];
                        $prevNarrativeMiles = $dbNarrative['miles'];

                        if ($dbNarrative['miles'] > $user_miles_travelled) {
                            $narrativeArray["miles_off"] = number_format($dbNarrative['miles'] - $user_miles_travelled,
                                2);
                        }

                        $legs['next'] = $narrativeArray;
                    }
                }

                $journeys[$dbJourney['jid']] = [
                    "name" => $dbJourney['name'],
                    "start_date" => $dbJourney['start_date'],
                    "usrMiles" => number_format($this->getUserMilesSince($dbJourney['start_date']), 2),
                    "blurb" => $dbJourney['blurb'],
                    "legs" => $legs
                ];
            }

            return $journeys;
        } else {
            return ["error" => "true", "code" => 104, "msg" => "Not on any jounry"];
        }
    }

    /**
     * @param string $start_date
     *
     * @todo Consider test case
     * @return bool|int
     */
    public function getUserMilesSince($start_date)
    {
        $dbMiles = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps",
            ['distance'],
            ["AND" => ["user" => $this->getUserID(), "date[>=]" => $start_date]]);

        return $dbMiles;
    }

    /**
     * @todo Consider test case
     * @return UserAnalytics
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * @param UserAnalytics $tracking
     *
     * @todo Consider test case
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordDashboard()
    {
        // Achivment
        $dbSteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps", [
            'distance',
            'floors',
            'steps',
            'syncd'
        ], $this->dbWhere());
        $dbStepsGoal = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps_goals", [
            'distance',
            'floors',
            'steps'
        ], $this->dbWhere());

        // Life time sum
        $dbStepsAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps", 'steps', ["user" => $this->getUserID()]);
        $dbDistanceAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps", 'distance', ["user" => $this->getUserID()]);
        $dbFloorsAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps", 'floors', ["user" => $this->getUserID()]);

        $thisDate = $this->getParamDate();
        $thisDate = explode("-", $thisDate);

        if ($dbSteps['distance'] > 0 && $dbStepsGoal['distance'] > 0) {
            $progdistance = number_format((($dbSteps['distance'] / $dbStepsGoal['distance']) * 100), 2);
        } else {
            $progdistance = 0;
        }
        if ($dbSteps['floors'] > 0 && $dbStepsGoal['floors'] > 0) {
            $progfloors = number_format((($dbSteps['floors'] / $dbStepsGoal['floors']) * 100), 2);
        } else {
            $progfloors = 0;
        }
        if ($dbSteps['steps'] > 0 && $dbStepsGoal['steps'] > 0) {
            $progsteps = number_format((($dbSteps['steps'] / $dbStepsGoal['steps']) * 100), 2);
        } else {
            $progsteps = 0;
        }

        $return = [
            'returnDate' => $thisDate,
            'syncd' => $dbSteps['syncd'],
            'distance' => number_format($dbSteps['distance'], 2),
            'floors' => number_format($dbSteps['floors'], 0),
            'steps' => number_format($dbSteps['steps'], 0),
            'progdistance' => $progdistance,
            'progfloors' => $progfloors,
            'progsteps' => $progsteps,
            'distanceAllTime' => number_format($dbDistanceAllTime, 2),
            'floorsAllTime' => number_format($dbFloorsAllTime, 0),
            'stepsAllTime' => number_format($dbStepsAllTime, 0)
        ];

        return $return;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordLeaderboard()
    {
        $leaderboard = $this->getAppClass()->getUserSetting($this->getUserID(), "leaderboard", "none");

        if ($leaderboard == "none") {
            return ["error" => "true", "code" => 104, "msg" => "No friends found"];
        }

        $leaderboard = json_decode($leaderboard, true);
        $totalFriends = count($leaderboard);

        foreach ($leaderboard as $encodedId => $friend) {
            if (!array_key_exists("stepsSum", $leaderboard[$encodedId])) {
                unset($leaderboard[$encodedId]);
            } else {
                unset($leaderboard[$encodedId]['gender']);
                unset($leaderboard[$encodedId]['memberSince']);
                unset($leaderboard[$encodedId]['city']);
                unset($leaderboard[$encodedId]['country']);
                unset($leaderboard[$encodedId]['age']);

                $leaderboard[$encodedId]['stepsAvg'] = number_format($leaderboard[$encodedId]['stepsAvg']);
                $leaderboard[$encodedId]['stepsSum'] = number_format($leaderboard[$encodedId]['stepsSum']);
                $leaderboard[$encodedId]['stepsLife'] = number_format($leaderboard[$encodedId]['stepsLife']);
            }
        }
        $activeFriends = count($leaderboard);

        $return = [
            "totalFriends" => $totalFriends,
            "activeFriends" => $activeFriends,
            "friends" => $leaderboard
        ];

        return $return;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordFoodDiary()
    {
        $returnArray = [];

        $where = $this->dbWhere();
        if (!array_key_exists("LIMIT", $where) OR $where['LIMIT'] == 1) {
            unset($where['AND']['date[<=]']);
            unset($where['AND']['date[>=]']);
            $where['AND']['date'] = $this->getParamDate();
        }

        $dbWater = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", null,
                false) . "water", 'liquid', $where);
        if (!array_key_exists("LIMIT", $where) OR $where['LIMIT'] == 1) {
            /** @var float $dbWater */
            $returnArray['water'] = [
                "liquid" => (String)round($dbWater, 2),
                "goal" => $this->getAppClass()->getUserSetting($this->getUserID(), "goal_water", '200')
            ];
        } else {
            /** @var float $dbWater */
            $returnArray['water'] = [
                "liquid" => (String)round($dbWater, 2),
                "goal" => ($this->getAppClass()->getUserSetting($this->getUserID(), "goal_water",
                        '200') * $where['LIMIT'])
            ];
        }

        $returnArray['food'] = [];
        $returnArray['food']['summary'] = [];
        $returnArray['food']['goals'] = [];
        $returnArray['food']['meals'] = [];
        $returnArray['food']['summary']['calories'] = 0;
        $returnArray['food']['summary']['carbs'] = 0;
        $returnArray['food']['summary']['fat'] = 0;
        $returnArray['food']['summary']['fiber'] = 0;
        $returnArray['food']['summary']['protein'] = 0;
        $returnArray['food']['summary']['sodium'] = 0;

        $returnArray['food']['goals']['calories'] = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix",
                null, false) . "food_goals", 'calories', $where);

        if (!array_key_exists("LIMIT", $where) OR $where['LIMIT'] == 1) {
            $returnArray['food']['goals']['carbs'] = $this->getAppClass()->getUserSetting($this->getUserID(),
                "goal_food_carbs", 310);
            $returnArray['food']['goals']['fat'] = $this->getAppClass()->getUserSetting($this->getUserID(),
                "goal_food_fat", 70);
            $returnArray['food']['goals']['fiber'] = $this->getAppClass()->getUserSetting($this->getUserID(),
                "goal_food_fiber", 30);
            $returnArray['food']['goals']['protein'] = $this->getAppClass()->getUserSetting($this->getUserID(),
                "goal_food_protein", 50);
            $returnArray['food']['goals']['sodium'] = $this->getAppClass()->getUserSetting($this->getUserID(),
                "goal_food_sodium", 2300);
        } else {
            $returnArray['food']['goals']['carbs'] = ($this->getAppClass()->getUserSetting($this->getUserID(),
                    "goal_food_carbs", 310) * $where['LIMIT']);
            $returnArray['food']['goals']['fat'] = ($this->getAppClass()->getUserSetting($this->getUserID(),
                    "goal_food_fat", 70) * $where['LIMIT']);
            $returnArray['food']['goals']['fiber'] = ($this->getAppClass()->getUserSetting($this->getUserID(),
                    "goal_food_fiber", 30) * $where['LIMIT']);
            $returnArray['food']['goals']['protein'] = ($this->getAppClass()->getUserSetting($this->getUserID(),
                    "goal_food_protein", 50) * $where['LIMIT']);
            $returnArray['food']['goals']['sodium'] = ($this->getAppClass()->getUserSetting($this->getUserID(),
                    "goal_food_sodium", 2300) * $where['LIMIT']);
        }

        unset($where['LIMIT']);
        $dbFood = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "food",
            ['date', 'meal', 'calories', 'carbs', 'fat', 'fiber', 'protein', 'sodium'],
            $where);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $returnArray['food']['meals']['Breakfast Summary'] = [];
        $returnArray['food']['meals']['Lunch Summary'] = [];
        $returnArray['food']['meals']['Dinner Summary'] = [];
        $returnArray['food']['meals']['Snacks Summary'] = [];

        foreach ($dbFood as $meal) {
            if (!isset($returnArray['food']['meals'][$meal['meal']]['calories'])) {
                $returnArray['food']['meals'][$meal['meal']]['calories'] = 0;
            }
            if (!isset($returnArray['food']['meals'][$meal['meal']]['carbs'])) {
                $returnArray['food']['meals'][$meal['meal']]['carbs'] = 0;
            }
            if (!isset($returnArray['food']['meals'][$meal['meal']]['fat'])) {
                $returnArray['food']['meals'][$meal['meal']]['fat'] = 0;
            }
            if (!isset($returnArray['food']['meals'][$meal['meal']]['fiber'])) {
                $returnArray['food']['meals'][$meal['meal']]['fiber'] = 0;
            }
            if (!isset($returnArray['food']['meals'][$meal['meal']]['protein'])) {
                $returnArray['food']['meals'][$meal['meal']]['protein'] = 0;
            }
            if (!isset($returnArray['food']['meals'][$meal['meal']]['sodium'])) {
                $returnArray['food']['meals'][$meal['meal']]['sodium'] = 0;
            }

            $returnArray['food']['meals'][$meal['meal']]['calories'] = $returnArray['food']['meals'][$meal['meal']]['calories'] + $meal['calories'];
            $returnArray['food']['meals'][$meal['meal']]['carbs'] = $returnArray['food']['meals'][$meal['meal']]['carbs'] + $meal['carbs'];
            $returnArray['food']['meals'][$meal['meal']]['fat'] = $returnArray['food']['meals'][$meal['meal']]['fat'] + $meal['fat'];
            $returnArray['food']['meals'][$meal['meal']]['fiber'] = $returnArray['food']['meals'][$meal['meal']]['fiber'] + $meal['fiber'];
            $returnArray['food']['meals'][$meal['meal']]['protein'] = $returnArray['food']['meals'][$meal['meal']]['protein'] + $meal['protein'];
            $returnArray['food']['meals'][$meal['meal']]['sodium'] = $returnArray['food']['meals'][$meal['meal']]['sodium'] + $meal['sodium'];

            $returnArray['food']['summary']['calories'] += $meal['calories'];
            $returnArray['food']['summary']['carbs'] += $meal['carbs'];
            $returnArray['food']['summary']['fat'] += $meal['fat'];
            $returnArray['food']['summary']['fiber'] += $meal['fiber'];
            $returnArray['food']['summary']['protein'] += $meal['protein'];
            $returnArray['food']['summary']['sodium'] += $meal['sodium'];
        }
        foreach ($dbFood as $meal) {
            $returnArray['food']['meals'][$meal['meal']]['precentage'] = ($meal['calories'] / $returnArray['food']['summary']['calories']) * 100;
        }

        if (count($returnArray['food']['meals']['Breakfast Summary']) == 0) {
            unset($returnArray['food']['meals']['Breakfast Summary']);
        }
        if (count($returnArray['food']['meals']['Lunch Summary']) == 0) {
            unset($returnArray['food']['meals']['Lunch Summary']);
        }
        if (count($returnArray['food']['meals']['Dinner Summary']) == 0) {
            unset($returnArray['food']['meals']['Dinner Summary']);
        }
        if (count($returnArray['food']['meals']['Snacks Summary']) == 0) {
            unset($returnArray['food']['meals']['Snacks Summary']);
        }

        return $returnArray;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordKeyPoints()
    {
        // Get Users Gender and leaderboard ranking
        $dbUsers = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null,
                false) . "users", [
            'rank',
            'friends',
            'distance',
            'gender'
        ], ["fuid" => $this->getUserID()]);
        if (array_key_exists("personal", $_GET) and $_GET['personal'] == "true") {
            $he = "I";
            $is = "am";
            $hes = "I've";
            $his = "my";
        } else {
            $is = "is";
            if ($dbUsers['gender'] == "MALE") {
                $he = "he";
                $hes = "he's";
                $his = "his";
            } else {
                $he = "she";
                $hes = "she's";
                $his = "her";
            }
        }

        /**
         * Get Keypoint records
         */
        $dbKeyPoints = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                null, false) . "keypoints",
            ['category', 'value', 'less', 'more']
        );
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $keyPoints = [];
        foreach ($dbKeyPoints as $point) {
            if (!array_key_exists($point['category'], $keyPoints)) {
                $keyPoints[$point['category']] = [];
            }

            array_push($keyPoints[$point['category']], [
                "value" => $point['value'],
                "less" => $point['less'],
                "more" => $point['more']
            ]);
        }

        $returnStats = [
            "distance" => [],
            "floors" => [],
            "max" => [],
            "friends" => []
        ];
        $returnStats["friends"] = $hes . " " . $dbUsers['friends'] . " friends ";
        if ($dbUsers['rank'] > 1) {
            $returnStats["friends"] .= "and " . $is . " currently ranked " . $this->ordinalSuffix($dbUsers['rank']) . ", with another " . number_format($dbUsers['distance'],
                    0) . " steps " . $he . " could take " . $this->ordinalSuffix($dbUsers['rank'] - 1) . " place.";
        } else {
            $returnStats["friends"] .= "and " . $is . " proudly at the top of the leaderboard.";
        }

        /**
         * Set key points for DISTANCE
         */
        $dbDistanceAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps", 'distance', ["user" => $this->getUserID()]);
        $less = [];
        $more = [];
        foreach ($keyPoints['distance'] as $values) {
            if ($dbDistanceAllTime < $values['value']) {
                array_push($less, number_format(($values['value'] - $dbDistanceAllTime),
                        0) . " miles until " . $hes . " walked " . $values['less']);
            } else if ($dbDistanceAllTime > $values['value']) {
                $times = number_format($dbDistanceAllTime / $values['value'], 0);
                if ($times == 1) {
                    $times = "";
                } else if ($times == 2) {
                    $times = "twice";
                } else {
                    $times = $times . " times";
                }
                if (array_key_exists("more", $values) && !is_null($values['more']) && $values['more'] != "") {
                    $msg = $hes . " walked " . $values['more'] . " " . $times;
                } else {
                    $msg = $hes . " walked " . $values['less'] . " " . $times;
                }
                if ($times > 1) {
                    $msg .= "s";
                }
                array_push($more, $msg);
            }
        }

        $maxItems = $this->getAppClass()->getSetting("kp_maxItems", 8);
        $lessItems = $this->getAppClass()->getSetting("kp_lessItems", 2);
        if (count($less) < $maxItems - $lessItems) {
            $lessItems = $maxItems - count($less);
            $lessItems += 1;
        }

        for ($iMore = ($lessItems - 1); $iMore >= 0; $iMore = $iMore - 1) {
            if (count($more) > $iMore) {
                array_push($returnStats['distance'], $more[(count($more) - 1) - $iMore]);
                $maxItems = $maxItems - 1;
            }
        }

        for ($iLess = $maxItems; $iLess >= 0; $iLess = $iLess - 1) {
            if (count($less) > $iLess) {
                array_push($returnStats['distance'], $less[(count($less) - 1) - $iLess]);
            }
        }

        /**
         * Set key points for Floors
         */
        $dbFloorsAllTime = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps", 'elevation', ["user" => $this->getUserID()]);

        $less = [];
        $more = [];
        foreach ($keyPoints['elevation'] as $values) {
            if ($dbFloorsAllTime < $values['value']) {
                array_push($less, number_format(($values['value'] - $dbFloorsAllTime),
                        0) . " meters more until " . $hes . " climbed " . $values['less']);
            } else if ($dbFloorsAllTime > $values['value']) {
                $times = number_format($dbFloorsAllTime / $values['value'], 0);
                if ($times == 1) {
                    $times = "";
                } else if ($times == 2) {
                    $times = "twice";
                } else {
                    $times = $times . " times";
                }
                if (array_key_exists("more", $values) && !is_null($values['more']) && $values['more'] != "") {
                    $msg = $hes . " climbed " . $values['more'] . " " . $times;
                } else {
                    $msg = $hes . " climbed " . $values['less'] . " " . $times;
                }
                if ($times > 1) {
                    $msg .= "s";
                }
                array_push($more, $msg);
            }
        }

        $maxItems = $this->getAppClass()->getSetting("kp_maxItems", 8);
        $lessItems = $this->getAppClass()->getSetting("kp_lessItems", 2);
        if (count($less) <= ($maxItems - $lessItems)) {
            $lessItems = $maxItems - count($less);
            $lessItems += 1;
        }

        for ($iMore = ($lessItems - 1); $iMore >= 0; $iMore = $iMore - 1) {
            if (count($more) > $iMore) {
                array_push($returnStats['floors'], $more[(count($more) - 1) - $iMore]);
                $maxItems = $maxItems - 1;
            }
        }

        for ($iLess = $maxItems; $iLess >= 0; $iLess = $iLess - 1) {
            if (count($less) > $iLess) {
                array_push($returnStats['floors'], $less[(count($less) - 1) - $iLess]);
            }
        }

        /**
         * Set Max values
         */
        $dbMaxSteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps", [
            'steps',
            'date'
        ], ["user" => $this->getUserID(), "ORDER" => ["steps" => "DESC"]]);
        array_push($returnStats["max"],
            $his . " highest step count, totalling " . number_format($dbMaxSteps['steps'],
                0) . ", for a day was on " . date("jS F, Y", strtotime($dbMaxSteps['date'])) . ".");

        $dbMaxDistance = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps", [
            'distance',
            'date'
        ], ["user" => $this->getUserID(), "ORDER" => ["distance" => "DESC"]]);
        if ($dbMaxDistance['date'] == $dbMaxSteps['date']) {
            $returnStats["max"][(count($returnStats["max"]) - 1)] .= " That's an impressive " . number_format($dbMaxDistance['distance'],
                    0) . " miles.";
        } else {
            array_push($returnStats["max"],
                $he . " traveled the furthest, " . number_format($dbMaxDistance['distance'],
                    0) . " miles, on " . date("jS F, Y", strtotime($dbMaxDistance['date'])) . ".");
        }

        $dbMaxFloors = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps", [
            'floors',
            'date'
        ], ["user" => $this->getUserID(), "ORDER" => ["floors" => "DESC"]]);
        array_push($returnStats["max"],
            $he . " walked up, " . number_format($dbMaxFloors['floors'], 0) . " floors, on " . date("jS F, Y",
                strtotime($dbMaxFloors['date'])) . ".");

        $dbMaxElevation = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps", [
            'elevation',
            'date'
        ], ["user" => $this->getUserID(), "ORDER" => ["elevation" => "DESC"]]);
        if ($dbMaxFloors['date'] == $dbMaxElevation['date']) {
            $returnStats["max"][(count($returnStats["max"]) - 1)] .= " That's a total of " . number_format($dbMaxElevation['elevation'],
                    2) . " meters.";
        } else {
            array_push($returnStats["max"], $he . " climed the highest on " . date("jS F, Y",
                    strtotime($dbMaxElevation['date'])) . ", a total of " . number_format($dbMaxElevation['elevation'],
                    2) . " meters.");
        }

        return $returnStats;
    }

    /**
     * @param int $input_num
     *
     * @return string
     */
    private function ordinalSuffix($input_num)
    {
        $num = $input_num % 100; // protect against large numbers
        if ($num < 11 || $num > 13) {
            switch ($num % 10) {
                case 1:
                    return $input_num . 'st';
                case 2:
                    return $input_num . 'nd';
                case 3:
                    return $input_num . 'rd';
            }
        }

        return $input_num . 'th';
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordSleep()
    {

        $dbSleepRecords = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                null, false) . "sleep", [
            "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                false) . "sleep_user" => ["logId" => "sleeplog"]
        ],
            [
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'sleep.startTime',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'sleep.timeInBed',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'sleep.minutesAsleep',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'sleep.minutesToFallAsleep',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'sleep.efficiency',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'sleep.awakeningsCount',
            ], [
                $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "sleep_user.user" => $this->getUserID(),
                "ORDER" => $this->getAppClass()->getSetting("db_prefix", null,
                        false) . "sleep.startTime DESC"
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $returnSleep = [
            "lastSleep" => [],
            "efficiency" => 0,
            "timeInBed" => 0,
            "minutesToFallAsleep" => 0,
            "minutesAsleep" => 0,
            "awakeningsCount" => 0
        ];
        foreach ($dbSleepRecords as $record) {
            if (count($returnSleep['lastSleep']) == 0) {
                $returnSleep['lastSleep'] = [
                    "efficiency" => $record['efficiency'],
                    "timeInBed" => $record['timeInBed'],
                    "minutesToFallAsleep" => $record['minutesToFallAsleep'],
                    "minutesAsleep" => $record['minutesAsleep'],
                    "awakeningsCount" => $record['awakeningsCount']
                ];
            }

            $returnSleep['efficiency'] = $returnSleep['efficiency'] + $record['efficiency'];
            $returnSleep['timeInBed'] = $returnSleep['timeInBed'] + $record['timeInBed'];
            $returnSleep['minutesToFallAsleep'] = $returnSleep['minutesToFallAsleep'] + $record['minutesToFallAsleep'];
            $returnSleep['minutesAsleep'] = $returnSleep['minutesAsleep'] + $record['minutesAsleep'];
            $returnSleep['awakeningsCount'] = $returnSleep['awakeningsCount'] + $record['awakeningsCount'];
        }

        $returnSleep['efficiency'] = round($returnSleep['efficiency'] / count($dbSleepRecords), 0);
        $returnSleep['timeInBedAvg'] = round($returnSleep['timeInBed'] / count($dbSleepRecords), 0);
        $returnSleep['minutesToFallAsleep'] = round($returnSleep['minutesToFallAsleep'] / count($dbSleepRecords),
            0);
        $returnSleep['minutesAsleep'] = round($returnSleep['minutesAsleep'] / count($dbSleepRecords), 0);
        $returnSleep['awakeningsCount'] = round($returnSleep['awakeningsCount'] / count($dbSleepRecords), 0);

        return $returnSleep;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordSteps()
    {
        $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps",
            ['distance', 'floors', 'steps'],
            $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        if (count($dbSteps) > 0) {
            $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "steps_goals",
                ['distance', 'floors', 'steps'],
                $this->dbWhere());
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);
            if (count($dbGoals) == 0) {
                // If todays goals are missing download the most recent goals
                $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "steps_goals",
                    ['distance', 'floors', 'steps'],
                    ["user" => $this->getUserID(), "ORDER" => ["date" => "DESC"]]);
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                    [
                        "METHOD" => __METHOD__,
                        "LINE" => __LINE__
                    ]);
            }

            $dbSteps[0]['steps_p'] = ($dbSteps[0]['steps'] / $dbGoals[0]['steps']) * 100;
            $dbSteps[0]['floors_p'] = ($dbSteps[0]['floors'] / $dbGoals[0]['floors']) * 100;
            $dbSteps[0]['distance_p'] = ($dbSteps[0]['distance'] / $dbGoals[0]['distance']) * 100;

            $dbSteps[0]['distance'] = (String)round($dbSteps[0]['distance'], 2);
            $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);

            $cheer = ["distance" => 0, "floors" => 0, "steps" => 0];
            foreach ($cheer as $key => $value) {
                if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 3) {
                    $cheer[$key] = 7;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 2.5) {
                    $cheer[$key] = 6;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 2) {
                    $cheer[$key] = 5;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 1.5) {
                    $cheer[$key] = 4;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key]) {
                    $cheer[$key] = 3;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 0.75) {
                    $cheer[$key] = 2;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 0.5) {
                    $cheer[$key] = 1;
                } else {
                    $cheer[$key] = 0;
                }
            }

            if (!is_null($this->getTracking())) {
                $this->getTracking()->track("JSON Get", $this->getUserID(), "Steps");
                $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");
            }

            return ['recorded' => $dbSteps[0], 'goal' => $dbGoals[0], 'cheer' => $cheer];
        } else {
            return ["error" => "true", "code" => 104, "msg" => "No results for given date"];
        }
    }

    /**
     * @todo Consider test case
     * @return array|bool
     */
    public function returnUserRecordStepsGoal()
    {
        $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps_goals",
            ['date', 'distance', 'floors', 'steps'],
            $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $dbGoals[0]['distance'] = (String)round($dbGoals[0]['distance'], 2);

        if (!is_null($this->getTracking())) {
            $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");
        }

        return $dbGoals;
    }

    /**
     * @todo Consider test case
     * @return array|bool
     */
    public function returnUserRecordTrackerHistoryChart()
    {
        $convertedOutput = $this->returnUserRecordTrackerHistory();

        $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps_goals",
            ['date', 'steps'],
            $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $totalsStepsGoal = 0;
        $date = [];
        $distance = [];
        $floors = [];
        $steps = [];
        $stepsGoal = [];

        foreach ($convertedOutput['tracked'] as $key => $value) {
            $date[] = $convertedOutput['tracked'][$key]['day'];
            $distance[] = $convertedOutput['tracked'][$key]['distance'];
            $floors[] = $convertedOutput['tracked'][$key]['floors'];
            $steps[] = $convertedOutput['tracked'][$key]['steps'];
            $stepsGoal[] = $dbGoals[$key]['steps'];

            $totalsStepsGoal += $dbGoals[$key]['steps'];
        }

        $convertedOutput['totals']['stepsGoal'] = $totalsStepsGoal;
        $convertedOutput['human']['stepsGoal'] = number_format($totalsStepsGoal, 0);

        $convertedOutput['precentages']['steps'] = round(($convertedOutput['totals']['steps'] / $convertedOutput['totals']['stepsGoal']) * 100,
            0);

        $cSteps = count($steps);
        $cFloors = count($floors);
        $cDistance = count($distance);

        $convertedOutput['analysis'] = [
            "steps7Day" => number_format(array_sum($steps) / $cSteps, 0),
            "floors7Day" => number_format(array_sum($floors) / $cFloors, 0),
            "distance7Day" => number_format(array_sum($distance) / $cDistance, 2),

            "stepsYesterday" => number_format($steps[1] - $steps[0], 0),
            "floorsYesterday" => number_format($floors[1] - $floors[0], 0),
            "distanceYesterday" => number_format($distance[1] - $distance[0], 0),

            "stepsYesterdayRaw" => $steps[1] - $steps[0],
            "floorsYesterdayRaw" => $floors[1] - $floors[0],
            "distanceYesterdayRaw" => $distance[1] - $distance[0]
        ];

        $convertedOutput['date'] = array_reverse($date);
        $convertedOutput['distance'] = array_reverse($distance);
        $convertedOutput['floors'] = array_reverse($floors);
        $convertedOutput['steps'] = array_reverse($steps);
        $convertedOutput['stepsGoal'] = array_reverse($stepsGoal);

        unset($convertedOutput['tracked']);

        return $convertedOutput;
    }

    /**
     * @todo Consider test case
     * @return array|bool
     */
    public function returnUserRecordTrackerHistory()
    {
        $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps",
            ['date', 'distance', 'floors', 'steps'],
            $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $totalsSteps = 0;
        $distanceSteps = 0;
        $floorsSteps = 0;

        foreach ($dbGoals as $key => $value) {
            $totalsSteps += $dbGoals[$key]['steps'];
            $floorsSteps += $dbGoals[$key]['floors'];
            $distanceSteps += $dbGoals[$key]['distance'];

            $dbGoals[$key]['day'] = date("l", strtotime($dbGoals[$key]['date']));
            $dbGoals[$key]['distance'] = round($dbGoals[$key]['distance'], 2);
        }

        if (!is_null($this->getTracking())) {
            $this->getTracking()->track("JSON Goal", $this->getUserID(), "Steps");
        }

        return [
            "totals" => [
                "steps" => round($totalsSteps, 0),
                "distance" => round($distanceSteps, 2),
                "floors" => round($floorsSteps, 0)
            ],
            "human" => [
                "steps" => number_format($totalsSteps, 0),
                "distance" => number_format($distanceSteps, 2),
                "floors" => number_format($floorsSteps, 0)
            ],
            "tracked" => $dbGoals
        ];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordInbox()
    {
        $returnArray = [];
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        $dbInbox = $this->getAppClass()->getDatabase()->select($db_prefix . "inbox",
            [
                'date',
                'ico',
                'icoColour',
                'subject',
                'body',
                'bold'
            ], [
                "AND" => ["fuid" => $this->getUserID(), "expires[>=]" => date("Y-m-d H:i:s")],
                "ORDER" => ['date' => "DESC"]
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        foreach ($dbInbox as $dbInboxItem) {
            if ($dbInboxItem['subject'] == "" && $dbInboxItem['body'] != "") {
                $dbInboxItem['subject'] = $dbInboxItem['body'];
                $dbInboxItem['body'] = "";
            }
            array_push($returnArray, $dbInboxItem);
        }

        return $returnArray;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordPendingRewards()
    {
        $returnArray = [];
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        $dbRewards = $this->getAppClass()->getDatabase()->select($db_prefix . "reward_queue", [
            "[>]" . $db_prefix . "reward_map" => ["rmid" => "rmid"],
            "[>]" . $db_prefix . "rewards" => ["reward" => "rid"]
        ],
            [
                $db_prefix . 'reward_queue.rqid',
                $db_prefix . 'reward_queue.date',
                $db_prefix . 'reward_queue.state',
                $db_prefix . 'reward_map.cat',
                $db_prefix . 'reward_map.event',
                $db_prefix . 'reward_map.rule',
                $db_prefix . 'reward_map.xp',
                $db_prefix . 'reward_map.name',
                $db_prefix . 'rewards.description',
            ], [
                $db_prefix . "reward_queue.fuid" => $this->getUserID(),
                "ORDER" => [$db_prefix . 'reward_queue.date' => "DESC"],
                "LIMIT" => 13
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        foreach ($dbRewards as $dbReward) {

            if (!array_key_exists($dbReward['state'], $returnArray) || !is_array($returnArray[$dbReward['state']])) $returnArray[$dbReward['state']] = [];

            $arrayKeyId = count($returnArray[$dbReward['state']]);

            $returnArray[$dbReward['state']][$arrayKeyId]['date'] = $dbReward['date'];

            if (strtolower($dbReward['cat']) == "nomie" && strtolower($dbReward['event']) == "logged") {
                $returnArray[$dbReward['state']][$arrayKeyId]['action'] = "Logged '" . $dbReward['rule'] . "' with Nomie";
            } elseif (strtolower($dbReward['cat']) == "nomie" && strtolower($dbReward['event']) == "score") {
                $returnArray[$dbReward['state']][$arrayKeyId]['action'] = "Logged a '" . $dbReward['rule'] . "' scoring item with Nomie";
            } elseif (strtolower($dbReward['cat']) == "activity" && strtolower($dbReward['rule']) == "avg2max") {
                $returnArray[$dbReward['state']][$arrayKeyId]['action'] = "Recoreded '" . $dbReward['event'] . "' with above average activity";
            } elseif ($dbReward['name'] != "") {
                $returnArray[$dbReward['state']][$arrayKeyId]['action'] = $dbReward['name'];
            } else {
                $returnArray[$dbReward['state']][$arrayKeyId]['action'] = $dbReward['cat'] . " " . $dbReward['event'] . " " . $dbReward['rule'];
            }

            $returnArray[$dbReward['state']][$arrayKeyId]['action'] = ucwords($returnArray[$dbReward['state']][$arrayKeyId]['action']);

            if (is_numeric($dbReward['xp'])) {
                if ($dbReward['xp'] > 0) {
                    $dbReward['xp'] = "awarded " . $dbReward['xp'];
                } else {
                    $dbReward['xp'] = "subtracted " . ($dbReward['xp'] * -1);
                }
            }

            if ($dbReward['description'] != "" && $dbReward['xp'] != "") {
                $returnArray[$dbReward['state']][$arrayKeyId]['reward'] = $dbReward['description'] . " and " . $dbReward['xp'] . " xp points";
            } else if ($dbReward['description'] != "") {
                $returnArray[$dbReward['state']][$arrayKeyId]['reward'] = $dbReward['description'];
            } else if ($dbReward['xp'] != "") {
                $returnArray[$dbReward['state']][$arrayKeyId]['reward'] = $dbReward['xp'] . " xp points";
            }
            $returnArray[$dbReward['state']][$arrayKeyId]['reward'] = ucwords($returnArray[$dbReward['state']][$arrayKeyId]['reward']);

            $returnArray[$dbReward['state']][$arrayKeyId]['state'] = $dbReward['state'];

            ksort($returnArray[$dbReward['state']]);
        }

        return $returnArray;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordTasker()
    {
        $taskerDataArray = [];

        $minecraftUsername = $this->getAppClass()->getUserSetting($this->getUserID(), "minecraft_username");
        if (!is_null($minecraftUsername)) {
            $taskerDataArray['minecraft'] = [];
            $dbRewards = $this->getAppClass()->getDatabase()->query(
                "SELECT `" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "rewards`.`reward` AS `reward`, `" . $this->getAppClass()->getSetting("db_prefix",
                    null, false) . "reward_map`.`name` AS `name`, `" . $this->getAppClass()->getSetting("db_prefix",
                    null, false) . "reward_queue`.`fuid` AS `fuid`"
                . " FROM `" . $this->getAppClass()->getSetting("db_prefix", null, false) . "rewards`"
                . " JOIN `" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "reward_queue` ON (`" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "reward_queue`.`reward` = `" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "rewards`.`rid`)"
                . " JOIN `" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "reward_map` ON (`" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "reward_map`.`rmid` = `" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "reward_queue`.`rmid`)"
                . " WHERE `" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "reward_queue`.`state` = 'pending'");
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            $data = [];
            $taskerDataArray['minecraft']['count'] = 0;
            foreach ($dbRewards as $dbReward) {
                if (!array_key_exists("reasons", $data)) {
                    $data['reasons'] = [];
                }

                $taskerDataArray['minecraft']['count'] = $taskerDataArray['minecraft']['count'] + 1;
                $dbReward['reward'] = str_replace("%s", $minecraftUsername, $dbReward['reward']);
                if (strpos($dbReward['reward'], 'give ' . $minecraftUsername . ' ') !== false) {
                    $dbReward['reward'] = str_replace('give ' . $minecraftUsername . ' ', '', $dbReward['reward']);
                    $dbReward['reward'] = explode(" ", $dbReward['reward']);

                    if (!array_key_exists("give", $data)) {
                        $data['give'] = [];
                    }
                    if (!array_key_exists($dbReward['reward'][0], $data['give'])) {
                        $data['give'][$dbReward['reward'][0]] = $dbReward['reward'][1];
                        array_push($data['reasons'],
                            $dbReward['name'] . " | give " . $dbReward['reward'][1] . " " . $dbReward['reward'][0]);
                    } else {
                        $data['give'][$dbReward['reward'][0]] = $data['give'][$dbReward['reward'][0]] + $dbReward['reward'][1];
                        array_push($data['reasons'],
                            $dbReward['name'] . " | give " . $dbReward['reward'][1] . " " . $dbReward['reward'][0]);
                    }
                    ksort($data['give']);

                } else {
                    if (!array_key_exists("other", $data) || !is_array($data['other'])) {
                        $data['other'] = [];
                    }
                    array_push($data['other'], str_replace("%s", $minecraftUsername, $dbReward['reward']));
                    array_push($data['reasons'], $dbReward['name'] . " | " . $dbReward['reward']);
                }

            }
            $taskerDataArray['minecraft']['rewards'] = $data;

        }

        $taskerDataArray['snapshot'] = [];

        $returnUserRecordWater = $this->returnUserRecordWater();
        $taskerDataArray['snapshot']['today']['water'] = round(($returnUserRecordWater[0]['liquid'] / $returnUserRecordWater[0]['goal']) * 100,
            0);
        $taskerDataArray['snapshot']['cheer']['water'] = $returnUserRecordWater[0]['cheer'];

        $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps", [
            'distance',
            'floors',
            'steps',
            'syncd'
        ], $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);
        $dbGoals = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps_goals", [
            'distance',
            'floors',
            'steps',
            'syncd'
        ], $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);
        $taskerDataArray['snapshot']['today']['steps'] = round(($dbSteps[0]['steps'] / $dbGoals[0]['steps']) * 100,
            0);
        $taskerDataArray['snapshot']['today']['distance'] = round((round($dbSteps[0]['distance'],
                    2) / round($dbGoals[0]['distance'], 2)) * 100, 0);
        $taskerDataArray['snapshot']['today']['floors'] = round(($dbSteps[0]['floors'] / $dbGoals[0]['floors']) * 100,
            0);

        $taskerDataArray['snapshot']['goals']['steps'] = $dbGoals[0]['steps'];
        $taskerDataArray['snapshot']['goals']['distance'] = round($dbGoals[0]['distance'], 2);
        $taskerDataArray['snapshot']['goals']['floors'] = $dbGoals[0]['floors'];

        $dbActive = $this->getAppClass()->getDatabase()->query("SELECT target, fairlyactive, veryactive, syncd FROM "
            . $this->getAppClass()->getSetting("db_prefix", null,
                false) . "activity WHERE user = '" . $this->getUserID() . "' AND date = '" . date("Y-m-d") . "'")->fetchAll();
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $dbActive = $dbActive[0];
        $taskerDataArray['snapshot']['today']['active'] = round((($dbActive['fairlyactive'] + $dbActive['veryactive']) / $dbActive['target']) * 100,
            2);
        $taskerDataArray['snapshot']['goals']['active'] = $dbActive['target'];

        $taskerDataArray['syncd']['active'] = $dbActive['syncd'];
        $taskerDataArray['syncd']['steps'] = $dbSteps[0]['syncd'];
        $taskerDataArray['syncd']['distance'] = $dbSteps[0]['syncd'];
        $taskerDataArray['syncd']['floors'] = $dbSteps[0]['syncd'];
        $taskerDataArray['syncd']['goals'] = $dbGoals[0]['syncd'];

        $cheer = ["distance" => 0, "floors" => 0, "steps" => 0];
        foreach ($cheer as $key => $value) {
            $taskerDataArray['snapshot']['raw'][$key] = round($dbSteps[0][$key], 2);

            if ($dbGoals[0][$key] > 0) {
                if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 3) {
                    $taskerDataArray['snapshot']['cheer'][$key] = 7;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 2.5) {
                    $taskerDataArray['snapshot']['cheer'][$key] = 6;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 2) {
                    $taskerDataArray['snapshot']['cheer'][$key] = 5;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 1.5) {
                    $taskerDataArray['snapshot']['cheer'][$key] = 4;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key]) {
                    $taskerDataArray['snapshot']['cheer'][$key] = 3;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 0.75) {
                    $taskerDataArray['snapshot']['cheer'][$key] = 2;
                } else if ($dbSteps[0][$key] >= $dbGoals[0][$key] * 0.5) {
                    $taskerDataArray['snapshot']['cheer'][$key] = 1;
                } else {
                    $taskerDataArray['snapshot']['cheer'][$key] = 0;
                }
            } else {
                $taskerDataArray['snapshot']['cheer'][$key] = 0;
            }
        }

        $returnUserRecordPush = $this->returnUserRecordPush();
        if (array_key_exists('current', $returnUserRecordPush)) {
            $taskerDataArray['push']['active'] = ($returnUserRecordPush['current']['active'] / $returnUserRecordPush['current']['active_g']) * 100;

            $taskerDataArray['push']['state'] = $returnUserRecordPush['pushActive'];

            $taskerDataArray['push']['start_date'] = $returnUserRecordPush['next']['startDateF'];
            $taskerDataArray['push']['end_date'] = $returnUserRecordPush['next']['endDateF'];

            $taskerDataArray['push']['length'] = round(($returnUserRecordPush['current']['day'] / $returnUserRecordPush['pushLength']) * 100,
                0);
            $taskerDataArray['push']['day'] = round(($returnUserRecordPush['current']['day_past'] / $returnUserRecordPush['current']['day']) * 100,
                0);

            $taskerDataArray['push']['distance'] = round(($returnUserRecordPush['current']['distance'] / $returnUserRecordPush['current']['distance_g']) * 100,
                0);
            $taskerDataArray['push']['active'] = round(($returnUserRecordPush['current']['active'] / $returnUserRecordPush['current']['active_g']) * 100,
                0);
            $taskerDataArray['push']['steps'] = round(($returnUserRecordPush['current']['steps'] / $returnUserRecordPush['current']['steps_g']) * 100,
                0);
        }

        $returnUserRecordFood = $this->returnUserRecordFood();
        if (array_key_exists("total", $returnUserRecordFood) && array_key_exists("goal", $returnUserRecordFood)) {
            $taskerDataArray['snapshot']['today']['food'] = round(($returnUserRecordFood['total'] / $returnUserRecordFood['goal']) * 100,
                2);
        } else {
            $taskerDataArray['snapshot']['today']['food'] = 0;
        }

        if (!is_null($this->getTracking())) {
            $this->getTracking()->track("JSON Get", $this->getUserID(), "Tasker");
            $this->getTracking()->track("JSON Goal", $this->getUserID(), "Tasker");
        }

        ksort($taskerDataArray['snapshot']['today']);
        ksort($taskerDataArray['snapshot']['cheer']);
        ksort($taskerDataArray['snapshot']['goals']);
        ksort($taskerDataArray['syncd']);
        ksort($taskerDataArray['snapshot']['raw']);

        $taskerDataArray['devices'] = $this->returnUserRecordDevices();
        $taskerDataArray['streak'] = $this->returnUserRecordGoalStreak();

        $taskerDataArray['journeys'] = [];
        $returnUserRecordJourneysState = $this->returnUserRecordJourneysState();
        if (array_key_exists("msg",
                $returnUserRecordJourneysState) && $returnUserRecordJourneysState['msg'] == "Not on any jounry"
        ) {
            $taskerDataArray['journeys']['name'] = "Not on any journey";
        } else {
            if (is_array($returnUserRecordJourneysState) && count($returnUserRecordJourneysState) >= 2) {
                $returnUserRecordJourneysState = $returnUserRecordJourneysState[1];
                $taskerDataArray['journeys']['name'] = $returnUserRecordJourneysState['name'];
                $taskerDataArray['journeys']['blurb'] = $returnUserRecordJourneysState['blurb'];
                $taskerDataArray['journeys']['start_date'] = $returnUserRecordJourneysState['start_date'];
                $returnUserRecordJourneys = $this->returnUserRecordJourneys();
                $returnUserRecordJourneys = $returnUserRecordJourneys[1];
                $taskerDataArray['journeys']['progress'] = $returnUserRecordJourneys['legs_progress'][1];

                $taskerDataArray['journeys']['legs'] = [
                    "last" => [
                        "name" => $returnUserRecordJourneysState['legs']['last']['legs_names'],
                        "miles" => $returnUserRecordJourneysState['legs']['last']['miles'],
                        "miles_off" => $returnUserRecordJourneysState['legs']['last']['miles_off'],
                        "subtitle" => $returnUserRecordJourneysState['legs']['last']['subtitle'],
                        "narrative" => $returnUserRecordJourneysState['legs']['last']['narrative']
                    ],
                    "next" => [
                        "name" => $returnUserRecordJourneysState['legs']['next']['legs_names'],
                        "miles" => $returnUserRecordJourneysState['legs']['next']['miles'],
                        "miles_off" => $returnUserRecordJourneysState['legs']['next']['miles_off'],
                        "subtitle" => $returnUserRecordJourneysState['legs']['next']['subtitle'],
                        "narrative" => $returnUserRecordJourneysState['legs']['next']['narrative']
                    ]
                ];
            }
        }

        $taskerDataArray['xp'] = $this->returnUserRecordXp();
        $taskerDataArray['xp']['ico'] = $this->getAppClass()->getSetting("http/admin") . "/img/xplevels/" . $taskerDataArray['xp']['level'] . ".png";
        $taskerDataArray['xp']['icoclass'] = $this->getAppClass()->getSetting("http/admin") . "/img/xplevels/" . strtolower($taskerDataArray['xp']['class']) . ".png";

        return $taskerDataArray;
    }

    /**
     * @todo Consider test case
     * @return array|bool
     */
    public function returnUserRecordWater()
    {
        $dbWater = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "water",
            ['date', 'liquid'],
            $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $dbWater[0]['liquid'] = (String)round($dbWater[0]['liquid'], 2);
        $dbWater[0]['goal'] = $this->getAppClass()->getUserSetting($this->getUserID(), "goal_water", '200');

        if ($dbWater[0]['liquid'] >= $dbWater[0]['goal'] * 3) {
            $dbWater[0]['cheer'] = 5;
        } else if ($dbWater[0]['liquid'] >= $dbWater[0]['goal'] * 2.5) {
            $dbWater[0]['cheer'] = 4;
        } else if ($dbWater[0]['liquid'] >= $dbWater[0]['goal'] * 2) {
            $dbWater[0]['cheer'] = 3;
        } else if ($dbWater[0]['liquid'] >= $dbWater[0]['goal'] * 1.5) {
            $dbWater[0]['cheer'] = 2;
        } else if ($dbWater[0]['liquid'] >= $dbWater[0]['goal']) {
            $dbWater[0]['cheer'] = 1;
        } else {
            $dbWater[0]['cheer'] = 0;
        }

        if (!is_null($this->getTracking())) {
            $this->getTracking()->track("JSON Get", $this->getUserID(), "Water");
            $this->getTracking()->track("JSON Goal", $this->getUserID(), "Water");
        }

        return $dbWater;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordFood()
    {
        $dbFoodLog = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "food",
            ['meal', 'calories'],
            $this->dbWhere(4));
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        if (count($dbFoodLog) > 0) {
            $total = 0;
            foreach ($dbFoodLog as $meal) {
                $total = $total + $meal['calories'];
            }

            $dbFoodGoal = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "food_goals",
                ['calories'],
                $this->dbWhere());
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            if (!is_null($this->getTracking())) {
                $this->getTracking()->track("JSON Get", $this->getUserID(), "Food");
                $this->getTracking()->track("JSON Goal", $this->getUserID(), "Food");
            }

            return ['goal' => $dbFoodGoal[0]['calories'], 'total' => $total, "meals" => $dbFoodLog];
        } else {
            return ["error" => "true", "code" => 104, "msg" => "No results for given date"];
        }
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordDevices()
    {
        $dbDevices = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "devices", [
            "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                false) . "devices_user" => ["id" => "device"]
        ],
            [
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'devices.id',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'devices.deviceVersion',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'devices.battery',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'devices.lastSyncTime',
                $this->getAppClass()->getSetting("db_prefix", null, false) . 'devices.type',
            ], [
                $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "devices_user.user" => $this->getUserID()
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        foreach ($dbDevices as $key => $dev) {
            $dbDevices[$key]['image'] = 'images/devices/' . str_ireplace(" ", "",
                    $dbDevices[$key]['deviceVersion']) . ".png";
            $dbDevices[$key]['imageSmall'] = 'images/devices/' . str_ireplace(" ", "",
                    $dbDevices[$key]['deviceVersion']) . "_small.png";

            $dbDevices[$key]['charges'] = $this->getAppClass()->getDatabase()->count($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "devices_charges", [
                "AND" => [
                    "charged" => 1,
                    "id" => $dbDevices[$key]['id']
                ]
            ]);

            if (strtolower($dbDevices[$key]['battery']) == "high") {
                $dbDevices[$key]['precentage'] = 100;
            } else if (strtolower($dbDevices[$key]['battery']) == "medium") {
                $dbDevices[$key]['precentage'] = 50;
            } else if (strtolower($dbDevices[$key]['battery']) == "low") {
                $dbDevices[$key]['precentage'] = 10;
            } else if (strtolower($dbDevices[$key]['battery']) == "empty") {
                $dbDevices[$key]['precentage'] = 0;
            }

            $dbDevices[$key]['unixTime'] = strtotime($dbDevices[$key]['lastSyncTime']);
            if ($dbDevices[$key]['type'] == "TRACKER") {
                $dbDevices[$key]['testTime'] = strtotime('now') - (4 * 60 * 60);
            } else {
                $dbDevices[$key]['testTime'] = strtotime('now') - (48 * 60 * 60);
            }

            if ($dbDevices[$key]['testTime'] > $dbDevices[$key]['unixTime']) {
                $dbDevices[$key]['alertTime'] = 1;
            } else {
                $dbDevices[$key]['alertTime'] = 0;
            }
        }

        return $dbDevices;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordGoalStreak()
    {
        $taskerDataArray = [
            "avg" => [
                "days" => round($this->getAppClass()->getDatabase()->avg($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "streak_goal", ['length'], ["fuid" => $this->getUserID()]), 0)
            ]
        ];

        $taskerDataArray['current'] = [];
        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "streak_goal",
            ["AND" => ["fuid" => $this->getUserID(), "goal" => "steps", "end_date" => null]]
        )
        ) {
            $currentStreakStart = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "streak_goal", [
                "start_date",
                "length"
            ],
                ["AND" => ["fuid" => $this->getUserID(), "goal" => "steps", "end_date" => null]]);

            $taskerDataArray['current']['start'] = $currentStreakStart["start_date"];
            $taskerDataArray['current']['days'] = $currentStreakStart["length"];
        } else {
            $taskerDataArray['current']['start'] = date('Y-m-d');
            $taskerDataArray['current']['days'] = 0;
        }

        if ($taskerDataArray['current']['days'] > 0) {
            $taskerDataArray['avg']['dist'] = round(($taskerDataArray['current']['days'] / $taskerDataArray['avg']['days']) * 100,
                0);
        } else {
            $taskerDataArray['avg']['dist'] = 0;
        }

        $taskerDataArray['max'] = [];
        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "streak_goal",
            ["AND" => ["fuid" => $this->getUserID(), "goal" => "steps", "end_date[!]" => null]]
        )
        ) {
            $databaseResults = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "streak_goal", [
                "start_date",
                "end_date",
                "length"
            ],
                [
                    "AND" => ["fuid" => $this->getUserID(), "goal" => "steps", "end_date[!]" => null],
                    "ORDER" => ["length" => "DESC"]
                ]);

            $taskerDataArray['max']['start'] = $databaseResults['start_date'];
            $taskerDataArray['max']['end'] = $databaseResults['end_date'];
            $taskerDataArray['max']['days'] = $databaseResults['length'];
            if ($taskerDataArray['current']['days'] > 0) {
                $taskerDataArray['max']['dist'] = round(($taskerDataArray['current']['days'] / $databaseResults['length']) * 100,
                    0);
            } else {
                $taskerDataArray['max']['dist'] = 0;
            }
        } else {
            $taskerDataArray['max']['start'] = date('Y-m-d');
            $taskerDataArray['max']['end'] = date('Y-m-d');
            $taskerDataArray['max']['days'] = 0;
            $taskerDataArray['max']['dist'] = 0;
        }

        $taskerDataArray['last'] = [];
        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "streak_goal",
            ["AND" => ["fuid" => $this->getUserID(), "goal" => "steps", "end_date[!]" => null]]
        )
        ) {
            $databaseResults = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "streak_goal", [
                "start_date",
                "end_date",
                "length"
            ],
                [
                    "AND" => ["fuid" => $this->getUserID(), "goal" => "steps", "end_date[!]" => null],
                    "ORDER" => ["start_date" => "DESC"]
                ]);

            $taskerDataArray['last']['start'] = $databaseResults['start_date'];
            $taskerDataArray['last']['end'] = $databaseResults['end_date'];
            $taskerDataArray['last']['days'] = $databaseResults['length'];
            if ($taskerDataArray['current']['days'] > 0) {
                $taskerDataArray['last']['dist'] = round(($taskerDataArray['last']['days'] / $databaseResults['length']) * 100,
                    0);
            } else {
                $taskerDataArray['last']['dist'] = 0;
            }
        } else {
            $taskerDataArray['last']['start'] = date('Y-m-d');
            $taskerDataArray['last']['end'] = date('Y-m-d');
            $taskerDataArray['last']['days'] = 0;
            $taskerDataArray['last']['dist'] = 0;
        }

        return $taskerDataArray;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordJourneys()
    {
        if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                false) . "journeys_travellers", ["fuid" => $this->getUserID()])
        ) {
            $dbJourneys = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "journeys_travellers", [
                "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                    false) . "journeys" => ["jid" => "jid"]
            ],
                [
                    $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys.jid',
                    $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys.name',
                    $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys.blurb',
                    $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_travellers.start_date',
                ], [
                    $this->getAppClass()->getSetting("db_prefix", null,
                        false) . "journeys_travellers.fuid" => $this->getUserID()
                ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            $journeys = [];
            foreach ($dbJourneys as $dbJourney) {
                $user_miles_travelled = $this->getUserMilesSince($dbJourney['start_date']);

                $dbLegs = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "journeys_legs", [
                    "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                        false) . "journeys" => ["jid" => "jid"]
                ],
                    [
                        $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_legs.lid',
                        $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_legs.name',
                        $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_legs.end_mile',
                    ], [
                        "AND" => [
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . "journeys.jid" => $dbJourney['jid'],
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . "journeys_legs.start_mile[<=]" => $user_miles_travelled,
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . "journeys_legs.end_mile[>=]" => $user_miles_travelled
                        ]
                    ]);
                $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                    [
                        "METHOD" => __METHOD__,
                        "LINE" => __LINE__
                    ]);

                $legs = [];
                $legsNames = [];
                $legsProgress = [];
                foreach ($dbLegs as $dbLeg) {
                    // Get all narative items the user has completed
                    $dbNarratives = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                            null, false) . "journeys_narrative", [
                        "[>]" . $this->getAppClass()->getSetting("db_prefix", null,
                            false) . "journeys_legs" => ["lid" => "lid"]
                    ],
                        [
                            $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_narrative.nid',
                            $this->getAppClass()->getSetting("db_prefix", null, false) . 'journeys_narrative.miles',
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . 'journeys_narrative.subtitle',
                            $this->getAppClass()->getSetting("db_prefix", null,
                                false) . 'journeys_narrative.narrative',
                        ], [
                            "AND" => [
                                $this->getAppClass()->getSetting("db_prefix", null,
                                    false) . "journeys_narrative.lid" => $dbLeg['lid']
                            ]
                        ]);
                    $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(),
                        [
                            "METHOD" => __METHOD__,
                            "LINE" => __LINE__
                        ]);

                    $narrative = [];
                    $prevNarrativeMiles = 0;
                    foreach ($dbNarratives as $dbNarrative) {
                        $narrativeArray = [
                            "miles" => $dbNarrative['miles'],
                            "miles_travelled" => $dbNarrative['miles'] - $prevNarrativeMiles,
                            "miles_off" => 0,
                            "subtitle" => $dbNarrative['subtitle'],
                            "narrative" => $dbNarrative['narrative']
                        ];
                        $prevNarrativeMiles = $dbNarrative['miles'];

                        if ($dbNarrative['miles'] > $user_miles_travelled) {
                            $narrativeArray["miles_off"] = number_format($dbNarrative['miles'] - $user_miles_travelled,
                                2);
                        }

                        array_push($narrative, $narrativeArray);
                    }

                    $legsProgress[$dbLeg['lid']] = number_format((($user_miles_travelled / $dbLeg['end_mile']) * 100),
                        2);
                    $legsNames[$dbLeg['lid']] = $dbLeg['name'];
                    $legs[$dbLeg['lid']] = $narrative;
                }

                $journeys[$dbJourney['jid']] = [
                    "name" => $dbJourney['name'],
                    "start_date" => $dbJourney['start_date'],
                    "usrMiles" => number_format($this->getUserMilesSince($dbJourney['start_date']), 2),
                    "blurb" => $dbJourney['blurb'],
                    "legs_names" => $legsNames,
                    "legs_progress" => $legsProgress,
                    "legs" => $legs
                ];
            }

            return $journeys;
        } else {
            return ["error" => "true", "code" => 104, "msg" => "Not on any jounry"];
        }
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordXp()
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        if (!$this->getAppClass()->getDatabase()->has($db_prefix . "users_xp", ['fuid' => $this->getUserID()])) {
            $this->getAppClass()->getDatabase()->insert($db_prefix . "users_xp", ["class" => "Rebel", "xp" => 0, "mana" => 0, "level" => 0, "percent" => 0, "gold" => 0, "health" => 100, "fuid" => $this->getUserID()]);
            $return = ["class" => "Rebel", "xp" => 0, "mana" => 0, "level" => 0, "percent" => 0, "gold" => 0, "health" => 100];
        } else {
            $return = $this->getAppClass()->getDatabase()->get($db_prefix . "users_xp", ['class','xp','mana','health','gold','level','percent'], ["fuid" => $this->getUserID()]);
        }

        $currancy = explode('.', $return['gold']);
        $return['gold'] = $currancy[0];
        $return['silver'] = $currancy[1];

        if ($return['level'] > 100)
            $return['level'] = 100;

        $return['ico'] = $this->getAppClass()->getSetting("http/admin") . "/img/xplevels/" . $return['level'] . ".png";
        $return['icoclass'] = $this->getAppClass()->getSetting("http/admin") . "/img/xplevels/" . strtolower($return['class']) . ".png";
        $return['avatar'] = $this->getAppClass()->getSetting("http/") . "/images/avatars/" . $this->getUserID() . "_habitica.png";

        return $return;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordTopBadges()
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix",null, false);
        $userBadges = $this->getAppClass()->getDatabase()->select($db_prefix . "bages_user",
            ["[>]" . $db_prefix . "bages" => ["badgeid" => "encodedId"]],
            [$db_prefix . 'bages.badgeType', $db_prefix . 'bages.value', $db_prefix . 'bages_user.dateTime', $db_prefix . 'bages_user.timesAchieved'],
            [$db_prefix . 'bages_user.fuid' => $this->getUserID()]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $badges = [];
        foreach ($userBadges as $userBadge) {
            if (!array_key_exists($userBadge['badgeType'], $badges)) {
                $badges[$userBadge['badgeType']] = [];
                $badges[$userBadge['badgeType']]['type'] = $userBadge['badgeType'];
                $badges[$userBadge['badgeType']]['value'] = $userBadge['value'];
                $badges[$userBadge['badgeType']]['dateTime'] = $userBadge['dateTime'];
                $badges[$userBadge['badgeType']]['timesAchieved'] = $userBadge['timesAchieved'];
            } else if ($userBadge['value'] > $badges[$userBadge['badgeType']]['value']) {
                $badges[$userBadge['badgeType']]['value'] = $userBadge['value'];
                $badges[$userBadge['badgeType']]['dateTime'] = $userBadge['dateTime'];
                $badges[$userBadge['badgeType']]['timesAchieved'] = $userBadge['timesAchieved'];
            }
        }

        foreach ($badges as $badge) {
            /** @var array $dbBadge */
            $dbBadge = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "bages",
                [
                    'image',
                    'badgeGradientEndColor',
                    'badgeGradientStartColor',
                    'earnedMessage',
                    'marketingDescription',
                    'name'
                ],
                ["AND" => ["badgeType" => $badge['type'], "value" => $badge['value']]]);
            $badges[$badge['type']] = array_merge($badges[$badge['type']], $dbBadge);
        }

        return ["images" => "images/badges/", "badges" => $badges];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordTracked()
    {
        $nx_fitbit_steps = $this->getAppClass()->getSetting("db_prefix", null, false) . 'steps';
        $nx_fitbit_steps_goals = $this->getAppClass()->getSetting("db_prefix", null, false) . 'steps_goals';

        $days = $this->getParamPeriod();
        $days = str_ireplace("last", "", $days);
        $then = date('Y-m-d', strtotime($this->getParamDate() . " -" . $days . " day"));

        $dbSteps = $this->getAppClass()->getDatabase()->query(
            "SELECT `$nx_fitbit_steps`.`date`,`$nx_fitbit_steps`.`floors`,`$nx_fitbit_steps`.`steps`,`$nx_fitbit_steps_goals`.`floors` AS `floors_g`,`$nx_fitbit_steps_goals`.`steps` AS `steps_g`"
            . " FROM `$nx_fitbit_steps`"
            . " JOIN `$nx_fitbit_steps_goals` ON (`$nx_fitbit_steps`.`date` = `$nx_fitbit_steps_goals`.`date`) AND (`$nx_fitbit_steps`.`user` = `$nx_fitbit_steps_goals`.`user`)"
            . " WHERE `$nx_fitbit_steps`.`user` = '" . $this->getUserID() . "' AND `$nx_fitbit_steps`.`date` <= '" . $this->getParamDate() . "' AND `$nx_fitbit_steps`.`date` >= '$then' "
            . " ORDER BY `$nx_fitbit_steps`.`date` DESC LIMIT $days");
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $returnDate = null;
        $graph_floors = [];
        $graph_floors_g = [];
        $graph_floors_min = 0;
        $graph_floors_max = 0;
        $graph_steps = [];
        $graph_steps_g = [];
        $graph_steps_min = 0;
        $graph_steps_max = 0;
        foreach ($dbSteps as $dbValue) {
            if (is_null($returnDate)) {
                $returnDate = explode("-", $dbValue['date']);
            }

            array_push($graph_floors, (String)round($dbValue['floors'], 0));
            array_push($graph_floors_g, (String)round($dbValue['floors_g'], 0));
            if ($dbValue['floors'] < $graph_floors_min || $graph_floors_min == 0) {
                $graph_floors_min = $dbValue['floors'];
            }
            if ($dbValue['floors'] > $graph_floors_max || $graph_floors_max == 0) {
                $graph_floors_max = $dbValue['floors'];
            }

            array_push($graph_steps, (String)round($dbValue['steps'], 0));
            array_push($graph_steps_g, (String)round($dbValue['steps_g'], 0));
            if ($dbValue['steps'] < $graph_steps_min || $graph_steps_min == 0) {
                $graph_steps_min = $dbValue['steps'];
            }
            if ($dbValue['steps'] > $graph_steps_max || $graph_steps_max == 0) {
                $graph_steps_max = $dbValue['steps'];
            }
        }

        $dbActive = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "activity",
            [
                'target',
                'fairlyactive',
                'veryactive'
            ],
            [
                "AND" => [
                    "user" => $this->getUserID(),
                    "date[>=]" => $then,
                    "date[<=]" => $this->getParamDate()
                ],
                "ORDER" => ["date" => "DESC"]
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $graph_active = [];
        $graph_active_g = [];
        $graph_active_min = 0;
        $graph_active_max = 0;

        foreach ($dbActive as $dbValue) {
            array_push($graph_active, (String)round($dbValue['fairlyactive'] + $dbValue['veryactive'], 0));
            array_push($graph_active_g, (String)round($dbValue['target'], 0));

            if (($dbValue['fairlyactive'] + $dbValue['veryactive']) < $graph_active_min) {
                $graph_active_min = $dbValue['fairlyactive'] + $dbValue['veryactive'];
            }
            if (($dbValue['fairlyactive'] + $dbValue['veryactive']) > $graph_active_max) {
                $graph_active_max = $dbValue['fairlyactive'] + $dbValue['veryactive'];
            }
        }

        $goalCalcSteps = $this->returnUserRecordStepGoal();
        $goalCalcFloors = $this->returnUserRecordFloorGoal();
        $goalCalcActive = $this->returnUserRecordActiveGoal();

        return [
            'returnDate' => $returnDate,

            'graph_steps' => $graph_steps,
            'graph_steps_g' => $graph_steps_g,
            'graph_steps_min' => $graph_steps_min,
            'graph_steps_max' => $graph_steps_max,
            'imp_steps' => $this->getAppClass()->getUserSetting($this->getUserID(), "desire_steps", 10) . "%",
            'avg_steps' => number_format($goalCalcSteps['newTargetSteps'], 0),
            'newgoal_steps' => number_format($goalCalcSteps['plusTargetSteps'], 0),
            'maxgoal_steps' => number_format($this->getAppClass()->getUserSetting($this->getUserID(),
                "desire_steps_max", 10000), 0),

            'graph_floors' => $graph_floors,
            'graph_floors_g' => $graph_floors_g,
            'graph_floors_min' => $graph_floors_min,
            'graph_floors_max' => $graph_floors_max,
            'imp_floors' => $this->getAppClass()->getUserSetting($this->getUserID(), "desire_floors",
                    10) . "%",
            'avg_floors' => number_format($goalCalcFloors['newTargetFloors'], 0),
            'newgoal_floors' => number_format($goalCalcFloors['plusTargetFloors'], 0),
            'maxgoal_floors' => number_format($this->getAppClass()->getUserSetting($this->getUserID(),
                "desire_floors_max", 20), 0),

            'graph_active' => $graph_active,
            'graph_active_g' => $graph_active_g,
            'graph_active_min' => $graph_active_min,
            'graph_active_max' => $graph_active_max,
            'imp_active' => $this->getAppClass()->getUserSetting($this->getUserID(), "desire_active",
                    10) . "%",
            'avg_active' => number_format($goalCalcActive['newTargetFloors'], 0),
            'newgoal_active' => number_format($goalCalcActive['plusTargetFloors'], 0),
            'maxgoal_active' => number_format($this->getAppClass()->getUserSetting($this->getUserID(),
                "desire_active_max", 30), 0)
        ];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordStepGoal()
    {
        $lastMonday = date('Y-m-d', strtotime('last sunday'));
        $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));

        $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps", 'steps',
            [
                "AND" => [
                    "user" => $this->getUserID(),
                    "date[>=]" => $oneWeek,
                    "date[<=]" => $lastMonday
                ],
                "ORDER" => ["date" => "DESC"],
                "LIMIT" => 7
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $totalSteps = 0;
        foreach ($dbSteps as $dbStep) {
            $totalSteps = $totalSteps + $dbStep;
        }

        $newTargetSteps = round($totalSteps / count($dbSteps), 0);
        if ($newTargetSteps < $this->getAppClass()->getUserSetting($this->getUserID(), "desire_steps_max", 10000)) {
            $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getUserSetting($this->getUserID(),
                            "desire_steps", 10) / 100), 0);
        } else {
            $plusTargetSteps = $this->getAppClass()->getUserSetting($this->getUserID(), "desire_steps_max", 10000);
        }

        return [
            "weekStart" => $lastMonday,
            "weekEnd" => $oneWeek,
            "totalSteps" => $totalSteps,
            "newTargetSteps" => $newTargetSteps,
            "plusTargetSteps" => $plusTargetSteps
        ];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordFloorGoal()
    {
        $lastMonday = date('Y-m-d', strtotime('last sunday'));
        $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));

        $dbSteps = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "steps", 'floors',
            [
                "AND" => [
                    "user" => $this->getUserID(),
                    "date[>=]" => $oneWeek,
                    "date[<=]" => $lastMonday
                ],
                "ORDER" => ["date" => "DESC"],
                "LIMIT" => 7
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $totalSteps = 0;
        foreach ($dbSteps as $dbStep) {
            $totalSteps = $totalSteps + $dbStep;
        }

        $newTargetSteps = round($totalSteps / count($dbSteps), 0);
        if ($newTargetSteps < $this->getAppClass()->getUserSetting($this->getUserID(), "desire_floors_max", 20)) {
            $plusTargetSteps = $newTargetSteps + round($newTargetSteps * ($this->getAppClass()->getUserSetting($this->getUserID(),
                            "desire_floors", 10) / 100), 0);
        } else {
            $plusTargetSteps = $this->getAppClass()->getUserSetting($this->getUserID(), "desire_floors_max", 20);
        }

        return [
            "weekStart" => $lastMonday,
            "weekEnd" => $oneWeek,
            "totalFloors" => $totalSteps,
            "newTargetFloors" => $newTargetSteps,
            "plusTargetFloors" => $plusTargetSteps
        ];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordActiveGoal()
    {
        $lastMonday = date('Y-m-d', strtotime('last sunday'));
        $oneWeek = date('Y-m-d', strtotime($lastMonday . ' -6 days'));

        $dbActiveMinutes = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                null, false) . "activity", [
            'veryactive',
            'fairlyactive'
        ],
            [
                "AND" => [
                    "user" => $this->getUserID(),
                    "date[>=]" => $oneWeek,
                    "date[<=]" => $lastMonday
                ],
                "ORDER" => ["date" => "DESC"],
                "LIMIT" => 7
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $totalMinutes = 0;
        foreach ($dbActiveMinutes as $dbStep) {
            $totalMinutes = $totalMinutes + $dbStep['veryactive'] + $dbStep['fairlyactive'];
        }

        $newTargetActive = round($totalMinutes / count($dbActiveMinutes), 0);
        if ($newTargetActive < $this->getAppClass()->getUserSetting($this->getUserID(), "desire_active_max", 30)) {
            $plusTargetActive = $newTargetActive + round($newTargetActive * ($this->getAppClass()->getUserSetting($this->getUserID(),
                            "desire_active", 10) / 100), 0);
        } else {
            $plusTargetActive = $this->getAppClass()->getUserSetting($this->getUserID(), "desire_active_max", 30);
        }

        return [
            "weekStart" => $lastMonday,
            "weekEnd" => $oneWeek,
            "totalFloors" => $totalMinutes,
            "newTargetFloors" => $newTargetActive,
            "plusTargetFloors" => $plusTargetActive
        ];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordTrend()
    {
        $trendArray = [];

        $estimation = $this->returnUserRecordWeightLossForcast();

        $dbBody = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null, false) . "body", ['date', 'fat', 'fatGoal'], ["user" => $this->getUserID(), "ORDER" => ["date" => "ASC"]]);
        $trendArray['weeksWeightTracked'] = round(abs(strtotime($this->getParamDate()) - strtotime($dbBody['date'])) / 604800, 0);

        $trendArray['fat'] = number_format($dbBody['fat']);
        $trendArray['fatToLose'] = number_format($dbBody['fat'] - $dbBody['fatGoal']);
        $trendArray['fatGoal'] = number_format($dbBody['fatGoal']);

        $userWeightUnits = $this->getAppClass()->getUserSetting($this->getUserID(), "unit_weight", "kg");

        $trendArray['weight'] = $this->convertWeight($estimation['weight'], $userWeightUnits) . " " . $userWeightUnits;
        $trendArray['weightToLose'] = $this->convertWeight($estimation['DesiredLoss'], $userWeightUnits) . " " . $userWeightUnits;
        $trendArray['weightGoal'] = $this->convertWeight($estimation['weightGoal'], $userWeightUnits) . " " . $userWeightUnits;

        $trendArray['estimatedDate'] = date("l", strtotime($estimation['EstDate'])) . " the " . date("jS \of F Y", strtotime($estimation['EstDate']));
        $trendArray['estimatedWeeks'] = round($estimation['EstWeeks'], 0);
        $trendArray['caldef'] = $estimation['caldef'];

        $dbUsers = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null, false) . "users", ['name', 'rank', 'friends', 'distance', 'gender'], ["fuid" => $this->getUserID()]);
        $trendArray['rank'] = $dbUsers['rank'];
        $trendArray['friends'] = $dbUsers['friends'];
        $trendArray['nextRank'] = number_format($dbUsers['distance'], 0);
        $trendArray['name'] = explode(" ", $dbUsers['name']);
        $trendArray['name'] = $trendArray['name'][0];

        if ($dbUsers['gender'] == "MALE") {
            $trendArray['he'] = "he";
            $trendArray['his'] = "his";
        } else {
            $trendArray['he'] = "she";
            $trendArray['his'] = "her";
        }

        return $trendArray;
    }

    /**
     * @todo Consider test case
     * @return array|bool
     */
    public function returnUserRecordWeightLossForcast()
    {
        $return = array();

        $dbSteps = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null, false) . "steps", ['caloriesOut'], ["user" => $this->getUserID(), "ORDER" => ["date" => "DESC"]]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

        $dbfood = $this->getAppClass()->getDatabase()->sum($this->getAppClass()->getSetting("db_prefix", null, false) . "food", ['calories'], ["AND" => ["user" => $this->getUserID(), "date" => $this->getParamDate()], "ORDER" => ["date" => "DESC"]]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

        $dbWeight = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix", null, false) . "body", ['date', 'weight', 'weightGoal'], ["AND" => ["user" => $this->getUserID(), "date[<=]" => $this->getParamDate()], "ORDER" => ["date" => "DESC"]]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), ["METHOD" => __METHOD__, "LINE" => __LINE__]);

        $return['caldef'] = (String)($dbSteps['caloriesOut'] - $dbfood);
        $return['weight'] = $dbWeight['weight'];
        $return['weightGoal'] = $dbWeight['weightGoal'];

        $return['DesiredLoss'] = $return['weight'] - $return['weightGoal'];
        $return['WeightLossWeekly'] = round((7 * $return['caldef']) / 7716, 2);
        $return['EstWeeks'] = round($return['DesiredLoss'] / $return['WeightLossWeekly'], 0);

        $return['StartDate'] = $dbWeight['date'];
        $return['EstDate'] = date('Y-m-d', strtotime($dbWeight['date'] . " +" . $return['EstWeeks'] . " week"));

        return $return;
    }

    /**
     * @param double|array $inputWeight
     * @param string $convertUnits
     *
     * @return array|float
     */
    private function convertWeight($inputWeight, $convertUnits)
    {
        $conversationUnit = 1;

        if ($convertUnits == "kg") {
            return $inputWeight;
        } else if ($convertUnits == "lb") {
            $conversationUnit = 2.20462;
        }

        if (!is_array($inputWeight)) {
            return round($inputWeight * $conversationUnit, 2);
        } else {
            foreach ($inputWeight as $key => $value) {
                $inputWeight[$key] = round($value * $conversationUnit, 2);
            }
        }

        return $inputWeight;
    }

    /**
     * @todo Consider test case
     * @return array|bool
     */
    public function returnUserRecordWeekPedometer()
    {
        $userActivity = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                null, false) . "steps",
            ['date', 'steps', 'distance', 'floors'],
            $this->dbWhere());
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        foreach ($userActivity as $key => $value) {
            $userActivity[$key]['distance'] = (String)round($value['distance'], 2);
            $userActivity[$key]['returnDate'] = explode("-", $value['date']);
        }

        return $userActivity;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordSyncState()
    {
        $timeToday = strtotime(date("Y-m-d H:i:s")) - (1 * 60 * 60);
        $userFirstSeenDb = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                null, false) . "users", 'seen', ["fuid" => $this->getUserID()]);
        $timeFirstSeen = strtotime($userFirstSeenDb . ' 00:00:00');

        $totalProgress = 0;
        $allowed_triggers = [];
        foreach ($this->getAppClass()->supportedApi() as $key => $name) {
            if ($this->getAppClass()->getSetting('scope_' . $key) && $this->getAppClass()->getUserSetting($this->getUserID(),
                    'scope_' . $key) && $key != "all"
            ) {
                $allowed_triggers[$key]['name'] = $this->getAppClass()->supportedApi($key);

                $oldestScope = $this->getOldestScope($key);
                $timeLastRun = strtotime($oldestScope->format("Y-m-d H:i:s"));

                $differenceLastRun = $timeLastRun - $timeToday;
                $differenceFirstSeen = $timeFirstSeen - $timeToday;
                $precentageCompleted = round((100 - ($differenceLastRun / $differenceFirstSeen) * 100), 1);
                if ($precentageCompleted < 1) {
                    $precentageCompleted = 0;
                }
                if ($precentageCompleted > 99) {
                    $precentageCompleted = 100;
                }

                $allowed_triggers[$key]['precentage'] = $precentageCompleted;
                $totalProgress += $precentageCompleted;
            }
        }

        ksort($allowed_triggers);

        return [
            "SyncProgress" => round(($totalProgress / (100 * count($allowed_triggers))) * 100, 1),
            "SyncProgressScopes" => $allowed_triggers
        ];
    }

    /**
     * @param null $scope
     *
     * @return DateTime
     */
    private function getOldestScope($scope = null)
    {
        if (is_null($scope)) {
            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                    false) . "runlog", ["user" => $this->getUserID()])
            ) {
                return new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "runlog", "lastrun", [
                    "user" => $this->getUserID(),
                    "ORDER" => ["lastrun" => "ASC"]
                ]));
            }
        } else {
            if ($this->getAppClass()->getDatabase()->has($this->getAppClass()->getSetting("db_prefix", null,
                    false) . "runlog", [
                "AND" => [
                    "user" => $this->getUserID(),
                    "activity" => $scope
                ]
            ])
            ) {
                $returnTime = new DateTime ($this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "runlog", "lastrun", [
                    "AND" => [
                        "user" => $this->getUserID(),
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
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordWeight()
    {
        $days = 7;
        $returnWeight = [];

        if (substr($this->getParamPeriod(), 0, strlen("last")) === "last") {
            $days = $this->getParamPeriod();
            $days = str_ireplace("last", "", $days);
        }

        $dbWeight = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "body",
            ['date', 'weight', 'weightGoal', 'weightAvg', 'fat', 'fatAvg', 'fatGoal'],
            [
                "AND" => [
                    "user" => $this->getUserID(),
                    "date[<=]" => $this->getParamDate(),
                    "date[>=]" => date('Y-m-d',
                        strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day"))
                ],
                "ORDER" => ["date" => "DESC"],
                "LIMIT" => ($days + 10)
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $latestDate = 0;
        foreach ($dbWeight as $key => $daysWeight) {
            if (strtotime($daysWeight['date']) > strtotime($latestDate)) {
                $latestDate = $daysWeight['date'];
            }

            $returnWeight[$daysWeight['date']] = $daysWeight;
            $returnWeight[$daysWeight['date']]['source'] = "Database";
        }

        if (count($dbWeight) == 0) {
            /*
                 * If no weights are returned by the we use the last recored weight and just propegate it forward
                 */

            /** @var DateTime $currentDate */
            $currentDate = new DateTime (date('Y-m-d', strtotime($this->getParamDate() . " +1 day")));
            /** @var DateTime $sevenDaysAgo */
            $sevenDaysAgo = new DateTime(date('Y-m-d',
                strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day")));
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod ($sevenDaysAgo, $interval, $currentDate);

            $weight = $this->getAppClass()->getFitbitAPI($this->getUserID())->getDBCurrentBody($this->getUserID(),
                "weight");
            $weightGoal = $this->getAppClass()->getFitbitAPI($this->getUserID())->getDBCurrentBody($this->getUserID(),
                "weightGoal");
            $fat = $this->getAppClass()->getFitbitAPI($this->getUserID())->getDBCurrentBody($this->getUserID(),
                "fat");
            $fatGoal = $this->getAppClass()->getFitbitAPI($this->getUserID())->getDBCurrentBody($this->getUserID(),
                "fatGoal");

            foreach ($period as $dt) {
                /** @var DateTime $dt */
                $returnWeight[$dt->format("Y-m-d")] = [
                    "date" => $dt->format("Y-m-d"),
                    "weight" => $weight,
                    "weightGoal" => $weightGoal,
                    "weightAvg" => $weight,
                    "fat" => $fat,
                    "fatGoal" => $fatGoal,
                    "fatAvg" => $fat,
                    "source" => "LatestRecord"
                ];
            }

        } else if (count($dbWeight) < ($days + 10)) {
            /*
                 * If there are missing records try filling in the blanks
                 */

            /** @var DateTime $currentDate */
            $currentDate = new DateTime (date('Y-m-d', strtotime($this->getParamDate() . " +1 day")));
            /** @var DateTime $sevenDaysAgo */
            $sevenDaysAgo = new DateTime(date('Y-m-d',
                strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day")));
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod ($sevenDaysAgo, $interval, $currentDate);

            $lastRecord = null;
            $foundMissingRecord = false;
            $arrayOfMissingDays = [];
            foreach ($period as $dt) {
                /**
                 * Find all missing dates
                 *
                 * @var DateTime $dt
                 */
                if (!array_key_exists($dt->format("Y-m-d"), $returnWeight)) {
                    if (strtotime($dt->format("Y-m-d")) > strtotime($latestDate)) {
                        // If missing date is after latest record use that

                        $returnWeight[$dt->format("Y-m-d")] = $lastRecord;
                        $returnWeight[$dt->format("Y-m-d")]['source'] = "LatestRecord";
                    } else {
                        // If missing date is before last record add it to list of missing dates

                        $foundMissingRecord = true;
                        array_push($arrayOfMissingDays, $dt->format("Y-m-d"));
                        $returnWeight[$dt->format("Y-m-d")] = 'Calc deviation';
                    }
                } else {
                    // if there are missing dates still pending
                    if ($foundMissingRecord) {
                        // If no last record has been set get it from database
                        if (is_null($lastRecord)) {
                            /** @var array $lastRecord */
                            $lastRecord = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                                    null, false) . "body",
                                ['date', 'weight', 'weightAvg', 'weightGoal', 'fat', 'fatAvg', 'fatGoal'],
                                [
                                    "AND" => [
                                        "user" => $this->getUserID(),
                                        "date[<=]" => date('Y-m-d',
                                            strtotime($this->getParamDate() . " -" . (($days + 10) - 1) . " day"))
                                    ],
                                    "ORDER" => ["date" => "DESC"]
                                ]);
                        }

                        // Fill in missing records between now and last recorded 'good' date
                        $returnWeight = $this->fillMissingBodyRecords($returnWeight, $arrayOfMissingDays,
                            $lastRecord, $returnWeight[$dt->format("Y-m-d")]);

                        // reset missing markers
                        $foundMissingRecord = false;
                        $arrayOfMissingDays = [];
                    }

                    // update last record with this one
                    $lastRecord = $returnWeight[$dt->format("Y-m-d")];
                }
            }
            if ($foundMissingRecord) {
                print "There are still missing dates\n";
            }

            ksort($returnWeight);

            $returnWeight = array_reverse($returnWeight);
        }

        $returnWeightKeys = array_keys($returnWeight);

        for ($interval = 0; count($returnWeight) > $interval; $interval++) {
            $averageRange = 15;
            if (count($returnWeight) > $interval + $averageRange) {
                $fatSum = 0;
                $weightSum = 0;
                for ($intervalTwo = 0; $intervalTwo < $averageRange; $intervalTwo++) {
                    $weightSum = $weightSum + $returnWeight[$returnWeightKeys[$interval + $intervalTwo]]['weight'];
                    $fatSum = $fatSum + $returnWeight[$returnWeightKeys[$interval + $intervalTwo]]['fat'];
                }
                $returnWeight[$returnWeightKeys[$interval]]['weightTrend'] = round($weightSum / $averageRange, 2);
                $returnWeight[$returnWeightKeys[$interval]]['fatTrend'] = $fatSum / $averageRange;
            } else {
                $returnWeight[$returnWeightKeys[$interval]]['weightTrend'] = round($returnWeight[$returnWeightKeys[$interval]]['weight'],
                    2);
                $returnWeight[$returnWeightKeys[$interval]]['fatTrend'] = $returnWeight[$returnWeightKeys[$interval]]['fat'];
            }
        }

        $weightEst = [];
        $estimation = $this->returnUserRecordWeightLossForcast();
        for ($interval = 0; $interval < count($returnWeight); $interval++) {
            $weightEst[] = $estimation['weight'] - (($estimation['WeightLossWeekly'] / 7) * ($interval + 1));
        }
        $weightEst = array_reverse($weightEst);

        $fatMin = 0;
        $fatMax = 0;
        $fat = [];
        $fatAvg = [];
        $fatGoal = [];
        $fatTrend = [];
        $weightMin = 0;
        $weightMax = 0;
        $weights = [];
        $weightAvg = [];
        $weightGoal = [];
        $weightTrend = [];
        foreach ($returnWeight as $db) {
            if ($db['weight'] < $weightMin || $weightMin == 0) {
                $weightMin = $db['weight'];
            }
            if ($db['weight'] > $weightMax || $weightMax == 0) {
                $weightMax = $db['weight'];
            }
            array_push($weights, (String)round($db['weight'], 2));
            array_push($weightGoal, (String)$db['weightGoal']);
            array_push($weightTrend, (String)$db['weightTrend']);
            array_push($weightAvg, (String)$db['weightAvg']);

            if ($db['fat'] < $fatMin || $fatMin == 0) {
                $fatMin = $db['fat'];
            }
            if ($db['fat'] > $fatMax || $fatMax == 0) {
                $fatMax = $db['fat'];
            }
            array_push($fat, (String)round($db['fat'], 2));
            array_push($fatGoal, (String)$db['fatGoal']);
            array_push($fatTrend, (String)$db['fatTrend']);
            array_push($fatAvg, (String)$db['fatAvg']);
        }

        $loss = [];
        $monthsBack = 1;
        $loopMonths = true;
        do {
            $timestamp = strtotime('now -' . $monthsBack . ' month');
            if (array_key_exists(date('Y-m-t', $timestamp), $returnWeight) AND array_key_exists(date('Y-m-01',
                    $timestamp), $returnWeight)
            ) {
                $loss["weight"][date('Y F', $timestamp)] = round(($returnWeight[date('Y-m-t',
                            $timestamp)]['weightTrend'] - $returnWeight[date('Y-m-01',
                            $timestamp)]['weightTrend']) / 4, 2);
                $loss["fat"][date('Y F', $timestamp)] = round(($returnWeight[date('Y-m-t',
                            $timestamp)]['fatTrend'] - $returnWeight[date('Y-m-01', $timestamp)]['fatTrend']) / 4,
                    2);
                $monthsBack += 1;
            } else {
                $loopMonths = false;
            }
        } while ($loopMonths);

        if (!array_key_exists("weight", $loss)) {
            $loss["weight"] = [];
        }
        if (!array_key_exists("fat", $loss)) {
            $loss["fat"] = [];
        }

        // Set variables require bellow
        $end = new DateTime('this monday');
        $begin = new DateTime('this monday');
        $begin->modify('-7 weeks');

        $interval = DateInterval::createFromDateString('1 week');
        $daterange = new DatePeriod($begin, $interval, $end);

        $WeighInArray = [];
        $FatInArray = [];
        /** @var DateTime $date */
        foreach ($daterange as $date) {
            if (array_key_exists($date->format("Y-m-d"), $returnWeight)) {
                $FatInArray[$date->format("Y-m-d")] = $returnWeight[$date->format("Y-m-d")]['weight'];
                $WeighInArray[$date->format("Y-m-d")] = $returnWeight[$date->format("Y-m-d")]['fat'];
            }
        }

        $userWeightUnits = $this->getAppClass()->getUserSetting($this->getUserID(), "unit_weight", "kg");

        return [
            'returnDate' => explode("-", $this->getParamDate()),
            'WeighInArray' => $WeighInArray,
            'FatInArray' => $FatInArray,
            'graph_fat' => $fat,
            'graph_fat_max' => $fatMax,
            'graph_fat_min' => $fatMin,
            'graph_fatAvg' => $fatAvg,
            'graph_fatGoal' => $fatGoal,
            'graph_fatTrend' => $fatTrend,
            'graph_weight' => $this->convertWeight($weights, $userWeightUnits),
            'graph_weight_max' => $this->convertWeight($weightMax, $userWeightUnits),
            'graph_weight_min' => $this->convertWeight($weightMin, $userWeightUnits),
            'graph_weightAvg' => $this->convertWeight($weightAvg, $userWeightUnits),
            'graph_weightGoal' => $this->convertWeight($weightGoal, $userWeightUnits),
            'graph_weightTrend' => $this->convertWeight($weightTrend, $userWeightUnits),
            'graph_weightEst' => $this->convertWeight($weightEst, $userWeightUnits),
            'loss_rate_fat' => $loss["fat"],
            'loss_rate_weight' => $this->convertWeight($loss["weight"], $userWeightUnits),
            'weight_units' => $userWeightUnits
        ];
    }

    /**
     * @param array $returnWeight
     * @param array $arrayOfMissingDays
     * @param array|NULL $lastRecord
     * @param array $nextRecord
     *
     * @return array
     */
    private function fillMissingBodyRecords($returnWeight, $arrayOfMissingDays, $lastRecord, $nextRecord)
    {
        $xDistance = count($arrayOfMissingDays) + 1;

        $yStartWeight = $lastRecord['weight'];
        $yEndWeight = $nextRecord['weight'];
        $dailyChangeWeight = ($yEndWeight - $yStartWeight) / $xDistance;

        $yStartWeightAvg = $lastRecord['weightAvg'];
        $yEndWeightAvg = $nextRecord['weightAvg'];
        $dailyChangeWeightAvg = ($yEndWeightAvg - $yStartWeightAvg) / $xDistance;

        $yStartFat = $lastRecord['fat'];
        $yEndFat = $nextRecord['fat'];
        $dailyChangeFat = ($yEndFat - $yStartFat) / $xDistance;

        $yStartFatAvg = $lastRecord['fatAvg'];
        $yEndFatAvg = $nextRecord['fatAvg'];
        $dailyChangeFatAvg = ($yEndFatAvg - $yStartFatAvg) / $xDistance;

        $dayNumber = 0;
        foreach ($arrayOfMissingDays as $date) {
            $dayNumber = $dayNumber + 1;
            $calcWeight = (String)round(($dailyChangeWeight * $dayNumber) + $yStartWeight, 2);
            $calcWeightAvg = (String)round(($dailyChangeWeightAvg * $dayNumber) + $yStartWeightAvg, 2);
            $calcFat = (String)round(($dailyChangeFat * $dayNumber) + $yStartFat, 2);
            $calcFatAvg = (String)round(($dailyChangeFatAvg * $dayNumber) + $yStartFatAvg, 2);
            $returnWeight[$date] = [
                "date" => $date,
                "weight" => $calcWeight,
                "weightAvg" => $calcWeightAvg,
                "weightGoal" => $nextRecord['weightGoal'],
                "fat" => $calcFat,
                "fatAvg" => $calcFatAvg,
                "fatGoal" => $nextRecord['fatGoal'],
                "source" => "CalcDeviation"
            ];
        }

        return $returnWeight;
    }

    /**
     * @param array $get
     *
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecords($get)
    {
        if (array_key_exists("period", $get)) {
            $this->setParamPeriod($get['period']);
        }

        if (array_key_exists("date", $get)) {
            $this->setParamDate($get['date']);
        }

        $functionName = 'returnUserRecord' . $get['data'];
        if (method_exists($this, $functionName)) {
            $dbUserName = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "users", 'name', ["fuid" => $this->getUserID()]);
            $resultsArray = [
                "error" => "false",
                "user" => $this->getUserID(),
                'username' => $dbUserName,
                "cache" => true,
                "data" => $get['data'],
                "time" => 0,
                "period" => $this->getParamPeriod(),
                "date" => $this->getParamDate()
            ];
            $resultsArray['results'] = $this->$functionName();
            if (array_key_exists("sole", $resultsArray['results']) && $resultsArray['results']['sole']) {
                $resultsArray = $resultsArray['results']['return'];
            } else {
                $resultsArray['cache'] = $this->getForCache();
            }

            if (array_key_exists("debug", $_GET) and $_GET['debug'] == "true") {
                $resultsArray['dbLog'] = $this->getAppClass()->getDatabase()->log();
                foreach ($resultsArray['dbLog'] as $key => $value) {
                    $resultsArray['dbLog'][$key] = str_ireplace("\"", "`", $value);
                }
            }

            if (!is_null($this->getTracking()) && is_array($_SERVER) && array_key_exists("SERVER_NAME",
                    $_SERVER)
            ) {
                $this->getTracking()->endEvent('JSON/' . $this->getUserID() . '/' . $this->getParamDate() . '/' . $get['data']);
            }

            return $resultsArray;
        } else {
            if (!is_null($this->getTracking()) && is_array($_SERVER) && array_key_exists("SERVER_NAME",
                    $_SERVER)
            ) {
                $this->getTracking()->track("Error", 103);
                $this->getTracking()->endEvent('Error/' . $this->getUserID() . '/' . $this->getParamDate() . '/' . $get['data']);
            }

            return ["error" => "true", "code" => 103, "msg" => "Unknown dataset: " . $functionName];
        }
    }

    /**
     * @todo Consider test case
     * @return int
     */
    public function getForCache()
    {
        if ($this->forCache) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @param bool $forCache
     *
     * @todo Consider test case
     */
    public function setForCache($forCache)
    {
        $this->forCache = $forCache;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordNomie()
    {
        $returnArray = [];
        $returnArray['dashboard'] = $this->returnUserRecordNomieDashboard();
        $returnArray['dbTrackers'] = $this->returnUserRecordNomieTrackers();

        return $returnArray;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordNomieDashboard()
    {
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        $returnArray = [];
        $returnArray['dashboard'] = [];

        $returnArray['dashboard']['trackers'] = $this->getAppClass()->getDatabase()->count($db_prefix . "nomie_trackers",
            'id', ["fuid" => $this->getUserID()]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $returnArray['dashboard']['events'] = $this->getAppClass()->getDatabase()->count($db_prefix . "nomie_events",
            'id', ["fuid" => $this->getUserID()]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $returnArray['dashboard']['notes'] = 0;

        $returnArray['dashboard']['spread'] = [];
        $returnArray['dashboard']['spread']['events'] = [];

        $returnArray['dashboard']['spread']['events']['positive'] = $this->getAppClass()->getDatabase()->count($db_prefix . "nomie_events",
            'id', [
                "AND" => [
                    "fuid" => $this->getUserID(),
                    "score[>]" => "0"
                ]
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $returnArray['dashboard']['spread']['events']['negative'] = $this->getAppClass()->getDatabase()->count($db_prefix . "nomie_events",
            'id', [
                "AND" => [
                    "fuid" => $this->getUserID(),
                    "score[<]" => "0"
                ]
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $returnArray['dashboard']['spread']['events']['netural'] = $this->getAppClass()->getDatabase()->count($db_prefix . "nomie_events",
            'id', [
                "AND" => [
                    "fuid" => $this->getUserID(),
                    "score" => "0"
                ]
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $returnArray['dashboard']['spread']['notes'] = [];
        $returnArray['dashboard']['spread']['notes']['positive'] = 0;
        $returnArray['dashboard']['spread']['notes']['negative'] = 0;
        $returnArray['dashboard']['spread']['notes']['netural'] = 0;

        return $returnArray['dashboard'];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordNomieTrackers()
    {
        $dbTrackers = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix",
                null, false) . "nomie_trackers",
            ['id', 'label', 'icon', 'color', 'charge', 'sort'],
            [
                "AND" => ["fuid" => $this->getUserID(), "sort[>]" => -1],
                "ORDER" => ["sort" => "ASC"]
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $trackerShared = [];
        foreach ($dbTrackers as $tracker) {
            unset($dbEventCount);
            unset($dbEventFirst);
            unset($dbEventLast);
            unset($days_between);
            unset($months_between);

            $dayAvg = 0;
            $monthAvg = 0;

            $dbEventCount = $this->getAppClass()->getDatabase()->count($this->getAppClass()->getSetting("db_prefix",
                    null, false) . "nomie_events",
                'id', ["AND" => ["fuid" => $this->getUserID(), "id" => $tracker['id']]]);

            if ($dbEventCount > 0) {
                $dbEventFirst = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                        null, false) . "nomie_events",
                    'datestamp', [
                        "AND" => ["fuid" => $this->getUserID(), "id" => $tracker['id']],
                        "ORDER" => ["datestamp" => "ASC"]
                    ]);

                if (empty($dbEventFirst)) {
                    $dbEventFirst = "0000-00-00 00:00:00";
                }

                if ($dbEventFirst != "0000-00-00 00:00:00") {
                    $dbEventLast = $this->getAppClass()->getDatabase()->get($this->getAppClass()->getSetting("db_prefix",
                            null, false) . "nomie_events",
                        'datestamp', [
                            "AND" => ["fuid" => $this->getUserID(), "id" => $tracker['id']],
                            "ORDER" => ["datestamp" => "DESC"]
                        ]);

                    if (empty($dbEventLast)) {
                        $dbEventLast = "0000-00-00 00:00:00";
                    }

                    if ($dbEventLast != "0000-00-00 00:00:00") {
                        $dateTimeFirst = new DateTime ($dbEventFirst);
                        $days_between = $dateTimeFirst->diff(new DateTime ($dbEventLast))->format("%a");
                        $days_between = (int)$days_between + 1;
                        $months_between = $dateTimeFirst->diff(new DateTime ($dbEventLast))->format("%m");
                        $months_between = (int)$months_between + 1;
                    }
                }

                if (empty($dbEventLast)) {
                    $dbEventLast = "0000-00-00 00:00:00";
                }

                if (!isset($days_between)) {
                    $days_between = 0;
                } else {
                    $dayAvg = round($dbEventCount / $days_between, 2);
                }

                if (!isset($months_between)) {
                    $months_between = 0;
                } else {
                    $monthAvg = round($dbEventCount / $months_between, 2);
                }

                $dateTimeFirst = new DateTime ($dbEventFirst);
                $dateTimeLast = new DateTime ($dbEventLast);

                $trackerShared[$tracker['id']] = [
                    "label" => $tracker['label'],
                    "icon" => $tracker['icon'],
                    "icon_raw" => $tracker['icon'],
                    "color" => $tracker['color'],
                    "charge" => $tracker['charge'],
                    "stats" => [
                        "events" => $dbEventCount,
                        "first" => $dateTimeFirst->format("Y-m-d H:i"),
                        "last" => $dateTimeLast->format("Y-m-d H:i"),
                        "dayAvg" => $dayAvg,
                        "monthAvg" => $monthAvg
                    ],
                ];
            } else {
                $trackerShared[$tracker['id']] = [
                    "label" => $tracker['label'],
                    "icon" => $tracker['icon'],
                    "icon_raw" => $tracker['icon'],
                    "color" => $tracker['color'],
                    "charge" => $tracker['charge'],
                    "stats" => [
                        "events" => 0,
                        "first" => "",
                        "last" => "",
                        "dayAvg" => 0,
                        "monthAvg" => 0
                    ],
                ];
            }
        }

        return $trackerShared;
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordNomieGPS()
    {
        if (array_key_exists('tracker', $_GET)) {
            $searchTracker = $_GET['tracker'];
        } else {
            return [];
        }

        $eventLimit = 500;
        $db_prefix = $this->getAppClass()->getSetting("db_prefix", null, false);

        $dbTrackers = $this->getAppClass()->getDatabase()->select($db_prefix . "nomie_events", [
            "[>]" . $db_prefix . "nomie_trackers" => ["id" => "id"]
        ], [
            $db_prefix . 'nomie_events.datestamp',
            $db_prefix . 'nomie_trackers.type',
            $db_prefix . 'nomie_trackers.math',
            $db_prefix . 'nomie_trackers.uom',
            $db_prefix . 'nomie_events.value',
            $db_prefix . 'nomie_events.score',
            $db_prefix . 'nomie_events.geo_lat',
            $db_prefix . 'nomie_events.geo_lon'
        ], [
            "AND" => [
                $db_prefix . "nomie_events.fuid" => $this->getUserID(),
                $db_prefix . "nomie_events.id" => $searchTracker,
                $db_prefix . "nomie_events.geo_lat[!]" => "",
                $db_prefix . "nomie_events.geo_lon[!]" => ""
            ],
            "ORDER" => ["datestamp" => "DESC"],
            "LIMIT" => $eventLimit
        ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        if ($dbTrackers[0]['type'] == "numeric") {
            $sum = $this->getAppClass()->getDatabase()->sum($db_prefix . "nomie_events", 'value', [
                "AND" => [
                    $db_prefix . "nomie_events.fuid" => $this->getUserID(),
                    $db_prefix . "nomie_events.id" => $searchTracker,
                    $db_prefix . "nomie_events.geo_lat[!]" => "",
                    $db_prefix . "nomie_events.geo_lon[!]" => ""
                ],
                "ORDER" => ["datestamp" => "DESC"],
                "LIMIT" => $eventLimit
            ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            $avg = $this->getAppClass()->getDatabase()->avg($db_prefix . "nomie_events", 'value', [
                "AND" => [
                    $db_prefix . "nomie_events.fuid" => $this->getUserID(),
                    $db_prefix . "nomie_events.id" => $searchTracker,
                    $db_prefix . "nomie_events.geo_lat[!]" => "",
                    $db_prefix . "nomie_events.geo_lon[!]" => ""
                ],
                "ORDER" => ["datestamp" => "DESC"],
                "LIMIT" => $eventLimit
            ]);
            $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
                "METHOD" => __METHOD__,
                "LINE" => __LINE__
            ]);

            if ($dbTrackers[0]['uom'] == "min") {
                if ($sum > 60) {
                    $hours = floor($sum / 60);
                    $minutes = ($sum % 60);
                    $sum = sprintf('%02d hours %02d minutes', $hours, $minutes);
                } else {
                    $sum = sprintf('%02d minutes', ($sum % 60));
                }

                if ($avg > 60) {
                    $hours = floor($avg / 60);
                    $minutes = ($avg % 60);
                    $avg = sprintf('%02d hours %02d minutes', $hours, $minutes);
                } else {
                    $avg = sprintf('%02d minutes', ($avg % 60));
                }
            }
        } else {
            $sum = 0;
            $avg = 0;
        }

        $lat = $this->getAppClass()->getDatabase()->avg($db_prefix . "nomie_events", 'geo_lat', [
            "AND" => [
                "fuid" => $this->getUserID(),
                "id" => $searchTracker,
                "geo_lat[!]" => "",
                "geo_lon[!]" => ""
            ],
            "ORDER" => ["datestamp" => "DESC"],
            "LIMIT" => $eventLimit
        ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        $long = $this->getAppClass()->getDatabase()->avg($db_prefix . "nomie_events", 'geo_lon', [
            "AND" => [
                "fuid" => $this->getUserID(),
                "id" => $searchTracker,
                "geo_lat[!]" => "",
                "geo_lon[!]" => ""
            ],
            "ORDER" => ["datestamp" => "DESC"],
            "LIMIT" => $eventLimit
        ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        return [
            "lat" => $lat,
            "long" => $long,
            "sum" => $sum,
            "avg" => $avg,
            "count" => count($dbTrackers),
            "from" => $dbTrackers[count($dbTrackers) - 1]['datestamp'],
            "till" => $dbTrackers[0]['datestamp'],
            "events" => $dbTrackers
        ];
    }

    /**
     * @todo Consider test case
     * @return array
     */
    public function returnUserRecordNomieScoreGraph()
    {
        $days = 30;
        $returnAr = [];
        $returnAr['graph'] = [];
        $returnAr['graph']['dates'] = [];
        $returnAr['graph']['score'] = [];
        $returnAr['graph']['positive'] = [];
        $returnAr['graph']['negative'] = [];
        $returnAr['graph']['neutral'] = [];
        $returnAr['db'] = [];

        $dbEvents = $this->getAppClass()->getDatabase()->select($this->getAppClass()->getSetting("db_prefix", null,
                false) . "nomie_events",
            ['datestamp', 'score'],
            [
                "AND" => [
                    "fuid" => $this->getUserID(),
                    "datestamp[<=]" => $this->getParamDate() . " 23:59:59",
                    "datestamp[>=]" => date('Y-m-d',
                            strtotime($this->getParamDate() . " -" . ($days - 1) . " day")) . " 00:00:00"
                ],
                "ORDER" => ["datestamp" => "ASC"]
            ]);
        $this->getAppClass()->getErrorRecording()->postDatabaseQuery($this->getAppClass()->getDatabase(), [
            "METHOD" => __METHOD__,
            "LINE" => __LINE__
        ]);

        foreach ($dbEvents as $dbEvent) {
            $dbEvent['datestamp'] = substr($dbEvent['datestamp'], 0, 10);

            if (!array_key_exists($dbEvent['datestamp'], $returnAr['db'])) {
                $returnAr['db'][$dbEvent['datestamp']] = [];
                $returnAr['db'][$dbEvent['datestamp']]['score'] = 0;
                $returnAr['db'][$dbEvent['datestamp']]['positive'] = 0;
                $returnAr['db'][$dbEvent['datestamp']]['negative'] = 0;
                $returnAr['db'][$dbEvent['datestamp']]['neutral'] = 0;
            }

            $returnAr['db'][$dbEvent['datestamp']]['score'] = $returnAr['db'][$dbEvent['datestamp']]['score'] + $dbEvent['score'];

            if ($dbEvent['score'] < 0) {
                $returnAr['db'][$dbEvent['datestamp']]['positive'] = $returnAr['db'][$dbEvent['datestamp']]['positive'] + 1;
            } else if ($dbEvent['score'] > 0) {
                $returnAr['db'][$dbEvent['datestamp']]['negative'] = $returnAr['db'][$dbEvent['datestamp']]['negative'] + 1;
            } else {
                $returnAr['db'][$dbEvent['datestamp']]['neutral'] = $returnAr['db'][$dbEvent['datestamp']]['neutral'] + 1;
            }
        }

        foreach ($returnAr['db'] as $date => $collatedDay) {
            array_push($returnAr['graph']['dates'], $date);
            array_push($returnAr['graph']['score'], $collatedDay['score']);
            array_push($returnAr['graph']['positive'], $collatedDay['positive']);
            array_push($returnAr['graph']['negative'], $collatedDay['negative']);
            array_push($returnAr['graph']['neutral'], $collatedDay['neutral']);
        }

        return $returnAr;
    }
}
