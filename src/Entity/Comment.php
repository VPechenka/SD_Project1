<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Collection;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'comments')]
    private ?Comment $parent = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'comment_likes')]
    private ?Collection $userLikes = null;

    #[ORM\Column()]
    private ?DateTimeImmutable $createdAt;
}
