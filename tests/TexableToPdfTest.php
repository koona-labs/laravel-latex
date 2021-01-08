<?php

namespace Abiturma\LaravelLatex\Tests;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Helpers\TemporaryDirectory;
use Abiturma\LaravelLatex\LatexToPdf;
use Abiturma\LaravelLatex\Tests\resources\texables\TestMe;
use Abiturma\LaravelLatex\Texable;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class TexableToPdfTest extends TestCase
{
    
    /** @test */
    public function it_passes_all_texable_data_to_the_latex_class()
    {
        $latex = $this->createMock(LatexToPdf::class);
        $texable = (new TestMe());
        
        $texable->view('someview')
            ->assets(['some,assets'])
            ->with(['some' => 'data'])
            ->runs(2);
        
        $texable->publicField = 'publicData'; 

        $latex->expects($this->once())->method('runs')->with($this->equalTo(2))->willReturn($this->returnSelf());
        $latex->expects($this->once())->method('view')->with($this->equalTo('someview'))->willReturn($this->returnSelf()); 
        $latex->expects($this->once())->method('assets')->with($this->equalTo(['some,assets']))->willReturn($this->returnSelf()); 
        $latex->expects($this->once())->method('with')->with($this->equalTo(['some' => 'data', 'publicField' => 'publicData']))->willReturn($this->returnSelf()); 
        
        $texable->make($latex); 
    }
    
    
    
}
