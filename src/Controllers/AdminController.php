<?php

namespace Controllers;

use Silex\ControllerProviderInterface;
use Silex\Application;

class AdminController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {

            return $app['twig']->render('admin.html.twig', array(

            ));
        
        })->bind('admin');

        return $controllers;
    }
}
