<?php

namespace Abiturma\LaravelLatex\Tests\Helpers;

use Abiturma\LaravelLatex\Helpers\BladeToLatex;
use Abiturma\LaravelLatex\Tests\TestCase;

class BladeToLatexTest extends TestCase
{
    
    /** @test */
    public function a_blade_view_is_compiled_to_a_string()
    {
        $variable = "This is a test"; 
        $actual = $this->make()->toString('BladeToLatex.entry',compact('variable')); 
        $this->assertEquals(trim("Variable: $variable"),trim($actual)); 
    }
    
    
    /** @test */
    public function a_blade_file_is_compile_to_a_string()
    {
        $variable = "This is a test";
        $actual = $this->make()->toStringFromPath(__DIR__ . '/../resources/views/BladeToLatex/entry.blade.php',compact('variable'));
        $this->assertEquals(trim("Variable: $variable"),trim($actual));
    }
    
    

    protected function make()
    {
        return $this->app->make(BladeToLatex::class);     
    }
    
    
}
