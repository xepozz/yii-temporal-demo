<?php

declare(strict_types=1);

use Yiisoft\Router\Route;

return [
    Route::get('/')
        ->action([\App\Controller\HomeController::class, 'index'])
        ->name('home'),

    Route::get('/simple/{name:\w+}')
        ->action([\App\Controller\WorkflowController::class, 'simpleAction'])
        ->name('temporal/simple'),

    Route::get('/complicated/{name:\w+}')
        ->action([\App\Controller\WorkflowController::class, 'complicatedAction'])
        ->name('temporal/complicated'),

    Route::get('/asynchronous/{name:\w+}')
        ->action([\App\Controller\WorkflowController::class, 'asynchronousAction'])
        ->name('temporal/asynchronous'),

    Route::get('/asynchronous-status/{id:[\w-]+}')
        ->action([\App\Controller\WorkflowController::class, 'asynchronousStatusAction'])
        ->name('temporal/asynchronous-status'),
];
