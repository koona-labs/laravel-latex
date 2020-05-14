<?php

namespace Abiturma\LaravelLatex\Helpers;


/**
 * Class LatexEscaper
 * @package Abiturma\LaravelLatex\Helpers
 */
class LatexEscaper
{


    /**
     * escapes an input string for LaTeX
     * 
     * @param $string
     * @return string
     */
    public static function esc($string)
    {
        
        $string = str_replace('\\','\\textbackslash',$string);
        $string = preg_replace('/([&%$#_{}])/','\\\\$1',$string);
        $string = str_replace("~","\\textasciitilde{}",$string);
        $string = str_replace("^","\\textasciicircum{}",$string); 
        $string = str_replace("\\textbackslash","\\textbackslash{}",$string);         
        
        return $string; 
        
        
    }
    
}
