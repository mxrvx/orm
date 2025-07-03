<?php

declare(strict_types=1);

namespace MXRVX\ORM\Registrators;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Collection;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Transaction\CommandGeneratorInterface;
use DI\Container;
use loophp\collection\Collection as LooPhpCollection;

final class RegisterORM
{
    public function __invoke(Container $c): void
    {
        $c->set(FactoryInterface::class, static function (Container $c): FactoryInterface {
            /** @var DatabaseProviderInterface $dbal */
            $dbal = $c->get(DatabaseProviderInterface::class);

            return (new Factory(
                dbal: $dbal,
                defaultCollectionFactory: new Collection\ArrayCollectionFactory(),
            ))->withCollectionFactory(
                LooPhpCollection::class,
                new Collection\LoophpCollectionFactory(),
                LooPhpCollection::class,
            );
        });

        $c->set(CommandGeneratorInterface::class, static function (Container $c): CommandGeneratorInterface {
            /** @var SchemaInterface $schema */
            $schema = $c->get(SchemaInterface::class);
            return new EventDrivenCommandGenerator($schema, $c);
        });

        $c->set(ORMInterface::class, static function (Container $c): ORMInterface {
            /** @var FactoryInterface $factory */
            $factory = $c->get(FactoryInterface::class);
            /** @var SchemaInterface $schema */
            $schema = $c->get(SchemaInterface::class);
            /** @var CommandGeneratorInterface|null $commandGenerator */
            $commandGenerator = $c->get(CommandGeneratorInterface::class) ?? null;

            return new ORM(
                factory: $factory,
                schema: $schema,
                commandGenerator: $commandGenerator,
            );
        });
    }
}
