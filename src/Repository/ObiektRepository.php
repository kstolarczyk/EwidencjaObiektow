<?php


namespace App\Repository;

use App\Entity\GrupaObiektow;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\Expr;

class ObiektRepository extends BaseRepository
{
    public function findAll(): Collection
    {
        $return = parent::findAll();
        if ($return instanceof Collection) {
            return $return;
        }
        return new ArrayCollection($return);
    }

    public function dtFindBy(array $criteria = [], array $orderBy = [], ?int $limit = null, ?int $offset = null, ?string $search = '', ?int &$total = 0, ?int &$filtered = 0): Collection
    {
        $filtered = $total = $this->count($criteria);
        $grupa = $criteria['grupa'] ?? null;
        if (!$grupa instanceof GrupaObiektow) {
            throw new \InvalidArgumentException("Wymagany parametr 'grupaObiektow' w \$criteria musi być typu: " . GrupaObiektow::class);
        }
        $typyParametrow = $grupa->getTypyParametrow();
        $query = $this->createQueryBuilder('o');
        $i = $j = 1;
        foreach ($criteria as $column => $value) {
            $query->andWhere("o.$column = :val$j")->setParameter('val' . $j++, $value);
        }
        $searchFields = ['o.nazwa', 'o.symbol'];
        $select = ['DISTINCT o.nazwa as nazwa', 'o.symbol as symbol', 'o.id as id'];
        foreach ($typyParametrow as $typ) {
            $query->leftJoin('o.parametry', "p$i", Expr\Join::WITH, "p$i.typ = :typ$i")
                ->setParameter("typ$i", $typ);
            $select[] = "p$i.value as param$i";
            $searchFields[] = "p$i.value";
            $i++;
        }
        if (strlen($search) > 0) {
            $query->andWhere('CONCAT(' . join(',', $searchFields) . ') LIKE :search')->setParameter('search', "%$search%");
            $filtered = $query->select($query->expr()->countDistinct('o.id'))->getQuery()->getSingleScalarResult();
        }
        $query->select($select);
        foreach ($orderBy as $column => $direction) {
            $query->addOrderBy($column, $direction);
        }
        $query->setMaxResults($limit)->setFirstResult($offset);
        return new ArrayCollection($query->getQuery()->getResult());
    }
}