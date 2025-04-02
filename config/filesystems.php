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
    ],
];
