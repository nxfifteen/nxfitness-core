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
            'database_name' => $this->getSetting("db_name"),
            'server' => $this->getSetting("db_server"),
            'username' => $this->getSetting("db_username"),
            'password' => $this->getSetting("db_password"),
            'charset' => 'utf8'
        ]));

        $this->getSettings()->setDatabase($this->getDatabase());
    }

    /**
     * Helper function to check for supported API calls
     * @param null $key
     * @return array|null|string
     */
    public function supportedApi($key = NULL)
    {
        $database_array = array(
            'all' => 'Everything',
            'floors' => 'Floors Climed',
            'foods' => 'Calorie Intake',
            'badges' => 'Badges',
            'sleep' => 'Sleep Records',
            'body' => 'Weight & Body Fat Records',
            'goals' => 'Personal Goals',
            'water' => 'Water Intake',
            'activities' => 'Pedomitor & Activities',
            'leaderboard' => 'Friends',
            'devices' => 'Device Status',
            'caloriesOut' => 'Calories Out',
            'goals_calories' => 'Calorie Goals',
            'minutesVeryActive' => 'Minutes Very Active',
            'minutesFairlyActive' => 'Minutes Fairly Active',
            'minutesLightlyActive' => 'Minutes Lightly Active',
            'minutesSedentary' => 'Minutes Sedentary',
            'elevation' => 'Elevation',
            'distance' => 'Distance Traveled',
            'pedomitor' => 'Steps Taken',
            'profile' => 'User Profile',
            'heart' => 'Heart Rates',
        );
        asort($database_array);

        if (is_null($key)) {
            return $database_array;
        } else {
            if (array_key_exists($key, $database_array)) {
                return $database_array[$key];
            } else {
                return $key;
            }
        }
    }

    /**
     * Get list of pending cron jobs from database
     * @return array|bool
     */
    public function getCronJobs()
    {
        return $this->getDatabase()->select($this->getSetting("db_prefix", null, false) . "queue", "*", ["ORDER" => "date ASC"]);
    }

    /**
     * Delete cron jobs from queue
     * @param $user_fitbit_id
     * @param $trigger
     * @return bool|int
     */
    public function delCronJob($user_fitbit_id, $trigger)
    {
        if ($this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "queue", ["AND" => [
            "user" => $user_fitbit_id,
            "trigger" => $trigger
        ]])
        ) {
            return $this->getDatabase()->delete($this->getSetting("db_prefix", null, false) . "queue", [
                "AND" => [
                    "user" => $user_fitbit_id,
                    "trigger" => $trigger
                ]
            ]);
        } else {
            return false;
        }
    }

    /**
     * @param string $user_fitbit_id
     * @return bool
     */
    public function isUser($user_fitbit_id)
    {
        if ($this->getDatabase()->has($this->getSetting("db_prefix", null, false) . "users", ["fuid" => $user_fitbit_id])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get settings from config class
     * @param $key
     * @param null $default
     * @param bool $query_db
     * @return string
     */
    public function getSetting($key, $default = NULL, $query_db = true) {
        return $this->getSettings()->get($key, $default, $query_db);
    }

    /**
     * Set value in database/config class
     * @param $key
     * @param $value
     * @return bool
     */
    public function setSetting($key, $value) {
        return $this->getSettings()->set($key, $value);
    }

    /**
     * @return config
     */
    private function getSettings()
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
