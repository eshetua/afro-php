<?php

namespace Afromessage\Laravel;

use Illuminate\Support\ServiceProvider;
use Afromessage\Laravel\Services\AfroMessageService;
use Afromessage\Laravel\Contracts\AfroMessageInterface;

class AfroMessageServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/Config/afromessage.php', 'afromessage'
        );

        // Bind the service with configuration
        $this->app->singleton(AfroMessageInterface::class, function ($app) {
            $config = $app['config']['afromessage'];
            
            return new AfroMessageService(
                $config['token'] ?? '',
                $config['base_url'] ?? 'https://api.afromessage.com/api/',
                $config['sender_id'] ?? null,
                $config['sender_name'] ?? null,
                $config['timeout'] ?? 120
            );
        });

        $this->app->alias(AfroMessageInterface::class, 'afromessage');
    }

    public function boot()
    {
        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/Config/afromessage.php' => config_path('afromessage.php'),
            ], 'afromessage-config');
        }
    }
}