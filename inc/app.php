<?php

/**
 * NxFitbit
 * @version 0.0.1
 * @author Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link http://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2015 Stuart McCulloch Anderson
 * @license http://stuart.nx15.at/mit/2015 MIT
 */
class NxFitbit
{
    /**
     * @var config
     */
    protected $settings;

    /**
     * @var medoo
     */
    protected $database;

    public function __construct()
    {
        require_once(dirname(__FILE__) . "/config.php");
        $this->setSettings(new config());

        require_once(dirname(__FILE__) . "/../library/medoo.php");
        $this->setDatabase(new medoo([
            'database_type' => 'mysql',
            'database_name' => $this->getSettings()->get("db_name"),
            'server' => $this->getSettings()->get("db_server"),
            'username' => $this->getSettings()->get("db_username"),
            'password' => $this->getSettings()->get("db_password"),
            'charset' => 'utf8'
        ]));

        $this->getSettings()->setDatabase($this->getDatabase());
    }

    /**
     * Get list of pending cron jobs from database
     * @return array|bool
     */
    public function getCronJobs() {
        return $this->getDatabase()->select($this->getSettings()->get("db_prefix", null, false) . "queue", "*", ["ORDER" => "date ASC"]);
    }

    /**
     * @param string $user_fitbit_id
     * @return bool
     */
    public function isUser($user_fitbit_id) {
        if ($this->getDatabase()->has($this->getSettings()->get("db_prefix", null, false) . "users", ["fuid" => $user_fitbit_id])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return config
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param config $settings
     */
    private function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return medoo
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param medoo $database
     */
    private function setDatabase($database)
    {
        $this->database = $database;
    }


}
