<?php

namespace Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class FrontController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function () use ($app) {

            return $app['twig']->render('index.html.twig', array(

            ));

        })->bind('home');


        /*
         *
         * Don't remove lines behind. it's for the firewall
         *
         */
        $controllers->get('/login', function (Request $request) use ($app) {

            $token = $app['security']->getToken();

            return $app['twig']->render('login.html.twig', array(
                'error' => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ));

        })->bind('login');

        $controllers->post('/login_check', function (Application $app) { })->bind('check_path');

        return $controllers;

    }
	
}