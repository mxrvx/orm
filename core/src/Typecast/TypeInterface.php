<?php

declare(strict_types=1);

namespace MXRVX\ORM\Typecast;

interface TypeInterface
{
    /**
     * converting a value from a database to PHP
     *
     */
    public function cast(mixed $value, CastContext $context): mixed;

    /**
     * converting a value from PHP to a database
     *
     */
    public function uncast(mixed $value, UncastContext $context): mixed;
}
