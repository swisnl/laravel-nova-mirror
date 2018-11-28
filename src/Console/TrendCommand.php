<?php

namespace Laravel\Nova\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class TrendCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nova:trend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new metric (trend) class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Metric';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $key = preg_replace('/[^a-zA-Z0-9]+/', '', $this->argument('name'));

        return str_replace('uri-key', Str::snake($key, '-'), $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/trend.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Nova\Metrics';
    }
}
