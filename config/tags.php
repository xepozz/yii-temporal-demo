<?php

declare(strict_types=1);

use Temporal\Activity\ActivityInterface;
use Temporal\Workflow\WorkflowInterface;
use Yiisoft\Classifier\Classifier;

$classifier = new Classifier(dirname(__DIR__) . '/../src');

return [
    'tag@temporal.workflow' => [
        $classifier->withAttribute(WorkflowInterface::class)->find(),
    ],
    'tag@temporal.activity' => [
        $classifier->withAttribute(ActivityInterface::class)->find(),
    ],
];
