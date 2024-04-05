<?php
namespace App\Controller;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use App\Repository\ProfileRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Regex;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[AsController]
class BlogController extends AbstractController
{
    private $entityManager;
    private $profileRepo;
    private $blogRepository;
    private $tagRepository;

    public function __construct(
        ProfileRepository $profileRepo, 
        BlogRepository $blogRepo ,
        EntityManagerInterface $em,
        TagRepository $tagRepository
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
        
        dd ($decoded);

        return new JsonResponse(['message' => 'You are owner'], Response::HTTP_ACCEPTED);

    }



}