<?php

declare(strict_types=1);

return [
    'date.timezone' => 'UTC',
    'memory_limit' => '128M',
    'max_execution_time' => '30',
    'log_errors' => '1',
    'display_errors' => '0',
    'display_startup_errors' => '0',
    'session' => [
        'name' => 'session',
        'use_strict_mode' => '1',
        'sid_length' => '64',
        'gc_probability' => '0',
        'save_handler' => 'files',
        'save_path' => $storagePath.'/session',
    ],
];
