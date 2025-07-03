<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console\Command;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use MXRVX\ORM\App;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    public const SUCCESS = SymfonyCommand::SUCCESS;
    public const FAILURE = SymfonyCommand::FAILURE;
    public const INVALID = SymfonyCommand::INVALID;

    protected App $app;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(
        protected Container $container,
        ?string             $name = null,
    ) {
        /** @var App $this->app */
        $this->app = $this->container->get(App::class);
        parent::__construct($name);
    }

    protected function runCommand(string $command, array $params, OutputInterface $output, bool $interactive = false): int
    {
        $application = $this->getApplication();
        if (!$application) {
            $output->writeln('<error>The Symfony Console application could not be received.</error>');
            return Command::FAILURE;
        }

        try {
            $commandInstance = $application->find($command);
        } catch (CommandNotFoundException $e) {
            $output->writeln("<error>Command '{$command}' not found.</error>");
            return Command::FAILURE;
        }

        $input = new ArrayInput(\array_merge(['command' => $command], $params));
        $input->setInteractive($interactive);

        return $commandInstance->run($input, $output);
    }
}
