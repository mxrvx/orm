<?php

declare(strict_types=1);

namespace MXRVX\ORM\Events;

use Cycle\ORM\ORMInterface;
use MXRVX\ORM\App;

class OnCacheUpdateEvent extends AbstractEvent
{
    public function __invoke(): void
    {
        App::clearCache();

        //NOTE: Reload ORM for new cache schema
        /** @var \DI\Container $container */
        $container = \MXRVX\Autoloader\App::getInstance($this->modx)->getContainer();
        $container->get(ORMInterface::class);
    }
}
