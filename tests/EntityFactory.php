<?php

namespace App\Tests;

use App\Entity\AbstractEntity;
use App\Entity\Article;
use App\Entity\Tag;

class EntityFactory
{

    /**
     * @param array $data
     *
     * @return Tag
     */
    public function getTag(array $data = []): Tag
    {
        $data = array_merge(
            [
                'id'   => 1,
                'name' => 'Article Tag',
            ],
            $data
        );

        /** @var Tag $entity */
        $entity = $this->createEntity(Tag::class, $data);

        return $entity;
    }

    /**
     * @param array $data
     *
     * @return Article
     */
    public function getArticle(array $data = []): Article
    {
        $data = array_merge(
            [
                'id'          => 1,
                'title'       => 'Article',
                'description' => 'Article Description',
                'authorName'  => 'Article Author',
                'createdAt'   => new \DateTime()
            ],
            $data
        );

        /** @var Article $entity */
        $entity = $this->createEntity(Article::class, $data);

        return $entity;
    }

    /**
     * @param string $class
     * @param array  $data
     *
     * @return AbstractEntity
     */
    protected function createEntity(string $class, array $data): AbstractEntity
    {
        /** @var AbstractEntity $entity */
        $entity = new $class();
        $entity->populate($data);

        if (isset($data['id'])) {
            $ref  = new \ReflectionObject($entity);
            $prop = $ref->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($entity, $data['id']);
        }

        return $entity;
    }

}