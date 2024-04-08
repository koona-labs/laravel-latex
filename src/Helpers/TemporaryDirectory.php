<?php

namespace Abiturma\LaravelLatex\Helpers;


use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\File;

/**
 * Class TemporaryDirectory
 * @package Abiturma\LaravelLatex\Helpers
 */
class TemporaryDirectory
{
    protected BladeToLatex $bladeToLatex;
    
    protected array $config;

    protected ?string $path = null;

    protected string $view = '';

    protected array $data = [];

    protected bool $includeViewFolder = false;

    protected array $assets = [];

    protected Filesystem $filesystem;


    public function __construct(BladeToLatex $bladeToLatex, Filesystem $filesystem, Repository $config)
    {
        $this->bladeToLatex = $bladeToLatex;
        $this->config = $config->get('latex');
        $this->filesystem = $filesystem;
    }

    /**
     * sets the view to compile
     */
    public function view($view): static
    {
        $this->view = $view;
        return $this;
    }


    /**
     * provides data for the view
     */
    public function with(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * returns the entry file to start compilation with
     */
    public function getEntryFile(): File
    {
        return new File($this->path . '/__main.tex');
    }

    /**
     * specifies if the ambient folder of the entry view should be copied to the temp directory
     */
    public function includeViewFolder(): static
    {
        $this->includeViewFolder = true;
        return $this;
    }


    public function withAssets(array $assets = []): static
    {
        $this->assets = $assets;
        return $this;
    }

    /**
     * creates a temporary directory
     */
    public function create(): static
    {
        $this->destroy();
        $this->createDirectory();
        $this->buildEntryFile();
        $this->handleAssets();
        return $this;
    }

    /**
     * returns the path of the temporary directory
     */
    public function getPath(): string 
    {
        return $this->path;
    }

    /**
     * destroys the temporary directory
     */
    public function destroy(): static
    {
        if (is_null($this->path)) {
            return $this;
        }
        $this->filesystem->deleteDirectory($this->path);
        $this->path = null;
        return $this;
    }


    protected function createDirectory()
    {
        $this->path = $this->config['temp_directory'] . '/' . uniqid();
        $this->createDirectoryRecursively($this->path);
    }

    protected function createDirectoryRecursively($path)
    {
        $dir = dirname($path);
        if (!$this->filesystem->exists($dir)) {
            $this->createDirectoryRecursively($dir);
        }
        $this->filesystem->makeDirectory($path);
    }

    protected function buildEntryFile()
    {
        $content = $this->bladeToLatex->toString($this->view, $this->data);
        $this->filesystem->put($this->path . '/__main.tex', $content);
    }

    protected function handleAssets()
    {
        if ($this->includeViewFolder) {
            $this->copyViewFolder();
        }

        foreach ($this->assets as $asset) {
            $this->copyAsset($asset);
        }

        $this->compileAssetsViews();
    }

    protected function copyAsset($asset)
    {
        if ($this->filesystem->isDirectory($asset)) {
            return $this->filesystem->copyDirectory($asset, $this->path);
        }

        if ($this->filesystem->exists($asset)) {
            $name = $this->filesystem->basename($asset);
            $this->filesystem->copy($asset, $this->path . '/' . $name);
        }
    }

    protected function copyViewFolder()
    {
        $this->filesystem->copyDirectory(dirname(view()->getFinder()->find($this->view)), $this->path);
    }

    protected function compileAssetsViews()
    {
        $files = $this->filesystem->allFiles($this->path);
        $files = array_filter($files, function ($file) {
            return preg_match('/\.blade\.php$/', $file->getFilename());
        });
        foreach ($files as $file) {
            $filename = preg_replace('/\.blade\.php$/', '', $file->getFilename());
            $content = $this->bladeToLatex->toStringFromPath($file->getRealPath(), $this->data);
            $this->filesystem->put($this->path . '/' . $filename, $content);
        }
    }


}
