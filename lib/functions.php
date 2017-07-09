<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 * Copyright (c) 2017. Stuart McCulloch Anderson
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

/**
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.DevelopmentCodeFragment)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
if ( ! function_exists( "nxr" ) ) {
    /**
     * NXR is a helper function. Past strings are recorded in a text file
     * and when run from a command line output is displayed on screen as
     * well
     *
     * @param integer             $indentation Log line indenation
     * @param string|array|object $msg         String input to be displayed in logs files
     * @param bool                $includeDate If true appends datetime stamp
     * @param bool                $newline     If true adds a new line character
     * @param bool                $echoLine    Print a new line or not
     */
    function nxr( $indentation, $msg, $includeDate = true, $newline = true, $echoLine = true ) {
        if ( is_array( $msg ) || is_object( $msg ) ) {
            $msg = print_r( $msg, true );
        }

        for ( $counter = 0; $counter < $indentation; $counter++ ) {
            $msg = " " . $msg;
        }

        if ( $includeDate ) {
            $msg = date( "Y-m-d H:i:s" ) . ": " . $msg;
        }
        if ( $newline ) {
            $msg = $msg . "\n";
        }

        if ( is_writable( dirname( __FILE__ ) . "/../fitbit.log" ) ) {
            $logFileName = fopen( dirname( __FILE__ ) . "/../fitbit.log", "a" );
            fwrite( $logFileName, $msg );
            fclose( $logFileName );
        }

        if ( ( ! defined( 'TEST_SUITE' ) || TEST_SUITE == false ) && $echoLine !== false && ( ! defined( 'IS_CRON_RUN' ) || ! IS_CRON_RUN ) && php_sapi_name() == "cli" ) {
            echo $msg;
        }
    }
}

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
if ( ! function_exists( "nxr_destroy_session" ) ) {

    function nxr_destroy_session() {
        // Unset all of the session variables.
        unset( $_SESSION );

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if ( ini_get( "session.use_cookies" ) ) {
            $params = session_get_cookie_params();
            setcookie( session_name(), '', time() - 42000,
                $params[ "path" ], $params[ "domain" ],
                $params[ "secure" ], $params[ "httponly" ]
            );
        }

        // Finally, destroy the session.
        session_destroy();
    }
}
