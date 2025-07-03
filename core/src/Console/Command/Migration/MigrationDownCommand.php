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

class MigrationDownCommand extends MigrationCommand
{
    public function configure(): void
    {
        $this
            ->setNameSpaceOption()
            ->setName('migration:down')
            ->setDescription('Migrate the database down')
            ->setHelp('This command allows you to migrate the database down')
            /** add arguments */
            ->addArgument('count', InputArgument::OPTIONAL, 'The number of migrations to run', 1);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrations = $this->findMigrations($output);
        // check any executed migration
        $exist = false;
        foreach (\array_reverse($migrations) as $migration) {
            if ($migration->getState()->getStatus() === State::STATUS_EXECUTED) {
                $exist = true;
                break;
            }
        }

        if (!$exist || !isset($migration) || !($migration instanceof MigrationInterface)) {
            $output->writeln('<fg=red>No migration found for rollback</>');
            return self::SUCCESS;
        }

        // Confirm
        if ($input->isInteractive()) {
            $output->writeln('<fg=yellow>Migration to be reverted:</>');
            $output->writeln(\sprintf('— <fg=cyan>%s</>', $migration->getState()->getName()));
            /** @var QuestionHelper $qaHelper */
            $qaHelper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Revert the above migration? (yes|no) ', false);
            if (!$qaHelper->ask($input, $output, $question)) {
                return self::SUCCESS;
            }

            $migration = $this->getMigrator()->rollback();
            if ($migration instanceof MigrationInterface) {
                $output->writeln(\sprintf(
                    '<fg=cyan>%s</> <fg=yellow>[%s]</>',
                    $migration->getState()->getName(),
                    $this->getMigrationStatus($migration),
                ));
            }
        } else {
            do {
                $migration = $this->getMigrator()->rollback();
                if (!$migration instanceof MigrationInterface) {
                    break;
                }
                $output->writeln(\sprintf(
                    '<fg=cyan>%s</> <fg=yellow>[%s]</>',
                    $migration->getState()->getName(),
                    $this->getMigrationStatus($migration),
                ));
            } while (true);
        }

        return self::SUCCESS;
    }
}
