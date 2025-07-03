<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class LoggerFactory
{
    protected Logger $logger;

    public function __construct(
        string $filename = 'filename.log',
        int|string|Level $level = Level::Debug,
    ) {
        $this->logger = new Logger(App::getNameSpaceSlug(), [
            new StreamHandler(\sprintf('%s/%s', App::getCacheDirectory(), $filename), $level),
        ]);
    }

    public function get(): Logger
    {
        return $this->logger;
    }
}
