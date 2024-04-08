<?php

namespace Abiturma\LaravelLatex\Tests\Facades;

use Abiturma\LaravelLatex\Facades\Latex;
use Abiturma\LaravelLatex\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LatexFacadeTest extends TestCase
{

    #[Test]
    public function the_facade_returns_an_instance_of_the_class()
    {
        $this->assertInstanceOf("\Abiturma\LaravelLatex\LatexToPdf",Latex::view('test')); 
    }
    
    
    
}
