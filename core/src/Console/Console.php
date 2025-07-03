<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console;

use DI\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\ListCommand;
use MXRVX\ORM\App;
use MXRVX\ORM\Console\Command\InstallCommand;
use MXRVX\ORM\Console\Command\RemoveCommand;
use MXRVX\ORM\Console\Command\Migration\MigrationListCommand;
use MXRVX\ORM\Console\Command\Migration\MigrationGenerateCommand;
use MXRVX\ORM\Console\Command\Migration\MigrationCreateCommand;
use MXRVX\ORM\Console\Command\Migration\MigrationDownCommand;
use MXRVX\ORM\Console\Command\Migration\MigrationUpCommand;

class Console extends Application
{
    public function __construct(protected Container $container)
    {
        parent::__construct(App::getNameSpaceSlug());
    }

    protected function getDefaultCommands(): array
    {
        return [
            new ListCommand(),
            new InstallCommand($this->container),
            new RemoveCommand($this->container),
            new MigrationListCommand($this->container),
            new MigrationGenerateCommand($this->container),
            new MigrationCreateCommand($this->container),
            new MigrationUpCommand($this->container),
            new MigrationDownCommand($this->container),
        ];
    }
}
