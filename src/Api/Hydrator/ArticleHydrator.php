<?php

namespace App\Api\Hydrator;

use App\Entity\Article;

class ArticleHydrator extends AbstractHydrator
{
    const CREATED_RESOURCE_FIELDS = [
        'id'          => true,
        'title'       => true,
        'description' => true,
        'authorName'  => true,
        'createdAt'   => true,
        'tags'        => true,
    ];

    /**
     * @var array
     */
    protected $defaultFields = ['id' => true, 'title' => true, 'createdAt' => true];

    /**
     * @var TagHydrator
     */
    private $tagHydrator;

    /**
     * ArticleHydrator constructor.
     */
    public function __construct()
    {

        $this->schema = [
            'id'          => [
                'value' => function (Article $entity) {
                    return $entity->getId();
                }
            ],
            'title'       => [
                'value' => function (Article $entity) {
                    return $entity->getTitle();
                }
            ],
            'description' => [
                'value' => function (Article $entity) {
                    return $entity->getDescription();
                }
            ],
            'authorName'  => [
                'value' => function (Article $entity) {
                    return $entity->getAuthorName();
                }
            ],
            'createdAt'   => [
                'value' => function (Article $entity) {
                    return $entity->getCreatedAt()->format('c');
                }
            ],
            'tags'        => [
                'hydrator' => function () {
                    return $this->tagHydrator;
                },
                'value'    => function (Article $entity, array $fields, int $hydrationLevel) {
                    return $this->tagHydrator->hydrateCollection(
                        $entity->getTags(),
                        $fields,
                        $hydrationLevel
                    );
                }
            ],
            'tagsCount'   => [
                'value' => function (Article $entity) {
                    return count($entity->getTags());
                }
            ]
        ];
    }

    /**
     * @param TagHydrator $tagHydrator
     */
    public function setTagHydrator(TagHydrator $tagHydrator)
    {
        $this->tagHydrator = $tagHydrator;
    }
}