<?php
    // set config settings
    autoloader(array(array(
        'debug' => true, // turn on debug mode (by default debug mode is off)
        'basepath' => dirname(__FILE__), // basepath is used to define where your project is located
        'extensions' => array('.php'), // allowed class file extensions
    )));

    // now we can set class autoload paths
    autoloader(array( './' ));

    if (!function_exists("nxr") || !function_exists("nxr_destroy_session")) {
        require_once(dirname(__FILE__) . "/functions.php");
    }

    /**
     * Autoloader
     *
     * @staticvar boolean $is_init
     * @staticvar array $conf
     * @staticvar array $paths
     * @param array|string|NULL $class_paths
     *		when loading class paths ex: ['path/one', 'path/two']
     *		when loading class ex: 'myclass'
     *		when returning cached paths: NULL
     * @param boolean $use_base_dir (when true will prepend class path with base directory)
     * @return array|boolean
     *		(default boolean if class paths registered/loaded, or when debugging
     *			(or NULL passed as $class_paths) array of registered class paths
     *			(and loaded class files, configuration settings) returned)
     */
    function autoloader($class_paths = NULL, $use_base_dir = true)
    {
        static $is_init = false;

        static $conf = [
            'basepath' => '',
            'debug' => false,
            'extensions' => ['.php'], // multiple extensions ex: ['.php', '.class.php']
            'namespace' => '',
            'verbose' => false
        ];

        static $paths = [];

        if(\is_null($class_paths)) // autoloader(); returns paths (for debugging)
        {
            return $paths;
        }

        if(\is_array($class_paths) && isset($class_paths[0]) && \is_array($class_paths[0])) // conf settings
        {
            foreach($class_paths[0] as $k => $v)
            {
                if(isset($conf[$k]) || \array_key_exists($k, $conf))
                {
                    $conf[$k] = $v; // set conf setting
                }
            }

            unset($class_paths[0]); // rm conf from class paths
        }

        if(!$is_init) // init autoloader
        {
            \spl_autoload_extensions(implode(',', $conf['extensions']));
            \spl_autoload_register(NULL, false); // flush existing autoloads
            $is_init = true;
        }

        if($conf['debug'])
        {
            $paths['conf'] = $conf; // add conf for debugging
        }

        if(!\is_array($class_paths)) // autoload class
        {
            // class with namespaces, ex: 'MyPack\MyClass' => 'MyPack/MyClass' (directories)
            $class_path = \str_replace('\\', \DIRECTORY_SEPARATOR, $class_paths);

            foreach($paths as $path)
            {
                if(!\is_array($path)) // do not allow cached 'loaded' paths
                {
                    foreach($conf['extensions'] as &$ext)
                    {
                        $ext = \trim($ext);

                        if(\file_exists($path . $class_path . $ext))
                        {
                            if($conf['debug'])
                            {
                                if(!isset($paths['loaded']))
                                {
                                    $paths['loaded'] = [];
                                }

                                $paths['loaded'][] = $path . $class_path . $ext;
                            }

                            require $path . $class_path . $ext;

                            if($conf['verbose'])
                            {
                                echo '<div>' . __METHOD__ . ': autoloaded class "' . $path
                                     . $class_path . $ext . '"</div>';
                            }

                            return true;
                        }
                    }

                    if($conf['verbose'])
                    {
                        if (!empty($ext)) {
                            echo '<div>' . __METHOD__ . ': failed to autoload class "' . $path
                                 . $class_path . $ext . '"</div>';
                        } else {
                            echo '<div>' . __METHOD__ . ': failed to autoload class "' . $path
                                 . $class_path . ' (no ext defined)"</div>';
                        }
                    }
                }
            }

            return false; // failed to autoload class
        }
        else // register class path
        {
            $is_unregistered = true;

            if(count($class_paths) > 0)
            {
                foreach($class_paths as $path)
                {
                    $tmp_path = ( $use_base_dir ? \rtrim($conf['basepath'], \DIRECTORY_SEPARATOR)
                                                  . \DIRECTORY_SEPARATOR : '' ) . \trim(\rtrim($path, \DIRECTORY_SEPARATOR))
                                . \DIRECTORY_SEPARATOR;

                    if(!\in_array($tmp_path, $paths))
                    {
                        $paths[] = $tmp_path;

                        if($conf['verbose'])
                        {
                            echo '<div>' . __METHOD__ . ': registered path "' . $tmp_path . '"</div>';
                        }
                    }
                }

                if(\spl_autoload_register(( strlen($conf['namespace']) > 0 // add namespace
                        ? rtrim($conf['namespace'], '\\') . '\\' : '' ) . 'autoloader', (bool)$conf['debug']))
                {
                    if($conf['verbose'])
                    {
                        echo '<div>' . __METHOD__ . ': autoload registered</div>';
                    }

                    $is_unregistered = false; // flag unable to register
                }
                else if($conf['verbose'])
                {
                    echo '<div>' . __METHOD__ . ': autoload register failed</div>';
                }
            }

            return !$conf['debug'] ? !$is_unregistered : $paths;
        }
    }
