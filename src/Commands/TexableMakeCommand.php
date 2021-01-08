<?php

namespace Abiturma\LaravelLatex\Commands;
use Illuminate\Console\GeneratorCommand;


class TexableMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:latex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Latex file';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Texable';


    public function handle()
    {
        parent::handle(); 
    }
    
    
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return  __DIR__.'/stubs/texable.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Latex';
    }

}
