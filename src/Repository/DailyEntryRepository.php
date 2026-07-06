<?php

namespace App\Repository;

use App\Entity\DailyEntry;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DailyEntry>
 */
class DailyEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailyEntry::class);
    }

    public function findLatestForUser(User $user): ?DailyEntry
    {
        return $this->createQueryBuilder('dailyEntry')
            ->where('dailyEntry.user = :user')
            ->setParameter('user', $user)
            ->orderBy('dailyEntry.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}