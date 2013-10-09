<?php

date_default_timezone_set("Europe/Paris");

// Local
$app['locale'] = 'fr';
$app['session.default_locale'] = $app['locale'];
$app['translator.messages'] = array(
    'fr' => __DIR__.'/locales/fr.yml',
    'en' => __DIR__.'/locales/en.yml',
);

// Cache
$app['cache.path'] = __DIR__ . '/../cache';

// Http cache
$app['http_cache.cache_dir'] = $app['cache.path'] . '/http';

// Twig cache
$app['twig.options.cache'] = $app['cache.path'] . '/twig';

// database
$app['db.options'] = array(
    'driver' => 'pdo_mysql',
    'dbhost' => 'localhost',
    'dbname' => 'controlStation',
    'user' => 'root',
    'password' => '',
    'driverOptions' => array(1002 => 'SET NAMES utf8 COLLATE utf8_general_ci'),
    //'path' => $app['cache.dir'].'/app.db', si sqlite
);


//sécurité de l'application
$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/login$',
    ),
    'secured' => array(
        'pattern' => '^.*$',
        'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
        "remember_me" => array('key' => 'MySuperMegaGigaNewSecret', 'lifetime' => '31536000'),
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