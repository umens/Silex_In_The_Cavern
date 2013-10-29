<?php

$app->mount('/', new Controllers\FrontController());
$app->mount('/admin', new Controllers\AdminController());