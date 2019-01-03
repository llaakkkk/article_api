<?php

namespace App\Api\Hydrator;

use App\Entity\Tag;

class TagHydrator extends AbstractHydrator
{
    /**
     * @var array
     */
    protected $defaultFields = ['id' => true, 'name' => true];

    /**
     * TagHydrator constructor.
     */
    public function __construct()
    {

        $this->schema = [
            'id'   => [
                'value' => function (Tag $entity) {
                    return $entity->getId();
                }
            ],
            'name' => [
                'value' => function (Tag $entity) {
                    return $entity->getName();
                }
            ]
        ];
    }
}