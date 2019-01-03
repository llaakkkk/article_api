<?php

namespace App\Entity;

interface EntityInterface
{
    /**
     * @param array $data
     *
     * @return EntityInterface
     */
    public function populate(array $data);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function getOwnClassName();

    /**
     * @return string
     */
    public function getOwnClassNameHuman();
}
