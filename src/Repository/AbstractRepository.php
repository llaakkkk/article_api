<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\EntityInterface;

abstract class AbstractRepository extends EntityRepository
{
    /**
     * @param $id
     *
     * @return bool|EntityInterface
     */
    public function getReference($id)
    {
        return $this->getEntityManager()->getReference($this->getEntityName(), $id);
    }

    /**
     * @param EntityInterface $entity
     * @param bool            $flush
     */
    public function store(EntityInterface $entity, $flush = false)
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
