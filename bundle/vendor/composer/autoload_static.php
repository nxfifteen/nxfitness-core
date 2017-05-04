<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3680e43b0e8b07fd89ded0a7ce80f03f
{
    public static $files = array (
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        '5255c38a0faeba867671b61dfda6d864' => __DIR__ . '/..' . '/paragonie/random_compat/lib/random.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'd' => 
        array (
            'djchen\\OAuth2\\Client\\' => 21,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'M' => 
        array (
            'Medoo\\' => 6,
        ),
        'L' => 
        array (
            'League\\OAuth2\\Client\\' => 21,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'djchen\\OAuth2\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/djchen/oauth2-fitbit/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Medoo\\' => 
        array (
            0 => __DIR__ . '/..' . '/catfan/medoo/src',
        ),
        'League\\OAuth2\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/oauth2-client/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'R' => 
        array (
            'Raven_' => 
            array (
                0 => __DIR__ . '/..' . '/sentry/sentry/lib',
            ),
        ),
    );

    public static $classMap = array (
        'PiwikTracker' => __DIR__ . '/..' . '/piwik/piwik-php-tracker/PiwikTracker.php',
        'couch' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couch.php',
        'couchAdmin' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchAdmin.php',
        'couchClient' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchClient.php',
        'couchConflictException' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchClient.php',
        'couchDocument' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchDocument.php',
        'couchException' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchClient.php',
        'couchExpectationException' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchClient.php',
        'couchForbiddenException' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchClient.php',
        'couchNoResponseException' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchClient.php',
        'couchNotFoundException' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchClient.php',
        'couchReplicator' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchReplicator.php',
        'couchUnauthorizedException' => __DIR__ . '/..' . '/dready92/php-on-couch/lib/couchClient.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3680e43b0e8b07fd89ded0a7ce80f03f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3680e43b0e8b07fd89ded0a7ce80f03f::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3680e43b0e8b07fd89ded0a7ce80f03f::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit3680e43b0e8b07fd89ded0a7ce80f03f::$classMap;

        }, null, ClassLoader::class);
    }
}
