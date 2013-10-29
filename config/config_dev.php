<?php

// include the prod configuration
require __DIR__.'/config.php';

ini_set('display_errors', 1);
error_reporting(-1);

// enable the debug mode
$app['debug'] = true;