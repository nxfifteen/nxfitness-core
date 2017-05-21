<?php
    /*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

    print __FILE__ . " @" . __LINE__ . "\n";

    // set config settings
    autoloader(array(
        array(
            'debug'      => true, // turn on debug mode (by default debug mode is off)
            'basepath'   => dirname(__FILE__), // basepath is used to define where your project is located
            'extensions' => array('.php'), // allowed class file extensions
        )
    ));

    // now we can set class autoload paths
    autoloader(array('.' . DIRECTORY_SEPARATOR));

    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "functions.php");
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "bundle" . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php");

    /**
     * Autoloader
     *
     * @staticvar boolean $is_init
     * @staticvar array $conf
     * @staticvar array $paths
     *
     * @param array|string|NULL $class_paths
     *                                        when loading class paths ex: ['path/one', 'path/two']
     *                                        when loading class ex: 'myclass'
     *                                        when returning cached paths: NULL
     * @param boolean           $use_base_dir (when true will prepend class path with base directory)
     *
     * @return array|boolean
     *        (default boolean if class paths registered/loaded, or when debugging
     *            (or NULL passed as $class_paths) array of registered class paths
     *            (and loaded class files, configuration settings) returned)
     */
    function autoloader($class_paths = null, $use_base_dir = true)
    {
        static $is_init = false;

        static $conf = [
            'basepath'   => '',
            'debug'      => true,
            'extensions' => ['.php'], // multiple extensions ex: ['.php', '.class.php']
            'namespace'  => '',
            'verbose'    => true
        ];

        static $paths = [];

        if (\is_null($class_paths)) // autoloader(); returns paths (for debugging)
        {
            return $paths;
        }

        if (\is_array($class_paths) && isset($class_paths[0]) && \is_array($class_paths[0])) // conf settings
        {
            foreach ($class_paths[0] as $k => $v) {
                if (isset($conf[$k]) || \array_key_exists($k, $conf)) {
                    $conf[$k] = $v; // set conf setting
                }
            }

            unset($class_paths[0]); // rm conf from class paths
        }

        if (!$is_init) // init autoloader
        {
            \spl_autoload_extensions(implode(',', $conf['extensions']));
            \spl_autoload_register(null, false); // flush existing autoloads
            $is_init = true;
        }

        if ($conf['debug']) {
            $paths['conf'] = $conf; // add conf for debugging
        }

        if (!\is_array($class_paths)) // autoload class
        {
            // class with namespaces, ex: 'MyPack\MyClass' => 'MyPack/MyClass' (directories)
            $class_path = \str_replace('\\', \DIRECTORY_SEPARATOR, $class_paths);

            foreach ($paths as $path) {
                if (!\is_array($path)) // do not allow cached 'loaded' paths
                {
                    foreach ($conf['extensions'] as &$ext) {
                        $ext = \trim($ext);

                        if (\file_exists($path . $class_path . $ext)) {
                            if ($conf['debug']) {
                                if (!isset($paths['loaded'])) {
                                    $paths['loaded'] = [];
                                }

                                $paths['loaded'][] = $path . $class_path . $ext;
                            }

                            /** @noinspection PhpIncludeInspection */
                            require $path . $class_path . $ext;

                            if ($conf['verbose']) {
                                nxr(0, 'autoloaded class "' . $path . $class_path . $ext . '"');
                                if (defined('TEST_SUITE')) print "autoloaded class '" . $path . $class_path . $ext . "'\n";
                            }

                            return true;
                        }
                    }

                    if ($conf['verbose']) {
                        if (!empty($ext)) {
                            nxr(0, 'failed to load class "' . $path . $class_path . $ext . '"');
                            if (defined('TEST_SUITE')) print "failed to load class '" . $path . $class_path . $ext . "'\n";
                        } else {
                            nxr(0, 'failed to load class "' . $path . $class_path . '"');
                            if (defined('TEST_SUITE')) print "failed to load class '" . $path . $class_path . $ext . "'\n";
                        }
                    }
                }
            }

            return false; // failed to autoload class
        } else // register class path
        {
            $is_unregistered = true;

            if (count($class_paths) > 0) {
                foreach ($class_paths as $path) {
                    $tmp_path = ($use_base_dir ? \rtrim($conf['basepath'], \DIRECTORY_SEPARATOR)
                                                 . \DIRECTORY_SEPARATOR : '') . \trim(\rtrim($path,
                            \DIRECTORY_SEPARATOR))
                                . \DIRECTORY_SEPARATOR;

                    if (!\in_array($tmp_path, $paths)) {
                        $paths[] = $tmp_path;

                        if ($conf['verbose']) {
                            if (function_exists("nxr")) {
                                nxr(0, __METHOD__ . ': registered path "' . $tmp_path . '"');
                                if (defined('TEST_SUITE')) print __METHOD__ . ": registered path '" . $tmp_path . "'\n";
                            }
                        }
                    }
                }

                if (\spl_autoload_register((strlen($conf['namespace']) > 0 // add namespace
                        ? rtrim($conf['namespace'], '\\') . '\\' : '') . 'autoloader', (bool)$conf['debug'])) {
                    if ($conf['verbose']) {
                        if (function_exists("nxr")) {
                            nxr(0, __METHOD__ . ': autoload registered');
                            if (defined('TEST_SUITE')) print __METHOD__ . ": autoload registered\n";
                        }
                    }

                    $is_unregistered = false; // flag unable to register
                } else if ($conf['verbose']) {
                    if (function_exists("nxr")) {
                        nxr(0, __METHOD__ . ': autoload register failed');
                        if (defined('TEST_SUITE')) print __METHOD__ . ": autoload register failed\n";
                    }
                }
            }

            return !$conf['debug'] ? !$is_unregistered : $paths;
        }
    }
