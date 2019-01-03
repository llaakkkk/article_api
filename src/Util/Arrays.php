<?php

namespace App\Util;

use Doctrine\Common\Collections\ArrayCollection;

class Arrays
{
    /**
     * @param ArrayCollection|array $entities
     * @param string                $key
     *
     * @return array
     */
    public static function getKeysArray($entities, string $key)
    {
        $keys   = [];
        $getter = 'get' . ucfirst($key);

        foreach ($entities as $entity) {
            if (is_array($entity)) {
                $keys[] = $entity[$key];
            } else {
                $keys[] = $entity->$getter();
            }
        }

        return $keys;
    }
}
