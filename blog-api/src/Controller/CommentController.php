<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CommentController extends AbstractController
{

    private $blogRepository;
    private $commentRepository;
    private $entityManager;
    private User $user;

    private $encoder ; //= [ new JsonEncoder()];
    private $normalizer; // = [new ObjectNormalizer()];
    private $serializer;

    public function __construct(
        EntityManagerInterface $em, 
        CommentRepository $cr, 
        BlogRepository $br,
    )
    {
        $this->entityManager = $em;
        $this->commentRepository = $cr;
        $this->blogRepository = $br;
        $this->encoder = [ new JsonEncoder()];
        $this->normalizer = [new ObjectNormalizer()];
        $this->serializer = new Serializer($this->normalizer, $this->encoder);
    }


    // public function b

    #[Route('/api/blogs/{id}/comments', name: 'get-comments', methods:['GET'])]
    public function blog(Blog $blog, $id)
    {
        // $blog = $this->blogRepository->find($id);
        $comments = $blog->getComments();

        // $jsonContent = $this->serializer->serialize($comments, 'json', ['groups' => 'comment']);
        foreach ($comments as $comment){
            $jsonContent[] = [
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
        // dd($comments);
        return new JsonResponse($jsonContent);
    }

    #[Route(
        name: 'post-comment',
        path: 'api/blogs/{id}/create-comment',
        methods: ['POST']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function post (Blog $blog, $id, Request $request) 
    {
        $this->user = $this->getUser();
    
        $newComment = new Comment();
        $newComment->setProfile($this->user->getProfile());

        $data = json_decode($request->getContent(), true);
        
        $newComment->setDescription($data['description']);
        $newComment->setRate($data['rate']);
        
        $newComment->setBlog($blog);
        $blog->addComment($newComment);

        $newComment->setCreated(new \DateTime('now'));


        // dd($newComment);
        $this->entityManager->persist($newComment);
        $this->entityManager->flush();

        $this->user->getProfile()->addComment($newComment);

        return new JsonResponse(['message' => 'Comment added'], Response::HTTP_ACCEPTED);


    }
}
