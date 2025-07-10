<?php

declare(strict_types=1);

namespace MXRVX\ORM\Typecast\Types\Array;

use MXRVX\ORM\Typecast\UncastContext;
use MXRVX\ORM\Typecast\CastContext;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ArrayToSerializedString extends ArrayType
{
    public function cast(mixed $value, CastContext $context): ?array
    {
        $default = $this->getCastDefault();

        if ($this->shouldUseDefault($value, $default, $this->castNullable)) {
            $value = $default;
        }

        if (\is_string($value)) {
            /** @psalm-var mixed $unserialized */
            $unserialized = @\unserialize($value, ['allowed_classes' => false]);
            if (\is_array($unserialized)) {
                $value = $unserialized;
            }
        }

        if (\is_array($value)) {
            return $value;
        }

        return $default;
    }

    public function uncast(mixed $value, UncastContext $context): ?string
    {
        $default = $this->getUncastDefault();

        if ($this->shouldUseDefault($value, $default, $this->uncastNullable)) {
            $value = $default;
        }

        if (\is_array($value)) {
            $value = \serialize($value);
        }

        if (\is_string($value)) {
            return $value;
        }

        return $default;
    }
}
