<?php

declare(strict_types=1);

namespace App;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Spiral\RoadRunner\Environment;
use Spiral\RoadRunner\Environment\Mode;
use Spiral\RoadRunner\Http\PSR7WorkerInterface;
use Temporal\WorkerFactory;
use Yiisoft\ErrorHandler\ErrorHandler;
use Yiisoft\Yii\Runner\ApplicationRunner as YiiApplicationRunner;
use Yiisoft\Yii\Runner\Http\HttpApplicationRunner;
use Yiisoft\Yii\Runner\RoadRunner\RoadRunnerApplicationRunner;

/**
 * `RoadRunnerApplicationRunner` runs the Yii HTTP application using RoadRunner.
 */
final class ApplicationRunner extends YiiApplicationRunner
{
    private ?ErrorHandler $temporaryErrorHandler = null;
    private ?PSR7WorkerInterface $psr7Worker = null;

    /**
     * @param string $rootPath The absolute path to the project root.
     * @param bool $debug Whether the debug mode is enabled.
     * @param string|null $environment The environment name.
     */
    public function __construct(string $rootPath, bool $debug, ?string $environment)
    {
        parent::__construct($rootPath, $debug, $environment);
        $this->bootstrapGroup = 'bootstrap-web';
        $this->eventsGroup = 'events-web';
    }

    public function run(): void
    {
        // Register temporary error handler to catch error while container is building.
        $config = $this->getConfig();
        $container = $this->getContainer($config, 'web');

        $this->runBootstrap($config, $container);
        $this->checkEvents($config, $container);

        $env = Environment::fromGlobals();

        if ($env->getMode() === Mode::MODE_TEMPORAL) {
            $this->runTemporal($container);
        } else if ($env->getMode() === Mode::MODE_HTTP) {
            $runner = new RoadRunnerApplicationRunner($this->rootPath, $this->debug, $this->environment);
            $runner->run();
        } else {
            // Leave support to run application as internal php server with: php -S 127.0.0.0:8080 public/index.php
            $runner = new HttpApplicationRunner($this->rootPath, $this->debug, $this->environment);
            $runner->run();
        }
    }

    private function runTemporal(ContainerInterface $container): void
    {
        $factory = WorkerFactory::create();

        $worker = $factory->newWorker(
            'default',
            \Temporal\Worker\WorkerOptions::new()
                ->withMaxConcurrentActivityTaskPollers(5)
                ->withMaxConcurrentWorkflowTaskPollers(5)
        );
        $workflows = $container->get('tag@temporal.workflow');
        $activities = $container->get('tag@temporal.activity');

        foreach ($workflows as $workflow) {
            $worker->registerWorkflowTypes(get_class($workflow));
        }

        foreach ($activities as $activity) {
            $worker->registerActivityImplementations($activity);
        }

        $factory->run();
    }
}
