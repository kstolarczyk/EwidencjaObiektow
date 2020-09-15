<?php


namespace App\Repository;


use App\Entity\TypParametru;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TypParametruRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypParametru::class);
    }

    public function findFromDate(string $dateFrom, User $user) : ArrayCollection {
        $query = $this->createQueryBuilder('tp')->join('tp.grupyObiektow', 'g')
            ->join('g.users', 'u', 'WITH', 'u.id = ?1')
            ->setParameter(1, $user->getId())->getQuery();
        return new ArrayCollection($query->getResult());
    }
}