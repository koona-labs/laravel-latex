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
            ->assets([__FILE__], true)
            ->with(['some' => 'data'])
            ->runs(2);
        
        $texable->publicField = 'publicData'; 

        $latex->expects($this->once())->method('runs')->with($this->equalTo(2))->willReturn($this->returnSelf());
        $latex->expects($this->once())->method('view')->with($this->equalTo('someview'))->willReturn($this->returnSelf()); 
        $latex->expects($this->once())->method('assets')->with($this->equalTo([__FILE__]))->willReturn($this->returnSelf()); 
        $latex->expects($this->once())->method('with')->with($this->equalTo(['some' => 'data', 'publicField' => 'publicData']))->willReturn($this->returnSelf()); 
        
        $texable->make($latex); 
    }

    /** @test */
    public function it_builds_assets_when_a_relative_path_is_given()
    {

        $latex = $this->createMock(LatexToPdf::class);
        $latex->method('runs')->willReturn($this->returnSelf());
        $latex->method('view')->willReturn($this->returnSelf());
        $latex->method('with')->willReturn($this->returnSelf());
        $latex->method('get')->willReturn($this->returnValue(0));

        $latex->expects($this->once())->method('assets')->with($this->equalTo([
            config('view.paths')[0].'/LatexToPdf/some_image.jpg'
        ]))->willReturn($this->returnSelf());


        (new Texable())->assets('some_image.jpg')->view('LatexToPdf.entry')->make($latex);

    }
    
    
}
