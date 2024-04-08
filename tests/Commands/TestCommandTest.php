<?php

namespace Abiturma\LaravelLatex\Tests\Commands;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Tests\TestCase;
use Illuminate\Http\File;
use PHPUnit\Framework\Attributes\Test;

class TestCommandTest extends TestCase
{

    #[Test]
    public function it_produces_three_pdfs_as_output()
    {
        $this->mock(LatexCompiler::class, function ($mock) {
            $mock->shouldReceive(['compile' => new File(__FILE__)]);
        });
        $this->artisan('latex:test')->assertExitCode(0);     
    }
    
    
    
}
