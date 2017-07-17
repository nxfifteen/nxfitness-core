<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/
use Core\Core;

/**
 * HabitRPHPG
 * A PHP interface to the API provided by the HabitRPG game.
 * https://github.com/binnyva/habitrphpg
 */
class HabitRPHPG {

    public $pet_types = [];
    public $tags = [];
    private $user_id = '';
    private $api_key = '';
    private $base_url = 'https://habitica.com/';
    private $json_return_format_is_array = true;
    private $options = array(
        'enable_cache'	=> false,   // FOR DEVELOPMENT ONLY.
        'cache_path'	=> '/tmp/',	// Use this for faster testing
        'debug'			=> false,	// Development only.
    );
    private $egg_types = array(
        'Armadillo', 'Axolotl', 'BearCub', 'Beetle', 'Bunny', 'Butterfly', 'Cactus', 'Cheetah', 'Cow', 'Cuttlefish', 'Deer', 'Dragon', 'Egg', 'Egg', 'Falcon', 'Falcon', 'Ferret',
        'FlyingPig', 'Fox', 'Frog', 'Gryphon', 'Gryphon', 'GuineaPig', 'Hedgehog', 'Horse', 'LionCub', 'Monkey', 'Nudibranch', 'Octopus', 'Owl', 'PandaCub', 'Parrot', 'Peacock',
        'Penguin', 'Rat', 'Rock', 'Rooster', 'Sabretooth', 'Seahorse', 'Sheep', 'Slime', 'Sloth', 'Snail', 'Snake', 'Spider', 'TigerCub', 'Treeling', 'TRex', 'Triceratops',
        'Turtle', 'Unicorn', 'Whale', 'Wolf'
    );
    private $hatch_types = array('Base','White','Desert','Red','Shade','Skeleton','Zombie','CottonCandyPink','CottonCandyBlue','Golden');
    private $food_types = array('Meat','Milk','Potatoe','Strawberry','Chocolate','Fish','RottenMeat','CottonCandyPink','CottonCandyBlue','Cake_Skeleton','Cake_Base','Honey','Saddle');

    ///Constructor

    function __construct($user_id, $api_key) {
        if (defined('ENVIRONMENT') && ENVIRONMENT == "develop") {
            nxr(0, "** Connecting to development habitica");
            $this->base_url = 'http://10.1.1.1:3000/';
        }

        $this->user_id = $user_id;
        $this->api_key = $api_key;

        $this->options['cache_path'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'rpg' . DIRECTORY_SEPARATOR;

        foreach ($this->egg_types as $pet) {
            foreach ($this->hatch_types as $type) {
                $this->pet_types[] = $pet . "-" . $type;
            }
        }
    }

    public function _request($method, $operation, $data = '', $returnError = false) {
        if(!function_exists("curl_init")) die("HabitRPG Library requires curl to function.");

        $url = $this->base_url . 'api/v3/' . $operation;
        if($method == 'get' and $data) $url .= '/' . $data;

        $url_parts = parse_url($url);
        $ch = curl_init($url_parts['host']);

        if(is_array($data)) {
            $options['encoding'] = true;
            if (count($data) > 0) {
                $data_sting = json_encode( $data );
            } else {
                $data_sting = '';
            }
        } else {
            $data_sting = $data;
        }

        $response = '';

        // If cacheing is on and we have a cached copy of the request, use that.
        if($this->options['enable_cache']) {
            $cache_file = $this->options['cache_path'] . md5($url) . ".json";
            if(file_exists($cache_file)) {
                $response = file_get_contents($cache_file);
            }
        }

        //nxr(0, __LINE__);
        if ($response == '') {
            curl_setopt($ch, CURLOPT_URL, $url) or die("Invalid cURL Handle Resouce");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Just return the data - not print the whole thing.
            //curl_setopt($ch, CURLOPT_HEADER, true); //We need the headers
            //if (isset($options['encoding'])) curl_setopt($ch, CURLOPT_ENCODING, "application/json");

            if ($method == 'post') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_sting);
            } else if ($method == 'delete') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            }

            $custom_headers = array(
                "x-api-user: {$this->user_id}",
                "x-api-key: {$this->api_key}",
                "Content-Type: application/json",
                "Content-Length: " . strlen($data_sting),
            );
            //nxr(1, $custom_headers);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ignores certificate error

            $response = curl_exec($ch);
            // output error message if an error is occured
            if ($response === false) die(curl_error($ch)); // more verbose output
            //nxr(1, $response);
            //nxr(1, curl_error($ch));
            curl_close($ch);

