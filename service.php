<?php
    // JSon request format is :
    //[{"collectionType":"activities","date":"2015-03-06","ownerId":"269VLG","ownerType":"user","subscriptionId":"1"}]

    // read JSon input
    $data = json_decode(file_get_contents('php://input'));

    $logMsg = '';

    if (validate_client()) {
        // set json string to php variables
        if (isset($data) and is_array($data)) {
            foreach ($data as $upreq) {
                // Do some data validation to make sure we are getting all we expect
                if (empty($upreq->collectionType) or $upreq->collectionType == "") {
                    $logMsg .= "collectionType not sent\n";
                } else if (empty($upreq->date) or $upreq->date == "") {
                    $logMsg .= "date not sent\n";
                } else if (empty($upreq->ownerId) or $upreq->ownerId == "") {
                    $logMsg .= "ownerId not sent\n";
                } else if (empty($upreq->ownerType) or $upreq->ownerType == "") {
                    $logMsg .= "ownerType not sent\n";
                } else if (empty($upreq->subscriptionId) or $upreq->subscriptionId == "") {
                    $logMsg .= "subscriptionId not sent\n";
                } else {
                    require_once(dirname(__FILE__) . "/inc/app.php");
                    $fitbitApp = new NxFitbit();
                    if ($fitbitApp->isUser($upreq->ownerId)) {
                        $cooldown = $fitbitApp->getUserCooldown($upreq->ownerId);
                        if (strtotime($cooldown) < strtotime(date("Y-m-d H:i:s"))) {
                            $logMsg .= "Processing queue item " . $fitbitApp->supportedApi($upreq->collectionType) . " for " . $upreq->ownerId . "\n";
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

                            $fitbitApp->addCronJob($upreq->ownerId, $upreq->collectionType, true);
                        } else {
                            $logMsg .= "Can not process " . $fitbitApp->supportedApi($upreq->collectionType) . ". API limit reached for " . $upreq->ownerId . ". Cooldown period ends " . $cooldown . "\n";
                        }
                    } else {
                        $logMsg .= "Can not process " . $fitbitApp->supportedApi($upreq->collectionType) . " since " . $upreq->ownerId . " is no longer a user.\n";
                    }
                }
            }
        }
    } else {
        $logMsg .= "Could not authorise client IP\n";
    }
    
    $fh      = fopen("/home/nxad/logs/fitbit.upload.txt", "a");
    fwrite($fh, "************\nNew API request:\n" . $logMsg . "\n");

    header('HTTP/1.0 204 No Content');

    function validate_client() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $host = gethostbyaddr($ip);
        if (strpos($host, '.fitbit.com') !== false) {
            $access = true;
        } else {
            $access = false;
        }

        return $access;
    }