<?php

declare(strict_types=1);

namespace MXRVX\ORM\Events;

class OnCacheUpdateEvent extends AbstractEvent
{
    public function __invoke(): void
    {
        $this->reloadCache();
    }
}
