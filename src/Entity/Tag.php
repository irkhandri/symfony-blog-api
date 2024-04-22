<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\TagController;


#[ApiResource(
    // normalizationContext: ['groups' => ['read'] ],
        // denormalizationContext: ['groups' => ['write'] ],
    operations: [
    
        new Get(),
        new GetCollection (),
        new Post(
            name: 'add-tag',
            uriTemplate: 'blogs/{blogId}/add-tag',
            controller: TagController::class,
        ),
        
        // new Post(
        //     name: 'del-tag',
        //     uriTemplate: 'blogs/{blogId}/del-tag',
        //     controller: TagController::class,
        // )
        
    ]
)]
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['read'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Blog::class, mappedBy: 'tags')]
    private Collection $blogs;

    public function __construct()
    {
        $this->blogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $blog->addTag($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): static
    {
        if ($this->blogs->removeElement($blog)) {
            $blog->removeTag($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
