<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 * Copyright (c) 2017. Stuart McCulloch Anderson
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @subpackage  UX
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

namespace Core\UX;

require_once( dirname( __FILE__ ) . "/../../autoloader.php" );

use Core\Config;
use Core\Core;
use Core\SessionObject;
use DateTime;
use Medoo\Medoo;

/**
 * Class NxFitAdmin
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class NxFitAdmin {

    /**
     * @var Medoo
     */
    protected $database;

    /**
     * @var string
     */
    protected $activeUser;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Config
     */
    protected $apiSettings;

    /**
     * @var Core
     */
    protected $nxFit;

    /**
     * @var Config
     */
    protected $dbUserProfile;

    /**
     * Create the admin UX class, parent class to Code with additonal UX methods
     */
    public function __construct() {
        $sessionObject = new SessionObject();
        $adminConfig   = $sessionObject->getVar( 'admin_config', FILTER_UNSAFE_RAW );

        if ( $adminConfig && is_array( $adminConfig ) && count( $adminConfig ) > 0
        ) {
            $this->setConfig( $adminConfig );
        } else {
            if ( isset( $config ) ) {
                $sessionObject->setVar( 'admin_config', $config );
                $this->setConfig( $config );
            }
        }

        $this->nxFit = new Core();

        $this->setDatabase( new medoo( [
            'database_type' => 'mysql',
            'database_name' => $this->getApiSetting( "db_name" ),
            'server'        => $this->getApiSetting( "db_server" ),
            'username'      => $this->getApiSetting( "db_username" ),
            'password'      => $this->getApiSetting( "db_password" ),
            'charset'       => 'utf8'
        ] ) );

        $this->getApiSettingClass()->setDatabase( $this->getDatabase() );

        if ( ! filter_input( INPUT_COOKIE, '_nx_fb_usr', FILTER_SANITIZE_STRING ) || ! filter_input( INPUT_COOKIE, '_nx_fb_key', FILTER_SANITIZE_STRING ) ) {
            header( "Location: " . $this->getConfig( 'url' ) . $this->getConfig( '/admin' ) . "/login" );
        } else if ( filter_input( INPUT_COOKIE, '_nx_fb_key', FILTER_SANITIZE_STRING ) != hash( "sha256",
                $this->getApiSetting( "salt" ) .
                filter_input( INPUT_COOKIE, '_nx_fb_usr', FILTER_SANITIZE_STRING ) .
                filter_input( INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING ) .
                filter_input( INPUT_SERVER, 'SERVER_ADDR', FILTER_SANITIZE_STRING ) .
                filter_input( INPUT_SERVER, 'SERVER_ADDR', FILTER_SANITIZE_STRING )
            )
        ) {
            header( "Location: " . $this->getConfig( 'url' ) . $this->getConfig( '/admin' ) . "/login" );
        }

        $this->setActiveUser( filter_input( INPUT_COOKIE, '_nx_fb_usr', FILTER_SANITIZE_STRING ) );

    }

    /**
     * @param string $key
     * @param null   $default
     * @param bool   $rawQueryBb
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @return string
     */
    public function getApiSetting( $key = "", $default = null, $rawQueryBb = true ) {
        return $this->getApiSettingClass()->get( $key, $default, $rawQueryBb );
    }

    /**
     * @return Config
     */
    public function getApiSettingClass() {
        return $this->getNxFit()->getSettings();
    }

    /**
     * @return Core
     */
    public function getNxFit() {
        return $this->nxFit;
    }

    /**
     * @return medoo
     */
    public function getDatabase() {
        return $this->database;
    }

    /**
     * @param medoo $database
     */
    public function setDatabase( $database ) {
        $this->database = $database;
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getConfig( $key = "" ) {
        if ( ! is_string( $key ) && $key == "" ) {
            return $this->config;
        } else {
            return $this->config[ $key ];
        }
    }

    /**
     * @param array $config
     */
    public function setConfig( $config ) {
        $this->config = $config;
    }

    /**
     * @return float
     */
    public function getSyncStatus() {
        $sessionObject      = new SessionObject();
        $syncProgress       = $sessionObject->getVar( 'SyncProgress', FILTER_VALIDATE_INT );
        $syncProgressScopes = $sessionObject->getVar( 'SyncProgressScopes', FILTER_UNSAFE_RAW );

        if ( ! $syncProgress || $syncProgress < 0 || $syncProgress > 100 || ! $syncProgressScopes || ! is_array( $syncProgressScopes ) ) {
            $timeToday     = strtotime( date( "Y-m-d H:i:s" ) );
            $timeFirstSeen = strtotime( $this->getUserProfile()[ 'seen' ] . ' 00:00:00' );

            $totalProgress   = 0;
            $allowedTriggers = [];
            foreach ( array_keys( $this->getNxFit()->supportedApi() ) as $key ) {
                if ( $this->getApiSetting( 'scope_' . $key ) && $this->getNxFit()->getUserSetting( filter_input( INPUT_COOKIE, '_nx_fb_usr', FILTER_SANITIZE_STRING ),
                        'scope_' . $key ) && $key != "all"
                ) {
                    $allowedTriggers[ $key ][ 'name' ] = $this->getNxFit()->supportedApi( $key );

                    /** @var \DateTime $oldestScope */
                    $oldestScope = $this->getOldestScope( $key );
                    $timeLastRun = strtotime( $oldestScope->format( "Y-m-d H:i:s" ) );

                    $differenceLastRun   = $timeLastRun - $timeToday;
                    $differenceFirstSeen = $timeFirstSeen - $timeToday;
                    $precentageCompleted = round( ( 100 - ( $differenceLastRun / $differenceFirstSeen ) * 100 ), 1 );
                    if ( $precentageCompleted < 0 ) {
                        $precentageCompleted = 0;
                    }
                    if ( $precentageCompleted > 100 ) {
                        $precentageCompleted = 100;
                    }

                    $allowedTriggers[ $key ][ 'precentage' ] = $precentageCompleted;
                    $totalProgress                           += $precentageCompleted;
                }
            }

            $sessionObject->setVar( 'SyncProgressScopes', $allowedTriggers );
            $sessionObject->setVar( 'SyncProgress', round( ( $totalProgress / ( 100 * count( $allowedTriggers ) ) ) * 100, 1 ) );
        }

        return $sessionObject->getVar( 'SyncProgress', FILTER_VALIDATE_INT );
    }

    /**
     * @return Config
     */
    public function getUserProfile() {
        if ( ! isset( $this->dbUserProfile ) ) {
            $userProfile         = $this->getDatabase()->get( $this->getApiSetting( "db_prefix", null,
                    false ) . "users", [
                'name',
                'avatar',
                'city',
                'country',
                'height',
                'stride_walking',
                'stride_running',
                'gender',
                'rank',
                'friends',
                'distance',
                'seen',
                'lastrun',
                'cooldown'
            ], [ "fuid" => $this->getActiveUser() ] );
            $this->dbUserProfile = $userProfile;
        }

        return $this->dbUserProfile;
    }

    /**
     * @return string
     */
    public function getActiveUser() {
        return $this->activeUser;
    }

    /**
     * @param string $activeUser
     */
    public function setActiveUser( $activeUser ) {
        $this->activeUser = $activeUser;
    }

    /**
     * @param null $scope
     *
     * @return DateTime
     */
    public function getOldestScope( $scope = null ) {
        if ( is_null( $scope ) ) {
            if ( $this->getDatabase()->has( $this->getApiSetting( "db_prefix", null, false ) . "runlog",
                [ "user" => $this->getActiveUser() ] )
            ) {
                return new DateTime ( $this->getDatabase()->get( $this->getApiSetting( "db_prefix", null,
                        false ) . "runlog", "lastrun", [
                    "user"  => $this->getActiveUser(),
                    "ORDER" => [ "lastrun" => "ASC" ]
                ] ) );
            }
        } else {
            if ( $this->getDatabase()->has( $this->getApiSetting( "db_prefix", null, false ) . "runlog", [
                "AND" => [
                    "user"     => $this->getActiveUser(),
                    "activity" => $scope
                ]
            ] )
            ) {
                $returnTime = new DateTime ( $this->getDatabase()->get( $this->getApiSetting( "db_prefix", null,
                        false ) . "runlog", "lastrun", [
                    "AND"   => [
                        "user"     => $this->getActiveUser(),
                        "activity" => $scope
                    ],
                    "ORDER" => [ "lastrun" => "ASC" ]
                ] ) );

                return $returnTime;
            }
        }

        return new DateTime ( "1970-01-01" );
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @return string
     */
    public function getLocalWeatherImage() {
        $usrProfile = $this->getUserProfile();
        $usrCity    = $usrProfile[ 'city' ];
        $usrCountry = $usrProfile[ 'country' ];

        if ( isset( $usrCity ) && ( ! is_string( $usrCity ) || $usrCity == "" ) ) {
            unset( $usrCity );
        } else {
            $usrCity = str_ireplace( " ", "", str_ireplace( ".", "", $usrCity ) );
        }
        if ( isset( $usrCountry ) && ( ! is_string( $usrCountry ) || $usrCountry == "" ) ) {
            unset( $usrCountry );
        } else {
            $usrCountry = str_ireplace( " ", "", str_ireplace( ".", "", $usrCountry ) );
        }

        $imagePath = CORE_UX . "img/local/";

        if ( isset( $usrCity ) && isset( $usrCountry ) && file_exists( $imagePath . strtolower( $usrCountry ) . "/" . strtolower( $usrCity ) . ".jpg" ) ) {
            return "img/local/" . strtolower( $usrCountry ) . "/" . strtolower( $usrCity ) . ".jpg";
        }

        if ( isset( $usrCity ) && file_exists( $imagePath . strtolower( $usrCity ) . ".jpg" ) ) {
            if ( isset( $usrCountry ) ) {
                nxr( 1,
                    "+** No Location Image for " . strtolower( $usrCountry ) . "/" . strtolower( $usrCity ) . ".jpg" );
            }

            return "img/local/" . strtolower( $usrCity ) . ".jpg";
        }

        if ( isset( $usrCountry ) && file_exists( $imagePath . strtolower( $usrCountry ) . ".jpg" ) ) {
            if ( isset( $usrCity ) ) {
                nxr( 1,
                    "+** No Location Image for " . strtolower( $usrCountry ) . "/" . strtolower( $usrCity ) . ".jpg" );
            }

            return "img/local/" . strtolower( $usrCountry ) . ".jpg";
        }

        return "img/local/default.jpg";
    }

    /**
     * @return string
     */
    public function getLocalWeatherCode() {
        $usrProfile = $this->getUserProfile();
        $usrCity    = $usrProfile[ 'city' ];
        $usrCountry = $usrProfile[ 'country' ];

        if ( isset( $usrCity ) && isset( $usrCountry ) ) {
            return "$usrCity, $usrCountry";
        }

        return "London, GB";
    }

    /**
     * @return string|array
     */
    public function getUserTheme() {
        //$themes = array('blue','default','green','orange','purple');
        //return $themes[array_rand($themes, 1)];

        return "default";
    }

    /**
     * @param string $trigger
     *
     * @return DateTime
     */
    public function getScopeCoolDown( $trigger ) {
        if ( $this->getDatabase()->has( $this->getApiSetting( "db_prefix", null, false ) . "runlog", [
            "AND" => [
                "user"     => $this->getActiveUser(),
                "activity" => $trigger
            ]
        ] )
        ) {
            return new DateTime ( $this->getDatabase()->get( $this->getApiSetting( "db_prefix", null,
                    false ) . "runlog", "cooldown", [
                "AND" => [
                    "user"     => $this->getActiveUser(),
                    "activity" => $trigger
                ]
            ] ) );
        } else {
            return new DateTime ( "1970-01-01" );
        }
    }

    /**
     * @param string $string
     * @param array  $array
     *
     * @return string
     */
    public function getThemeWidgets( $string, $array ) {
        unset( $string );
        unset( $array );

        return "";
    }
}