<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Find messages in conversations
     *
     * @param $id
     *
     * @return int|mixed|string
     */
    public function findByConversation($id)
    {
        return $this->createQueryBuilder('p')
            ->join('p.messaging', 'Conversation')
            ->andWhere('Conversation.id = :id')
            ->setParameter('id', $id)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count unread messages in conversations
     *
     * @param $user
     *
     * @return int|mixed|string
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countMessages($user)
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m)')
            ->join('m.messaging', 'C')
            ->andWhere(':user MEMBER OF C.participants')
            ->andWhere(':user NOT MEMBER OF m.readBy')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMessages(int $id)
    {
        return $this->createQueryBuilder('m')
            ->select('CONCAT(a.lastname, \' \',  a.firstname) AS author_username', 'a.avatar, m.content, m.createdAt, m.id, CONCAT(p.lastname, \' \',  p.firstname) AS contributors_username', 'a.avatar')
            ->innerJoin('m.messaging', 'msg')
            ->innerJoin('m.author', 'a')
            ->innerJoin('msg.participants', 'p')
            ->andWhere('msg.id = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
