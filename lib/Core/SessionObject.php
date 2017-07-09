<?php
/*******************************************************************************
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

namespace Core;

require_once( dirname( __FILE__ ) . "/../autoloader.php" );

/**
 * Session helper class
 *
 * @link      https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/phpdoc-class-session phpDocumentor wiki
 *            for Config.
 * @version   0.0.1
 * @since     0.0.4
 * @author    Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link      https://nxfifteen.me.uk NxFIFTEEN
 * @copyright 2017 Stuart McCulloch Anderson
 * @license   https://nxfifteen.me.uk/api/license/mit/ MIT
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class SessionObject {

    public $vars;

    /**
     * SessionObject constructor.
     */
    public function __construct() {
        $this->vars = &$_SESSION; //this will still trigger a phpmd warning
        //nxr(0, $this->vars);
    }

    /**
     * @param string $variableName
     * @param int    $filter
     * @param null   $options
     *
     * @return mixed
     */
    public function getVar( $variableName, $filter = FILTER_DEFAULT, $options = null ) {
        return filter_var( $this->vars[ $variableName ], $filter, $options );
    }

    /**
     * @param $variableName
     * @param $variableValue
     */
    public function setVar( $variableName, $variableValue ) {
        $this->vars[ $variableName ] = $variableValue;
    }
}