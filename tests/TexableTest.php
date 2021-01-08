<?php

namespace Abiturma\LaravelLatex\Tests;


use Abiturma\LaravelLatex\Texable;

class TexableTest extends TestCase
{
    /** @test */
    public function it_stores_the_view()
    {
        $texable = (new Texable());
        $this->assertInstanceOf(Texable::class,$texable->view('my_test_view'));
        $this->assertEquals('my_test_view',$texable->view); 
    }
    
    /** @test */
    public function it_stores_the_number_of_runs()
    {
        $texable = (new Texable());
        $this->assertInstanceOf(Texable::class,$texable->runs(5));
        $this->assertEquals(5,$texable->runs);
    }

    /** @test */
    public function it_stores_data()
    {
        $texable = (new Texable());
        $data = ['some' => 'data']; 
        $this->assertInstanceOf(Texable::class,$texable->with($data));
        $this->assertEquals($data,$texable->viewData);
    }
    
    /** @test */
    public function it_stores_assets()
    {
        $texable = (new Texable());
        $assets = ['some','assets'];
        $this->assertInstanceOf(Texable::class,$texable->assets($assets));
        $this->assertEquals($assets,$texable->assets);
    }
    
    /** @test */
    public function it_adds_a_single_asset()
    {
        $texable = (new Texable());
        $texable->assets(['some','assets']);
        
        $newAsset = 'some/path';
        $this->assertInstanceOf(Texable::class,$texable->addAsset($newAsset));
        $this->assertEquals(['some','assets','some/path'],$texable->assets); 
    }
    
    /** @test */
    public function it_adds_multiple_assets()
    {
        $texable = (new Texable());
        $texable->assets(['some','assets']);

        $newAssets = ['some/path1','some/path2'];
        $this->assertInstanceOf(Texable::class,$texable->addAsset($newAssets));
        $this->assertEquals(['some','assets','some/path1','some/path2'],$texable->assets);
    }
    
    
    
    
        
    
}
