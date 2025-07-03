<?php

declare(strict_types=1);

namespace MXRVX\ORM\AR;

use Cycle\ActiveRecord\ActiveRecord;
use Cycle\ORM\MapperInterface;

abstract class AR extends ActiveRecord implements ARInterface
{
    public function getRole(): string
    {
        return $this->getMapper()->getRole();
    }

    public function toArray(): array
    {
        return $this->getMapper()->fetchFields($this);
    }

    public function fromArray(array $data): static
    {
        $this->getMapper()->hydrate($this, $data);
        return $this;
    }

    /**
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    private function getMapper(): MapperInterface
    {
        return \Cycle\ActiveRecord\Facade::getOrm()->getMapper($this);
    }
}
