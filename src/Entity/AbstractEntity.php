<?php

namespace App\Entity;

abstract class AbstractEntity implements EntityInterface
{
    /**
     * @return string
     */
    public static function staticGetOwnClassName()
    {
        $fullClassNameParts = explode('\\', static::class);

        return end($fullClassNameParts);
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function populate(array $data)
    {
        foreach ($data as $name => $value) {
            $setter = 'set' . ucfirst($name);
            if (is_callable([$this, $setter])) {
                $this->$setter($value);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);

        $data = [];
        foreach ($properties as $property) {
            $propertyName  = $property->getName();
            $getter        = "get" . ucfirst($propertyName);
            $booleanGetter = "is".ucfirst($propertyName);
            if (is_callable([$this, $getter])) {
                $data[$propertyName] = $this->$getter();
            } elseif (is_callable([$this, $booleanGetter])) {
                $data[$propertyName] = $this->$booleanGetter();
            }
        }

        return $data;
    }

    /**
     * Gets Movie for \ShedBundle\Entity\Movie, etc.
     *
     * @return string
     */
    public function getOwnClassName(): string
    {
        return static::staticGetOwnClassName();
    }

    /**
     * @return string
     */
    public function getOwnClassNameHuman()
    {
        $ownClassName = $this->getOwnClassName();

        $ownClassNameHuman = preg_replace('/(?<!^)([A-Z])/', ' \\1', $ownClassName);

        return $ownClassNameHuman;
    }
}
