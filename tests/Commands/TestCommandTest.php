<?php

namespace Abiturma\LaravelLatex\Tests\Commands;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Tests\TestCase;
use Illuminate\Http\File;
use TitasGailius\Terminal\Terminal;

class TestCommandTest extends TestCase
{

    /** @test */
    public function it_produces_two_pdfs_as_output()
    {
        $this->mock(LatexCompiler::class, function ($mock) {
            $mock->shouldReceive(['compile' => new File(__FILE__)]);
        });
        $this->artisan('latex:test')->assertExitCode(0);     
    }
    
    
    
}
