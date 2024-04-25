<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Json;

class CommentController extends AbstractController
{

    private $blogRepository;
    private $commentRepository;
    private $entityManager;
    private $userRepository;

    private $encoder ; //= [ new JsonEncoder()];
    private $normalizer; // = [new ObjectNormalizer()];
    private $serializer;

    public function __construct(
        EntityManagerInterface $em, 
        CommentRepository $cr, 
        BlogRepository $br,
        UserRepository $ur
    )
    {
        $this->entityManager = $em;
        $this->commentRepository = $cr;
        $this->blogRepository = $br;
        $this->userRepository = $ur;
        // $this->encoder = [ new JsonEncoder()];
        // $this->normalizer = [new ObjectNormalizer()];
        // $this->serializer = new Serializer($this->normalizer, $this->encoder);
    }



    // #[Route('/api/blogs/{id}/comments', name: 'get-comments', methods:['GET'])]
    public static function blogsComments(Blog $blog, $id)
    {
        $comments = $blog->getComments();

        $jsonContent = [];
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
        // return new JsonResponse($jsonContent);
        return $jsonContent;
    }


    #[Route(
        name:'already-commented',
        path: 'api/commented/{id}',
        methods: ['GET']
    )]
    public function commented ($id, Request $request)
    {
        $token = $request->headers->get('x-api-token');
        $userId = Utils::tokenToUserId($token);
        // $user = $this->userRepository->find($userId);

        // $profile = $user->getProfile();
        $commented = $this->blogRepository->findBlogsByAuthorAndBlogId($id, $userId);

        return new JsonResponse((int)$commented);

    }




    #[Route(
        name: 'post-comment',
        path: 'api/blogs/{id}/create-comment',
        methods: ['POST']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function post ( $id, Request $request) 
    {
        $blog = $this->blogRepository->find($id);
        $token = $request->headers->get('x-api-token');

        $userId = Utils::tokenToUserId($token);
        $user = $this->userRepository->find($userId);
    
        $newComment = new Comment();
        $newComment->setProfile($user->getProfile());

        $data = json_decode($request->getContent(), true);
        $newComment->setDescription($data['description']);

        // if ($data['rate'] == 'like')
        // {
        //     $blog->likeIt();
        //     // return new JsonResponse($blog->getLikes());
        // }

        // $data['rate'] == 'like' ? dd($data['rate']) : null; //$blog->likeIt() : null;
        $data['rate'] == 'dislike' ? $blog->dislikeIt() : $blog->likeIt();


        $newComment->setRate($data['rate']);
        // return new JsonResponse($data);

        $newComment->setBlog($blog);
        $blog->addComment($newComment);

        $newComment->setCreated(new \DateTime('now'));
        $user->getProfile()->addComment($newComment);


        // dd($newComment);
        $this->entityManager->persist($newComment);
        $this->entityManager->flush();
     
        return new JsonResponse(['message' => 'Comment added'], Response::HTTP_ACCEPTED);


    }
}
