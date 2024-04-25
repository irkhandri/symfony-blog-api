<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 *
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

//    /**
//     * @return Blog[] Returns an array of Blog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findBySearchQuery($searchQuery, $orderBy = null, $orderDirection = 'DESC') 
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->leftJoin('b.profile', 'p')
            ->leftJoin('b.tags', 't')
            ->andWhere('b.title LIKE :searchQuery OR t.name LIKE :searchQuery OR p.name LIKE :searchQuery')
            ->setParameter('searchQuery', '%'.$searchQuery.'%');

        if ($orderBy !== null)
            $queryBuilder->orderBy('b.' . $orderBy, $orderDirection);
        
        return $queryBuilder
            ->getQuery()
            ->getResult();
    }


    public function findBlogsByAuthorAndBlogId($blogId,  $authorId )
    { 
        $result = $this->createQueryBuilder('b')
            ->leftJoin('b.comments', 'c')
            ->leftJoin('c.profile', 'p')
            ->andWhere('p.id = :authorId AND b.id = :blogId')
            ->setParameter('authorId', $authorId)
            ->setParameter('blogId', $blogId)
            ->getQuery()
            ->getResult();

        return !empty($result);
    }

//    public function findOneBySomeField($value): ?Blog
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
