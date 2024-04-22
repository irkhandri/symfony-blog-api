<?php
namespace App\Controller;

use App\Entity\Profile;
use App\Repository\BlogRepository;
use App\Repository\InterestRepository;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
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
        name: 'get-by-token',
        path: 'api/token',
        methods: ["POST"]
    )]
    public function getProfileByToken (Request $request)
    {
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        $token = $decoded['token'];
        // return new JsonResponse(['message' => $decoded]);

        $user = $this->userRepo->findByApiToken($token);

        if (!$user)
            return new JsonResponse(['message' => 'Loh again'], Response::HTTP_BAD_REQUEST);

        $profile = $user->getProfile();


        $jsonContent = [
            'id' => $profile->getId(),
            'name' => $profile->getName(),
            'location' => $profile->getLocation(),
            'imageUrl' => $profile->getImageUrl(),
        ];
    

        return new JsonResponse($jsonContent);

    }






    #[Route (
        name: 'get-profile',
        path: 'api/profiles/{id}',
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
    public function getProfiles ()
    {
        $profiles = $this->profileRepo->findAll();

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
        // dd('HERE');

        $profile = $this->profileRepo->find($id);
        // dd('HERE');

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
