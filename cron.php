<?php

    define('IS_CRON_RUN', TRUE);

    /**
     * NxFitbit - Cron commandline tool
     *
     * @version   0.0.1
     * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link      http://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2015 Stuart McCulloch Anderson
     * @license   http://stuart.nx15.at/mit/2015 MIT
     */

    if (!function_exists("nxr")) {
        /**
         * NXR is a helper function. Past strings are recorded in a text file
         * and when run from a command line output is displayed on screen as
         * well
         *
         * @param string $msg String input to be displayed in logs files
         */
        function nxr($msg) {
            if (is_writable(dirname(__FILE__) . "/fitbit.log")) {
                $fh = fopen(dirname(__FILE__) . "/fitbit.log", "a");
                fwrite($fh, date("Y-m-d H:i:s") . ": " . $msg . "\n");
                fclose($fh);
            }

            /*if (php_sapi_name() == "cli") {
                echo date("Y-m-d H:i:s") . ": " . $msg . "\n";
            }*/
        }
    }

    require_once(dirname(__FILE__) . "/inc/app.php");
    $fitbitApp = new NxFitbit();

    $end = time() + 20;
    $repopulate_queue = run_through_queue();

    if ($repopulate_queue) {
        nxr("Ready to repopulate the queue");

        $unfinishedUsers = $fitbitApp->getDatabase()->query("-- noinspection SqlDialectInspection
        SELECT fuid, name from " . $fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users where
        UNIX_TIMESTAMP(str_to_date(lastrun,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date("Y-m-d H:i:s", strtotime('-1 day')) . "') AND
        UNIX_TIMESTAMP(str_to_date(cooldown,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date("Y-m-d H:i:s") . "')")->fetchAll();

        if (!empty($unfinishedUsers) and count($unfinishedUsers) > 0 and $fitbitApp->getSetting('nx_fitbit_ds_all_cron', TRUE)) {
            foreach ($unfinishedUsers as $user) {
                if (!$fitbitApp->valdidateOAuth($fitbitApp->getUserOAuthTokens($user['fuid'], FALSE))) {
                    nxr($user['name'] . " has not completed the OAuth configuration");
                } else {
                    if (time() < $end) {
                        nxr("Adding all to Q for " . $user['name']);
                        $fitbitApp->addCronJob($user['fuid'], 'all');
                    }
                }
            }
        }

        $allowed_triggers = Array();
        foreach ($fitbitApp->supportedApi() as $key => $name) {
            if ($fitbitApp->getSetting('nx_fitbit_ds_' . $key, FALSE) && $fitbitApp->getSetting('nx_fitbit_ds_' . $key . '_cron', FALSE) && $key != "all") {
                $allowed_triggers[] = $key;
            }
        }

        if (count($allowed_triggers) == 0) {
            nxr("I am not allowed to re-queue anything so will re-queue with empty records");
        } else {
            $unfinishedUsers = $fitbitApp->getDatabase()->query("-- noinspection SqlDialectInspection
            SELECT fuid, name from " . $fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users where UNIX_TIMESTAMP(str_to_date(cooldown,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date("Y-m-d H:i:s") . "')")->fetchAll();

            if (!empty($unfinishedUsers) and count($unfinishedUsers) > 0) {
                foreach ($unfinishedUsers as $user) {
                    if (!$fitbitApp->valdidateOAuth($fitbitApp->getUserOAuthTokens($user['fuid'], FALSE))) {
                        nxr($user['name'] . " has not completed the OAuth configuration");
                    } else {
                        nxr(" Repopulating for " . $user['name']);

                        $fitbitApp->getFitbitapi()->setActiveUser($user['fuid']);
                        foreach ($allowed_triggers as $allowed_trigger) {
                            if (!is_numeric($fitbitApp->getFitbitapi()->isAllowed($allowed_trigger, TRUE))) {
                                if ($fitbitApp->getFitbitapi()->api_isCooled($allowed_trigger)) {
                                    nxr("  + $allowed_trigger added to queue");
                                    $fitbitApp->addCronJob($user['fuid'], $allowed_trigger);
                                } else {
                                    nxr("  - $allowed_trigger still too hot");
                                }
                            }
                        }
                    }
                }
            } else {
                nxr("There is nothing to queue");
            }
        }

        if (time() < $end) {
            run_through_queue();
        }
    }

    /**
     * @return bool
     */
    function run_through_queue() {
        global $fitbitApp, $end;
        $repopulate_queue = TRUE;

        $queuedJobs = $fitbitApp->getCronJobs();
        if (count($queuedJobs) > 0) {
            foreach ($queuedJobs as $job) {
                if (time() < $end) {
                    if ($fitbitApp->isUser($job['user'])) {
                        $cooldown = $fitbitApp->getUserCooldown($job['user']);
                        if ($fitbitApp->getSetting('nx_fitbit_ds_' . $job['trigger'], TRUE)) { //TODO: Set top false by default
                            if (strtotime($cooldown) < strtotime(date("Y-m-d H:i:s"))) {
                                nxr("Processing queue item " . $fitbitApp->supportedApi($job['trigger']) . " for " . $job['user']);
                                $jobRun = $fitbitApp->getFitbitapi(TRUE)->pull($job['user'], $job['trigger']);
                                if ($fitbitApp->getFitbitapi()->isApiError($jobRun)) {
                                    nxr("* Cron Error: " . $fitbitApp->lookupErrorCode($jobRun));
                                } else {
                                    $fitbitApp->delCronJob($job['user'], $job['trigger']);
                                }
                            } else {
                                nxr("Can not process " . $fitbitApp->supportedApi($job['trigger']) . ". API limit reached for " . $job['user'] . ". Cooldown period ends " . $cooldown);
                                $fitbitApp->delCronJob($job['user'], $job['trigger']);
                            }
                        } else {
                            nxr(" " . $fitbitApp->supportedApi($job['trigger']) . " has been disabled");
                            $fitbitApp->delCronJob($job['user'], $job['trigger']);
                        }

                        if (strtotime($cooldown) < strtotime(date("Y-m-d H:i:s"))) {
                            $lastrun = strtotime($fitbitApp->getDatabase()->get($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users", "lastrun", array("fuid" => $job['user'])));
                            if ($lastrun < (strtotime('now') - (60 * 60 * 24))) {
                                $fitbitApp->addCronJob($job['user'], 'all');
                                $repopulate_queue = FALSE;
                            }
                        }
                    } else {
                        nxr("  Can not process " . $fitbitApp->supportedApi($job['trigger']) . " since " . $job['user'] . " is no longer a user.");
                        $fitbitApp->delCronJob($job['user'], $job['trigger']);
                    }
                } else {
                    nxr("Timeout reached skipping " . $fitbitApp->supportedApi($job['trigger']) . " for " . $job['user']);
                    $repopulate_queue = FALSE;
                }
            }
        }

        return $repopulate_queue;
    }