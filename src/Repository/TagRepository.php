<?php

namespace App\Repository;

use App\Entity\Tag;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends AbstractRepository
{

    public function findTagByName($name, $articleId): ?Tag
    {
        return $this->createQueryBuilder('t')
                    ->select('t')
                    ->leftJoin('t.articles', 'a')
                    ->andWhere('t.name = :name')
                    ->andWhere('a.id = :articleId')
                    ->setParameter('name', $name)
                    ->setParameter('articleId', $articleId)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
