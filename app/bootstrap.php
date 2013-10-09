<?php

/**  Bootstraping */

/*class Application extends Silex\Application{   
    use Silex\Application\TwigTrait;
    use Silex\Application\SecurityTrait;
    use Silex\Application\FormTrait;
    use Silex\Application\UrlGeneratorTrait;
    use Silex\Application\SwiftmailerTrait;
    use Silex\Application\MonologTrait;
    use Silex\Application\TranslationTrait; 
}*/

/** Silex Extensions */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\RememberMeServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;

use Providers\FunctionsServiceProvider;

use Symfony\Component\Translation\Loader\YamlFileLoader;


$app->register(new HttpCacheServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());

$app->register(new DoctrineServiceProvider());

$app->register(new TranslationServiceProvider());
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/locales/fr.yml', 'fr');
    $translator->addResource('yaml', __DIR__.'/locales/en.yml', 'en');

    return $translator;
}));

$app->register(new TwigServiceProvider(), array(
    'twig.path'             => array(__DIR__.'/../src/views'),
    'twig.options'          => array(
        'charset'           => 'utf-8',
        'strict_variables'  => true,
        'cache'             => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false
    ),
    'twig.form.templates'   => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
));

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../logs/app.log',
    'monolog.name' => 'controlStation',
    'monolog.level'   => 300 // = \Monolog\Logger::WARNING
));

$app->register(new SecurityServiceProvider());
$app->register(new RememberMeServiceProvider());
$app->register(new SessionServiceProvider(), array(
    'session.test' => true,
));

$app->register(new FunctionsServiceProvider());

if (!file_exists(__DIR__.'/config.php')) {
    throw new RuntimeException('You must create your own configuration file. See "src/config.php.dist" for an example config file.');
}

return $app;