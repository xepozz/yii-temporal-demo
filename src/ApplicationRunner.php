<?php

declare(strict_types=1);

namespace App;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\RoadRunner\Environment;
use Spiral\RoadRunner\Environment\Mode;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;
use Temporal\WorkerFactory;
use Throwable;
use Yiisoft\Config\Config;
use Yiisoft\Di\Container;
use Yiisoft\ErrorHandler\ErrorHandler;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\ErrorHandler\Renderer\JsonRenderer;
use Yiisoft\Http\Method;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;
use Yiisoft\Yii\Event\ListenerConfigurationChecker;
use function dirname;
use function microtime;

final class ApplicationRunner
{
    private bool $debug = false;
    private float $startTime = 0.0;

    public function debug(bool $enable = true): void
    {
        $this->debug = $enable;
    }

    public function run(): void
    {
        $this->startTime = microtime(true);
        // Register temporary error handler to catch error while container is building.
        $tmpLogger = new Logger([new FileTarget(dirname(__DIR__) . '/runtime/logs/app.log')]);
        $errorHandler = new ErrorHandler($tmpLogger, new JsonRenderer());
        $this->registerErrorHandler($errorHandler);

        $config = new Config(
            dirname(__DIR__),
            '/config/packages', // Configs path.
        );

        $container = new Container(
            $config->get('web'),
            $config->get('providers'),
            [],
            null,
            $this->debug
        );

        // Register error handler with real container-configured dependencies.
        $this->registerErrorHandler($container->get(ErrorHandler::class), $errorHandler);

        $container = $container->get(ContainerInterface::class);

        if ($this->debug) {
            $container->get(ListenerConfigurationChecker::class)->check($config->get('events-web'));
        }

        $env = Environment::fromGlobals();

        if ($env->getMode() === Mode::MODE_TEMPORAL) {
            $this->runTemporal($container);
        } else if ($env->getMode() === Mode::MODE_HTTP) {
            $this->runRoadrunner($container);
        } else {
            // Leave support to run application as internal php server with: php -S 127.0.0.0:8080 public/index.php
            $this->runApplication($container);
        }
    }

    private function emit(RequestInterface $request, ResponseInterface $response): void
    {
        (new SapiEmitter())->emit($response, $request->getMethod() === Method::HEAD);
    }

    private function createThrowableHandler(Throwable $throwable): RequestHandlerInterface
    {
        return new class($throwable) implements RequestHandlerInterface {
            private Throwable $throwable;

            public function __construct(Throwable $throwable)
            {
                $this->throwable = $throwable;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw $this->throwable;
            }
        };
    }

    private function registerErrorHandler(ErrorHandler $registered, ErrorHandler $unregistered = null): void
    {
        if ($unregistered !== null) {
            $unregistered->unregister();
        }

        if ($this->debug) {
            $registered->debug();
        }

        $registered->register();
    }

    private function runApplication(ContainerInterface $container): void
    {
        $application = $container->get(Application::class);

        $request = $container->get(ServerRequestFactory::class)->createFromGlobals();
        $request = $request->withAttribute('applicationStartTime', $this->startTime);

        $this->runYiiApplication($application, $request, fn($req, $res) => $this->emit($req, $res), $container);
    }

    private function runRoadrunner(ContainerInterface $container): void
    {
        $application = $container->get(Application::class);

        $serverRequestFactory = $container->get(ServerRequestFactoryInterface::class);
        $streamFactoryInterface = $container->get(StreamFactoryInterface::class);
        $uploadedFileFactoryInterface = $container->get(UploadedFileFactoryInterface::class);

        $worker = Worker::create();

        $worker = new PSR7Worker(
            $worker,
            $serverRequestFactory,
            $streamFactoryInterface,
            $uploadedFileFactoryInterface
        );

        while ($request = $worker->waitRequest()) {
            $request = $request
                ->withAttribute('applicationStartTime', microtime(true));

            try {
                $this->runYiiApplication($application, $request, fn($req, $res) => $worker->respond($res), $container);
            } catch (Throwable $e) {
                $worker->getWorker()->error((string)$e);
            }
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

    private function runYiiApplication(Application $application, ServerRequestInterface $request, \Closure $emitter, ContainerInterface $container): void
    {
        try {
            $application->start();
            $response = $application->handle($request);
            $emitter($request, $response);
        } catch (Throwable $throwable) {
            $handler = $this->createThrowableHandler($throwable);
            $response = $container->get(ErrorCatcher::class)->process($request, $handler);
            $emitter($request, $response);
        } finally {
            $application->afterEmit($response ?? null);
            $application->shutdown();
        }
    }
}
