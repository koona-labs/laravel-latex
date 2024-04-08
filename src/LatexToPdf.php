<?php

namespace Abiturma\LaravelLatex;


use Abiturma\LaravelLatex\Helpers\LatexCompiler;
use Abiturma\LaravelLatex\Helpers\TemporaryDirectory;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class LatexToPdf
 * @package Abiturma\LaravelLatex
 */
class LatexToPdf
{

    protected Repository $config;
    protected LatexCompiler $compiler;

    protected string $view = '';

    protected array $data = [];

    protected array $assets = [];

    protected array $absoluteAssetPaths = [];

    protected bool $includeViewFolder = false;

    protected int $runs = 1;
    protected TemporaryDirectory $temporaryDirectory;

    public function __construct(Repository $config, LatexCompiler $compiler, TemporaryDirectory $temporaryDirectory)
    {
        $this->config = $config;
        $this->compiler = $compiler;
        $this->temporaryDirectory = $temporaryDirectory;
    }

    /**
     * set the view to be compiled
     */
    public function view($view): static
    {
        $this->view = $view;
        return $this;
    }

    /**
     * provide data for the view
     */
    public function with(array $data = []): static
    {
        $this->data = $data;
        return $this;
    }

    /*
     * Add assets to the compiler
     */
    public function assets(array $assets = [], bool $absolutePath = false): static
    {
        if ($absolutePath) {
            $this->absoluteAssetPaths = $assets;
            return $this;
        }
        $this->assets = $assets;
        return $this;
    }

    /**
     * specify if the ambient folder of the view should be copied to the compilation directory
     */
    public function includeViewFolder(): static
    {
        $this->includeViewFolder = true;
        return $this;
    }

    /**
     * specify how many times the compiler should run
     */
    public function runs(int $number): static
    {
        //the number of runs is between 1 and 10
        $this->runs = Min(10, Max(1, (int)$number));
        return $this;
    }

    /**
     * after view, data, etc is specified compile the .tex file and get the path of the resulting pdf relative to the disk's root
     *
     * @throws Exceptions\CompilationFailedException
     */
    public function get(): string
    {
        $dir = $this->buildTemporaryDirectory();
        for ($k = 1; $k <= $this->runs; $k++) {
            $pdf = $this->compile($dir->getEntryFile());
        }
        $result = $this->getOutput($pdf);
        $dir->destroy();
        return $result;
    }


    public function make(Texable $texable): string
    {
        return $texable->make($this);
    }

    /**
     * @throws Exception
     */
    protected function buildTemporaryDirectory(): TemporaryDirectory
    {
        return tap($this->temporaryDirectory, function ($dir) {
            if ($this->includeViewFolder) {
                $dir->includeViewFolder();
            }
        })
            ->view($this->view)
            ->with($this->data)
            ->withAssets($this->buildAssets())
            ->create();
    }

    /**
     * @throws \Exception
     */
    protected function buildAssets(): array|string
    {
        if (!$this->assets) {
            return $this->absoluteAssetPaths;
        }

        $dirname = dirname(view()->getFinder()->find($this->view));

        if (!$dirname) {
            throw new \Exception('A view has to be provided in order to use relative asset paths');
        }

        $assets = array_map(function ($relPath) use ($dirname) {
            return $dirname . Str::start($relPath, '/');
        }, $this->assets);

        return array_merge($this->absoluteAssetPaths, $assets);

    }

    protected function getOutput(File $file): string|bool
    {
        $storage = Storage::disk($this->config->get('latex.output.disk'));
        return $storage->putFile($this->config->get('latex.output.path'), $file);
    }

    /**
     * @throws Exceptions\CompilationFailedException
     */
    protected function compile($file): File
    {
        return $this->compiler->compile($file);
    }


}
