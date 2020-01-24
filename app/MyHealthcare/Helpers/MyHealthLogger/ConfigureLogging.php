<?php

namespace App\MyHealthcare\Helpers\MyHealthLogger;

use Illuminate\Log\Writer;
use Monolog\Logger as Monolog;
use Illuminate\Contracts\Foundation\Application;

class ConfigureLogging
{

    public function bootstrap(Application $app)
    {
        $log = $this->registerLogger($app);
        if ($app->hasMonologConfigurator()) {
            call_user_func($app->getMonologConfigurator(), $log->getMonolog());
        } else {
            $this->configureHandlers($app, $log);
        }
    }

    protected function registerLogger(Application $app)
    {
        $app->instance('log', $log = new MyHealthErrorLogger(new Monolog($app->environment()), $app['events']));
        return $log;
    }

    protected function configureHandlers(Application $app, Writer $log)
    {
        $method = 'configure' . ucfirst($app['config']['app.log']) . 'Handler';
        $this->{$method}($app, $log);
    }

    protected function configureSingleHandler(Application $app, Writer $log)
    {
        $log->useFiles($app->storagePath() . '/logs/laravel.log', $app->make('config')->get('app.log_level', 'debug'));
    }

    protected function configureDailyHandler(Application $app, Writer $log)
    {
        $config = $app->make('config');
        $maxFiles = $config->get('app.log_max_files');
        $log->useDailyFiles($app->storagePath() . '/logs/laravel.log', is_null($maxFiles) ? 5 : $maxFiles, $config->get('app.log_level', 'debug'));
    }

    protected function configureSyslogHandler(Application $app, Writer $log)
    {
        $log->useSyslog('laravel', $app->make('config')->get('app.log_level', 'debug'));
    }

    protected function configureErrorlogHandler(Application $app, Writer $log)
    {
        $log->useErrorLog($app->make('config')->get('app.log_level', 'debug'));
    }

}
