<?php

namespace Abiturma\LaravelLatex\Tests\Helpers;

use Abiturma\LaravelLatex\Helpers\AssetBuilder;
use Abiturma\LaravelLatex\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AssetBuilderTest extends TestCase
{
    
    public function setUp() :void 
    {
        
        parent::setUp(); 
            
        $this->builder = (new AssetBuilder())->view('AssetBuilder.entry');  
            
    }
    
    /** @test */
    public function it_parses_simple_relative_assets()
    {
        $allAssets = $this->builder->assets(['some_image.jpg'])->get();
        $this->assertEquals($this->paths('AssetBuilder/some_image.jpg'),$allAssets); 
    }
    
    /** @test */
    public function it_parses_relative_assets_using_glob_patterns()
    {
        $allAssets = $this->builder->assets(['*.jpg'])->get();
        $this->assertEquals($this->paths(['AssetBuilder/some_image.jpg','AssetBuilder/some_other_image.jpg']),$allAssets);
    }
    
    /** @test */
    public function the_entry_file_is_never_an_asset()
    {
        $allAssets = $this->builder->assets(['*'])->get(); 
        $this->assertNotContains($this->paths(['AssetBuilder/entry.blade.php']),$allAssets);
    }
    
    /** @test */
    public function it_parses_blacklisting()
    {
        $allAssets = $this->builder->assets(['*'])->excludedAssets(['*other*.*'])->get();
        $this->assertEquals($this->paths(['AssetBuilder/some_image.jpg','AssetBuilder/some_text.txt']),$allAssets); 
    }
    
    /** @test */
    public function duplicates_are_removed()
    {
        $allAssets = $this->builder->assets(['*.txt','*.txt'])->get();
        $this->assertCount(1,$allAssets); 
    }
    
    
    

    protected function paths($paths)
    {
        $paths = collect(Arr::wrap($paths)); 
        return $paths->map(function($path) {
            return config('view.paths')[0] . Str::start($path,'/');
        })->toArray();  
    }
    
    
}
