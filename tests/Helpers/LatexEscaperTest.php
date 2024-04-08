<?php

namespace Abiturma\LaravelLatex\Tests\Helpers;


use Abiturma\LaravelLatex\Helpers\LatexEscaper;
use Abiturma\LaravelLatex\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LatexEscaperTest extends TestCase
{
    
    
    #[Test]
    public function it_escapes_simple_characters()
    {
        
        $subjects = [
            'This is a \\test' => 'This is a \\textbackslash{}test',
            'Some chars % or & or $' => 'Some chars \\% or \\& or \\$', 
            'Some { brackets }' => 'Some \\{ brackets \\}',
            'Underscores_or_hashes#' => 'Underscores\\_or\\_hashes\\#',
            'Tildes~And-Circumflex^' => 'Tildes\\textasciitilde{}And-Circumflex\\textasciicircum{}'
        ];
        
        foreach($subjects as $actual => $expected) {
            $this->assertEquals($expected,LatexEscaper::esc($actual));     
        }
        
    }
    
    #[Test]
    public function it_escapes_nasty_combinations_correctly()
    {
        $subject = 'Here a {Test\\that contains ~every#hing}'; 
        $this->assertEquals('Here a \\{Test\\textbackslash{}that contains \\textasciitilde{}every\\#hing\\}', LatexEscaper::esc($subject)); 
    }
    
    
    
}
