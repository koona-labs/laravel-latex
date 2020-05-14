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
    public function it_copies_assets_if_necessary()
    {
        $dir = $this->make()->view('TemporaryDirectory.entry')->with(['variable' => 'my_test'])->includeViewFolder()->create()->getPath();
        $files = collect(File::allFiles($dir))->map->getFilename();
        $this->assertTrue($files->contains('__main.tex')); 
        $this->assertTrue($files->contains('some_image.jpg')); 
        
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
    


    protected function make()
    {
        return $this->app->make(TemporaryDirectory::class);
    }


}
