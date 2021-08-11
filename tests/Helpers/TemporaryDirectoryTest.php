<?php

namespace Abiturma\LaravelLatex\Tests\Helpers;


use Abiturma\LaravelLatex\Helpers\TemporaryDirectory;
use Abiturma\LaravelLatex\Tests\TestCase;
use Illuminate\Support\Facades\File;

class TemporaryDirectoryTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }


    /** @test */
    public function it_creates_an_entry_file()
    {
        $dir = $this->make()->view('TemporaryDirectory.entry')->with(['variable' => 'my_test'])->create();
        $this->assertInstanceOf('Illuminate\Http\File', $dir->getEntryFile());
    }
    
    /** @test */
    public function it_creates_a_nested_temporary_directory()
    {
        $temp = config('latex.temp_directory'); 
        $this->app->config->set('latex.temp_directory', $temp . '/' . uniqid() .'/'. uniqid() ); 
        $dir = $this->make()->view('TemporaryDirectory.entry')->with(['variable' => 'my_test'])->create(); 
        $this->assertInstanceOf('Illuminate\Http\File',$dir->getEntryFile()); 
    }
    

    /** @test */
    public function it_copies_the_viewfolder_if_advised_to()
    {
        $dir = $this->make()->view('TemporaryDirectory.entry')->with(['variable' => 'my_test'])->includeViewFolder()->create()->getPath();
        $files = collect(File::allFiles($dir))->map->getFilename();
        $this->assertTrue($files->contains('__main.tex')); 
        $this->assertTrue($files->contains('some_image.jpg')); 
        
    }
    
    /** @test */
    public function it_copies_specified_asset_files()
    {
        $dir = $this->make()->view('TemporaryDirectory.entry')
            ->with(['variable' => 'my_test'])
            ->withAssets([__DIR__ .'/../resources/views/TemporaryDirectory/asset.sty.blade.php'])
            ->create()
            ->getPath();
        $files = collect(File::allFiles($dir))->map->getFilename();
        $this->assertTrue($files->contains('asset.sty'));
    }
    
    /** @test */
    public function it_copies_specified_asset_directories()
    {
        $dir = $this->make()->view('TemporaryDirectory.entry')
            ->with(['variable' => 'my_test'])
            ->withAssets([__DIR__ .'/../resources/views/TemporaryDirectory'])
            ->create()
            ->getPath();
        $files = collect(File::allFiles($dir))->map->getFilename();
        $this->assertTrue($files->contains('asset.sty'));
    }
    
    
    /** @test */
    public function it_compiles_all_blade_files_in_the_directory_if_necessary()
    {
        $dir = $this->make()->view('TemporaryDirectory.entry')->with(['variable' => 'myAssetTest'])->includeViewFolder()->create()->getPath();
        $asset = collect(File::allFiles($dir))->first(function($file) use ($dir) {
            return $file->getFilename() === 'asset.sty';
        });
        $this->assertNotNull($asset);
        $this->assertStringContainsString('myAssetTest',File::get($asset));
    }
    
    /** @test */
    public function it_compiles_additional_files_too()
    {
        $dir = $this->make()->view('TemporaryDirectory.entry')
            ->with(['variable' => 'anotherAssetTest'])
            ->withAssets([__DIR__ .'/../resources/views/TemporaryDirectory/asset.sty.blade.php'])
            ->create()
            ->getPath();
        $asset = collect(File::allFiles($dir))->first(function($file) use ($dir) {
            return $file->getFilename() === 'asset.sty';
        });
        $this->assertNotNull($asset);
        $this->assertStringContainsString('anotherAssetTest',File::get($asset));
    }
    
    


    protected function make()
    {
        return $this->app->make(TemporaryDirectory::class);
    }


}
