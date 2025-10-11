<?php

namespace Afromessage\Laravel\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Afromessage\Laravel\AfroMessageServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [AfroMessageServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default config for testing
        $app['config']->set('afromessage', [
            'token' => 'test-token',
            'base_url' => 'https://api.afromessage.com/api/',
            'sender_id' => 'TEST',
            'sender_name' => 'TestSender',
            'timeout' => 120,
        ]);
    }
}