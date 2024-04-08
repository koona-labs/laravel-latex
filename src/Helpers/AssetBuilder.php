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

    public function assets(array $assets): static
    {
        $this->assets = $assets;
        return $this;
    }

    public function absoluteAssetPaths(array $assets): static
    {
        $this->absoluteAssetPaths = $assets;
        return $this;
    }


    public function excludedAbsoluteAssetsPaths(array $assets): static
    {
        $this->excludedAbsoluteAssetPaths = $assets;
        return $this;
    }

    public function excludedAssets(array $assets): static
    {
        $this->excludedAssets = $assets;
        return $this;
    }

    public function view(string $view): static
    {
        try {
            $this->viewPath = view()->getFinder()->find($view);
            return $this;
        } catch (\Exception $e) {
            return $this;
        }
    }

    /**
     * Gets builds all absolute asset Paths
     */
    public function get(): array
    {
        if (!$this->viewPath && count($this->assets) + count($this->excludedAssets) > 0) {
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
