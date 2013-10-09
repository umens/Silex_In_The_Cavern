<?php

namespace Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class FrontController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function () use ($app) {

            return $app['twig']->render('index.html.twig', array(

            ));

        })->bind('home');

        $controllers->get('/monitoring', function () use ($app) {

            return $app['twig']->render('monitoring.html.twig', array(

            ));

        })->bind('monitoring');

        $controllers->get('/sites', function () use ($app) {

            return $app['twig']->render('site.html.twig', array(

            ));

        })->bind('site');

        $controllers->get('/login', function (Request $request) use ($app) {

            $token = $app['security']->getToken();

            return $app['twig']->render('login.html.twig', array(
                'error' => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ));

        })->bind('login');

        /*$controllers->get('/archives', function () use ($app) {
            

            $token = $app['security']->getToken();

            if (null !== $token) {
                $user = $token->getUser();
            }
            else{
                $user = null;
            }
            echo "<pre>";
            print_r($user);
            echo "</pre>";
            die();

        })->bind('archives');

        $controllers->get('/contact', function () use ($app) {

            return $app['twig']->render('contact.html.twig', array(
                'title' => "Contactez nous",
            ));

        })->bind('contact');*/

        $controllers->post('/login_check', function (Application $app) { })->bind('check_path');

        return $controllers;

    }
	
}