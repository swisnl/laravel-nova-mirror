<?php

namespace Laravel\Nova\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Nova resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Nova Assets / Resources...');
        $this->callSilent('nova:publish');

        $this->comment('Publishing Nova Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'nova-provider']);

        $this->registerNovaServiceProvider();

        $this->comment('Generating User Resource...');
        $this->callSilent('nova:resource', ['name' => 'User']);
        copy(__DIR__.'/stubs/user-resource.stub', app_path('Nova/User.php'));

        $this->info('Nova scaffolding installed successfully.');
    }

    /**
     * Register the Nova service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerNovaServiceProvider()
    {
        file_put_contents(config_path('app.php'), str_replace(
            "App\\Providers\EventServiceProvider::class,".PHP_EOL,
            "App\\Providers\EventServiceProvider::class,".PHP_EOL."        App\Providers\NovaServiceProvider::class,".PHP_EOL,
            file_get_contents(config_path('app.php'))
        ));
    }
}
