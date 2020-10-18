<?php


namespace App\Repository;

use App\Entity\GrupaObiektow;
use App\Entity\Obiekt;
use App\Entity\TypParametru;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

class ObiektRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Obiekt::class);
    }

    public function findInBounds($neLat, $neLng, $swLat, $swLng)
    {
        $qb = $this->createQueryBuilder('o');
        $query = $qb
            ->where('o.usuniety = false')
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

    public function findFromDate(string $dateFrom, GrupaObiektow $grupaObiektow) : ArrayCollection {
        $qb = $this->createQueryBuilder('o');
        $query = $qb->where('o.grupa = ?1')->andWhere('o.ostatniaAktualizacja > ?2')
            ->setParameter(1, $grupaObiektow)->setParameter(2, $dateFrom)->getQuery();
        return new ArrayCollection($query->getResult());
    }

    public function dtFindBy(array $criteria = [], array $orderBy = [], ?int $limit = null,
                             ?int $offset = null, ?string $search = '', ?int &$total = 0, ?int &$filtered = 0): Collection
    {
        $filtered = $total = $this->count($criteria);
        $grupa = $criteria['grupa'] ?? null;
        if (!$grupa instanceof GrupaObiektow) {
            throw new \InvalidArgumentException(
                "Wymagany parametr 'grupaObiektow' w \$criteria musi być typu: " . GrupaObiektow::class);
        }
        $typyParametrow = $grupa->getTypyParametrow()->getValues();
        $query = $this->createQueryBuilder('o');
        $i = $j = 1;
        foreach ($criteria as $column => $value) {
            $query->andWhere("o.$column = :val$j")->setParameter('val' . $j++, $value);
        }
        $searchFields = ['o.nazwa', 'o.symbol'];
        $select = ['DISTINCT o.nazwa as nazwa', 'o.symbol as symbol', 'o.id as obiektId, o.zdjecie as zdjecie, o.potwierdzony as potwierdzony'];
        foreach ($typyParametrow as $typ) {
            $query->leftJoin('o.parametry', "p$i", Expr\Join::WITH, "p$i.typ = :typ$i")
                ->setParameter("typ$i", $typ);
            $select[] = "p$i.value as param$i";
            $searchFields[] = "p$i.value";
            $i++;
        }
        if (strlen($search) > 0) {
            $query->andWhere('CONCAT(' . join(',', $searchFields) . ') LIKE :search')
                ->setParameter('search', "%$search%");
            $filtered = $query->select($query->expr()->countDistinct('o.id'))->getQuery()->getSingleScalarResult();
        }
        $query->select($select);
        foreach ($orderBy as $column => $direction) {
            $query->addOrderBy($column, $direction);
        }
        $query->setMaxResults($limit)->setFirstResult($offset);
        $result = new ArrayCollection($query->getQuery()->getResult());
        return $result->map(fn($row) => $this->formatRow($row, $typyParametrow));
    }

    private function formatRow($row, array $typyParametrow)
    {
        foreach ($row as $key => $value) {
            if(($pos = strpos($key, "param")) === false) continue;
            if(!$value instanceof \DateTime) continue;
            $i = (int) substr($key, $pos + 5);
            /** @var TypParametru $typ */
            $typ = $typyParametrow[$i-1];

            switch($typ->getTypDanych()) {
                case TypParametru::DATETIME:
                    $row[$key] = $value->format('d.m.Y H:i');
                    break;
                case TypParametru::DATE:
                    $row[$key] = $value->format('d.m.Y');
                    break;
                case TypParametru::TIME:
                    $row[$key] = $value->format('H:i');
                    break;
            }
        }
        return $row;
    }
}