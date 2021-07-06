<?php

namespace Abiturma\LaravelLatex\Tests;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Helpers\TemporaryDirectory;
use Abiturma\LaravelLatex\LatexToPdf;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class LatexToPdfTest extends TestCase
{
    /** @test */
    public function it_passes_view_and_data_to_the_latex_directory()
    {
        Storage::fake(); 
        
        $assets = ['path/to/asset']; 
        
        $dir = $this->createMock(TemporaryDirectory::class); 
        $dir->expects($this->once())->method('view')->with($this->equalTo('someview'))->willReturn($this->returnSelf()); 
        $dir->expects($this->once())->method('with')->with($this->equalTo(['variable' => 'test']))->willReturn($this->returnSelf()); 
        $dir->expects($this->once())->method('withAssets')->with($this->equalTo($assets))->willReturn($this->returnSelf()); 
        $dir->method('create')->willReturn($this->returnSelf()); 
        $dir->method('getEntryFile')->willReturn(new File(__FILE__));
        
        $compiler = $this->createMock(LatexCompiler::class); 
        $compiler->method('compile')->willReturn(new File(__FILE__)); 
        
        $latex = new LatexToPdf($this->app->make('config'),$compiler, $dir);  
        $latex->view('someview')->with(['variable' => 'test'])->assets($assets)->get();
        
    }
    
    /** @test */
    public function it_runs_the_compiler_a_spefic_amount_of_times()
    {
        Storage::fake();

        $dir = $this->createMock(TemporaryDirectory::class);
        $dir->method('view')->willReturn($this->returnSelf());
        $dir->method('with')->willReturn($this->returnSelf());
        $dir->method('withAssets')->willReturn($this->returnSelf());
        $dir->method('create')->willReturn($this->returnSelf());
        $dir->method('getEntryFile')->willReturn(new File(__FILE__));
        $compiler = $this->createMock(LatexCompiler::class);
        $compiler->expects($this->exactly(3))->method('compile')->willReturn(new File(__FILE__));

        $latex = new LatexToPdf($this->app->make('config'),$compiler, $dir);
        $latex->runs(3)->get();

    }
    
    
}
