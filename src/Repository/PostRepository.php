<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function findWithCountStats(): array {
        return $this->createQueryBuilder('p')
            ->select([
                'p.id',
                'p.title',
                'p.text',
                'p.createdAt',
                'c.title as categoryTitle',
                'COUNT(DISTINCT l.id) as likesCount',
                'COUNT(DISTINCT com.id) as commentsCount'
            ])
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.userLikes', 'l')
            ->leftJoin('p.comments', 'com')
            ->groupBy('p.id')
            ->getQuery()->getResult();
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function findOneWithCountStats(int $id): array {
        return $this->createQueryBuilder('p')
            ->select([
                'p.id',
                'p.title',
                'p.text',
                'p.createdAt',
                'c.title as categoryTitle',
                'COUNT(DISTINCT l.id) as likesCount',
                'COUNT(DISTINCT com.id) as commentsCount'
            ])
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.userLikes', 'l')
            ->leftJoin('p.comments', 'com')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->groupBy('p.id')
            ->getQuery()->getOneOrNullResult();
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
