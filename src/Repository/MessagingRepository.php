<?php

namespace App\Repository;

use App\Entity\Messaging;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Messaging|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messaging|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messaging[]    findAll()
 * @method Messaging[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messaging::class);
    }

    public function findByAuthorOrParticipants(?User $id): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.participants', 'p')
            ->where('m.author = :id')
            ->orWhere('p.id = :id')
            ->orderBy('m.createdAt', 'DESC')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findByAuthorAndParticipant($user, $currentUser)
    {
        return $this->createQueryBuilder('p')
            ->join('p.participants', 'Participants')
            ->where('p.author = :currentUser AND Participants.id = :user')
            ->orWhere('Participants.id = :currentUser AND p.author = :user')
            ->setParameters(['user' => $user, 'currentUser' => $currentUser])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
