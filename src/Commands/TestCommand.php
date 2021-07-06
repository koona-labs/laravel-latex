<?php

namespace Abiturma\LaravelLatex\Commands;

use Abiturma\LaravelLatex\Facades\Latex;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Factory;

/**
 * Class TestCommand
 * @package Abiturma\LaravelLatex\Commands
 */
class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'latex:test';


    /**
     * @var Factory
     */
    protected $view;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Test-Pdf';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Factory $view)
    {
        parent::__construct();
        $this->view = $view;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //both adaption are necessary since the view finder is instantiated two times
        $location = __DIR__.'/../resources/views';
        config()->set('view.paths',[$location]);
        $this->view->addLocation($location);
        
        $disk = Storage::disk(config('latex.output.disk')); 
        $path1 = Latex::view('test.withoutAssets')->with(['variable' => 'test'])->get();
        if($disk->exists($path1)) {
            $this->info('Simple pdf file created at '. $disk->path($path1)); 
        }
        else {
            $this->error('Could not generate pdf file from withoutAssets.blade.php'); 
        }

        $path2 = Latex::view('test.withAssets')->with(['variable' => 'test'])->includeViewFolder()->get();
        if($disk->exists($path2)) {
            $this->info('A pdf file using assets was created at '. $disk->path($path2));
        }
        else {
            $this->error('Could not generate pdf file from withAssets.blade.php');
        }
        
    }
}
