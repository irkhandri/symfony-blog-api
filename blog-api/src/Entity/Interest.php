<?php

namespace App\Entity;

use App\Repository\InterestRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;

use App\Controller\InterestController;

#[ApiResource(
    denormalizationContext:['groups' => ['write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            securityMessage: 'Only authenticated user can add interests.',
            controller: InterestController::class,
            name: 'post-interest'
        ),
        new Put(
            securityPostDenormalizeMessage: 'Sorry, but you are not the actual interest owner.',
            controller: InterestController::class,
            name:'edit-interest'
        ),
        new Delete (
            // security: "is_granted()"
            securityMessage: 'Only owners can delete interests.',
            controller: InterestController::class,
            name:'delete-interest'
        )
    ]

)]
#[ORM\Entity(repositoryClass: InterestRepository::class)]
class Interest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[Groups(['write'])]
    #[ORM\Column(length: 2047, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'interests')]
    private ?Profile $profile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }
}
