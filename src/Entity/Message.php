<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\MessageController;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    denormalizationContext: ['groups' => ['write']],
    operations: [
        new Post(
            security:  "is_granted('IS_AUTHENTICATED_FULLY')",
            securityMessage: "LOGINFIRST",
            controller: MessageController::class,
            name: "post-message",
            // uriTemplate: 'messages'
        ),  
        new Get(
            controller: MessageController::class,
            name: 'inbox-messages'
        ),
        new Get(
            controller: MessageController::class,
            name: 'outbox-messages'
        ),
        new Get(
            controller: MessageController::class,
            name: 'current-message'
        ),
        new Get(
            controller: MessageController::class,
            name: 'count-unread'
        )
    ]
)]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['write'])]
    #[ORM\ManyToOne(inversedBy: 'messages_sent')]
    private ?Profile $sender = null;

    #[Groups(['write'])]
    #[ORM\ManyToOne(inversedBy: 'messages_received')]
    private ?Profile $recipient = null;

    #[Groups(['write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subject = null;

    #[Groups(['write'])]
    #[ORM\Column(length: 2047, nullable: true)]
    private ?string $text = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_read = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?Profile
    {
        return $this->sender;
    }

    public function setSender(?Profile $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?Profile
    {
        return $this->recipient;
    }

    public function setRecipient(?Profile $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
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

    public function isIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(?bool $is_read): static
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(?\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }
}