            if ($this->options['debug']) {
                file_put_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . str_replace("/", '_', $operation) . "_" . rand() . "_.json", $response);
            }

            // Save cached version of the file
            if ($this->options['enable_cache']) {
                file_put_contents($cache_file, $response);
            }
        }

        $return = json_decode($response, $this->json_return_format_is_array);
        $tasks = $return;

        if($this->json_return_format_is_array) {
            if($return['success']) {
                if(isset($return['data'])) $tasks = $return['data'];
            } else {
                if ( $return[ 'error' ] == "NotAuthorized" && strpos( $return[ 'message' ], 'Account has been suspended' ) !== false ) {
                    nxr( 0, "******************************************************************" );
                    $fitbitApp = new Core();
                    $db_prefix = $fitbitApp->getSetting( "db_prefix", null, false );
                    if ( $fitbitApp->getDatabase()->has( $db_prefix . "settings_users", [ "AND" => [ "var" => "habitica_user_id", "data" => $this->user_id ] ] ) ) {
                        $coreUserId = $fitbitApp->getDatabase()->get( $db_prefix . "settings_users", "fuid", [ "AND" => [ "var" => "habitica_user_id", "data" => $this->user_id ] ] );

                        $fitbitApp->setUserSetting( $coreUserId, 'scope_habitica', false );

                        $cacheFile = '../cache' . DIRECTORY_SEPARATOR . '_' . $coreUserId . '_Account';
                        if ( file_exists( $cacheFile ) ) {
                            unlink( $cacheFile );
                        }
                    }

                    unset( $fitbitApp );
                }

                if ($returnError) {
                    return $return;
                } else {
                    nxr(1, "HAB ERROR: '" . $url . "'");
                    if ( isset( $custom_headers ) ) {
                        nxr(1, $custom_headers);
                    }
                    nxr(1, $response);
                    nxr(1, $data_sting);
                    return false;
                }
            }
        } else {
            die("Make sure HabitRPHPG::json_return_format_is_array is true for this to work");
        }

        return $tasks;
    }

    function user() {
        return $this->_request("get", "user");
    }

    function task($task_id = 0, $type = '') {
        $query = '';
        if($type) $query = "?type=$type";

        if($task_id == 0) $return = $this->_request("get", "tasks/user" . $query);
        else $return = $this->_request("get", "tasks/user/$task_id");

        return $return;
    }

    // Returns all the tasks matchnig the task string.
    function findTask($task_string, $type = '') {
        $returns = array();
        $data = $this->task(0, $type);

        if(!$task_string) return $data;

        foreach ($data as $task) {
            if($task['text'] == $task_string) { // Exact match - must be the task we are looking for.
                return array($task);

            } else if(stripos($task['text'], $task_string) !== false and (!isset($task['completed']) or $task['completed'] == false)) {
                $returns[] = $task;
            }
        }

        return $returns;
    }

    function getStats($stats = false) {
        if(!$stats) {
            $data = $this->user();
            $stats = $data['stats'];
        }

        $stats['hp'] = round($stats['hp'], 1);

        if(strpos($stats['exp'], '.') !== false) list($experience,$dec) = explode(".", $stats['exp']);
        else $experience = $stats['exp'];

        if(strpos($stats['gp'], '.') !== false) {
            list($gold,$silver) = explode(".", $stats['gp']);
            $silver = substr($silver, 0, 2);
        }
        else {
            $gold = $stats['gp'];
            $silver = 0;
        }

        return array(
            'hp'			=> $stats['hp'],
            'exp'			=> $experience,
            'maxHealth'		=> empty($stats['maxHealth']) ? 0 : $stats['maxHealth'],
            'toNextLevel'	=> empty($stats['toNextLevel']) ? 0 : $stats['toNextLevel'],
            'gold'			=> $gold,
            'silver'		=> $silver,
            'mp'			=> $stats['mp'],
            'maxMP'			=> empty($stats['maxMP']) ? 0 : $stats['maxMP'],
            'lvl'			=> empty($stats['lvl']) ? 0 : $stats['lvl']
        );
    }

    function createTask($type, $text, $data = array()) {
        $data['type'] = $type;
        $data['text'] = $text;
        if(!isset($data['completed'])) $data['completed'] = false;
        if(!isset($data['value'])) $data['value'] = 0;
        if(!isset($data['notes'])) $data['notes'] = "";
        if (!array_key_exists("alias", $data)) {
            $data['alias'] = sha1("nx" . $data['text']);
        }

        return $this->_request("post", "tasks/user", $data);
    }

    /**
     * @param $task_id
     * @param $data
     * @return bool|mixed
     */
    function updateTask($task_id, $data) {
        return $this->_request("put", "tasks/$task_id", $data);
    }

    /**
     * @return mixed
     */
    function getStatus() {
        $request = $this->_request("get", "status");
        return $request['status'];
    }

    /**
     * Arguments:    $task_id - The ID of the task that should be marked done on not done.
     *                $direction - should be 'up' or 'down'
     * @param $task_id
     * @param $direction
     * @return bool|mixed
     */
    function doTask($task_id, $direction) {
        return $this->_request("post", "tasks/$task_id/score/$direction", array('apiToken'=>$this->api_key));
        //return $this->_request("post", "tasks/$task_id/score/$direction", array());
    }

    /**
     * Arguments:	$food - The food item that should be fed. You must have this.
     *				$pet - Pet indentifier. Will be something like 'Fox-Desert'. You have to have this
     *				$show_error - Prints an error if you enter an invalid food or pet type.
     */
    function feed($food, $pet, $show_error = true) {
        if(!in_array($food, $this->food_types)) {
            if($show_error) print "'$food' is not a valid food type.";
            return false;
        }
        if(!in_array($pet, $this->pet_types)) {
            if($show_error) print "'$pet' is not a valid pet type.";
            return false;
        }

        return $this->_request("post", "user/feed/$pet/$food");
    }

    /**
     * Arguments:	$egg - The eggo that should be made preggo. You must have this.
     *				$hatching_portion - Hatching portion to be used on the egg. Must have this.
     *				$show_error - Prints an error if you enter an invalid food or pet type.
     */
    function hatch($egg, $hatching_portion, $show_error = true) {
        if(!in_array($egg, $this->egg_types)) {
            if($show_error) print "'$egg' is not a valid egg type.";
            return false;
        }
        if(!in_array($hatching_portion, $this->hatch_types)) {
            if($show_error) print "'$hatching_portion' is not a valid hatching portion.";
            return false;
        }

        return $this->_request("post", "user/hatch/$egg/$hatching_portion");
    }

    /**
     * @return bool|mixed
     */
    public function getTags()
    {
        if (count($this->tags) == 0) {
            $this->tags = $this->_request("get", "tags");
        }

        return $this->tags;
    }

    public function clearTags()
    {
        $this->tags = [];
    }
}