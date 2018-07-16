<?php

namespace Laravel\Nova\Tests;

abstract class MySqlIntegrationTest extends IntegrationTest
{
    /**
     * Load the migrations for the test environment.
     *
     * @return void
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom([
            '--database' => 'mysql',
            '--realpath' => realpath(__DIR__.'/Migrations'),
        ]);
    }

    /**
     * Define environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');

        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'nova_test',
            'username' => 'root',
            'password' => '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);
    }
}
