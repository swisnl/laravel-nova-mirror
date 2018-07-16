<?php

namespace Laravel\Nova\Tests;

use Mockery;
use Laravel\Nova\Nova;
use Orchestra\Testbench\TestCase;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Hash;
use Laravel\Nova\Tests\Fixtures\NoopAction;
use Laravel\Nova\Tests\Fixtures\TagResource;
use Laravel\Nova\Tests\Fixtures\FileResource;
use Laravel\Nova\Tests\Fixtures\PostResource;
use Laravel\Nova\Tests\Fixtures\RoleResource;
use Laravel\Nova\Tests\Fixtures\UserResource;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Nova\Tests\Fixtures\PanelResource;
use Laravel\Nova\Tests\Fixtures\AddressResource;
use Laravel\Nova\Tests\Fixtures\BooleanResource;
use Laravel\Nova\Tests\Fixtures\CommentResource;
use Laravel\Nova\Tests\Fixtures\ForbiddenUserResource;
use Laravel\Nova\Tests\Fixtures\SoftDeletingFileResource;

abstract class IntegrationTest extends TestCase
{
    /**
     * The user the request is currently authenticated as.
     *
     * @var mixed
     */
    protected $authenticatedAs;

    /**
     * Setup the test case.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        Hash::driver('bcrypt')->setRounds(4);

        $this->loadMigrations();

        $this->withFactories(__DIR__.'/Factories');

        Nova::$tools = [];
        Nova::$resources = [];
        NoopAction::$applied = [];
        NoopAction::$appliedToComments = [];

        Nova::resources([
            AddressResource::class,
            BooleanResource::class,
            CommentResource::class,
            FileResource::class,
            PanelResource::class,
            PostResource::class,
            RoleResource::class,
            SoftDeletingFileResource::class,
            TagResource::class,
            UserResource::class,
            ForbiddenUserResource::class,
        ]);

        Nova::auth(function () {
            return true;
        });
    }

    /**
     * Load the migrations for the test environment.
     *
     * @return void
     */
    protected function loadMigrations()
    {
        $this->loadMigrationsFrom([
            '--database' => 'sqlite',
            '--realpath' => realpath(__DIR__.'/Migrations'),
        ]);
    }

    /**
     * Authenticate as an anonymous user.
     *
     * @return $this
     */
    protected function authenticate()
    {
        $this->actingAs($this->authenticatedAs = Mockery::mock(Authenticatable::class));

        $this->authenticatedAs->shouldReceive('getAuthIdentifier')->andReturn(1);
        $this->authenticatedAs->shouldReceive('getKey')->andReturn(1);

        return $this;
    }

    /**
     * Run the next job on the queue.
     *
     * @param  int  $times
     * @return void
     */
    protected function work($times = 1)
    {
        for ($i = 0; $i < $times; $i++) {
            $this->worker()->runNextJob(
                'redis', 'default', $this->workerOptions()
            );
        }
    }

    /**
     * Get the queue worker instance.
     *
     * @return \Illuminate\Queue\Worker
     */
    protected function worker()
    {
        return resolve('queue.worker');
    }

    /**
     * Get the options for the worker.
     *
     * @return \Illuminate\Queue\WorkerOptions
     */
    protected function workerOptions()
    {
        return tap(new WorkerOptions, function ($options) {
            $options->sleep = 0;
            $options->maxTries = 1;
        });
    }

    /**
     * Get the service providers for the package.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Orchestra\Database\ConsoleServiceProvider',
            'Laravel\Nova\NovaCoreServiceProvider',
            'Laravel\Nova\NovaServiceProvider',
            'Laravel\Nova\Tests\TestServiceProvider',
        ];
    }

    /**
     * Define environment.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
