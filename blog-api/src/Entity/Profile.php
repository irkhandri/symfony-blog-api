<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\ProfileController;



// #[ApiResource(
//     normalizationContext: ['groups' => ['read'] ],
//     denormalizationContext: ['groups' => ['write'] ],
// )]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Patch(
            name: 'edit-profile',
            controller: ProfileController::class            
        )
        ],
    normalizationContext: ['groups' => ['read'] ],
    denormalizationContext: ['groups' => ['write'] ],
)]
#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $name = 'New User';

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $soc_facebook = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $soc_linkedin = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $imageUrl = 'https://static.vecteezy.com/system/resources/previews/019/879/186/original/user-icon-on-transparent-background-free-png.png';//null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read'])]
    private ?string $intro = null;

    #[ORM\Column(length: 2222, nullable: true)]
    #[Groups(['read'])]
    private ?string $bio = null;

    #[ORM\OneToMany(targetEntity: Blog::class, mappedBy: 'profile')]
    #[Groups(['read'])]
    private Collection $blogs;

    #[ORM\OneToMany(targetEntity: Interest::class, mappedBy: 'profile')]
    #[Groups(['read'])]
    private Collection $interests;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages_sent;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'recipient')]
    private Collection $messages_received;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'profile', orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->blogs = new ArrayCollection();
        $this->interests = new ArrayCollection();
        $this->messages_sent = new ArrayCollection();
        $this->messages_received = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getSocFacebook(): ?string
    {
        return $this->soc_facebook;
    }

    public function setSocFacebook(?string $soc_facebook): static
    {
        $this->soc_facebook = $soc_facebook;

        return $this;
    }

    public function getSocLinkedin(): ?string
    {
        return $this->soc_linkedin;
    }

    public function setSocLinkedin(?string $soc_linkedin): static
    {
        $this->soc_linkedin = $soc_linkedin;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): static
    {
        $this->intro = $intro;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * @return Collection<int, Blog>
     */
    public function getBlogs(): Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): static
    {
        if (!$this->blogs->contains($blog)) {
            $this->blogs->add($blog);
            $blog->setProfile($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): static
    {
        if ($this->blogs->removeElement($blog)) {
            // set the owning side to null (unless already changed)
            if ($blog->getProfile() === $this) {
                $blog->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Interest>
     */
    public function getInterests(): Collection
    {
        return $this->interests;
    }

    public function addInterest(Interest $interest): static
    {
        if (!$this->interests->contains($interest)) {
            $this->interests->add($interest);
            $interest->setProfile($this);
        }

        return $this;
    }

    public function removeInterest(Interest $interest): static
    {
        if ($this->interests->removeElement($interest)) {
            // set the owning side to null (unless already changed)
            if ($interest->getProfile() === $this) {
                $interest->setProfile(null);
            }
        }

        return $this;
    }



    public function __toString()
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesSent(): Collection
    {
        return $this->messages_sent;
    }

    public function addMessagesSent(Message $messagesSent): static
    {
        if (!$this->messages_sent->contains($messagesSent)) {
            $this->messages_sent->add($messagesSent);
            $messagesSent->setSender($this);
        }

        return $this;
    }

    public function removeMessagesSent(Message $messagesSent): static
    {
        if ($this->messages_sent->removeElement($messagesSent)) {
            // set the owning side to null (unless already changed)
            if ($messagesSent->getSender() === $this) {
                $messagesSent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesReceived(): Collection
    {
        return $this->messages_received;
    }

    public function addMessagesReceived(Message $messagesReceived): static
    {
        if (!$this->messages_received->contains($messagesReceived)) {
            $this->messages_received->add($messagesReceived);
            $messagesReceived->setRecipient($this);
        }

        return $this;
    }

    public function removeMessagesReceived(Message $messagesReceived): static
    {
        if ($this->messages_received->removeElement($messagesReceived)) {
            // set the owning side to null (unless already changed)
            if ($messagesReceived->getRecipient() === $this) {
                $messagesReceived->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setProfile($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProfile() === $this) {
                $comment->setProfile(null);
            }
        }

        return $this;
    }


}
