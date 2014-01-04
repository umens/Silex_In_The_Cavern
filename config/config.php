<?php

date_default_timezone_set("Europe/Paris");
ini_set('display_errors', 0);

// Load configuration file
$ini_config = parse_ini_file(__DIR__.'/config.ini', TRUE);
$config = $ini_config;

// Others
$app['app.name'] = $config['app.name'];
$app['charset']  = $config['app.charset'];

// Log
$app['log.path'] = __DIR__ . '/../app/logs';

// Language
$app['translation.path'] = __DIR__ . '/../app/translations';
$app['locale'] = $config['app.local'];
$app['session.default_locale'] = $app['locale'];
$app['translator.messages'] = array(
    'fr' => $app['translation.path'] . '/fr.yml',
    'en' => $app['translation.path'] . '/en.yml',
);

// Cache
$app['cache.path'] = __DIR__ . '/../app/cache';

// Http cache
$app['http_cache.cache_dir'] = $app['cache.path'] . '/http';

// Twig cache
$app['twig.options.cache'] = $app['cache.path'] . '/twig';

// Database
$app['db.options'] = array(
    'driver'   => $config['db.driver'],
    'dbhost'   => $config['db.host'],
    'dbname'   => $config['db.dbname'],
    'user'     => $config['db.user'],
    'password' => $config['db.password'],
    'driverOptions' => array(1002 => 'SET NAMES utf8 COLLATE utf8_general_ci'),    
);


// Security
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/login$',
    ),
    'secured' => array(
        'pattern' => '^.*$',
        'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
        "remember_me" => array('key' => $config['app.secret'], 'lifetime' => $config['app.lifetime'], 'always_remember_me' => true),
        'logout' => array('logout_path' => '/logout'),
        'users' => $app->share(function() use ($app) {
                // La classe Providers\UserProvider est spécifique à l'application
                return new Providers\UserProvider($app['db']);
        }),
    ),
);

$app['security.role_hierarchy'] = array(
    'ROLE_ADMIN' => array('ROLE_USER'),
);
            
$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN'),
    array('^.*$', 'ROLE_USER'),
    array('^/$', ''),
);