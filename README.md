# Laravel LaTeX

This package compiles Blade views containing LaTeX source code to a pdf file

## Requirements

You need an installation of a latex compiler (e.g. texlive) on your server. This package is designed for the use with Laravel 10 or newer. Php 8.1 ist required starting from version 2.0.0.

## Older Laravel versions
For laravel 8 and 9 use version ^1.3 of this package.


## Installation and Setup

Install using composer:

`composer require abiturma/laravel-latex`

The Service Provider and the Facade will be installed automatically via laravel package auto-discover. 

To publish the config file to config/latex.php run:

`php artisan vendor:publish --provider="Abiturma\LaravelLatex\LatexServiceProvider`

The default configuration looks like so 

```
return [
    'temp_directory' => storage_path().'/latex/temp',
    'output' => [
        'disk' => 'local',
        'path' => 'latex/output' 
    ],
    'debug' => false, 
    'pdflatex' => env('PATH_TO_PDFLATEX','pdflatex')
];
```

With the key `pdflatex` you have to set the absolute path to the pdflatex binary on your server. 


## Usage

You can compile a blade view either by calling the Facade `Latex` or by injecting the class `Abiturma\LaravelLatex\LatexToPdf`. Within the documentation we will 


### Basic usage

The class has a fluent interface, which is terminated by a call of `->get()` to start compilation. 

You have to pass a blade view together with the data of the view. A standard use case looks like so 

```
use Latex

...

$pdf = Latex::view('your.favourite.latex.view')
    ->with(['key' => 'value'])
    ->get()

```
The return value will be a path to compiled pdf. The path is relative to the root directory of the storage disk given in your config file.  

```
...

Storage::disk(config('latex.output.disk'))
->exists($pdf) //true 

```


### Escaping input

Since LaTeX has its own syntax it is not advised to use the standard blade syntax `{{ $variable }}` or `{!! $variable !!}`. Instead you can use `@latex($variable)` in your blade templates instead, which handles the suitable escaping of reserved LaTeX characters. 


### Multiple runs

To enable LaTeX to handle cross references it is sometimes necessary to compile a document multiple times. You can specify the number of runs like so:  
```
    Latex::view('myView')->runs($numberOfRuns)->get()
```
The number of runs will be automatically truncated to an integer between 1 and 10. 

### Asset Files

Once you use graphics or custom sty-files in your template your compiler needs more than one file. To handle this situation you can call 
```
->assets([
    './relative/path/to/one/asset',
    './relative/path/to/another/asset
])
``` 
on the compiler. This will copy all specified files to the compilation directory. The path is relative to the directory of the view. If absolute paths are needed, set the `absolutePath` flag to `true`: 
```
->assets([
    '/absolute/path/to/asset',
    '/absolute/path/to/another/asset
],
true)
``` 

Very often you have the case, that you want to include all files in the directory of the view as assets. In this case you can simply call `->includeViewFolder()` on the compiler. This will copy all files of the ambient directory of the view in the compilation directory.

 After copying the asset files to the compilation directory all blade views are compiled to the according tex files using the variables passed by `->with($data)`. 

#### Example 

Given the following tree structure 
```
views
│
└── myDocument
     │   main.blade.php
     │   package.sty.blade.php 
     │   picture.jpg
    ...
```
the command
```
Latex::view('myDocument.main)
    ->with($data)
    ->includeViewFolder()
    ->get()
```
will produce a (temporary) compilation directory of the form 
```
temp
│
└── #someHash
     │   main.tex
     │   package.sty 
     │   picture.jpg
    ...
```
where `package.sty` is the compiled version of `package.sty.blade.php` using `$data`. 


### Using Texables
Instead of calling the Compiler with all the necessary parameters you can also pass a `Texable` class which act similarly like [Laravel Mailables](https://laravel.com/docs/master/mail#generating-mailables). 

For creating a mailable run the command `latex:make MyTexable` to create a stub in your view directory. 

To compile a Texable run `make::latex(new MyTexable())`. Texables have basically the same methods as the compiler class. Before the latex compiler run, the `->build()` method is run on the Texable. Moreover, all public variables on the Texable are available as data for the given view. 

#### Example 

Say the class `MyTexable` is given by

```
use Abiturma\LaravelLatex\Texable

class MyTexable extends Texable
{
    public $message;     

    __construct($message) {
        $this->message = $message;        
    }

    public function build() {
        return $this->view('someview')
    }
    
}

```
then running `Latex::make(MyTexable("someString")` is equivalent to 
```
Latex::view('someview')->with(["message" => "someString"])->get()
```

#### Assets
Besides the `->asset()` method, Texables have more options to handle assets. It is possible to add assets using `->addAsset()`. These methods not only understand absolute and relative paths via the `absolutePath` flag as second argument, they also parse file patterns as glob. So it also possible to add assets like this `->addAsset('*.jpg')`. To exclude file patterns use the method `->excludeAssets($assets)` 


### Debugging

If the compilation fails a `CompilationFailedException` is raised. By default the exception message contains the truncated compilation log. If `debug` is set to `true` the exception message contains the full compilation log. 


### Commands 

#### Testing

The command `latex:test` runs the compiler on two test files. If the compilation is successful the output paths of the files are printed. 

#### Garbage Collection 

The command `latex:gc` removes all temporary directories and files which were not garbage collected during the compilation process. This might happen, if the process failes before the automatic clean-up process of the compiler kicked in. 

If you add the flag `--output` also the given output directory is cleaned. This makes sense if you are used to move the output files after generation to someplace else anyways. 

## Upgrade/Breaking changes

As of version 1.2.0, the `->assets()` method of the LatexToPdf and Texable class now expect paths relative to the view folder instead of absolute paths. For downward compatibility use the `absolutePath` flag as second argument, e.g. `->assets($someLegacyPath, true)`.   


## Acknowledgements

This project is inspired by [latexcompiler](https://github.com/fvhockney/latexcompiler) by [Vernon Hockney](https://github.com/fvhockney).


