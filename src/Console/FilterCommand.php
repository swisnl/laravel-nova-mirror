<?php

namespace Laravel\Nova\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class FilterCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nova:filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new filter class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('boolean')) {
            return __DIR__.'/stubs/boolean-filter.stub';
        } elseif ($this->option('date')) {
            return __DIR__.'/stubs/date-filter.stub';
        }

        return __DIR__.'/stubs/filter.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Nova\Filters';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['boolean', null, InputOption::VALUE_NONE, 'Indicates if the generated filter should be a boolean filter'],
            ['date', null, InputOption::VALUE_NONE, 'Indicates if the generated filter should be a date filter'],
        ];
    }
}
