<?php
namespace App\Controller;

use App\Entity\Tag;
use App\Repository\BlogRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;



#[AsController]
class TagController extends AbstractController
{
    private $entityManager;
    private $tagRepository;
    private $blogRepository;
    public function __construct(TagRepository $tagRepo, BlogRepository $blogRepo ,EntityManagerInterface $em)
    {
        $this->blogRepository = $blogRepo;
        $this->tagRepository = $tagRepo;
        $this->entityManager = $em;
    }

    #[Route (
        name: 'add-tag',
        path: 'api/blogs/{blogId}/add-tag',
        methods: ["POST"]
    )]
    public function __invoke( $blogId , Request $request)
    {
        // $this->em->persist($tag);
        // $this->em->flush();

        // return $tag;


        $data = $request->getContent();
        

        $blog = $this->blogRepository->find($blogId);

        if (!$blog) {
            throw $this->createNotFoundException('Blog not found');
        }

        $decoded =  json_decode($data, true);

        // echo $decoded['name'];x

        $tagName = $decoded['name'] ;
        // echo  'TAG NAME  ' . '|'. $tagName . '|'; 
        if (!$tagName) {
            throw $this->createNotFoundException('Tagname not found');
        }

        $tag = $this->tagRepository->findOneBy(['name' => $tagName]);

        // echo "SECOND : " . $tag->getName();

        if (!$tag) {
            $tag = new Tag();
            $tag->setName($tagName);
            $tag->addBlog($blog);
        }

        $blog->addTag($tag);

        $this->entityManager->persist($blog);
        $this->entityManager->persist($tag);
        $this->entityManager->flush();
        // echo '!!!!!!!!!!!!!!!!!!!!!!!';

        return new JsonResponse(['message' => 'Tag added to blog successfully'], Response::HTTP_CREATED);
   
    }

    // #[Route('/blogs/create')]
    // public function create ()
    // {

    // }
}