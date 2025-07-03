<?php

declare(strict_types=1);

namespace MXRVX\ORM\AR;

interface ARInterface
{
    public function getRole(): string;

    public function toArray(): array;

    public function fromArray(array $data): static;
}
