<?php


use Abiturma\LaravelLatex\Tests\TestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;

class TexableMakeCommandTest extends TestCase
{

    public function setUp() :void 
    {
        
        parent::setUp(); 
            
        File::deleteDirectory(app_path('Latex'));  
            
    }
    
    #[Test]
    public function it_generates_a_texable_file()
    {
        $this->assertFalse(File::exists(app_path('Latex/SomeTexableClass.php'))); 
        $this->artisan('latex:make SomeTexableClass')->assertExitCode(0);    
        $this->assertTrue(File::exists(app_path('Latex/SomeTexableClass.php'))); 
    }
    
    
    
}
