<?php
declare(strict_types=1);

use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;

return [
    // Workflows

    \App\Temporal\Workflow\FastWorkflow::class => [
        'class' => \App\Temporal\Workflow\FastWorkflow::class,
        'tags' => ['temporal.workflow']
    ],
    \App\Temporal\Workflow\LongWorkflow::class => [
        'class' => \App\Temporal\Workflow\LongWorkflow::class,
        'tags' => ['temporal.workflow']
    ],

    // Activities

    \App\Temporal\Activity\CommonActivity::class => [
        'class' => \App\Temporal\Activity\CommonActivity::class,
        'tags' => ['temporal.activity']
    ],

    // Temporal

    \Temporal\Client\WorkflowClientInterface::class => WorkflowClient::create(ServiceClient::create('localhost:7233')),

];
