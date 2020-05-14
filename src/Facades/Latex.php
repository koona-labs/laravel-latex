<?php

namespace Abiturma\LaravelLatex\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class Latex
 * @package Abiturma\LaravelLatex\Facades
 */
class Latex extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return "\Abiturma\LaravelLatex\LatexToPdf"; 
    }
}
