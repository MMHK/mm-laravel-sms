<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/3/5
 * Time: 11:08
 */

namespace MMHK\SMS;


use Illuminate\Support\ServiceProvider;
use MMHK\SMS\Contracts\GatewayInterface;

class SMSServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    public function boot() {
        $configPath = dirname(__DIR__). '/config/sms-service.php';

        if (function_exists('config_path')) {
            $publishPath = config_path('sms-service.php');
        } else {
            $publishPath = base_path('config/sms-service.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = dirname(__DIR__). '/config/sms-service.php';;
        $this->mergeConfigFrom($configPath, 'sms-service');

        $this->app->bind(GatewayInterface::class, function () {
            $config = config();
            $default = $config->get('sms-service.default');
            $driver = $config->get('sms-service.services.'.$default.'.driver');
            return new $driver($config->get('sms-service.services.'.$default.'.config'));
        });

        /**
         * 注入一个别名
         */
        $this->app->singleton('sms', function ($app){
            /**
             * @var $app \Illuminate\Contracts\Foundation\Application
             */
            return $app->make(GatewayInterface::class);
        });
    }
}