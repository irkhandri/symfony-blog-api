<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\CommentController;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ApiResource(
     normalizationContext: ['groups' => ['read'] ],
     denormalizationContext: ['groups' => ['write'] ],
    operations: [
        new Get(
            controller: CommentController::class,
            name: 'get-comments'
        ),
        new Post (
            controller: CommentController::class,
            name: 'post-comment'
        )

    ]
)]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read', "comment"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups("read")]
    private ?Profile $profile = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    #[Groups("read")]
    private ?Blog $blog = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("read")]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(length: 2047, nullable: true)]
    #[Groups("read")]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("read")]
    private ?string $rate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): static
    {
        $this->blog = $blog;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(?string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }
}
