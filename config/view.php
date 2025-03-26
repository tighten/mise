<?php

return [
    'paths' => [
        resource_path('views'),
    ],
    'compiled' => \Phar::running()
        ? sys_get_temp_dir()
        : env('VIEW_COMPILED_PATH', realpath(storage_path('framework/views'))),
];
