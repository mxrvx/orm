<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use Monolog\Level;
use Monolog\Logger;
use MXRVX\ORM\Handlers\SizeRotatingStreamHandler;

class LoggerFactory
{
    protected Logger $logger;

    public function __construct(
        protected string $filePath,
        protected int|string|Level $level = Level::Debug,
        protected int $maxFileSize = 10 * 1024 * 1024,
    ) {
        $handler = new SizeRotatingStreamHandler($filePath, Level::Debug, true, $maxFileSize);
        $this->logger = new Logger(App::getNameSpaceSlug(), [$handler]);
    }

    public function get(): Logger
    {
        return $this->logger;
    }
}
