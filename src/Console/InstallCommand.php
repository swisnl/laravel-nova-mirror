<?php

namespace Laravel\Nova\Console;

use Laravel\Nova\Nova;
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

        $this->comment('Generating User Resource...');
        $this->callSilent('nova:resource', ['name' => 'User']);
        copy(__DIR__.'/stubs/user-resource.stub', app_path('Nova/User.php'));

        $this->info('Nova scaffolding installed successfully.');
    }
}
