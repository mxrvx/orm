<?php

declare(strict_types=1);

namespace MXRVX\ORM\AR;

use Cycle\ActiveRecord\Exception\Transaction\TransactionException;
use Cycle\ActiveRecord\Facade;
use Cycle\ActiveRecord\Internal\TransactionFacade;
use Cycle\ActiveRecord\Query\ActiveQuery;
use Cycle\ActiveRecord\TransactionMode;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Exception\RunnerException;
use Cycle\ORM\MapperInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;

abstract class AR implements ARInterface
{
    /**
     * Create a new entity instance with the given data.
     * It is preferable to use this method instead of the constructor because
     * it uses ORM services to create the entity.
     *
     * @note Equals to calling {@see ORMInterface::make()}.
     *
     * Example:
     *
     * ```php
     * $user = User::make([
     *    'name' => 'John Doe',
     *    'email' => 'johndoe@example.com',
     * ]);
     * ```
     *
     * @param array<non-empty-string, mixed> $data An associative array where keys are property names
     *        and values are property values.
     */
    public static function make(array $data): static
    {
        return self::getOrm()->make(static::class, $data);
    }

    /**
     * Find a single record based on the given primary key.
     *
     */
    public static function findByPK(mixed $primaryKey): ?static
    {
        /** @var int|string|list<int|string>|object $primaryKey */
        return static::query()->wherePK($primaryKey)->fetchOne();
    }

    /**
     * Find the first single record based on the given scope.
     *
     * @note Limit of 1 will be added to the query.
     */
    public static function findOne(array $scope = []): ?static
    {
        return static::query()->fetchOne($scope);
    }

    /**
     * Find all records based on the given scope.
     *
     * @return iterable<static>
     */
    public static function findAll(array $scope = []): iterable
    {
        return static::query()->where($scope)->fetchAll();
    }

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
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    public static function groupActions(
        callable        $callback,
        TransactionMode $mode = TransactionMode::OpenNew,
    ): mixed {
        return TransactionFacade::groupOrmActions($callback, $mode);
    }

    /**
     * @template TResult
     * @param callable(): TResult $callback
     * @return TResult
     *
     * @throws TransactionException
     * @throws \Throwable
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    public static function transact(
        callable $callback,
    ): mixed {
        return TransactionFacade::transact($callback, static::class === self::class ? null : static::class);
    }

    /**
     * Get an ActiveQuery instance for the entity.
     *
     * @return ActiveQuery<static>
     */
    public static function query(): ActiveQuery
    {
        return new ActiveQuery(static::class);
    }

    public static function getRepository(): RepositoryInterface
    {
        return self::getOrm()->getRepository(static::class);
    }

    /**
     * Persist the entity.
     */
    public function save(bool $cascade = true): bool
    {
        $transacting = static::getTransactionEntityManager();
        if ($transacting === null) {
            /** @psalm-suppress TooManyArguments */
            return static::getFacadeEntityManager()
                ->persist($this, $cascade)
                ->run(false)
                ->isSuccess();
        }

        $transacting->persist($this, $cascade);
        return true;
    }

    /**
     * Persist the entity and throw an exception if an error occurs.
     * The exception will be thrown if the action is happening not in a {@see self::transcat()} scope.
     *
     * @throws \Throwable
     */
    public function saveOrFail(bool $cascade = true): void
    {
        static::getTransactionEntityManager()?->persist($this, $cascade) ?? static::getFacadeEntityManager()
            ->persist($this, $cascade)
            ->run();
    }

    /**
     * Delete the entity.
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    public function delete(bool $cascade = true): bool
    {
        $transacting = static::getTransactionEntityManager();
        if ($transacting === null) {
            /** @psalm-suppress TooManyArguments */
            return static::getFacadeEntityManager()
                ->delete($this, $cascade)
                ->run(false)
                ->isSuccess();
        }

        $transacting->delete($this, $cascade);
        return true;
    }

    /**
     * Delete the entity and throw an exception if an error occurs.
     * The exception will be thrown if the action is happening not in a {@see self::transcat()} scope.
     *
     * @throws \Throwable
     *
     */
    public function deleteOrFail(bool $cascade = true): void
    {
        static::getTransactionEntityManager()?->delete($this, $cascade) ?? static::getFacadeEntityManager()
            ->delete($this, $cascade)
            ->run();
    }

    public function getRole(): string
    {
        return $this->getMapper()->getRole();
    }

    public function toArray(?array $fields = null): array
    {
        if ($fields === null) {
            return $this->getMapper()->fetchFields($this);
        }
        return \array_intersect_key(
            $this->getMapper()->fetchFields($this),
            \array_flip($fields),
        );
    }

    public function fromArray(array $data): static
    {
        $this->getMapper()->hydrate($this, $data);
        return $this;
    }

    protected function getMapper(): MapperInterface
    {
        return static::getOrm()->getMapper($this);
    }

    /**
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    private static function getOrm(): ORMInterface
    {
        return Facade::getOrm();
    }

    /**
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    private static function getTransactionEntityManager(): ?EntityManagerInterface
    {
        return TransactionFacade::getEntityManager();
    }

    /**
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     */
    private static function getFacadeEntityManager(): EntityManagerInterface
    {
        return Facade::getEntityManager();
    }
}
