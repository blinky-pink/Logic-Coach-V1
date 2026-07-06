<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[]
     */
    public function findHistoryForUser(User $user): array
    {
        return $this->createQueryBuilder('message')
            ->where('message.sender = :user')
            ->orWhere('message.receiver = :user')
            ->setParameter('user', $user)
            ->orderBy('message.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}