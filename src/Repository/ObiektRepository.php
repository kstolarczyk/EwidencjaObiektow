<?php


namespace App\Repository;

use App\Entity\GrupaObiektow;
use App\Entity\Searchable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ObiektRepository extends BaseRepository
{
    public function dtFindBy(array $criteria = [], array $orderBy = [], ?int $limit = null, ?int $offset = null, ?string $search = '', ?int &$total = 0, ?int &$filtered = 0): Collection
    {
        $filtered = $total = $this->count($criteria);
        $grupa = $criteria['grupa'] ?? null;
        if (!$grupa instanceof GrupaObiektow) {
            throw new \InvalidArgumentException("Wymagany parametr 'grupaObiektow' w \$criteria musi być typu: " . GrupaObiektow::class);
        }
        $typyParametrow = $grupa->getTypyParametrow();
        $query = $this->createQueryBuilder('o')->innerJoin('o.parametry', 'p');
        $i = 1;
        foreach ($criteria as $column => $value) {
            $query->andWhere("o.$column = :val$i")->setParameter('val' . $i++, $value);
        }
        $i = 1;
        if (strlen($search) > 0 && in_array(Searchable::class, class_implements($this->_entityName))) {
            $searchableFields = forward_static_call([$this->_entityName, 'getSearchableProperties']);
            $fields = array_map(function ($field) {
                return 'o.' . $field;
            }, $searchableFields);
            $query->andWhere('CONCAT(' . join(',', $fields) . ') LIKE :search')->setParameter('search', "%$search%");
            $filtered = $query->select($query->expr()->countDistinct('o.id'))->getQuery()->getSingleScalarResult();
        }
        $query->groupBy('o.id');
        $query->select('o.nazwa as nazwa', 'o.symbol as symbol', 'o.id as id');
        foreach ($typyParametrow as $typ) {
            $query->addSelect("max(case when p.typ = :typ$i then p.value else '' end) as p$i")->setParameter('typ' . $i++, $typ);
        }

        foreach ($orderBy as $column => $direction) {
            $query->addOrderBy($column, $direction);
        }
        $query->setMaxResults($limit)->setFirstResult($offset);
        return new ArrayCollection($query->getQuery()->getResult());
    }
}