<?php

declare(strict_types=1);

namespace MXRVX\ORM\Driver\MySQL;

use Cycle\Database\Exception\StatementException;
use Cycle\Database\StatementInterface;
use MXRVX\ORM\Driver\DriverWIthModxInterface;

/** @psalm-suppress DeprecatedMethod */
class MySQLDriver extends \Cycle\Database\Driver\MySQL\MySQLDriver implements DriverWIthModxInterface
{
    private ?\modX $modx = null;

    public function setModx(\modX $modx): void
    {
        $this->modx = $modx;
    }

    /**
     * psalm-assert $this->modx !== null
     */
    public function getModx(): ?\modX
    {
        return $this->modx;
    }

    /**
     * Execute query and return query statement.
     *
     * @psalm-param non-empty-string $statement
     *
     * @throws StatementException
     */
    public function query(string $statement, array $parameters = []): StatementInterface
    {
        $queryStart = \microtime(true);

        $modx = $this->getModx();

        if (isset($modx)) {
            $modx->executedQueries++;
        }

        $result = $this->statement($statement, $parameters);

        if (isset($modx)) {
            /** @psalm-suppress InvalidPropertyAssignmentValue */
            $modx->queryTime += \microtime(true) - $queryStart;
        }


        return $result;
    }
}
