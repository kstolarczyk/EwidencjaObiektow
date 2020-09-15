<?php


namespace App\Repository;

use App\Entity\GrupaObiektow;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class GrupaObiektowRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrupaObiektow::class);
    }

    public function findFromDate(string $dateFrom, User $user) : ArrayCollection {
        $query = $this->getGrupyByUser($user)->where('g.ostatniaAktualizacja > ?2')
        ->select('g')->setParameter(2, $dateFrom)->getQuery();
        return new ArrayCollection($query->getResult());
    }

    public function getGrupyByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('g')->innerJoin('g.users', 'u', 'WITH', 'u.id = ?1')->setParameter(1, $user->getId());
    }
}