<?php

namespace Abiturma\LaravelLatex;


use Abiturma\LaravelLatex\Commands\GcCommand;
use Abiturma\LaravelLatex\Commands\TestCommand;
use Abiturma\LaravelLatex\Commands\TexableMakeCommand;
use Abiturma\LaravelLatex\Helpers\LatexEscaper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Class LatexServiceProvider
 * @package Abiturma\LaravelLatex\Providers
 */
class LatexServiceProvider extends ServiceProvider
{


    public function boot()
    {

        $this->publishes([
            __DIR__ . '/config/latex.php' => config_path('latex.php'),
        ]);

        Blade::directive('latex', function ($exp) {
            $path = LatexEscaper::class;
            return "<?php echo $path::esc($exp) ?>";
        });


        if ($this->app->runningInConsole()) {
            $this->commands([
                TestCommand::class,
                GcCommand::class,
                TexableMakeCommand::class
            ]);
        }
        
        
        
    }

    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/config/latex.php', 'latex'
        );
    }


}
