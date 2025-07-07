<?php

namespace App\Entity;

use App\Repository\PostRepository;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id')]
    private ?Category $category = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'post_likes')]
    private ArrayCollection $userLikes;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    private ArrayCollection $comments;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->userLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function setCreatedAtNow(): static
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }

    public function getUrl(): ?string
    {
        return "http://" . $_SERVER['HTTP_HOST'] . '/post/' . $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setLikeDislike(User $user): static
    {
        if ($this->userLikes->contains($user)) {
            $this->userLikes->removeUser($user);
        }
        $this->userLikes->add($user);

        return $this;
    }
}
