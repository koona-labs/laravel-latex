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

    /**
     * @var int
     */
    public $runs = 1;

    /**
     * @var string
     */
    public $view = '';

    /**
     * @var array
     * Array of asset paths, relative to view entry
     * Glob patterns are supported
     */
    public $assets = [];

    /**
     * @var array
     */
    public $absoluteAssetPaths = [];

    /**
     * @var array
     */
    public $viewData = [];


    /**
     * @var array
     * Exclusion takes precedence over asset abd  absoluteAssetPaths property
     */
    public $excludedAbsoluteAssetPaths = [];


    /**
     * @var array
     * Array of asset paths, relative to view entry that should be removed from the asset list
     * Glob patterns are supported
     * Exclusion takes precedence over asset abd  absoluteAssetPaths property
     */
    public $excludedAssets = [];


    /**
     * @param $runs
     * @return $this
     */
    public function runs($runs)
    {
        $this->runs = $runs;
        return $this;
    }

    /**
     * @param $view
     * @return $this
     */
    public function view($view)
    {
        $this->view = $view;
        return $this;
    }


    /**
     * @param array $with
     * @return $this
     */
    public function with(array $with = [])
    {
        $this->viewData = $with;
        return $this;
    }

    /**
     * @param array|string $assets
     * @param bool $absolutePath
     * @return $this
     */
    public function assets(array|string $assets = [], bool $absolutePath = false)
    {
        $assets = Arr::wrap($assets);
        if ($absolutePath) {
            $this->absoluteAssetPaths = $assets;
            return $this;
        }
        $this->assets = $assets;
        return $this;
    }


    /**
     * @param array|string $asset
     * @param bool $absolutePath
     * @return $this
     */
    public function addAsset(array|string $asset, $absolutePath = false)
    {
        $asset = Arr::wrap($asset);
        if ($absolutePath) {
            $this->absoluteAssetPaths = array_merge($this->absoluteAssetPaths, $asset);
            return $this;
        }

        $this->assets = array_merge($this->assets, $asset);
        return $this;
    }


    /**
     * @param $asset
     * @param $absolutePath
     * @return $this
     */
    public function excludeAsset($asset, $absolutePath = false)
    {
        $asset = Arr::wrap($asset);
        if ($absolutePath) {
            $this->excludedAbsoluteAssetPaths = array_merge($this->excludedAbsoluteAssetPaths, $asset);
            return $this;
        }

        $this->excludedAssets = array_merge($this->excludedAssets, $asset);
        return $this;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    protected function buildViewData()
    {
        $data = $this->viewData;

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->getDeclaringClass()->getName() !== self::class) {
                $data[$property->getName()] = $property->getValue($this);
            }
        }

        return $data;
    }

    /**
     * @return $this
     */
    public function build()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function includeViewFolder()
    {
        return $this->addAsset('*');
    }

    /**
     * @param LatexToPdf $compiler
     * @return string
     * @throws Exceptions\CompilationFailedException
     * @throws \ReflectionException
     */
    public function make(LatexToPdf $compiler)
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
