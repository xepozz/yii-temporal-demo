<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'common' => [
            '/' => [
                'config/common/*.php',
            ],
            'yiisoft/log-target-file' => [
                'common.php',
            ],
            'yiisoft/router-fastroute' => [
                'common.php',
            ],
            'yiisoft/yii-event' => [
                'common.php',
            ],
            'yiisoft/aliases' => [
                'common.php',
            ],
            'yiisoft/router' => [
                'common.php',
            ],
            'yiisoft/validator' => [
                'common.php',
            ],
        ],
        'console' => [
            '/' => [
                '$common',
                'config/console/*.php',
            ],
            'yiisoft/yii-event' => [
                'console.php',
            ],
        ],
        'events' => [
            '/' => [
                'config/events.php',
            ],
            'yiisoft/yii-event' => [
                'events.php',
            ],
        ],
        'events-console' => [
            '/' => [
                '$events',
                'config/events-console.php',
            ],
            'yiisoft/yii-event' => [
                '$events',
                'events-console.php',
            ],
            'yiisoft/log' => [
                'events-console.php',
            ],
        ],
        'events-web' => [
            '/' => [
                '$events',
                'config/events-web.php',
            ],
            'yiisoft/yii-event' => [
                '$events',
                'events-web.php',
            ],
            'yiisoft/log' => [
                'events-web.php',
            ],
        ],
        'params' => [
            '/' => [
                'config/params.php',
                '?config/params-local.php',
            ],
            'yiisoft/log-target-file' => [
                'params.php',
            ],
            'yiisoft/router-fastroute' => [
                'params.php',
            ],
            'yiisoft/aliases' => [
                'params.php',
            ],
        ],
        'providers' => [
            '/' => [
                'config/providers.php',
            ],
        ],
        'providers-console' => [
            '/' => [
                '$providers',
                'config/providers-console.php',
            ],
        ],
        'providers-web' => [
            '/' => [
                '$providers',
                'config/providers-web.php',
            ],
        ],
        'routes' => [
            '/' => [
                'config/routes.php',
            ],
        ],
        'web' => [
            '/' => [
                '$common',
                'config/web/*.php',
            ],
            'yiisoft/error-handler' => [
                'web.php',
            ],
            'yiisoft/router-fastroute' => [
                'web.php',
            ],
            'yiisoft/yii-event' => [
                'web.php',
            ],
            'yiisoft/middleware-dispatcher' => [
                'web.php',
            ],
        ],
    ],
];
