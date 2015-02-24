<?php

    /**
     * NxFitbit - Cron commandline tool
     * @version 0.0.1
     * @author Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link http://nxfifteen.me.uk NxFIFTEEN
     * @copyright 2015 Stuart McCulloch Anderson
     * @license http://stuart.nx15.at/mit/2015 MIT
     */

    require_once(dirname(__FILE__) . "/inc/app.php");
    $fitbitApp = new NxFitbit();

    $end = time() + 500;
    $queuedJobs = $fitbitApp->getCronJobs();

    $repopulate_queue = TRUE;
    if (count($queuedJobs) > 0) {
        foreach ($queuedJobs as $job) {
            if (time() < $end) {
                if ($fitbitApp->isUser($job['user'])) {
                    if ($fitbitApp->getSetting('nx_fitbit_ds_' . $job['trigger'], TRUE)) { //TODO: Set top false by default
                        $cooldown = $fitbitApp->getUserCooldown($job['user']);
                        if (strtotime($cooldown) < strtotime(date("Y-m-d H:i:s"))) {
                            nxr("Processing queue item " . $fitbitApp->supportedApi($job['trigger']) . " for " . $job['user']);
                            $fitbitApp->getFitbitapi()->pull($job['user'], $job['trigger']);
                            $fitbitApp->delCronJob($job['user'], $job['trigger']);
                        } else {
                            nxr("Can not process " . $fitbitApp->supportedApi($job['trigger']) . ". API limit reached for " . $job['user'] . ". Cooldown period ends " . $cooldown);
                        }
                    } else {
                        nxr(" " . $fitbitApp->supportedApi($job['trigger']) . " has been disabled");
                        $fitbitApp->delCronJob($job['user'], $job['trigger']);
                    }

                    $lastrun = strtotime($fitbitApp->getDatabase()->get($fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users", "lastrun", array("fuid" => $job['user'])));
                    if ($lastrun < (strtotime('now') - (60 * 60 * 24))) {
                        $fitbitApp->addCronJob($job['fuid'], 'all');
                        $repopulate_queue = FALSE;
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

    if ($repopulate_queue) {
        nxr("Ready to repopulate the queue");

        $unfinishedUsers = $fitbitApp->getDatabase()->query("-- noinspection SqlDialectInspection
        SELECT fuid, name from " . $fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users where
        UNIX_TIMESTAMP(str_to_date(lastrun,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date("Y-m-d H:i:s", strtotime('-1 day')) . "') AND
        UNIX_TIMESTAMP(str_to_date(cooldown,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date("Y-m-d H:i:s") . "')")->fetchAll();

        if (!empty($unfinishedUsers) and count($unfinishedUsers) > 0 and $fitbitApp->getSetting('nx_fitbit_ds_all_cron', TRUE)) {
            foreach ($unfinishedUsers as $user) {
                if (time() < $end) {
                    nxr("Adding all to Q for " . $user['name']);
                    $fitbitApp->addCronJob($user['fuid'], 'all');
                }
            }
        } else {
            $allowed_triggers = Array();
            foreach ($fitbitApp->supportedApi() as $key => $name) {
                if ($fitbitApp->getSetting('nx_fitbit_ds_' . $key . '_cron', FALSE) && $key != "all") {
                    $allowed_triggers[] = $key;
                }
            }

            if (count($allowed_triggers) == 0) {
                nxr("I am not allowed to requeue anything so will requeue with empty records");
            } else {
                $unfinishedUsers = $fitbitApp->getDatabase()->query("-- noinspection SqlDialectInspection
                SELECT fuid, name from " . $fitbitApp->getSetting("db_prefix", NULL, FALSE) . "users where
                UNIX_TIMESTAMP(str_to_date(lastrun,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date("Y-m-d H:i:s", strtotime('-1 minute')) . "') AND
                UNIX_TIMESTAMP(str_to_date(cooldown,'%Y-%m-%d %H:%i:%s')) < UNIX_TIMESTAMP('" . date("Y-m-d H:i:s") . "')")->fetchAll();

                if (!empty($unfinishedUsers) and count($unfinishedUsers) > 0) {
                    foreach ($unfinishedUsers as $user) {
                        nxr("Repopulating queue for " . $user['name']);

                        foreach ($allowed_triggers as $trigger) {
                            if ($fitbitApp->getFitbitapi()->api_isCooled($trigger, $user['fuid'])) {
                                $fitbitApp->addCronJob($user['fuid'], $trigger);
                            } else {
                                nxr("  " . $fitbitApp->supportedApi($trigger) . " isn't cool enought yet");
                            }
                        }
                    }
                } else {
                    nxr("There is nothing to queue");
                }
            }

        }

    }

