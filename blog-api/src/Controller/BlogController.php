<?php
namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Tag;
use App\Entity\Profile;

use App\Controller\CommentController;

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
use App\Repository\CommentRepository;
use OpenApi\Annotations as OA;


#[AsController]
class BlogController extends AbstractController
{
    private $entityManager;
    private $profileRepo;
    private $blogRepository;
    private $tagRepository;
    private ?User $user ;
    private $commentRepository;

    public function __construct(
        ProfileRepository $profileRepo, 
        BlogRepository $blogRepo ,
        EntityManagerInterface $em,
        TagRepository $tagRepository,
        CommentRepository $cr,
        )
    {
        $this->blogRepository = $blogRepo;
        $this->profileRepo = $profileRepo;
        $this->tagRepository = $tagRepository;
        $this->entityManager = $em;
        $this->commentRepository = $cr;
    }


    public static function serializeBlog ($blog)
    {
        
        $tags = [];
        foreach ($blog->getTags() as $tag)
        {
            $tags[] = [
                'name' => $tag->getName()
            ];
        }

        $jsonContent = [
            'id' => $blog->getId(),
            'title' => $blog->getTitle(),
            'description' => $blog->getDescription(),
            'imageUrl' => $blog->getImageUrl(),
            'profile' => [
                'id' => $blog->getProfile()->getId(),
                'name' => $blog->getProfile()->getName()
            ],
            'tags' => $tags,
            'comments' => [],
            'likes' => $blog->getLikes(),
            'dislikes' => $blog->getDislikes()
        ];

        return $jsonContent;

        
        // dd($jsonContent);

        
    }


    #[Route(
        name: 'post-blog',
        path: 'api/blogs',
        methods: ['POST']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function post (Request $request)
    {
        $blog = new Blog ();
        $data = json_decode($request->getContent(), true);

        
        // $token = $request->headers->get('x-api-token');

        // return new  JsonResponse(['message' => $token ], Response::HTTP_ACCEPTED);

        $blog->setTitle($data['title']);
        $blog->setDescription($data['description']);
        $data['imageUrl'] == '' ? null :  $blog->setImageUrl($data['imageUrl']);
        // $blog->setImageUrl($data['imageUrl']);



        // foreach ($data as $key => $value)
        // {
        //     if (property_exists(Blog::class, $key) && $value !== null)
        //     {
        //         $setterMethod = 'set' . ucfirst($key);
        //         $blog->$setterMethod($value);
        //     }
        // }


        $this->user = $this->getUser();
        $blog->setProfile($this->user->getProfile());


        $this->entityManager->persist($blog);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Added successfully'], Response::HTTP_ACCEPTED);
    }




    #[Route(
        name: 'edit-blog',
        path: 'api/blogs/{id}',
        methods: ['PATCH']
    )]
    #[IsGranted('BLOG_OWNER', subject: 'blog')]
   
    public function edit (Blog $blog, $id, Request $request)
    {
        $blog = $this->blogRepository->find($id);
        $data = $request->getContent();
        $decoded = json_decode($data, true);
    
        foreach ($decoded as $key => $value)
        {
            // if ($key === 'tags')
            // {
            //     $blog->removeAllTags();
            //     foreach ($value as $tag)
            //     {
            //         $newTag = new Tag();
            //         $newTag->setName($tag);
            //         // dd($newTag);
            //         $this->entityManager->persist($newTag);
            //         $blog->addTag($newTag);
            //         // dd($blog);
            //     }
            //     break;
            // }

        }
        $blog->setTitle($decoded['title']);
        $blog->setDescription($decoded['description']);
        $decoded['imageUrl'] == '' ? null :  $blog->setImageUrl($decoded['imageUrl']);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'You are owner'], Response::HTTP_ACCEPTED);
    }


    // #[Route(
    //     name: 'search',
    //     path: 'api/blogs/query',
    //     methods:['GET']
    // )]
    

    #[Route(
        name: 'get-blogsCollection',
        path: 'api/blogs',
        methods:['GET']
    )]
    public function getCollections (Request $request)
    {
        $query = trim($request->query->get("query"), '"' );

        $blogs = !$query  ? $this->blogRepository->findBy([], ['likesCounter' => 'DESC']) : $this->blogRepository->findBySearchQuery($query, "likesCounter");

        $jsonContent = [];

        foreach ($blogs as $blog) 
        {
            $jsonContent[] = $this->serializeBlog($blog);

        }        
        return new JsonResponse($jsonContent);

    }

    #[Route(
        name: 'get-blog',
        path: 'api/blogs/{id}',
        methods: ['GET']
    )]
    public function get ($id) 
    {
        $blog = $this->blogRepository->find($id);
        //dd($blog);
        // $comments = $this->commentRepository->findBy(['blog' => $blog]);
        if ($blog){
            $comments = $blog->getComments();

            $jsonContent = [];
            $data = $this->serializeBlog($blog);
            foreach ($comments as $comment){
                $data['comments'][] = [
                    'id' => $comment->getId(),
                    'description' => $comment->getDescription(),
                    'rate' => $comment->getRate(),
                    'profile' => [
                        'id' => $comment->getProfile()->getId(),
                        'name' => $comment->getProfile()->getName(),
                        'imageUrl' => $comment->getProfile()->getImageUrl()
                    ],
                    'created' => $comment->getCreated()
                ];
            }
    
            // dd($data);
    
    
            return new JsonResponse($data);
        }
        
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



    




    


}