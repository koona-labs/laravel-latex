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
     */
    public static function esc($string): string
    {

        $string = str_replace('\\', '\\textbackslash', $string);
        $string = preg_replace('/([&%$#_{}])/', '\\\\$1', $string);
        $string = str_replace("~", "\\textasciitilde{}", $string);
        $string = str_replace("^", "\\textasciicircum{}", $string);
        $string = str_replace("\\textbackslash", "\\textbackslash{}", $string);
        $string = preg_replace('/[\x{202a}-\x{202f}]/u', '', $string);

        return $string;


    }

}
