<?php

namespace Abiturma\LaravelLatex\Helpers;


use Abiturma\LaravelLatex\Exceptions\CompilationFailedException;
use Illuminate\Config\Repository;
use Illuminate\Http\File;
use TitasGailius\Terminal\Terminal;

/**
 * Class LatexCompiler
 * @package Abiturma\LaravelLatex\Helpers
 */
class LatexCompiler
{


    /**
     * @var Repository
     */
    protected $config;

    /**
     * LatexCompiler constructor.
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * takes a latex file wrapped in Illuminate\Http\File and compiles it within the ambient directory
     * 
     * 
     * @param File $file
     * @return File
     * @throws CompilationFailedException
     */
    public function compile(File $file)
    {
        return $this->compileFromPath($file->path());
    }

    /**
     *  takes an absolute path of a latex file and compiles it within the ambient directory
     * 
     * @param $path
     * @return File
     * @throws CompilationFailedException
     */
    public function compileFromPath($path)
    {
        $pathInfo = pathinfo($path);
        $dir = $pathInfo['dirname'];
        $bin = $this->config->get('latex.pdflatex');
        $response = Terminal::in($dir)->run("$bin $path --interaction=nonstopmode");
        $outputPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.pdf';
        if (!$response->successful()) {
            $msg = $this->config->get('latex.debug') ? $response->output() : substr($response->output(),200); 
            throw new CompilationFailedException($msg);
        }
        return new File($outputPath);
    }


}
