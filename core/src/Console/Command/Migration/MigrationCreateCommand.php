<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console\Command\Migration;

use Cycle\Schema\Generator\Migrations\MigrationImage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCreateCommand extends MigrationCommand
{
    public function configure(): void
    {
        $this
            ->setNameSpaceOption()
            ->setName('migration:create')
            ->setDescription('Create a database migration')
            ->setHelp('This command allows you to create a custom migration')
            /** add arguments */
            ->addArgument('name', InputArgument::REQUIRED, 'Migration name');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = (string) $input->getArgument('name');
        $migration = new MigrationImage($this->getMigrator()->getConfig(), self::DATABASE_NAME);
        $migration->setName($name);

        $className = $migration->getClass()->getName();
        \assert($className !== null);

        try {
            $migrationFile = $this->getMigrator()->getRepository()->registerMigration(
                $migration->buildFileName(),
                $className,
                $migration->getFile()->render(),
            );
        } catch (\Throwable $e) {
            $output->writeln('<fg=yellow>Can not create migration</>');
            $output->writeln(\sprintf('<fg=red>%s</>', $e->getMessage()));
            return self::FAILURE;
        }

        $output->writeln('<info>New migration file has been created</info>');
        $output->writeln(\sprintf('<fg=cyan>`%s`</>', $migrationFile));

        return self::SUCCESS;
    }
}
