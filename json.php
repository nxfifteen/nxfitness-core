<?php
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');

    if (array_key_exists("user", $_GET) && array_key_exists("data", $_GET)) {
        if ((!array_key_exists("debug", $_GET) or $_GET['debug'] != "true") && is_writable('cache') && (!array_key_exists("cache", $_GET) || $_GET['cache'] != "false")) {
            // cache files are created like cache/abcdef123456...
            $cacheFileName = '';
            //user" => "269VLG", "data" => "Tracked", "period
            if (array_key_exists("user", $_GET)) {
                $cacheFileName = $cacheFileName . '_' . $_GET['user'];
            }
            if (array_key_exists("data", $_GET)) {
                $cacheFileName = $cacheFileName . '_' . $_GET['data'];
            }
            if (array_key_exists("tcx", $_GET)) {
                $cacheFileName = $cacheFileName . '_' . $_GET['tcx'];
            }
            if (array_key_exists("date", $_GET)) {
                $cacheFileName = $cacheFileName . '_' . $_GET['date'];
            }
            if (array_key_exists("period", $_GET)) {
                $cacheFileName = $cacheFileName . '_' . $_GET['period'];
            }
            if (array_key_exists("start", $_GET)) {
                $cacheFileName = $cacheFileName . '_' . $_GET['start'];
            }
            if (array_key_exists("end", $_GET)) {
                $cacheFileName = $cacheFileName . '_' . $_GET['end'];
            }
            if (array_key_exists("debug", $_GET)) {
                $cacheFileName = $cacheFileName . '_' . $_GET['debug'];
            }
            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . $cacheFileName;

            if (file_exists($cacheFile)) {
                $fh = fopen($cacheFile, 'r');
                $cacheTime = trim(fgets($fh));

                // if data was cached recently, return cached data
                if ($cacheTime > strtotime('-45 minutes')) {
                    echo fread($fh, filesize($cacheFile));

                    return TRUE;
                }

                // else delete cache file
                fclose($fh);
                unlink($cacheFile);
            }

            $json = query_api();
            if ($json != "") {
                echo $json;

                $fh = fopen($cacheFile, 'w');
                fwrite($fh, time() . "\n");
                fwrite($fh, $json);
                fclose($fh);
            }
        } else {
            echo query_api();
        }
    } elseif (!array_key_exists("user", $_GET)) {
        echo json_error(100);
    } elseif (!array_key_exists("data", $_GET)) {
        echo json_error(102);
    }

    function query_api() {
        require_once(dirname(__FILE__) . "/inc/dataReturn.php");
        $dataReturnClass = new dataReturn($_GET['user']);
        if ($dataReturnClass->isUser()) {
            $json = $dataReturnClass->returnUserRecords($_GET);

            if (array_key_exists("debug", $_GET) and $_GET['debug'] == "true") {
                return print_r($json, TRUE);
            } else {
                if (is_array($json) and array_key_exists("results", $json) and array_key_exists("error", $json['results'])) {
                    if (array_key_exists("debug", $_GET) and $_GET['debug'] == "true") {
                        $json['error'] = TRUE;
                        $json['code'] = "000";
                        $json['msg'] = $json['results']['error'];
                        $json['results'] = $json['results']['return'];
                    }
                    echo json_encode($json);

                    return "";
                } else if (array_key_exists("cache", $json) and $json['cache'] == 0) {
                    echo json_encode($json);

                    return "";
                } else {
                    return json_encode($json);
                }
            }
        } else {
            echo json_error(101);

            return "";
        }
    }

    function json_error($errNumber) {
        $errMessage = "";
        switch ($errNumber) {
            case "100":
                $errMessage = "You must specifiy a user";
                break;
            case "101":
                $errMessage = "You must specifiy a valid user";
                break;
            case "102":
                $errMessage = "You havent stated what to return";
                break;
        }

        return json_encode(array("error" => "true", "code" => $errNumber, "msg" => $errMessage));
    }
