<?php

declare(strict_types=1);

namespace MXRVX\ORM\Exceptions;

class FileNotFoundException extends FilesException
{
    public function __construct(string $filename)
    {
        parent::__construct(\sprintf('File `%s` not found', $filename));
    }
}
