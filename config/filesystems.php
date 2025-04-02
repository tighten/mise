<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => getcwd(),
        ],
        'mise' => [
            'driver' => 'local',
            'root' => dirname(__FILE__) . '/..',
        ],
        'local-recipes' => [
            'driver' => 'local',
            'root' => $_SERVER['HOME'] . '/.mise/Recipes',
        ],
    ],
];
