<?php

namespace Abiturma\LaravelLatex\Tests\Helpers;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Tests\TestCase;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;

class LatexCompilerTest extends TestCase
{
    
    
    #[Test]
    public function it_calls_the_binary_with_the_correct_options()
    {
        Process::fake();
        $path = __DIR__.'/../resources/latex/test.tex'; 
        $file = new File($path); 
        $this->make()->compile($file); 
        Process::assertRan("pdflatex {$file->path()} --interaction=nonstopmode");
    }


    protected function make()
    {
        return $this->app->make(LatexCompiler::class); 
    }

    
    
}
