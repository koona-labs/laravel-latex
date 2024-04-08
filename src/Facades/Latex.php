<?php

namespace Abiturma\LaravelLatex\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class Latex
 * @package Abiturma\LaravelLatex\Facades
 */
class Latex extends Facade
{
    public static function getFacadeAccessor() : string 
    {
        return "\Abiturma\LaravelLatex\LatexToPdf"; 
    }
}
