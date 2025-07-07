<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'subcomments')]
    private ?Comment $parent = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'comment_likes')]
    private ArrayCollection $userLikes;

    #[ORM\Column()]
    private ?DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $isDeleted = false;

    public function __construct()
    {
        $this->userLikes = new ArrayCollection();
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function setUser(user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function setCreatedAtNow(): static
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }

    public function setParent(Comment $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function addLike(User $user): static
    {
        $this->userLikes->add($user);

        return $this;
    }
}
