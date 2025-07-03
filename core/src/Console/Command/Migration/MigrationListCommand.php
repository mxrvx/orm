<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console\Command\Migration;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationListCommand extends MigrationCommand
{
    public function configure(): void
    {
        $this
            ->setNameSpaceOption()
            ->setName('migration:list')
            ->setDescription('Prints a list of all migrations');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $list = $this->findMigrations($output);

        foreach ($list as $migration) {
            $state = $migration->getState();
            $output->writeln(\sprintf(
                '<fg=cyan>%s</> <fg=yellow>[%s]</>',
                $state->getName(),
                $this->getMigrationStatus($migration),
            ));
        }

        return self::SUCCESS;
    }
}
