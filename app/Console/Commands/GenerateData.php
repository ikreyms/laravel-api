<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class GenerateData extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new DTO class';

    protected function getStub()
    {
        return __DIR__ . '/stubs/data.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {   
        return $rootNamespace . '\Data';
    }
}
