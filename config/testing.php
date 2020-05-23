<?php

declare(strict_types=1);

return [
    'enabled' => env('TESTING_MODE', false),
    'factories' => [
        'dir' => env('TESTING_FACTORIES_DIR', 'tests/factories')
    ],
];