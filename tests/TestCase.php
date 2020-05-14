<?php

namespace Abiturma\LaravelLatex\Tests;


class TestCase extends \Orchestra\Testbench\TestCase
{

    public function setUp() :void 
    {
        
        parent::setUp(); 
            
        config()->set('view.paths',[__DIR__.'/resources/views']);  
            
        
    }

    protected function getPackageProviders($app)
    {
        return ['Abiturma\LaravelLatex\LatexServiceProvider'];
    }
    
    
}
