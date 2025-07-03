<?php

declare(strict_types=1);

namespace MXRVX\ORM\Events;

abstract class AbstractEvent
{
    public function __construct(protected \modX &$modx, protected array $properties = []) {}

    abstract public function __invoke(): void;
}
