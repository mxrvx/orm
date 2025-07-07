<?php

declare(strict_types=1);

namespace MXRVX\ORM\AR;

use Cycle\ActiveRecord\Exception\Transaction\TransactionException;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ActiveRecord\TransactionMode;
use Cycle\ORM\Exception\RunnerException;
use Cycle\ORM\RepositoryInterface;

interface ARInterface
{
    /**
     * @param array<non-empty-string, mixed> $data An associative array where keys are property names
     *        and values are property values.
     */
    public static function make(array $data): static;

    public static function findByPK(mixed $primaryKey): ?static;

    public static function findOne(array $scope = []): ?static;

    public static function findAll(array $scope = []): iterable;

    /**
     * Execute a callback within a single transaction.
     *
     * All the ActiveRecord write operations within the callback will be registered
     * using the Entity Manager without being executed until the end of the callback.
     *
     * @note DBAL operations will not be executed within the transaction. Use {@see self::transact()} for that.
     *
     * @template TResult
     * @param callable(): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws RunnerException
     * @throws \Throwable
     *
     */
    public static function groupActions(
        callable $callback,
        TransactionMode $mode = TransactionMode::OpenNew,
    ): mixed;

    /**
     * @template TResult
     * @param callable(): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws \Throwable
     *
     */
    public static function transact(
        callable $callback,
    ): mixed;

    public static function query(): ActiveQuery;

    public static function getRepository(): RepositoryInterface;

    public function save(bool $cascade = true): bool;

    public function saveOrFail(bool $cascade = true): void;

    public function delete(bool $cascade = true): bool;

    public function deleteOrFail(bool $cascade = true): void;

    public function getRole(): string;

    public function toArray(array $fields): array;

    public function fromArray(array $data): static;
}
