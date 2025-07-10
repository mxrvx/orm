<?php

declare(strict_types=1);

namespace MXRVX\ORM\Typecast;

final class CastContext
{
    public function __construct(
        public readonly string $property,
        public readonly array $data,
    ) {}
}
