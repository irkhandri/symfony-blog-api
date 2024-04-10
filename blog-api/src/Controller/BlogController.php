<?php
namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Tag;
use App\Entity\Profile;

use App\Repository\BlogRepository;
use App\Repository\ProfileRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Regex;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;

use OpenApi\Annotations as OA;


#[AsController]
class BlogController extends AbstractController
{
    private $entityManager;
    private $profileRepo;
    private $blogRepository;
    private $tagRepository;
    private ?User $user ;

    public function __construct(
        ProfileRepository $profileRepo, 
        BlogRepository $blogRepo ,
        EntityManagerInterface $em,
        TagRepository $tagRepository,
        )
    {
        $this->blogRepository = $blogRepo;
        $this->profileRepo = $profileRepo;
        $this->tagRepository = $tagRepository;
        $this->entityManager = $em;
    }

    #[Route(
        name: 'edit-blog',
        path: 'api/blogs/{id}',
        methods: ['PUT']
    )]
    #[IsGranted('BLOG_OWNER', subject: 'blog')]
   
    public function edit (Blog $blog, $id, Request $request)
    {
        // $blog = $this->blogRepository->find($id);
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        
        // dd ($decoded);

        foreach ($decoded as $key => $value)
        {
            if ($key === 'tags')
            {
                $blog->removeAllTags();
                foreach ($value as $tag)
                {
                    $newTag = new Tag();
                    $newTag->setName($tag);
                    // dd($newTag);
                    $this->entityManager->persist($newTag);
                    $blog->addTag($newTag);
                    // dd($blog);
                }
                continue;
            }

            if (property_exists(Blog::class, $key) && $value !== null)
            {
                $setterMethod = 'set' . ucfirst($key);
                $blog->$setterMethod($value);
            }
        }

        // dd($blog);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'You are owner'], Response::HTTP_ACCEPTED);

    }



    #[Route(
        name: 'delete-blog',
        path: 'api/blogs/{id}',
        methods: ['DELETE']
    )]
    #[IsGranted('BLOG_OWNER', subject: 'blog')]
    public function delete (Blog $blog, $id)
    {
        $this->entityManager->remove($blog);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'DEleted successfully'], Response::HTTP_ACCEPTED);
    }



    #[Route(
        name: 'post-blog',
        path: 'api/blogs',
        methods: ['POST']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function post ( Request $request)
    {
        $blog = new Blog ();
        $data = json_decode($request->getContent(), true);

        foreach ($data as $key => $value)
        {
            if (property_exists(Blog::class, $key) && $value !== null)
            {
                $setterMethod = 'set' . ucfirst($key);
                $blog->$setterMethod($value);
            }
        }

        $this->user = $this->getUser();
        $blog->setProfile($this->user->getProfile());


        // dd($this->user->getProfile());


        // $blog->setProfile();

        // dd ($blog);

        $this->entityManager->persist($blog);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Added successfully'], Response::HTTP_ACCEPTED);
    }
    


}