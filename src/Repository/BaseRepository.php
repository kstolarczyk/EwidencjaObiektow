<?php


namespace App\Repository;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    public function findAll(): Collection
    {
        return new ArrayCollection(parent::findAll());
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): Collection
    {
        return new ArrayCollection(parent::findBy($criteria, $orderBy, $limit, $offset));
    }

}