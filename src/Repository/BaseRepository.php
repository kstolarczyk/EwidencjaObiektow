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
        $return = parent::findAll();
        if ($return instanceof Collection) {
            return $return;
        }
        return new ArrayCollection($return);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): Collection
    {
        $return = parent::findBy($criteria, $orderBy, $limit, $offset);
        if ($return instanceof Collection) {
            return $return;
        }
        return new ArrayCollection($return);

    }

    public function dtFindBy(array $criteria = [], array $orderBy = [], ?int $limit = null, ?int $offset = null, ?string $search = '', ?int &$total = 0): Collection
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