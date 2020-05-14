<?php

namespace Abiturma\LaravelLatex\Tests\Helpers;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Tests\TestCase;
use Illuminate\Http\File;
use TitasGailius\Terminal\Terminal;

class LatexCompilerTest extends TestCase
{
    
    
    /** @test */
    public function it_calls_the_binary_with_the_correct_options()
    {
        Terminal::fake(); 
        $path = __DIR__.'/../resources/latex/test.tex'; 
        $file = new File($path); 
        $this->make()->compile($file); 
        Terminal::assertExecuted("pdflatex {$file->path()} --interaction=nonstopmode");
    }


    protected function make()
    {
        return $this->app->make(LatexCompiler::class); 
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Terminal::reset();
    }
    
    
}
