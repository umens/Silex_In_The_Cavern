<?php

require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();
require __DIR__.'/../config/config.php';

require __DIR__.'/../app/app.php';

$app['http_cache']->run();