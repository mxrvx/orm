<?php

declare(strict_types=1);

namespace MXRVX\ORM\Registrators;

use DI\Container;
use MXRVX\ORM\GeneratorsFactory;

final class RegisterGenerators
{
    public function __invoke(Container $c): void
    {
        $c->set(GeneratorsFactory::class, \DI\autowire());
    }
}
