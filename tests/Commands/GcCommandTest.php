<?php

namespace Abiturma\LaravelLatex\Tests\Commands;


use Abiturma\LaravelLatex\Helpers\TemporaryDirectory;
use Abiturma\LaravelLatex\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GcCommandTest extends TestCase
{
    
    public function setUp() :void 
    {
        
        parent::setUp(); 
        config()->set('latex.temp_directory', __DIR__. '../resources/temp');  
    }

    #[Test]
    public function it_deletes_all_temporary_files()
    {
        
        $tempPath = $this->app->make(TemporaryDirectory::class)->view('GcCommand.entry')->create()->getPath();
        $this->assertTrue(file_exists($tempPath));
        $this->artisan('latex:gc')->assertExitCode(0);
        $this->assertFalse(file_exists($tempPath));
    }

    #[Test]
    public function it_prompts_the_user_before_output_files_are_deleted()
    {
        $this->artisan('latex:gc --output')
            ->expectsQuestion('The output directory will be deleted. Are you sure?','no')
            ->assertExitCode(0);
    }
    


}
