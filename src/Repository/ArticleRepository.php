<?php

namespace App\Repository;

use App\Entity\Article;
use App\Util\Arrays;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends AbstractRepository
{
    /**
     * @param $articles
     */
    public function fetchTags($articles): void
    {
        if (count($articles) === 0) {
            return;
        }

        $qb = $this->createQueryBuilder('a')
                   ->select('PARTIAL a.{id}, t')
                   ->leftJoin('a.tags', 't')
                   ->where('a.id IN (:articleIds)')
                   ->setParameter('articleIds', Arrays::getKeysArray($articles, 'id'));

        $qb->getQuery()->getResult();
    }

    public function fetchByQueryParams(array $params)
    {
        $qb = $this->createQueryBuilder('a')
                   ->select('a')
                   ->setFirstResult($params['offset'])
                   ->setMaxResults($params['limit']);

        if ($params['sort'] === 'newest') {
            $qb->orderBy('a.createdAt', 'desc');
        } else {
            $qb->orderBy('a.createdAt', 'asc');
        }

        $articles = $qb->getQuery()->getResult();

        if ($params['fetchTags']) {
            $this->fetchTags($articles);
        }

        return $articles;
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByQueryParams(): int
    {
        $qb = $this->createQueryBuilder('a');

        $qb->select('COUNT(DISTINCT a.id)');

        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }

}
