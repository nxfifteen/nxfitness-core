<?php
    // JSon request format is :
    //[{"collectionType":"activities","date":"2015-03-06","ownerId":"269VLG","ownerType":"user","subscriptionId":"1"}]

    use Core\Core;

    date_default_timezone_set('Europe/London');

    if (!function_exists("nxr")) {
        require_once(dirname(__FILE__) . "/inc/functions.php");
    }

    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');

    // read JSon input
    $data = json_decode(file_get_contents('php://input'));

    //    nxr(0, print_r($data, true));

    $logMsg = '';

    if (validate_client()) {
        // set json string to php variables
        if (isset($data) and is_array($data)) {
            foreach ($data as $upLoadedRequest) {
                // Do some data validation to make sure we are getting all we expect
                if (empty($upLoadedRequest->collectionType) or $upLoadedRequest->collectionType == "") {
                    $logMsg .= "collectionType not sent";
                } else if (empty($upLoadedRequest->date) or $upLoadedRequest->date == "") {
                    $logMsg .= "date not sent";
                } else if (empty($upLoadedRequest->ownerId) or $upLoadedRequest->ownerId == "") {
                    $logMsg .= "ownerId not sent";
                } else if (empty($upLoadedRequest->ownerType) or $upLoadedRequest->ownerType == "") {
                    $logMsg .= "ownerType not sent";
                } else if (empty($upLoadedRequest->subscriptionId) or $upLoadedRequest->subscriptionId == "") {
                    $logMsg .= "subscriptionId not sent";
                } else {
                    if ($upLoadedRequest->collectionType == "nomie") {
                        $upLoadedRequest->collectionType = "nomie_trackers";
                    }

                    require_once(dirname(__FILE__) . "/inc/Core.php");
                    $fitbitApp = new Core();
                    if ($fitbitApp->isUser($upLoadedRequest->ownerId)) {
                        $cooldown = $fitbitApp->getUserCooldown($upLoadedRequest->ownerId);
                        if (strtotime($cooldown) < strtotime(date("Y-m-d H:i:s"))) {
                            $logMsg .= "Processing queue item " . $fitbitApp->supportedApi($upLoadedRequest->collectionType) . " for " . $upLoadedRequest->ownerId . "";
                            if ($fitbitApp->getDatabase()->has($fitbitApp->getSetting("db_prefix", null,
                                    false) . "runlog", array(
                                "AND" => array(
                                    "user"     => $upLoadedRequest->ownerId,
                                    "activity" => $upLoadedRequest->collectionType
                                )
                            ))
                            ) {
                                $fields = array(
                                    "date"     => date("Y-m-d H:i:s"),
                                    "cooldown" => "1970-01-01 01:00:00"
                                );
                                $fitbitApp->getDatabase()->update($fitbitApp->getSetting("db_prefix", null,
                                        false) . "runlog", $fields, array(
                                    "AND" => array(
                                        "user"     => $upLoadedRequest->ownerId,
                                        "activity" => $upLoadedRequest->collectionType
                                    )
                                ));
                                $fitbitApp->getErrorRecording()->postDatabaseQuery($fitbitApp->getDatabase(), array(
                                    "METHOD" => __FILE__,
                                    "LINE"   => __LINE__
                                ));
                            } else {
                                $fields = array(
                                    "user"     => $upLoadedRequest->ownerId,
                                    "activity" => $upLoadedRequest->collectionType,
                                    "date"     => date("Y-m-d H:i:s"),
                                    "cooldown" => "1970-01-01 01:00:00"
                                );
                                $fitbitApp->getDatabase()->insert($fitbitApp->getSetting("db_prefix", null,
                                        false) . "runlog", $fields);
                                $fitbitApp->getErrorRecording()->postDatabaseQuery($fitbitApp->getDatabase(), array(
                                    "METHOD" => __FILE__,
                                    "LINE"   => __LINE__
                                ));
                            }

                            if ($upLoadedRequest->collectionType == "foods") {
                                if ($fitbitApp->getDatabase()->has($fitbitApp->getSetting("db_prefix", null,
                                        false) . "runlog", array(
                                    "AND" => array(
                                        "user"     => $upLoadedRequest->ownerId,
                                        "activity" => "water"
                                    )
                                ))
                                ) {
                                    $fields = array(
                                        "date"     => date("Y-m-d H:i:s"),
                                        "cooldown" => "1970-01-01 01:00:00"
                                    );
                                    $fitbitApp->getDatabase()->update($fitbitApp->getSetting("db_prefix", null,
                                            false) . "runlog", $fields, array(
                                        "AND" => array(
                                            "user"     => $upLoadedRequest->ownerId,
                                            "activity" => "water"
                                        )
                                    ));
                                    $fitbitApp->getErrorRecording()->postDatabaseQuery($fitbitApp->getDatabase(), array(
                                        "METHOD" => __FILE__,
                                        "LINE"   => __LINE__
                                    ));
                                } else {
                                    $fields = array(
                                        "user"     => $upLoadedRequest->ownerId,
                                        "activity" => "water",
                                        "date"     => date("Y-m-d H:i:s"),
                                        "cooldown" => "1970-01-01 01:00:00"
                                    );
                                    $fitbitApp->getDatabase()->insert($fitbitApp->getSetting("db_prefix", null,
                                            false) . "runlog", $fields);
                                    $fitbitApp->getErrorRecording()->postDatabaseQuery($fitbitApp->getDatabase(), array(
                                        "METHOD" => __FILE__,
                                        "LINE"   => __LINE__
                                    ));
                                }
                            }

                            $fitbitApp->addCronJob($upLoadedRequest->ownerId, $upLoadedRequest->collectionType, true);
                        } else {
                            $logMsg .= "Can not process " . $fitbitApp->supportedApi($upLoadedRequest->collectionType) . ". API limit reached for " . $upLoadedRequest->ownerId . ". Cooldown period ends " . $cooldown . "";
                        }
                    } else {
                        $logMsg .= "Can not process " . $fitbitApp->supportedApi($upLoadedRequest->collectionType) . " since " . $upLoadedRequest->ownerId . " is no longer a user.";
                    }
                }
            }
        } else if (isset($data) and is_object($data)) {
            require_once(dirname(__FILE__) . "/inc/Core.php");
            $fitbitApp = new Core();
            if ($fitbitApp->isUser($data->ownerId)) {
                nxr(0, $data->ownerId . " is a valid user");
                $api = $fitbitApp->getDatabase()->get($fitbitApp->getSetting("db_prefix", null, false) . "users", "api",
                    array("fuid" => $data->ownerId));
                if (isset($api)) {
                    if (hash('sha256', $api . date("Y-m-d H:i")) == $data->auth) {
                        nxr(1, "Valid API Access Key");
                        foreach ($data->unit as $unit) {
                            nxr(2, "Recording " . $unit->key . " as " . $unit->value);
                            $fitbitApp->getDatabase()->insert($fitbitApp->getSetting("db_prefix", null,
                                    false) . "units", array(
                                "user"  => $data->ownerId,
                                "unit"  => $unit->key,
                                "value" => $unit->value
                            ));
                            $fitbitApp->getErrorRecording()->postDatabaseQuery($fitbitApp->getDatabase(), array(
                                "METHOD" => __FILE__,
                                "LINE"   => __LINE__
                            ));
                        }
                    } else {
                        nxr(1, "Invalid API");
                        nxr(2, "Expected: "
                            . substr(hash('sha256', $api . date("Y-m-d H:i")), 0, 5)
                            . "......................................................"
                            . substr(hash('sha256', $api . date("Y-m-d H:i")), -5));
                        nxr(2, "Received: " . $data->auth);
                        echo date("Y-m-d H:i:s") . ":: Invalid API";
                        die();
                    }
                } else {
                    nxr(1, "No API Access");
                    echo date("Y-m-d H:i:s") . ":: No API Access";
                    die();
                }
            }
        }
    } else {
        $logMsg .= "Could not authorise client IP";
    }

    if (isset($logMsg) && $logMsg != "") {
        nxr(0, "New API request: " . $logMsg);
    }

    header('HTTP/1.0 204 No Content');

    /**
     * @return bool
     */
    function validate_client()
    {
        return true;
    }
