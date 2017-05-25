<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

namespace Core\Analytics;

require_once( dirname( __FILE__ ) . "/../../autoloader.php" );

use Core\Core;
use Exception;
use Medoo\Medoo;
use Raven_Autoloader;
use Raven_Client;
use Raven_ErrorHandler;

/**
 * ErrorRecording
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-ErrorRecording
 *            phpDocumentor wiki for ErrorRecording.
 * @version   0.0.1
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 */
class ErrorRecording {

    /**
     * @var Raven_Client
     */
    protected $sentryClient;
    /**
     * @var Raven_ErrorHandler
     */
    protected $sentryErrorHandler;
    /**
     * @var Core
     */
    protected $appClass;

    /**
     * ErrorRecording constructor.
     *
     * @param Core $appClass
     */
    public function __construct( $appClass ) {
        if ( defined( 'SENTRY_DSN' ) ) {
            Raven_Autoloader::register();

            $this->appClass = $appClass;
        }
    }

    /**
     * @return Raven_Client
     */
    public function getSentryClient() {
        if ( defined( 'SENTRY_DSN' ) ) {
            if ( is_null( $this->sentryClient ) ) {
                $this->sentryClient = ( new Raven_Client( SENTRY_DSN ) )
                    ->setAppPath( __DIR__ )
                    ->setRelease( $this->appClass->getSetting( "version", "0.0.0.1", true ) )
                    ->setEnvironment( $this->appClass->getSetting( "environment", "development", false ) )
                    ->setPrefixes( [ __DIR__ ] )
                    ->install();

                $this->sentryClient->user_context( [
                    'id'         => sha1( gethostbyname( gethostname() ) . gethostname() . $this->appClass->getSetting( "ownerFuid",
                            "Unknown", false ) ),
                    'username'   => $this->appClass->getSetting( "ownerFuid", "Unknown", false ),
                    'ip_address' => gethostbyname( gethostname() )
                ] );
            }

            return $this->sentryClient;
        } else {
            return null;
        }
    }

    /**
     * @return Raven_ErrorHandler
     */
    public function getSentryErrorHandler() {
        if ( defined( 'SENTRY_DSN' ) ) {
            if ( is_null( $this->sentryErrorHandler ) ) {
                $this->sentryErrorHandler = new Raven_ErrorHandler( $this->getSentryClient() );
                $this->sentryErrorHandler->registerExceptionHandler();
                $this->sentryErrorHandler->registerErrorHandler();
                $this->sentryErrorHandler->registerShutdownFunction();
            }

            return $this->sentryErrorHandler;
        } else {
            return null;
        }
    }

    /**
     * Log an exception to sentry
     *
     * @param Exception $exception The Exception object.
     * @param array     $data      Additional attributes to pass with this event (see Sentry docs).
     * @param null      $logger
     * @param null      $vars
     *
     * @return int|null
     */
    public function captureException( $exception, $data = null, $logger = null, $vars = null ) {
        if ( defined( 'SENTRY_DSN' ) ) {
            nxr( 0, "### Exception Recorded ###" );

            return $this->getSentryClient()->captureException( $exception, $data, $logger, $vars );
        } else {
            return null;
        }
    }

    /**
     * Log a message to sentry
     *
     * @param string $message The message (primary description) for the event.
     * @param array  $params  params to use when formatting the message.
     * @param array  $data    Additional attributes to pass with this event (see Sentry docs).
     * @param bool   $stack
     * @param null   $vars
     *
     * @return int|null
     */
    public function captureMessage( $message, $params = [], $data = [], $stack = false, $vars = null ) {
        nxr( 0, "[ERROR] $message" );
        if ( defined( 'SENTRY_DSN' ) ) {
            nxr( 0, "### Message Recorded ###" );

            return $this->getSentryClient()->captureMessage( $message, $params, $data, $stack, $vars );
        } else {
            return null;
        }
    }

    /**
     * @param Medoo $medoo
     * @param array $parameters
     *
     * @return int|null
     */
    public function postDatabaseQuery( $medoo, $parameters ) {
        if ( defined( 'SENTRY_DSN' ) ) {
            $medoo_error = $medoo->error();
            if ( $medoo_error[ 0 ] != 0000 ) {
                $medoo_info = $medoo->info();

                return $this->captureMessage( $medoo_error[ 2 ], [ 'database' ], [
                    'level' => 'error',
                    'extra' => [
                        'method'         => $parameters[ 'METHOD' ],
                        'method_line'    => $parameters[ 'LINE' ],
                        'sql_server'     => $medoo_info[ 'server' ],
                        'sql_client'     => $medoo_info[ 'client' ],
                        'sql_driver'     => $medoo_info[ 'driver' ],
                        'sql_version'    => $medoo_info[ 'version' ],
                        'sql_connection' => $medoo_info[ 'connection' ],
                        'sql_last_query' => $medoo->last(),
                        'php_version'    => phpversion(),
                        'core_version'   => $this->appClass->getSetting( "version", "0.0.0.1", true )
                    ]
                ] );
            }
        }

        return null;
    }
}
