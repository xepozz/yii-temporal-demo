<?php

declare(strict_types=1);

/**
 * @psalm-var string $_SERVER['REQUEST_URI']
 */

// PHP built-in server routing.
use Yiisoft\Yii\Runner\RoadRunner\RoadRunnerApplicationRunner;

if (PHP_SAPI === 'cli-server') {
    // Serve static files as is.
    /** @psalm-suppress MixedArgument */
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file(__DIR__ . $path)) {
        return false;
    }

    // Explicitly set for URLs with dot.
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

require_once dirname(__DIR__) . '/autoload.php';

if (getenv('YII_ENV') === 'test') {
    $c3 = dirname(__DIR__) . '/c3.php';
    if (file_exists($c3)) {
        require_once $c3;
    }
}

$runner = (new RoadRunnerApplicationRunner(dirname(__DIR__), $_ENV['YII_DEBUG'], $_ENV['YII_ENV']))
    ->withEnabledTemporal(true);
$runner->run();
