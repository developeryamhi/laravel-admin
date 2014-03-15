<?php

return array(

    //	Tables
    'table_modules' => 'modules',
    'table_module_versions' => 'module_versions',

    //	Modules Path
    'path' => 'app/modules',

    //	Module Meta File
    'meta_file' => 'module.json',

    //	Include Folder
    'includes_folder' => 'includes/',

    //  Preload Helper File
    'preload_helper' => 'helpers.php',

    //	Module Files to Include
    'include' => array(
        'bindings.php',
        'observers.php',
        'filters.php',
        'composers.php',
        'routes.php',
        'start.php'
    ),

    //	Default Handler File
    'handler_file' => 'module.php'
);