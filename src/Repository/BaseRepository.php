<?php


namespace App\Repository;


use App\Entity\Searchable;
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

    public function dtFindBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $search = '', &$total = 0): Collection
    {
        $total = $this->count($criteria);
        $query = $this->createQueryBuilder('e');
        foreach ($criteria as $column => $value) {
            $query->andWhere("e.$column = :$column")->setParameter($column, $value);
        }

        if (strlen($search) > 0 && in_array(Searchable::class, class_implements($this->_entityName))) {
            $searchableFields = forward_static_call([$this->_entityName, 'getSearchableProperties']);
            $fields = array_map(function ($field) {
                return 'e.' . $field;
            }, $searchableFields);
            $query->andWhere('CONCAT(' . join(',', $fields) . ') LIKE :search')->setParameter('search', "%$search%");
        }
        foreach ($orderBy as $column => $direction) {
            $query->addOrderBy('e.' . $column, $direction);
        }
        $query->setMaxResults($limit)->setFirstResult($offset);

        return new ArrayCollection($query->getQuery()->getResult());
    }

}