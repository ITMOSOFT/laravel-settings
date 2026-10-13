<?php

namespace ItmoSoft\Settings\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ItmoSoft\Settings\SettingsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SettingsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');

        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $app['config']->set(
            'itmosoft-settings.table',
            'itmosoft_settings'
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate')->run();
    }
}