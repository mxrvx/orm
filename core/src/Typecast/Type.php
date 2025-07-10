<?php

declare(strict_types=1);

namespace MXRVX\ORM\Typecast;

abstract class Type implements TypeInterface
{
    public function __construct(
        protected bool $castNullable = false,
        protected bool $uncastNullable = false,
        protected mixed $castDefault = null,
        protected mixed $uncastDefault = null,
    ) {}

    abstract public function cast(mixed $value, CastContext $context): mixed;

    abstract public function uncast(mixed $value, UncastContext $context): mixed;

    public function isCastNullable(): bool
    {
        return $this->castNullable;
    }

    public function isUncastNullable(): bool
    {
        return $this->uncastNullable;
    }

    public function getCastDefault(): mixed
    {
        return $this->castDefault;
    }

    public function getUncastDefault(): mixed
    {
        return $this->uncastDefault;
    }

    public function shouldUseDefault(mixed $value, mixed $default, bool $nullable, bool $skipEmpty = true): bool
    {
        if ($nullable) {
            return false;
        }

        if ($value === null) {
            return $default !== null;
        }

        if ($skipEmpty && empty($value)) {
            return $default !== null;
        }

        return false;
    }
}
