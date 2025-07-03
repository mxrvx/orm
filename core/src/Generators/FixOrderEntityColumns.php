<?php

declare(strict_types=1);

namespace MXRVX\ORM\Generators;

use Cycle\Schema\Exception\EntityException;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;

class FixOrderEntityColumns implements GeneratorInterface
{
    /**
     *
     * @throws EntityException
     *
     */
    public function run(Registry $registry): Registry
    {
        foreach ($registry->getIterator() as &$entity) {
            if (\count($entity->getFields()) === 0) {
                throw new EntityException(
                    "Entity `{$entity->getRole()}` is empty",
                );
            }

            $fieldsMap = $entity->getFields();
            $primaryFields = [];
            $nonPrimaryFields = [];
            foreach ($fieldsMap->getIterator() as $name => $field) {
                if ($field->isPrimary()) {
                    $primaryFields[$name] = $field;
                } else {
                    $nonPrimaryFields[$name] = $field;
                }
                $fieldsMap->remove($name);
            }
            $sortedFields = $primaryFields + $nonPrimaryFields;

            foreach ($sortedFields as $name => $field) {
                $fieldsMap->set($name, $field);
            }
        }

        return $registry;
    }
}
