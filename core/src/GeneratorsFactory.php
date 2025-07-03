<?php

declare(strict_types=1);

namespace MXRVX\ORM;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
use Cycle\Schema\Generator\ForeignKeys;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderModifiers;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\GeneratorInterface;
use MXRVX\ORM\Contracts\PathConfigInterface;
use MXRVX\ORM\Generators\FixOrderEntityColumns;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

class GeneratorsFactory
{
    public const ResetTables = 'ResetTables';
    public const Embeddings = 'Embeddings';
    public const Entities = 'Entities';
    public const TableInheritance = 'TableInheritance';
    public const MergeColumns = 'MergeColumns';
    public const GenerateRelations = 'GenerateRelations';
    public const GenerateModifiers = 'GenerateModifiers';
    public const ValidateEntities = 'ValidateEntities';
    public const RenderTables = 'RenderTables';
    public const RenderRelations = 'RenderRelations';
    public const RenderModifiers = 'RenderModifiers';
    public const ForeignKeys = 'ForeignKeys';
    public const MergeIndexes = 'MergeIndexes';
    public const SyncTables = 'SyncTables';
    public const GenerateTypecast = 'GenerateTypecast';
    public const FixOrderEntityColumns = 'FixOrderEntityColumns';

    protected ClassLocator $classLocator;

    /**
     * @var array<string, GeneratorInterface|callable():GeneratorInterface>
     */
    private array $generators;

    /**
     * @param string[] $include
     * @param string[] $exclude
     */
    public function __construct(
        protected PathConfigInterface $entityPathConfig,
        private array $include = [],
        private array $exclude = [],
    ) {
        if ($entityPathConfig->isEmpty()) {
            $entityPaths = [__DIR__ . '/../src/Entities'];
        } else {
            $entityPaths = $entityPathConfig->getPaths();
        }

        $finder = (new Finder())->files()->in($entityPaths);
        $this->classLocator = new ClassLocator($finder);
        $this->generators = $this->filterGenerators($this->getGenerators(), $this->include, $this->exclude);
    }

    /**
     * @return GeneratorInterface[]
     */
    public function get(): array
    {
        $generators = [];
        foreach ($this->generators as $callable) {
            $generators[] = $this->getGenerator($callable);
        }
        return $generators;
    }

    /**
     * @param non-empty-string $name
     * @param GeneratorInterface|callable():GeneratorInterface $generator
     * @return $this
     */
    public function addGenerator(string $name, GeneratorInterface|callable $generator): static
    {
        if (!isset($this->generators[$name])) {
            $this->generators[$name] = $generator;
        }

        return $this;
    }

    /**
     * @param GeneratorInterface|callable():GeneratorInterface $generator
     */
    private function getGenerator(GeneratorInterface|callable $generator): GeneratorInterface
    {
        if (\is_callable($generator)) {
            return $generator();
        }
        return $generator;
    }

    /**
     * re-declared table schemas (remove columns)
     */
    private function createResetTables(): ResetTables
    {
        return new ResetTables();
    }

    /**
     * register embeddable entities
     */
    private function createEmbeddings(): Embeddings
    {
        return new Embeddings(new TokenizerEmbeddingLocator($this->classLocator));
    }

    /**
     * register annotated entities
     */
    private function createEntities(): Entities
    {
        return new Entities(new TokenizerEntityLocator($this->classLocator));
    }

    /**
     * Setup Single Table or Joined Table Inheritance
     */
    private function createTableInheritance(): TableInheritance
    {
        return new TableInheritance();
    }

    /**
     * Integrate table #[Column] attributes
     */
    private function createMergeColumns(): MergeColumns
    {
        return new MergeColumns();
    }

    /**
     * Define entity relationships
     */
    private function createGenerateRelations(): GenerateRelations
    {
        return new GenerateRelations();
    }

    /**
     * Apply schema modifications
     */
    private function createGenerateModifiers(): GenerateModifiers
    {
        return new GenerateModifiers();
    }

    /**
     * Ensure entity schemas adhere to conventions
     */
    private function createValidateEntities(): ValidateEntities
    {
        return new ValidateEntities();
    }

    /**
     * Create table schemas
     */
    private function createRenderTables(): RenderTables
    {
        return new RenderTables();
    }

    /**
     * Establish keys and indexes for relationships
     */
    private function createRenderRelations(): RenderRelations
    {
        return new RenderRelations();
    }

    /**
     * Implement schema modifications
     */
    private function createRenderModifiers(): RenderModifiers
    {
        return new RenderModifiers();
    }

    /**
     * Define foreign key constraints
     */
    private function createForeignKeys(): ForeignKeys
    {
        return new ForeignKeys();
    }

    /**
     * Merge table index attributes
     */
    private function createMergeIndexes(): MergeIndexes
    {
        return new MergeIndexes();
    }

    /**
     * Align table changes with the database
     */
    private function createSyncTables(): SyncTables
    {
        return new SyncTables();
    }

    /**
     * Align table changes with the database
     */
    private function createGenerateTypecast(): GenerateTypecast
    {
        return new GenerateTypecast();
    }

    private function createFixOrderEntityColumns(): FixOrderEntityColumns
    {
        return new FixOrderEntityColumns();
    }

    /**
     * @return array<string, callable():GeneratorInterface>
     */
    private function getGenerators(): array
    {
        return [
            self::ResetTables => fn() => $this->createResetTables(),
            self::Embeddings => fn() => $this->createEmbeddings(),
            self::Entities => fn() => $this->createEntities(),
            self::FixOrderEntityColumns => fn() => $this->createFixOrderEntityColumns(),
            self::TableInheritance => fn() => $this->createTableInheritance(),
            self::MergeColumns => fn() => $this->createMergeColumns(),
            self::GenerateRelations => fn() => $this->createGenerateRelations(),
            self::GenerateModifiers => fn() => $this->createGenerateModifiers(),
            self::ValidateEntities => fn() => $this->createValidateEntities(),
            self::RenderTables => fn() => $this->createRenderTables(),
            self::RenderRelations => fn() => $this->createRenderRelations(),
            self::RenderModifiers => fn() => $this->createRenderModifiers(),
            self::ForeignKeys => fn() => $this->createForeignKeys(),
            self::MergeIndexes => fn() => $this->createMergeIndexes(),
            // self::SyncTables => fn() => $this->createSyncTables(),
            self::GenerateTypecast => fn() => $this->createGenerateTypecast(),

        ];
    }

    /**
     * @param array<string, callable():GeneratorInterface> $generators
     * @param string[] $include
     * @param string[] $exclude
     * @return array<string, callable():GeneratorInterface>
     */
    private function filterGenerators(array $generators, array $include, array $exclude): array
    {
        if (empty($include)) {
            $include = \array_keys($generators);
        }

        $filtered = \array_filter($generators, static function ($key) use ($include, $exclude) {
            return \in_array($key, $include, true) && !\in_array($key, $exclude, true);
        }, ARRAY_FILTER_USE_KEY);

        return $filtered;
    }
}
