<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MXRVX\ORM\App;

class RemoveCommand extends Command
{
    public function configure(): void
    {
        $this
            ->setName('remove')
            ->setDescription(\sprintf('Remove `%s` extra from MODX', App::getNameSpaceSlug()));
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $modx = $this->app->modx;
        $ns = App::getNameSpaceSlug();

        $corePath = MODX_CORE_PATH . 'components/' . $ns;
        if (\is_dir($corePath)) {
            \unlink($corePath);
            $output->writeln('<info>Removed symlink for `core`</info>');
        }

        /** @var \modNamespace $namespace */
        if ($namespace = $modx->getObject(\modNamespace::class, ['name' => $ns])) {
            $namespace->remove();
            $output->writeln(\sprintf('<info>Removed namespace `%s`</info>', $ns));
        }

        $modx->getCacheManager()->refresh();
        $output->writeln('<info>Cleared MODX cache</info>');

        return Command::SUCCESS;
    }
}
