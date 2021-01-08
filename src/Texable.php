<?php

namespace Abiturma\LaravelLatex;


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
     */
    public $assets = [];

    /**
     * @var array
     */
    public $viewData = [];

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
     * @param array $assets
     * @return $this
     */
    public function assets(array $assets = [])
    {
        $this->assets = $assets;
        return $this; 
    }

    /**
     * @param $asset
     * @return $this
     */
    public function addAsset($asset)
    {
        if(is_array($asset)) {
            $this->assets = array_merge($this->assets,$asset);
            return $this; 
        } 
        
        array_push($this->assets,$asset);     
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
     * @param LatexToPdf $compiler
     * @return string
     * @throws Exceptions\CompilationFailedException
     * @throws \ReflectionException
     */
    public function make(LatexToPdf $compiler)
    {
        $this->build(); 
        
        return $compiler
            ->runs($this->runs)
            ->assets($this->assets)
            ->view($this->view)
            ->with($this->buildViewData())
            ->get(); 
    }
    
    
    
}
