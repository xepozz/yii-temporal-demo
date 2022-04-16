<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'params' => [
            'yiisoft/aliases' => [
                'config/params.php',
            ],
            'yiisoft/log-target-file' => [
                'config/params.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/params.php',
            ],
            '/' => [
                'params.php',
                '?params-local.php',
            ],
        ],
        'common' => [
            'yiisoft/aliases' => [
                'config/common.php',
            ],
            'yiisoft/log-target-file' => [
                'config/common.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/common.php',
            ],
            'yiisoft/yii-event' => [
                'config/common.php',
            ],
            'yiisoft/router' => [
                'config/common.php',
            ],
            '/' => [
                'common/*.php',
            ],
        ],
        'web' => [
            'yiisoft/error-handler' => [
                'config/web.php',
            ],
            'yiisoft/router-fastroute' => [
                'config/web.php',
            ],
            'yiisoft/yii-event' => [
                'config/web.php',
            ],
            'yiisoft/middleware-dispatcher' => [
                'config/web.php',
            ],
            '/' => [
                '$common',
                'web/*.php',
            ],
        ],
        'console' => [
            'yiisoft/yii-event' => [
                'config/console.php',
            ],
            '/' => [
                '$common',
                'console/*.php',
            ],
        ],
        'events' => [
            'yiisoft/yii-event' => [
                'config/events.php',
            ],
            '/' => [
                'events.php',
            ],
        ],
        'events-web' => [
            'yiisoft/yii-event' => [
                '$events',
                'config/events-web.php',
            ],
            'yiisoft/log' => [
                'config/events-web.php',
            ],
            '/' => [
                '$events',
                'events-web.php',
            ],
        ],
        'events-console' => [
            'yiisoft/yii-event' => [
                '$events',
                'config/events-console.php',
            ],
            'yiisoft/log' => [
                'config/events-console.php',
            ],
            '/' => [
                '$events',
                'events-console.php',
            ],
        ],
        'providers' => [
            '/' => [
                'providers.php',
            ],
        ],
        'providers-web' => [
            '/' => [
                '$providers',
                'providers-web.php',
            ],
        ],
        'providers-console' => [
            '/' => [
                '$providers',
                'providers-console.php',
            ],
        ],
        'delegates' => [
            '/' => [
                'delegates.php',
            ],
        ],
        'delegates-web' => [
            '/' => [
                '$delegates',
                'delegates-web.php',
            ],
        ],
        'delegates-console' => [
            '/' => [
                '$delegates',
                'delegates-console.php',
            ],
        ],
        'routes' => [
            '/' => [
                'routes.php',
            ],
        ],
        'bootstrap' => [
            '/' => [
                'bootstrap.php',
            ],
        ],
        'bootstrap-web' => [
            '/' => [
                '$bootstrap',
                'bootstrap-web.php',
            ],
        ],
        'bootstrap-console' => [
            '/' => [
                '$bootstrap',
                'bootstrap-console.php',
            ],
        ],
    ],
];
