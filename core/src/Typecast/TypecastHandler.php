<?php

declare(strict_types=1);

namespace MXRVX\ORM\Typecast;

use Cycle\Annotated\Annotation\Column;
use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\Parser\UncastableInterface;
use Cycle\ORM\SchemaInterface;

final class TypecastHandler implements CastableInterface, UncastableInterface
{
    /**
     * @var array<array-key, TypeInterface>
     */
    private array $types = [];

    public function __construct(SchemaInterface $schema, string $role)
    {
        $class = $schema->define($role, SchemaInterface::ENTITY);
        if (!\is_string($class) || !\class_exists($class)) {
            return;
        }

        try {
            $class = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return;
        }

        foreach ($class->getProperties() as $property) {
            $typecast = $property->getAttributes(TypeInterface::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
            if ($typecast === null) {
                continue;
            }

            $column = $property->getAttributes(Column::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
            if ($column === null) {
                continue;
            }

            $args = [
                'castNullable' => $this->getCastNullable($property),
                'uncastNullable' => $this->getUncastNullable($property),
                'castDefault' => $this->getCastDefault($property),
                'uncastDefault' => $this->getUncastDefault($property),
            ] + $typecast->getArguments();

            /** @var class-string<TypeInterface> $className */
            $className = $typecast->getName();

            $this->types[$property->getName()] = new $className(...$args);
        }
    }

    public function getCastNullable(\ReflectionProperty $property): bool
    {
        $type = $property->getType();
        if ($type instanceof \ReflectionNamedType) {
            return $type->allowsNull();
        } elseif ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $namedType) {
                if ($namedType->getName() === 'null') {
                    return true;
                }
            }
        }
        return false;
    }

    public function getCastDefault(\ReflectionProperty $property): mixed
    {
        try {
            return $property->getDefaultValue();
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    public function getUncastNullable(\ReflectionProperty $property): bool
    {
        $column = $property->getAttributes(Column::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
        if ($column === null) {
            return false;
        }
        return (bool) ($column->getArguments()['nullable'] ?? false);
    }

    public function getUncastDefault(\ReflectionProperty $property): mixed
    {
        $column = $property->getAttributes(Column::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
        if ($column === null) {
            return false;
        }
        return $column->getArguments()['default'] ?? null;
    }

    public function setRules(array $rules): array
    {
        foreach ($rules as $key => $_rule) {
            if (isset($this->types[$key])) {
                unset($rules[$key]);
            }
        }
        return $rules;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    public function cast(array $data): array
    {
        /**
         * @psalm-var array<array-key, mixed> $data
         * @psalm-var non-empty-string $key
         * @psalm-var mixed $value
         */
        foreach ($data as $key => $value) {
            if (isset($this->types[$key])) {
                /** @psalm-suppress MixedAssignment */
                $data[$key] = $this->types[$key]->cast($value, new CastContext($key, $data));
            }
        }
        return $data;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    public function uncast(array $data): array
    {
        /**
         * @psalm-var array<array-key, mixed> $data
         * @psalm-var non-empty-string $key
         * @psalm-var mixed $value
         */
        foreach ($data as $key => $value) {
            if (isset($this->types[$key])) {
                /** @psalm-suppress MixedAssignment */
                $data[$key] = $this->types[$key]->uncast($value, new UncastContext($key, $data));
            }
        }
        return $data;
    }
}
