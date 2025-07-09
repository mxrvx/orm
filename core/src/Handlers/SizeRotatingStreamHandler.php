<?php

declare(strict_types=1);

namespace MXRVX\ORM\Handlers;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;

class SizeRotatingStreamHandler extends StreamHandler
{
    protected int $maxFileSize;

    /**
     * @param resource|string $stream
     *
     */
    public function __construct($stream, int|string|Level $level = Level::Debug, bool $bubble = true, int $maxFileSize = 10485760)
    {
        parent::__construct($stream, $level, $bubble);
        $this->maxFileSize = $maxFileSize;
    }

    protected function write(LogRecord $record): void
    {
        if ($this->url) {
            \clearstatcache(true, $this->url);
            if (\file_exists($this->url) && \filesize($this->url) >= $this->maxFileSize) {
                \file_put_contents($this->url, '');
            }
        }

        parent::write($record);
    }
}
