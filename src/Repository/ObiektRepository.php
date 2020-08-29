<?php


namespace App\Repository;

use App\Entity\GrupaObiektow;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\Expr;

class ObiektRepository extends BaseRepository
{
    public function findInBounds($neLat, $neLng, $swLat, $swLng)
    {
        $qb = $this->createQueryBuilder('o');
        $query = $qb
            ->andWhere($qb->expr()->between('o.dlugosc', ':swLng', ':neLng'))
            ->andWhere($qb->expr()->between('o.szerokosc', ':swLat', ':neLat'))
            ->setParameters([
                'swLng' => $swLng,
                'swLat' => $swLat,
                'neLng' => $neLng,
                'neLat' => $neLat
            ])->getQuery();
        return $query->getResult();
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
        $select = ['DISTINCT o.nazwa as nazwa', 'o.symbol as symbol', 'o.id as id, o.zdjecie as zdjecie'];
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