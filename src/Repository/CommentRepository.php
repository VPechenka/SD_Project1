<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @return Comment[] Returns an array of Comment objects
     */
    public function findByPostId(int $postId): array
    {
        $comments = $this->createQueryBuilder("c")
            ->select([
                "c.id",
                "c.text",
                "c.createdAt",
                "c.isDeleted",
                "u.username as username",
                "u.id as userId",
                "u.isBlocked as userIsBlocked",
                "COUNT(DISTINCT l.id) as likesCount",
                "IDENTITY(c.parent) as parentId"
            ])
            ->leftJoin("c.user", "u")
            ->leftJoin("c.userLikes", "l")
            ->where("c.post = :postId")
            ->setParameter("postId", $postId)
            ->groupBy("c.id", "u.username")
            ->getQuery()
            ->getResult();

        $tree = [];

        foreach ($comments as $comment) {
            $tree[$comment["id"]] = $comment;
            $tree[$comment["id"]]["children"] = [];

            if (!empty($comment["parentId"])) {
                $tree[$comment["parentId"]]["children"][] = $comment["id"];
            }
        }

        return $tree;
    }
}
