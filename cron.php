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

$end = time() + 60;
$queuedJobs = $fitbitApp->getCronJobs();

$repopulate_queue = false;
if (count($queuedJobs) > 0) {
    foreach ($queuedJobs as $job) {
        print_r($job);
    }
}
