<?php

return [
    
    'temp_directory' => storage_path().'/latex/temp',
    'output' => [
        'disk' => 'local',
        'path' => 'latex/output' 
    ],
    'view_directory' => 'latex', 
    'debug' => false, 
    'pdflatex' => env('PATH_TO_PDFLATEX','pdflatex')
];

