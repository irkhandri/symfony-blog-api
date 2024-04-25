<?php
namespace App\Controller;

use App\Utils;
use App\Entity\Profile;
use App\Repository\BlogRepository;
use App\Repository\InterestRepository;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Regex;

#[AsController]
class ProfileController extends AbstractController
{
    private $entityManager;
    private $profileRepo;
    private $userRepo;
    private $interestRepository;

    public function __construct(
        ProfileRepository $profileRepo, 
        UserRepository $userRepo ,
        EntityManagerInterface $em,
        InterestRepository $interestRepository
        )
    {
        $this->userRepo = $userRepo;
        $this->profileRepo = $profileRepo;
        $this->interestRepository = $interestRepository;
        $this->entityManager = $em;
    }


    

    #[Route (
        name: 'account',
        path: '/api/profiles/account',
        methods: ["GET"]
    )]
    public function  account (Request $request)
    {
        // dd($request->headers->get('x-api-token'));

        $token = $request->headers->get('x-api-token');

        $userId = Utils::tokenToUserId($token);
        $user = $this->userRepo->find($userId);
        $profile = $user->getProfile();

        $interests = [];
        foreach($profile->getInterests() as $interest)
        {
            $interests[] = [
                'id' => $interest->getId(),
                'name' => $interest->getName(),
                'description' => $interest->getDescription(),
            ];
        }

         // serialize blogs
         $blogs = [];
         foreach($profile->getBlogs() as $blog)
            $blogs[] = BlogController::serializeBlog($blog);
         
        $jsonContent = [
            'id' => $profile->getId(),
            'name' => $profile->getName(),
            'username' => $profile->getUsername(),
            'number' => $profile->getNumber(),
            'soc_facebook' => $profile->getSocFacebook(),
            'soc_linkedin' => $profile->getSocLinkedin(),
            'email' => $profile->getEmail(),
            'intro' => $profile->getIntro(),
            'bio' => $profile->getBio(),
            'location' => $profile->getLocation(),
            'imageUrl' => $profile->getImageUrl(),
            'interests' => $interests,
            'blogs' => $blogs
        ];

        return new JsonResponse($jsonContent);
    }


    #[Route (
        name: 'get-profile',
        path: '/api/profiles/{id}',
        methods: ["GET"]
    )]
    public function getProfile ($id)
    {
        $profile = $this->profileRepo->find($id);

        // serialize interests
        $interests = [];
        foreach($profile->getInterests() as $interest)
        {
            $interests[] = [
                'id' => $interest->getId(),
                'name' => $interest->getName(),
                'description' => $interest->getDescription(),
            ];
        }

         // serialize blogs
         $blogs = [];
         foreach($profile->getBlogs() as $blog)
            $blogs[] = BlogController::serializeBlog($blog);
         
        $jsonContent = [
            'id' => $profile->getId(),
            'name' => $profile->getName(),
            'username' => $profile->getUsername(),
            'number' => $profile->getNumber(),
            'soc_facebook' => $profile->getSocFacebook(),
            'soc_linkedin' => $profile->getSocLinkedin(),
            'email' => $profile->getEmail(),
            'intro' => $profile->getIntro(),
            'bio' => $profile->getBio(),
            'location' => $profile->getLocation(),
            'imageUrl' => $profile->getImageUrl(),
            'interests' => $interests,
            'blogs' => $blogs
        ];
        return new JsonResponse($jsonContent);
    }


    



    #[Route (
        name: 'get-profiles',
        path: 'api/profiles',
        methods: ["GET"]
    )]
    public function getProfiles (Request $request)
    {
        $query = trim($request->query->get("query"), '"' );
        // dd($query);
        $profiles = !$query ? $this->profileRepo->findAll() : $this->profileRepo->findBySearchQuery($query) ;

        $jsonContent = [];

        foreach ($profiles as $profile)
        {
            $jsonContent[] = [
                'id' => $profile->getId(),
                'name' => $profile->getName(),
                'location' => $profile->getLocation(),
                'imageUrl' => $profile->getImageUrl(),
            ];
        }
        return new JsonResponse($jsonContent);
    }


    #[Route (
        name: 'edit-profile',
        path: 'api/profiles/{id}',
        methods: ["Patch"]
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    public function edit ($id, Request $request)
    {
        $profile = $this->profileRepo->find($id);

        $data = json_decode($request->getContent(), true);

        foreach ($data as $key => $value) {
            if (property_exists(Profile::class, $key) && $value !== null) {
                $setterMethod = 'set' . ucfirst($key);
                $profile->$setterMethod($value);
            }
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Edit successfully'], Response::HTTP_CREATED);
    }


    

}
