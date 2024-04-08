<?php

namespace Abiturma\LaravelLatex;


use Abiturma\LaravelLatex\Helpers\AssetBuilder;
use Illuminate\Support\Arr;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class Texable
 * @package Abiturma\LaravelLatex
 */
class Texable
{

    public int $runs = 1;

    public string $view = '';

    /**
     * Array of asset paths, relative to view entry
     * Glob patterns are supported
     */
    public array $assets = [];

    public array $absoluteAssetPaths = [];

    public array $viewData = [];


    /**
     * Exclusion takes precedence over asset abd  absoluteAssetPaths property
     */
    public array $excludedAbsoluteAssetPaths = [];


    /**
     * Array of asset paths, relative to view entry that should be removed from the asset list
     * Glob patterns are supported
     * Exclusion takes precedence over asset abd  absoluteAssetPaths property
     */
    public array $excludedAssets = [];


    public function runs(int $runs): static
    {
        $this->runs = $runs;
        return $this;
    }

    public function view($view): static
    {
        $this->view = $view;
        return $this;
    }


    public function with(array $with = []): static
    {
        $this->viewData = $with;
        return $this;
    }

    public function assets(array|string $assets = [], bool $absolutePath = false): static
    {
        $assets = Arr::wrap($assets);
        if ($absolutePath) {
            $this->absoluteAssetPaths = $assets;
            return $this;
        }
        $this->assets = $assets;
        return $this;
    }


    public function addAsset(array|string $asset, bool $absolutePath = false): static
    {
        $asset = Arr::wrap($asset);
        if ($absolutePath) {
            $this->absoluteAssetPaths = array_merge($this->absoluteAssetPaths, $asset);
            return $this;
        }

        $this->assets = array_merge($this->assets, $asset);
        return $this;
    }


    public function excludeAsset($asset, bool $absolutePath = false): static 
    {
        $asset = Arr::wrap($asset);
        if ($absolutePath) {
            $this->excludedAbsoluteAssetPaths = array_merge($this->excludedAbsoluteAssetPaths, $asset);
            return $this;
        }

        $this->excludedAssets = array_merge($this->excludedAssets, $asset);
        return $this;
    }

    protected function buildViewData(): array
    {
        $data = $this->viewData;

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->getDeclaringClass()->getName() !== self::class) {
                $data[$property->getName()] = $property->getValue($this);
            }
        }

        return $data;
    }

    public function build(): static 
    {
        return $this;
    }

    public function includeViewFolder(): static 
    {
        return $this->addAsset('*');
    }

    /**
     * @throws Exceptions\CompilationFailedException
     */
    public function make(LatexToPdf $compiler): string 
    {
        $this->build();

        $assets = (new AssetBuilder())
            ->assets($this->assets)
            ->absoluteAssetPaths($this->absoluteAssetPaths)
            ->excludedAssets($this->excludedAssets)
            ->excludedAbsoluteAssetsPaths($this->excludedAbsoluteAssetPaths)
            ->view($this->view);

        return $compiler
            ->runs($this->runs)
            ->assets($assets->get(), true)
            ->view($this->view)
            ->with($this->buildViewData())
            ->get();
    }


}
