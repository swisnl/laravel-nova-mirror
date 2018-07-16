<?php

namespace Laravel\Nova\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;

class LensCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nova:lens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new lens class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Lens';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        return str_replace('uri-key', Str::snake($this->argument('name'), '-'), $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/lens.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Nova\Lenses';
    }
}
