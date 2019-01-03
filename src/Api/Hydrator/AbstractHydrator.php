<?php

namespace App\Api\Hydrator;

abstract class AbstractHydrator
{
    const ALLOWED_HYDRATION_LEVEL = 2; // zero-based

    /**
     * @var array
     */
    protected $schema = [];

    /**
     * @var array
     */
    protected $defaultFields = [];

    /**
     * @param $fields
     *
     * @return mixed
     */
    public function extendDefaultFields($fields)
    {
        return $fields + $this->defaultFields;
    }

    /**
     * @param       $entity
     * @param array $fields
     * @param int   $hydrationLevel
     * @param array $context
     *
     * @return array
     */
    public function hydrate($entity, array $fields = [], int $hydrationLevel = 0, array $context = [])
    {
        if ($entity === null) {
            return null;
        }

        if (empty($fields)) {
            $fields = $this->defaultFields;
        }

        $result = [];
        foreach ($fields as $field => $childFields) {
            if ($hydrationLevel >= self::ALLOWED_HYDRATION_LEVEL && isset($this->schema[$field]['hydrator'])) {
                continue;
            }

            if (!is_array($childFields)) {
                $childFields = [];
            }

            $result[$field] = $this->schema[$field]['value']($entity, $childFields, $hydrationLevel + 1, $context);
        }

        return $result;
    }

    /**
     * @param iterable $entities
     * @param array    $fields
     * @param int      $hydrationLevel
     * @param array    $context
     *
     * @return array
     */
    public function hydrateCollection(
        iterable $entities,
        array $fields = [],
        int $hydrationLevel = 0,
        array $context = []
    ) {
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $this->hydrate($entity, $fields, $hydrationLevel, $context);
        }

        return $result;
    }

    /**
     * @param array $fields
     *
     * @return bool
     */
    public function validateFields(array $fields): bool
    {
        foreach ($fields as $field => $childFields) {
            if (!isset($this->schema[$field])) {
                return false;
            }

            if (is_array($childFields)) {
                if (!isset($this->schema[$field]['hydrator']) || !$this->schema[$field]['hydrator']()) {
                    return false;
                }

                if (!$this->schema[$field]['hydrator']()->validateFields($childFields)) {
                    return false;
                }
            }
        }

        return true;
    }
}
