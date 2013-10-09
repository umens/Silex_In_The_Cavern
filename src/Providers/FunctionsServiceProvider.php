<?php

namespace Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;

Class FunctionsServiceProvider implements ServiceProviderInterface {

	public function register(Application $app) {

		$app['add'] = $app->protect(function ($date_str, $months) use ($app) {

			$date = new \DateTime($date_str);
		    $start_day = $date->format('j');

		    $date->modify("+{$months} month");
		    $end_day = $date->format('j');

		    if ($start_day != $end_day)
		        $date->modify('last day of last month');

		    return $date;

		});

		$app['sub'] = $app->protect(function ($date_str, $months) use ($app) {

			$date = new \DateTime($date_str);
		    $start_day = $date->format('j');

		    $date->modify("-{$months} month");
		    $end_day = $date->format('j');

		    if ($start_day != $end_day)
		        $date->modify('last day of last month');

		    return $date;

		});

    }

    public function boot(Application $app) { }

}

?>