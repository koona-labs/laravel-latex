<?php

namespace Abiturma\LaravelLatex\Tests;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Helpers\TemporaryDirectory;
use Abiturma\LaravelLatex\LatexToPdf;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class LatexToPdfTest extends TestCase
{
    #[Test]
    public function it_passes_view_and_data_to_the_latex_directory()
    {
        Storage::fake(); 
        
        $assets = ['path/to/asset']; 
        
        $dir = $this->createMock(TemporaryDirectory::class); 
        $dir->expects($this->once())->method('view')->with($this->equalTo('someview'))->willReturnSelf(); 
        $dir->expects($this->once())->method('with')->with($this->equalTo(['variable' => 'test']))->willReturnSelf(); 
        $dir->expects($this->once())->method('withAssets')->with($this->equalTo($assets))->willReturnSelf(); 
        $dir->method('create')->willReturnSelf(); 
        $dir->method('getEntryFile')->willReturn(new File(__FILE__));
        
        $compiler = $this->createMock(LatexCompiler::class); 
        $compiler->method('compile')->willReturn(new File(__FILE__)); 
        
        $latex = new LatexToPdf($this->app->make('config'),$compiler, $dir);  
        $latex->view('someview')->with(['variable' => 'test'])->assets($assets, true)->get();
        
    }
    
    #[Test]
    public function it_runs_the_compiler_a_spefic_amount_of_times()
    {
        Storage::fake();

        $dir = $this->createMock(TemporaryDirectory::class);
        $dir->method('view')->willReturnSelf();
        $dir->method('with')->willReturnSelf();
        $dir->method('withAssets')->willReturnSelf();
        $dir->method('create')->willReturnSelf();
        $dir->method('getEntryFile')->willReturn(new File(__FILE__));
        $compiler = $this->createMock(LatexCompiler::class);
        $compiler->expects($this->exactly(3))->method('compile')->willReturn(new File(__FILE__));

        $latex = new LatexToPdf($this->app->make('config'),$compiler, $dir);
        $latex->runs(3)->get();

    }
    
    #[Test]
    public function it_handles_relative_view_paths()
    {
        Storage::fake();

        $viewPath = config('view.paths')[0];
        
        $assets = ['/some_image.jpg'];
        $assetPaths = [ $viewPath .'/LatexToPdf/some_image.jpg']; 
        
        $dir = $this->createMock(TemporaryDirectory::class);
        $dir->expects($this->once())->method('view')->with($this->equalTo('LatexToPdf.entry'))->willReturnSelf();
        $dir->method('with')->willReturnSelf();
        $dir->expects($this->once())->method('withAssets')->with($this->equalTo($assetPaths))->willReturnSelf();
        $dir->method('create')->willReturnSelf();
        $dir->method('getEntryFile')->willReturn(new File(__FILE__));
        $compiler = $this->createMock(LatexCompiler::class);
        $compiler->method('compile')->willReturn(new File(__FILE__));
        $latex = new LatexToPdf($this->app->make('config'),$compiler, $dir);
        $latex->view('LatexToPdf.entry')->assets($assets)->get();
    }
    
    
    
}
