<?php

declare(strict_types=1);

namespace MXRVX\ORM\Console\Command;

use MXRVX\ORM\App;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    public function configure(): void
    {
        $this
            ->setName('install')
            ->setDescription(\sprintf('Install `%s` extra for MODX', App::getNameSpaceSlug()));
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $app = $this->app;
        $modx = $this->app->modx;
        $ns = App::getNameSpaceSlug();

        $srcPath = MODX_CORE_PATH . 'vendor/' . (string) \preg_replace('/-/', '/', $ns, 1);
        $corePath = MODX_CORE_PATH . 'components/' . $ns;
        if (!\is_dir($corePath)) {
            \symlink($srcPath . '/core', $corePath);
            $output->writeln('<info>Created symlink for `core`</info>');
        }

        if (!$modx->getObject(\modNamespace::class, ['name' => $ns])) {
            /** @var \modNamespace $namespace */
            $namespace = $modx->newObject(\modNamespace::class);
            $namespace->fromArray(
                [
                    'name' => $ns,
                    'path' => '{core_path}components/' . $ns . '/',
                    'assets_path' => '',
                ],
                '',
                true,
            );
            $namespace->save();
            $output->writeln(\sprintf('<info>Created namespace `%s`</info>', $ns));
        }


        /** @var array{key: string, value: mixed} $row */
        foreach ($app->config->getSettingsArray() as $row) {
            if (!$modx->getObject(\modSystemSetting::class, $row['key'])) {
                /** @var \modSystemSetting $setting */
                $setting = $modx->newObject(\modSystemSetting::class);
                $setting->fromArray($row, '', true);
                $setting->save();
                $output->writeln(\sprintf('<info>Created system setting `%s`</info>', $row['key']));
            }
        }

        $modx->getCacheManager()->refresh();
        $output->writeln('<info>Cleared MODX cache</info>');

        return Command::SUCCESS;
    }
}
