<?php

ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

require __DIR__.'/../app/config.php';
require __DIR__.'/../app/bootstrap.php';

//require __DIR__.'/../src/controllers.php';

$app->mount('/', new Controllers\FrontController());
$app->mount('/admin', new Controllers\AdminController());

$app->error(function (\Exception $e) use ($app) {
        if ($e instanceof Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return $app['twig']->render('404.html.twig', array('code' => 404));
        }
        else{
        	return new Symfony\Component\HttpFoundation\Response('We are sorry, but something went terribly wrong.');
        }
 
        /*$code = ($e instanceof HttpException) ? $e->getStatusCode() : 500;
        return $app['twig']->render('error.twig', array('code' => $code));*/
    }
);

$app['http_cache']->run();