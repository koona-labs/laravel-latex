<?php

namespace Abiturma\LaravelLatex\Helpers;


use Illuminate\View\Factory;

/**
 * Class BladeToLatex
 * @package Abiturma\LaravelLatex\Helpers
 */
class BladeToLatex
{
    protected Factory $view;

    public function __construct(Factory $view)
    {
        $this->view = $view;
    }

    /**
     * Compiles a blade view to a string
     */
    public function toString($view, $data): string
    {
        return $this->view->make($view, $data)->render();
    }

    /**
     * Compiles a blade view given by an absolute path to a string
     */
    public function toStringFromPath($path, $data): string
    {
        return $this->view->file($path, $data)->render();
    }

}
