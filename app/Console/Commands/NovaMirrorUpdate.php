<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class NovaMirrorUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova-mirror:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads Nova releases, syncs and pushes to a private git repository';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Updating Nova mirror using Dusk...');
        Artisan::call('dusk', [base_path('tests/Browser/NovaUpdaterTest.php')]);
    }

}
