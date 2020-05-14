<?php

namespace Abiturma\LaravelLatex\Helpers;


use Illuminate\View\Factory;

/**
 * Class BladeToLatex
 * @package Abiturma\LaravelLatex\Helpers
 */
class BladeToLatex
{
    /**
     * @var Factory
     */
    protected $view;

    /**
     * BladeToLatex constructor.
     * @param Factory $view
     */
    public function __construct(Factory $view)
    {
        $this->view = $view;
    }

    /**
     * Compiles a blade view to a string
     * 
     * @param $view
     * @param $data
     * @return string
     */
    public function toString($view,$data)
    {
        return $this->view->make($view,$data)->render();  
    }

    /**
     * Compiles a blade view given by an absolute path to a string
     * 
     * @param $path
     * @param $data
     * @return string
     */
    public function toStringFromPath($path,$data)
    {
        return $this->view->file($path,$data)->render(); 
    }
    
}
