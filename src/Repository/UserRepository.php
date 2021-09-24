<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function withoutCurrentUser($user)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id != :userId')
            ->setParameter('userId', $user);
    }

    /**
     * Search users
     *
     * @param [type] $term
     * @return void
     */
    public function search($term)
    {
        return $this->createQueryBuilder('u')
            ->where("SOUNDEX(u.firstname) LIKE SOUNDEX(:term) OR u.firstname LIKE :term")
            ->orWhere("SOUNDEX(u.lastname) LIKE SOUNDEX(:term) OR u.lastname LIKE :term")
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->execute();
    }

    /**
     * Autocomplete
     */
    public function autocomplete($term): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.id, u.firstname, u.lastname, u.avatar')
            ->where("SOUNDEX(u.firstname) LIKE SOUNDEX(:term) OR u.firstname LIKE :term")
            ->orWhere("SOUNDEX(u.lastname) LIKE SOUNDEX(:term) OR u.lastname LIKE :term")
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }
}
