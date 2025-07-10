<?php

declare(strict_types=1);

namespace MXRVX\ORM\Typecast;

final class Typecaster
{
    /**
     * @var array<string, TypeInterface>
     */
    private array $config;

    /**
     * @param array<string, TypeInterface> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function prepareAfterExtract(array $data): array
    {
        foreach ($this->config as $column => $type) {
            if (\array_key_exists($column, $data)) {
                /** @psalm-suppress MixedAssignment */
                $data[$column] = $type->uncast($data[$column], new UncastContext($column, $data));
            }
        }
        return $data;
    }

    public function prepareBeforeHydrate(array $data): array
    {
        foreach ($this->config as $column => $type) {
            if (\array_key_exists($column, $data)) {
                /** @psalm-suppress MixedAssignment */
                $data[$column] = $type->cast($data[$column], new CastContext($column, $data));
            }
        }
        return $data;
    }
}
