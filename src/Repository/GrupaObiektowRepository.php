<?php


namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

class GrupaObiektowRepository extends BaseRepository
{
    public function getGrupyByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('g')->innerJoin('g.users', 'u', 'WITH', 'u.id = ?1')->setParameter(1, $user->getId());
    }
}