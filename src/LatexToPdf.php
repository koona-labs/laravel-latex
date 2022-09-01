<?php

namespace Abiturma\LaravelLatex;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Helpers\TemporaryDirectory;
use Illuminate\Config\Repository;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class LatexToPdf
 * @package Abiturma\LaravelLatex
 */
class LatexToPdf
{

    /**
     * @var Repository
     */
    protected $config;
    /**
     * @var LatexCompiler
     */
    protected $compiler;

    protected $view = '';

    protected $data = [];
    
    protected $assets = []; 
    
    protected $absoluteAssetPaths = []; 
    
    protected $includeViewFolder = false;

    protected $runs = 1;
    /**
     * @var TemporaryDirectory
     */
    protected $temporaryDirectory;

    /**
     * LatexToPdf constructor.
     * @param Repository $config
     * @param LatexCompiler $compiler
     * @param TemporaryDirectory $temporaryDirectory
     */
    public function __construct(Repository $config, LatexCompiler $compiler, TemporaryDirectory $temporaryDirectory)
    {
        $this->config = $config;
        $this->compiler = $compiler;
        $this->temporaryDirectory = $temporaryDirectory;
    }

    /**
     * 
     * set the view to be compiled
     * 
     * @param $view
     * @return $this
     */
    public function view($view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * provide data for the view
     * 
     * @param array $data
     * @return $this
     */
    public function with(array $data = [])
    {
        $this->data = $data; 
        return $this; 
    }

    /**
     * Add assets to the compiler
     * 
     * @param array $assets
     * @return $this
     */
    public function assets(array $assets = [], bool $absolutePath = false)
    {
        if($absolutePath) {
            $this->absoluteAssetPaths = $assets; 
            return $this; 
        }
        $this->assets = $assets; 
        return $this; 
    }

    /**
     * specify if the ambient folder of the view should be copied to the compilation directory
     * 
     * @return $this
     */
    public function includeViewFolder()
    {
        $this->includeViewFolder = true;
        return $this;
    }

    /**
     * specify how many times the compiler should run
     * 
     * @param $number
     * @return $this
     */
    public function runs($number)
    {
        //the number of runs is between 1 and 10
        $this->runs = Min(10, Max(1, (int)$number));
        return $this;
    }

    /**
     * after view, data, etc is specified compile the .tex file and get the path of the resulting pdf relative to the disk's root
     * 
     * @return string
     * @throws Exceptions\CompilationFailedException
     */
    public function get()
    {
        $dir = $this->buildTemporaryDirectory();
        for ($k = 1; $k <= $this->runs; $k++) {
            $pdf = $this->compile($dir->getEntryFile());
        }
        $result = $this->getOutput($pdf);
        $dir->destroy(); 
        return $result; 
    }


    /**
     * @param Texable $texable
     * @return string
     */
    public function make(Texable $texable)
    {
        return $texable->make($this);             
    }

    /**
     * @return TemporaryDirectory
     */
    protected function buildTemporaryDirectory()
    {
        return tap($this->temporaryDirectory, function ($dir) {
            if ($this->includeViewFolder) {
                $dir->includeViewFolder();
            }
        })
            ->view($this->view)
            ->with($this->data)
            ->withAssets($this->buildAssets())
            ->create(); 
    }

    /**
     * @return array|string[]
     * @throws \Exception
     */
    protected function buildAssets()
    {
        if(!$this->assets) {
            return $this->absoluteAssetPaths; 
        }
        
        $dirname = dirname(app('view.finder')->find($this->view)); 
        
        if(!$dirname) {
            throw new \Exception('A view has to be provided in order to use relative asset paths'); 
        }
            
        $assets = array_map(function($relPath) use ($dirname) {
            return $dirname. Str::start($relPath,'/');         
        },$this->assets);  
        
        return array_merge($this->absoluteAssetPaths,$assets); 
        
    }

    /**
     * @param File $file
     * @return mixed
     */
    protected function getOutput(File $file)
    {
        $storage = Storage::disk($this->config->get('latex.output.disk')); 
        return $storage->putFile($this->config->get('latex.output.path'),$file);
    }

    /**
     * @param $file
     * @return File
     * @throws Exceptions\CompilationFailedException
     */
    protected function compile($file)
    {
        return $this->compiler->compile($file); 
    }


}
