<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console\Command\Migration;

use Cycle\Migrations\MigrationInterface;
use Cycle\Migrations\State;
use Cycle\Schema;
use Cycle\Schema\Generator\Migrations\GenerateMigrations;
use Cycle\Schema\Generator\Migrations\NameBasedOnChangesGenerator;
use Cycle\Schema\Generator\Migrations\Strategy\MultipleFilesStrategy;
use Cycle\Schema\Generator\PrintChanges;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MXRVX\ORM\GeneratorsFactory;

class MigrationGenerateCommand extends MigrationCommand
{
    public function configure(): void
    {
        $this
            ->setNameSpaceOption()
            ->setName('migration:generate')
            ->setDescription('Generate a database migration')
            ->setHelp('This command allows you to generate a database migration');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrationsBefore = $this->findMigrations($output);
        foreach ($migrationsBefore as $migration) {
            if ($migration->getState()->getStatus() !== State::STATUS_EXECUTED) {
                $output->writeln('<fg=red>Outstanding migrations found, run `migration:up` first</>');
                return self::SUCCESS;
            }
        }

        $registry = new Schema\Registry($this->getDatabaseManager());
        $compiler = new Schema\Compiler();

        $factory = (new GeneratorsFactory($this->getMigrationPathConfig()))
            ->addGenerator('print', new PrintChanges($output))
            ->addGenerator('migration', new GenerateMigrations(
                repository: $this->getMigrator()->getRepository(),
                migrationConfig: $this->getMigrator()->getConfig(),
                strategy: new MultipleFilesStrategy($this->getMigrator()->getConfig(), new NameBasedOnChangesGenerator()),
            ));


        $schema = $compiler->compile($registry, $factory->get());

        //var_export($schema);die;

        if (empty($schema)) {
            $output->writeln('<error>Registry are not configured yet</error>');
            return self::FAILURE;
        }

        $migrationsAfter = $this->findMigrations($output);

        /** @var MigrationInterface[] $generated */
        $migrationsGenerated = [];
        if (\count($migrationsBefore) <> \count($migrationsAfter)) {
            $migrationsGenerated = \array_slice($migrationsAfter, \count($migrationsBefore));
        }
        $output->writeln(\sprintf('<info>Added `%s` file(s)</info>', \count($migrationsGenerated)));

        if (!empty($migrationsGenerated)) {
            $output->writeln('<info>Generated migrations:</info>');
            foreach ($migrationsGenerated as $migration) {
                $output->writeln(
                    \sprintf(
                        '<fg=green>`%s`</> <fg=yellow>[%s]</>',
                        $migration->getState()->getName(),
                        $migration->getState()->getTimeCreated()->format('Y-m-d H:i:s'),
                    ),
                );
            }
        }

        return self::SUCCESS;
    }
}
