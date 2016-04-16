<?php
    // JSon request format is :
    //[{"collectionType":"activities","date":"2015-03-06","ownerId":"269VLG","ownerType":"user","subscriptionId":"1"}]

    date_default_timezone_set('Europe/London');

    if (!function_exists("nxr")) {
        function nxr($msg) {
            if (is_writable(dirname(__FILE__) . "/../fitbit.log")) {
                $fh = fopen(dirname(__FILE__) . "/../fitbit.log", "a");
                fwrite($fh, date("Y-m-d H:i:s") . ": " . $msg . "\n");
                fclose($fh);
            }

            if (php_sapi_name() == "cli") {
                echo date("Y-m-d H:i:s") . ": " . $msg . "\n";
            }
        }
    }

    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');

    // read JSon input
    $data = json_decode(file_get_contents('php://input'));

    //    nxr(print_r($data, true));

    $logMsg = '';

    if (validate_client()) {
        // set json string to php variables
        if (isset($data) and is_array($data)) {
            foreach ($data as $upreq) {
                // Do some data validation to make sure we are getting all we expect
                if (empty($upreq->collectionType) or $upreq->collectionType == "") {
                    $logMsg .= "collectionType not sent";
                } else if (empty($upreq->date) or $upreq->date == "") {
                    $logMsg .= "date not sent";
                } else if (empty($upreq->ownerId) or $upreq->ownerId == "") {
                    $logMsg .= "ownerId not sent";
                } else if (empty($upreq->ownerType) or $upreq->ownerType == "") {
                    $logMsg .= "ownerType not sent";
                } else if (empty($upreq->subscriptionId) or $upreq->subscriptionId == "") {
                    $logMsg .= "subscriptionId not sent";
                } else {
                    require_once(dirname(__FILE__) . "/inc/app.php");
                    $fitbitApp = new NxFitbit();
                    if ($fitbitApp->isUser($upreq->ownerId)) {
                        $cooldown = $fitbitApp->getUserCooldown($upreq->ownerId);
                        if (strtotime($cooldown) < strtotime(date("Y-m-d H:i:s"))) {
                            $logMsg .= "Processing queue item " . $fitbitApp->supportedApi($upreq->collectionType) . " for " . $upreq->ownerId . "";
                            if ($fitbitApp->getDatabase()->has($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user" => $upreq->ownerId, "activity" => $upreq->collectionType)))) {
                                $fields = array(
                                    "date"     => date("Y-m-d H:i:s"),
                                    "cooldown" => "1970-01-01 01:00:00"
                                );
                                $fitbitApp->getDatabase()->update($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields, array("AND" => array("user" => $upreq->ownerId, "activity" => $upreq->collectionType)));
                            } else {
                                $fields = array(
                                    "user"     => $upreq->ownerId,
                                    "activity" => $upreq->collectionType,
                                    "date"     => date("Y-m-d H:i:s"),
                                    "cooldown" => "1970-01-01 01:00:00"
                                );
                                $fitbitApp->getDatabase()->insert($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields);
                            }

                            if ($upreq->collectionType == "foods") {
                                if ($fitbitApp->getDatabase()->has($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "runlog", array("AND" => array("user"     => $upreq->ownerId,
                                                                                                                                                     "activity" => "water")))
                                ) {
                                    $fields = array(
                                        "date"     => date("Y-m-d H:i:s"),
                                        "cooldown" => "1970-01-01 01:00:00"
                                    );
                                    $fitbitApp->getDatabase()->update($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields, array("AND" => array("user"     => $upreq->ownerId,
                                                                                                                                                                 "activity" => "water")));
                                } else {
                                    $fields = array(
                                        "user"     => $upreq->ownerId,
                                        "activity" => "water",
                                        "date"     => date("Y-m-d H:i:s"),
                                        "cooldown" => "1970-01-01 01:00:00"
                                    );
                                    $fitbitApp->getDatabase()->insert($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "runlog", $fields);
                                }
                            }

                            $fitbitApp->addCronJob($upreq->ownerId, $upreq->collectionType, TRUE);
                        } else {
                            $logMsg .= "Can not process " . $fitbitApp->supportedApi($upreq->collectionType) . ". API limit reached for " . $upreq->ownerId . ". Cooldown period ends " . $cooldown . "";
                        }
                    } else {
                        $logMsg .= "Can not process " . $fitbitApp->supportedApi($upreq->collectionType) . " since " . $upreq->ownerId . " is no longer a user.";
                    }
                }
            }
        } else if (isset($data) and is_object($data)) {
            require_once(dirname(__FILE__) . "/inc/app.php");
            $fitbitApp = new NxFitbit();
            if ($fitbitApp->isUser($data->ownerId)) {
                nxr($data->ownerId . " is a valid user");
                $api = $fitbitApp->getDatabase()->get($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users", "api", array("fuid" => $data->ownerId));
                if (isset($api)) {
                    if (hash('sha256', $api . date("Y-m-d H:i")) == $data->auth) {
                        nxr(" Valid API Access Key");
                        foreach ($data->unit as $unit) {
                            nxr("  Recording " . $unit->key . " as " . $unit->value);
                            $fitbitApp->getDatabase()->insert($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "units", array(
                                "user"  => $data->ownerId,
                                "unit"  => $unit->key,
                                "value" => $unit->value
                            ));
                        }
                    } else {
                        nxr(" Invalid API");
                        nxr("  Expected: "
                            . substr(hash('sha256', $api . date("Y-m-d H:i")), 0, 5)
                            . "......................................................"
                            . substr(hash('sha256', $api . date("Y-m-d H:i")), -5));
                        nxr("  Received: " . $data->auth);
                        echo date("Y-m-d H:i:s") . ":: Invalid API";
                        die();
                    }
                } else {
                    nxr(" No API Access");
                    echo date("Y-m-d H:i:s") . ":: No API Access";
                    die();
                }
            }
        }
    } else {
        $logMsg .= "Could not authorise client IP";
    }

    if (isset($logMsg) && $logMsg != "") nxr("New API request: " . $logMsg);

    header('HTTP/1.0 204 No Content');

    function validate_client() {
        return TRUE;
    }

