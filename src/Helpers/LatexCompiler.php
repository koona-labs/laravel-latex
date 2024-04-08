<?php

namespace Abiturma\LaravelLatex\Helpers;


use Abiturma\LaravelLatex\Exceptions\CompilationFailedException;
use Illuminate\Config\Repository;
use Illuminate\Http\File;
use Illuminate\Process\Factory;

/**
 * Class LatexCompiler
 * @package Abiturma\LaravelLatex\Helpers
 */
class LatexCompiler
{


    protected Repository $config;
    protected Factory $process;

    public function __construct(Repository $config, Factory $processFactory)
    {
        $this->config = $config;
        $this->process = $processFactory;
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
        $response = $this->process->path($dir)->run("$bin $path --interaction=nonstopmode");
        $outputPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.pdf';
        if (!$response->successful()) {
            $msg = $this->config->get('latex.debug') ? $response->output() : substr($response->output(), 200);
            throw new CompilationFailedException($msg);
        }
        return new File($outputPath);
    }


}
