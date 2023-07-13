<?php

namespace Abiturma\LaravelLatex\Helpers;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AssetBuilder
{

    protected array $assets = [];

    protected array $absoluteAssetPaths = [];

    protected array $excludedAssets = [];

    protected array $excludedAbsoluteAssetPaths = [];

    protected ?string $viewPath = null;

    /**
     * @param array $assets
     * @return $this
     */
    public function assets(array $assets)
    {
        $this->assets = $assets;
        return $this;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function absoluteAssetPaths(array $assets)
    {
        $this->absoluteAssetPaths = $assets;
        return $this;
    }


    /**
     * @param array $assets
     * @return $this
     */
    public function excludedAbsoluteAssetsPaths(array $assets)
    {
        $this->excludedAbsoluteAssetPaths = $assets;
        return $this;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function excludedAssets(array $assets)
    {
        $this->excludedAssets = $assets;
        return $this;
    }

    /**
     * @param string $view
     * @return $this
     */
    public function view(string $view)
    {
        try {
            $this->viewPath = view()->getFinder()->find($view);
            return $this;
        }
        catch (\Exception $e) {
            return $this; 
        }
    }

    /**
     * Gets builds all absolute asset Paths
     * @return array
     */
    public function get()
    {
        if(!$this->viewPath && count($this->assets) + count($this->excludedAssets) > 0) {
            throw new \Exception("A view has to be provided to use relative Asset paths"); 
        }
        
        $assets = $this->parseRelativePaths($this->assets)
            ->merge($this->parseAbsolutePaths($this->absoluteAssetPaths))
            ->unique();
        $excludes = $this->parseRelativePaths($this->excludedAssets)
            ->merge($this->parseAbsolutePaths($this->excludedAbsoluteAssetPaths))
            ->push($this->viewPath)
            ->unique();
        
        
        return $assets->diff($excludes)->values()->toArray(); 
    }

    protected function parseRelativePaths(array $paths)
    {
        return collect($paths)->map(function ($path) {
            return dirname($this->viewPath) . Str::start($path, '/');
        })->flatMap(function ($path) {
            return File::glob($path);
        });
    }

    protected function parseAbsolutePaths(array $paths)
    {
        return collect($paths)->flatMap(function ($path) {
            return File::glob($path);
        });
    }


}
