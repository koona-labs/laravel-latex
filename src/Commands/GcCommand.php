<?php

namespace Abiturma\LaravelLatex\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * Class GcCommand
 * @package Abiturma\LaravelLatex\Commands
 */
class GcCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'latex:gc {--output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all temporary files {--output| deletes also all files in output directory}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Filesystem $filesystem
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
        $tempDirectory = config('latex.temp_directory');
        $filesystem->deleteDirectory($tempDirectory); 
        if($this->option('output') && $this->confirm("The output directory will be deleted. Are you sure?")) {
            Storage::disk(config('latex.output.disk'))->deleteDirectory(config('latex.output.path')); 
        }
        $this->info('Garbage collection done!'); 
        
    }
}
