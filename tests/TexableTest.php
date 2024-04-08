<?php

namespace Abiturma\LaravelLatex\Tests;


use Abiturma\LaravelLatex\LatexToPdf;
use Abiturma\LaravelLatex\Texable;
use PHPUnit\Framework\Attributes\Test;

class TexableTest extends TestCase
{
    #[Test]
    public function it_stores_the_view()
    {
        $texable = (new Texable());
        $this->assertInstanceOf(Texable::class, $texable->view('my_test_view'));
        $this->assertEquals('my_test_view', $texable->view);
    }

    #[Test]
    public function it_stores_the_number_of_runs()
    {
        $texable = (new Texable());
        $this->assertInstanceOf(Texable::class, $texable->runs(5));
        $this->assertEquals(5, $texable->runs);
    }

    #[Test]
    public function it_stores_data()
    {
        $texable = (new Texable());
        $data = ['some' => 'data'];
        $this->assertInstanceOf(Texable::class, $texable->with($data));
        $this->assertEquals($data, $texable->viewData);
    }


    #[Test]
    public function it_stores_assets()
    {
        $texable = (new Texable());
        $assets = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class, $texable->assets($assets));
        $this->assertEquals($assets, $texable->assets);
    }
    
    
    #[Test]
    public function it_stores_absolute_asset_paths()
    {
        $texable = (new Texable());
        $assets = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class, $texable->assets($assets, true));
        $this->assertEquals($assets, $texable->absoluteAssetPaths);
    }


    #[Test]
    public function it_adds_a_single_asset()
    {
        $texable = new Texable();
        $texable->assets = ['some', 'assets']; 
        $this->assertInstanceOf(Texable::class,$texable->addAsset('new_asset'));
        $this->assertEquals(['some','assets','new_asset'],$texable->assets);
    }

    #[Test]
    public function it_adds_multiple_assets()
    {
        $texable = new Texable();
        $texable->assets = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class,$texable->addAsset(['asset_1','asset_2']));
        $this->assertEquals(['some','assets','asset_1','asset_2'],$texable->assets);
    }
    
    
    #[Test]
    public function it_adds_the_absolute_path_of_a_single_asset()
    {
        $texable = new Texable();
        $texable->absoluteAssetPaths = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class,$texable->addAsset('new_asset', true));
        $this->assertEquals(['some','assets','new_asset'],$texable->absoluteAssetPaths);
        
    }
    
    #[Test]
    public function it_adds_the_absolute_path_of_multiple_assets()
    {
        $texable = new Texable();
        $texable->absoluteAssetPaths = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class,$texable->addAsset(['asset_1','asset_2'], true));
        $this->assertEquals(['some','assets','asset_1','asset_2'],$texable->absoluteAssetPaths);
    }
    
    #[Test]
    public function it_exludes_a_single_asset()
    {
        $texable = new Texable();
        $texable->excludedAssets = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class,$texable->excludeAsset('new_asset'));
        $this->assertEquals(['some','assets','new_asset'],$texable->excludedAssets);
    }
    
    #[Test]
    public function it_excludes_multiple_assets()
    {
        $texable = new Texable();
        $texable->excludedAssets = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class,$texable->excludeAsset(['asset_1','asset_2']));
        $this->assertEquals(['some','assets','asset_1','asset_2'],$texable->excludedAssets);
    }
    
    #[Test]
    public function it_excludes_the_absolute_paths_of_a_single_asset()
    {
        $texable = new Texable();
        $texable->excludedAbsoluteAssetPaths = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class,$texable->excludeAsset('new_asset', true));
        $this->assertEquals(['some','assets','new_asset'],$texable->excludedAbsoluteAssetPaths);
    }
    
    #[Test]
    public function it_excludes_the_absolute_path_of_multiple_assets()
    {
        $texable = new Texable();
        $texable->excludedAbsoluteAssetPaths = ['some', 'assets'];
        $this->assertInstanceOf(Texable::class,$texable->excludeAsset(['asset_1','asset_2'], true));
        $this->assertEquals(['some','assets','asset_1','asset_2'],$texable->excludedAbsoluteAssetPaths);
    }
    
    #[Test]
    public function it_allows_to_include_the_view_folder()
    {
       $texable = (new Texable())->includeViewFolder(); 
       $this->assertContains('*',$texable->assets); 
    }
    
    
    
    
    
    


}
