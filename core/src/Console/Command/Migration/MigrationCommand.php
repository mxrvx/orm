<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console\Command\Migration;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Migrations\MigrationInterface;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\State;
use MXRVX\Autoloader\Composer\Package\Package;
use MXRVX\ORM\Console\Command\Command;
use MXRVX\ORM\MigrationPathConfig;
use MXRVX\ORM\MigratorFactory;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class MigrationCommand extends Command
{
    /** @var string */
    public const DATABASE_NAME = 'modx';

    protected ?Package $package = null;
    protected ?MigratorFactory $migratorFactory = null;

    protected function setNameSpaceOption(): static
    {
        return $this
            ->addOption(
                'namespace',
                null,
                InputOption::VALUE_REQUIRED,
                'Package namespace, to load migration',
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $nameSpace = (string) $input->getOption('namespace');

        if (empty($nameSpace) && $input->isInteractive()) {
            $question = new Question('Enter the namespace of the package: ');

            /** @var QuestionHelper $qaHelper */
            $qaHelper = $this->getHelper('question');
            $nameSpace = (string) $qaHelper->ask($input, $output, $question);
        }

        if (empty($nameSpace)) {
            throw new \RuntimeException('Option --namespace is required');
        }

        /** @var \modX $modx */
        $modx = $this->container->get(\modX::class);
        $autoloader = \MXRVX\Autoloader\App::getInstance($modx);
        if (!$package = $autoloader->manager()->getPackage($nameSpace)) {
            throw new \RuntimeException(\sprintf('Package namespace `%s` not found', $nameSpace));
        }

        $this->getMigratorFactory($package);
        $this->package = $package;
    }

    protected function getMigratorFactory(?Package $package = null): MigratorFactory
    {
        $package ??= $this->package;

        if (!$package) {
            throw new \RuntimeException('Package not exists');
        }

        /** @var DatabaseProviderInterface $dbal */
        $dbal = $this->container->get(DatabaseProviderInterface::class);

        return $this->migratorFactory ??= new MigratorFactory(
            (new MigrationPathConfig())->setNameSpace($package->namespace),
            $dbal,
        );
    }

    protected function getMigrator(): Migrator
    {
        return $this->getMigratorFactory()->get();
    }

    protected function getDatabaseManager(): DatabaseProviderInterface
    {
        return $this->getMigratorFactory()->getDatabaseManager();
    }

    protected function getMigrationPathConfig(): MigrationPathConfig
    {
        return $this->getMigratorFactory()->getMigrationPathConfig();
    }

    /**
     * @return MigrationInterface[]
     */
    protected function findMigrations(OutputInterface $output): array
    {
        if ($list = $this->getMigrator()->getMigrations()) {
            $output->writeln(
                \sprintf(
                    '<info>Total %d migration(s) found in `%s`</info>',
                    \count($list),
                    $this->getMigrator()->getConfig()->getDirectory(),
                ),
            );
            return $list;
        }

        return [];
    }

    protected function getMigrationStatus(MigrationInterface $migration): string
    {
        return match ($migration->getState()->getStatus()) {
            State::STATUS_EXECUTED => 'executed',
            State::STATUS_PENDING => 'pending',
            State::STATUS_UNDEFINED => 'undefined',
            default => 'unknown',
        };
    }
}
