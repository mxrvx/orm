<?php

declare(strict_types=1);

namespace MXRVX\ORM\Typecast\Types\Array;

use MXRVX\ORM\Typecast\Type;

abstract class ArrayType extends Type
{
    public function getCastDefault(): ?array
    {
        if ($this->castDefault === null) {
            return null;
        }

        return \is_array($this->castDefault) ? $this->castDefault : null;
    }

    public function getUncastDefault(): ?string
    {
        if ($this->uncastDefault === null) {
            return null;
        }

        return \is_string($this->uncastDefault) ? $this->uncastDefault : null;
    }
}
