<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Controller\RegistrationController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Controller\SecurityController;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    denormalizationContext: ['groups' => ['write']],
    operations: [
        new Post(
            name: 'registration',
            // uriTemplate: '/register',
            controller: RegistrationController::class,
        ),
        new Post(
            name: 'login',
            controller: RegistrationController::class,
            // controller: SecurityController::class
        ),
        new Post(
            name: 'logout',
            controller: RegistrationController::class
        )
    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['write'])]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Profile $profile = null;

    // #[ORM\OneToMany(targetEntity: ApiToken::class, mappedBy: 'user', orphanRemoval: true)]
    // private Collection $apiTokens;

    public function __construct()
    {
        $this->roles[] = 'ROLE_USER';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        // $this->roles[] = 'ROLE_USER';
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): static
    {
        // set the owning side of the relation if necessary
        if ($profile->getUser() !== $this) {
            $profile->setUser($this);
        }

        $this->profile = $profile;

        return $this;
    }

    // /**
    //  * @return Collection<int, ApiToken>
    //  */
    // public function getApiTokens(): Collection
    // {
    //     return $this->apiTokens;
    // }

    // public function addApiToken(ApiToken $apiToken): static
    // {
    //     if (!$this->apiTokens->contains($apiToken)) {
    //         $this->apiTokens->add($apiToken);
    //         $apiToken->setUser($this);
    //     }

    //     return $this;
    // }

    // public function removeApiToken(ApiToken $apiToken): static
    // {
    //     if ($this->apiTokens->removeElement($apiToken)) {
    //         // set the owning side to null (unless already changed)
    //         if ($apiToken->getUser() === $this) {
    //             $apiToken->setUser(null);
    //         }
    //     }

    //     return $this;
    // }
}
