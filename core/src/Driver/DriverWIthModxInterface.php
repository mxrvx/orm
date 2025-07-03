<?php

declare(strict_types=1);

namespace MXRVX\ORM\Driver;

interface DriverWIthModxInterface
{
    public function setModx(\modX $modx): void;

    public function getModx(): ?\modX;
}
