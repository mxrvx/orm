<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console\Command\Migration;

use Cycle\Migrations\MigrationInterface;
use Cycle\Migrations\State;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class MigrationUpCommand extends MigrationCommand
{
    public function configure(): void
    {
        $this
            ->setNameSpaceOption()
            ->setName('migration:up')
            ->setDescription('Migrate the database up')
            ->setHelp('This command allows you to migrate the database')
            /** add arguments */
            ->addArgument('count', InputArgument::OPTIONAL, 'The number of migrations to run', 1);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrations = $this->findMigrations($output);
        // check any not executed migration
        $exist = false;
        foreach ($migrations as $migration) {
            if ($migration->getState()->getStatus() === State::STATUS_PENDING) {
                $exist = true;
                break;
            }
        }

        if (!$exist) {
            $output->writeln('<fg=red>No migration found for execute</>');
            return self::SUCCESS;
        }

        if ($input->isInteractive()) {
            $newMigrations = [];
            foreach ($migrations as $migration) {
                if ($migration->getState()->getStatus() === State::STATUS_PENDING) {
                    $newMigrations[] = $migration;
                }
            }
            $countNewMigrations = \count($newMigrations);
            $output->writeln(
                \sprintf('<fg=yellow>%s to be applied:</>', $countNewMigrations === 1 ? 'Migration' : $countNewMigrations . ' migrations'),
            );

            foreach ($newMigrations as $migration) {
                $output->writeln(\sprintf('— <fg=cyan>%s</>', $migration->getState()->getName()));
            }
            $question = new ConfirmationQuestion(
                \sprintf('Apply the above %s? (yes|no) ', ($countNewMigrations === 1 ? 'migration' : 'migrations')),
            );

            /** @var QuestionHelper $qaHelper*/
            $qaHelper = $this->getHelper('question');
            if (!$qaHelper->ask($input, $output, $question)) {
                return self::SUCCESS;
            }
        }


        do {
            $migration = $this->getMigrator()->run();
            if (!$migration instanceof MigrationInterface) {
                break;
            }
            $output->writeln(\sprintf(
                '<fg=cyan>%s</> <fg=yellow>[%s]</>',
                $migration->getState()->getName(),
                $this->getMigrationStatus($migration),
            ));
        } while (true);

        return self::SUCCESS;
    }
}
