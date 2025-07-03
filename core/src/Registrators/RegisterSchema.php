<?php

declare(strict_types=1);

namespace MXRVX\ORM\Registrators;

use Cycle\ORM\SchemaInterface;
use DI\Container;
use MXRVX\ORM\SchemaFactory;

final class RegisterSchema
{
    public function __invoke(Container $c): void
    {
        $c->set(SchemaFactory::class, \DI\autowire());
        $c->set(SchemaInterface::class, static function (Container $c) {
            /** @var SchemaFactory $factory */
            $factory = $c->get(SchemaFactory::class);
            return $factory->get();
        });
    }
}
