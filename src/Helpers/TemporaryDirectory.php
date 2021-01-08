<?php

namespace Abiturma\LaravelLatex\Helpers;


use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\File;

/**
 * Class TemporaryDirectory
 * @package Abiturma\LaravelLatex\Helpers
 */
class TemporaryDirectory
{
    /**
     * @var BladeToLatex
     */
    protected $bladeToLatex;
    /**
     * @var Repository
     */
    protected $config;

    protected $path = null;

    protected $view = '';

    protected $data = [];

    protected $includeViewFolder = false;
    
    protected $assets = []; 
    /**
     * @var Filesystem
     */
    protected $filesystem;


    /**
     * TemporaryDirectory constructor.
     * @param BladeToLatex $bladeToLatex
     * @param Filesystem $filesystem
     * @param Repository $config
     */
    public function __construct(BladeToLatex $bladeToLatex, Filesystem $filesystem, Repository $config)
    {
        $this->bladeToLatex = $bladeToLatex;
        $this->config = $config->get('latex');
        $this->filesystem = $filesystem;
    }

    /**
     * sets the view to compile
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
     * provides data for the view
     * 
     * @param $data
     * @return $this
     */
    public function with($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * returns the entry file to start compilation with
     * 
     * @return File
     */
    public function getEntryFile()
    {
        return new File($this->path . '/__main.tex');
    }

    /**
     * specifies if the ambient folder of the entry view should be copied to the temp directory
     * 
     * @return $this
     */
    public function includeViewFolder()
    {
        $this->includeViewFolder = true;
        return $this;
    }


    /**
     * @param array $assets
     * @return $this
     */
    public function withAssets(array $assets = [])
    {
        $this->assets = $assets;
        return $this;
    }

    /**
     * creates a temporary directory
     * 
     * @return $this
     */
    public function create()
    {
        $this->destroy();
        $this->createDirectory();
        $this->buildEntryFile();
        $this->handleAssets();
        return $this;
    }

    /**
     * returns the path of the temporary directory
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * destroys the temporary directory
     * 
     * @return $this
     */
    public function destroy()
    {
        if (is_null($this->path)) {
            return $this;
        }
        $this->filesystem->deleteDirectory($this->path);
        $this->path = null;
        return $this;
    }


    protected function createDirectory()
    {
        $this->path = $this->config['temp_directory'] . '/' . uniqid();
        $segments = explode('/', $this->path);
        $dir = '';
        foreach ($segments as $newSegment) {
            $dir .= "/$newSegment";
            if (!$this->filesystem->exists($dir)) {
                $this->filesystem->makeDirectory($dir);
            }
        }
    }

    protected function buildEntryFile()
    {
        $content = $this->bladeToLatex->toString($this->view, $this->data);
        $this->filesystem->put($this->path . '/__main.tex', $content);
    }

    protected function handleAssets()
    {
        if ($this->includeViewFolder) {
            $this->copyViewFolder();
        }
        
        foreach($this->assets as $asset) {
            $this->copyAsset($asset); 
        }
        
        $this->compileAssetsViews();
    }

    protected function copyAsset($asset)
    {
        if($this->filesystem->isDirectory($asset)) {
            return $this->filesystem->copyDirectory($asset,$this->path); 
        }
        
        if($this->filesystem->exists($asset)) {
           $name = $this->filesystem->basename($asset); 
           $this->filesystem->copy($asset, $this->path . '/' . $name);    
        }
    }

    protected function copyViewFolder()
    {
        $this->filesystem->copyDirectory(dirname(app('view.finder')->find($this->view)), $this->path);
    }

    protected function compileAssetsViews()
    {
        $files = $this->filesystem->allFiles($this->path);
        $files = array_filter($files, function ($file) {
            return preg_match('/\.blade\.php$/', $file->getFilename());
        });
        foreach ($files as $file) {
            $filename = preg_replace('/\.blade\.php$/', '', $file->getFilename());
            $content = $this->bladeToLatex->toStringFromPath($file->getRealPath(), $this->data); 
            $this->filesystem->put($this->path . '/' . $filename, $content);
        }
    }


}
