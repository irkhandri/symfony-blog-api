<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

use App\Controller\BlogController;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(  
    // normalizationContext: ['groups' => ['read'] ],
    denormalizationContext: ['groups' => ['write'] ],
    operations: [
        new GetCollection(),
        new Get(),
        new Put(
            securityPostDenormalize: "is_granted('BLOG_OWNER')  ", 
            securityPostDenormalizeMessage: 'Sorry, but you are not the actual book owner.',
            controller: BlogController::class,
            name:'edit-blog'
        ),
        new Delete(
            security: "is_granted('BLOG_OWNER')", 
            securityMessage: 'Only owners can delete blogs.',
            controller: BlogController::class,
            name:'delete-blog'
        ),
        new Post(
            securityMessage: 'Only authenticated user can post blogs.',
            controller: BlogController::class,
            name:'post-blog',
            // uriTemplate: 'ap'
        )

    ]
)]
#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // #[Groups(['read'])]
    private ?int $id = null;

    #[Groups(['write'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['write'])]  
    #[ORM\Column(length: 2222, nullable: true)]
    private ?string $description = null;

    #[Groups(['write'])]
    #[ORM\Column(length: 1111, nullable: true)]   
    private ?string $imageUrl = 'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_1280.jpg';

    #[Groups(['write'])]
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'blogs')]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'blogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'blog', orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function removeAllTags()
    {
        foreach($this->tags as $tag)
        {
            $this->tags->removeElement($tag);
        }
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
            $comment->setBlog($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBlog() === $this) {
                $comment->setBlog(null);
            }
        }

        return $this;
    }

   
}
