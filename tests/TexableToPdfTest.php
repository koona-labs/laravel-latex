<?php

namespace Abiturma\LaravelLatex\Tests;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Helpers\TemporaryDirectory;
use Abiturma\LaravelLatex\LatexToPdf;
use Abiturma\LaravelLatex\Tests\resources\texables\TestMe;
use Abiturma\LaravelLatex\Texable;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class TexableToPdfTest extends TestCase
{
    
    #[Test]
    public function it_passes_all_texable_data_to_the_latex_class()
    {
        $latex = $this->createMock(LatexToPdf::class);
        $texable = (new TestMe());
        
        $texable->view('someview')
            ->assets([__FILE__], true)
            ->with(['some' => 'data'])
            ->runs(2);
        
        $texable->publicField = 'publicData'; 

        $latex->expects($this->once())->method('runs')->with($this->equalTo(2))->willReturnSelf();
        $latex->expects($this->once())->method('view')->with($this->equalTo('someview'))->willReturnSelf(); 
        $latex->expects($this->once())->method('assets')->with($this->equalTo([__FILE__]))->willReturnSelf(); 
        $latex->expects($this->once())->method('with')->with($this->equalTo(['some' => 'data', 'publicField' => 'publicData']))->willReturnSelf(); 
        
        $texable->make($latex); 
    }

    #[Test]
    public function it_builds_assets_when_a_relative_path_is_given()
    {

        $latex = $this->createMock(LatexToPdf::class);
        $latex->method('runs')->willReturnSelf();
        $latex->method('view')->willReturnSelf();
        $latex->method('with')->willReturnSelf();
        $latex->method('get')->willReturn("");

        $latex->expects($this->once())->method('assets')->with($this->equalTo([
            config('view.paths')[0].'/LatexToPdf/some_image.jpg'
        ]))->willReturnSelf();


        (new Texable())->assets('some_image.jpg')->view('LatexToPdf.entry')->make($latex);

    }
    
    
}
