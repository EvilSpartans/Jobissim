<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Find post by category
     *
     * @param [type] $id
     * @return void
     */
    public function findPostByCategory($id)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->join('p.subcategory', 'SubCategories')
            ->andWhere('SubCategories.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findPostByCategoryAndSubcategory($cat, $sub, $id)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->join('p.subcategory', 'SubCategories')
            ->andWhere('SubCategories.id = :sub')
            ->join('p.category', 'Categories')
            ->andWhere('Categories.id = :cat')
            ->andWhere('p.id != :id')
            ->setParameter('cat', $cat)
            ->setParameter('sub', $sub)
            ->setParameter('id', $id)
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find user's post
     *
     * @param [type] $id
     * @return void
     */
    public function findPostByUser($id)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->andWhere('p.user = :id')
            ->setParameter('id', $id)
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find post liked by user
     *
     * @param [type] $id
     * @param [type] $user
     * @return void
     */
    public function findPostByLikeAndCategory($id, $user)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->join('p.subcategory', 'SubCategories')
            ->andWhere('SubCategories.id = :id')
            ->join('p.likes', 'liked')
            ->andWhere('liked.user = :user')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * Search posts
     *
     * @param [type] $term
     * @return void
     */
    public function search($term)
    {
        return $this->createQueryBuilder('p')
            ->where("SOUNDEX(p.title) LIKE SOUNDEX(:term) OR p.title LIKE :term")
            ->orWhere("SOUNDEX(p.hashtag) LIKE SOUNDEX(:term) OR p.hashtag LIKE :term")
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
            ->select('u.id, u.title, u.image, u.hashtag')
            ->where("SOUNDEX(u.title) LIKE SOUNDEX(:term) OR u.title LIKE :term")
            ->orWhere("SOUNDEX(u.hashtag) LIKE SOUNDEX(:term) OR u.hashtag LIKE :term")
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }
}
