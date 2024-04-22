<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Entity\Profile;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\RequiresHttps;

//schemes:['https'],

class RegistrationController extends AbstractController
{

    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        
    }

    #[Route('/register', name: 'registration',  methods:['POST', "GET"] )]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
   
        $data = $request->getContent();
        $decoded =  json_decode($data, true);



        try {

            !isset($decoded['password']) ?  throw new \Exception('Missing  password') : null ;
            !isset($decoded['email']) ?  throw new \Exception('Missing  email') : null ;

        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        $user = new User();


        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $decoded['password']
            )
        );
        // dd($user);

        $user->setEmail($decoded['email']);

        $profile = new Profile ();
        $profile->setEmail($decoded['email']);

        $user->setProfile($profile);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User was added'], Response::HTTP_CREATED);
    }


    
    #[Route('/login', name: 'login',  methods:['POST', "GET"] )]
    public function login (Request $request,UserRepository $userRepo, EntityManagerInterface $entityManager): Response
    {
        $token = bin2hex(random_bytes(60));

        $data = $request->getContent();
        $decoded =  json_decode($data, true);

        $user = $userRepo->findOneBy(['email' => $decoded['email'] ]);

        if (!$user) 
        {
            return new JsonResponse(['message' => 'Wrong email'], Response::HTTP_BAD_REQUEST);
        }
        if ($this->userPasswordHasher->isPasswordValid($user, $decoded['password']))
        {
            $newToken = new ApiToken();

            $newToken->setToken($token);
            $newToken->setUser($user);

            // $user->addApiToken($newToken);   
            $entityManager->persist($newToken);
            // $entityManager->pers
            $entityManager->flush();
            // dd( $newToken);
            // dd($newToken);

            return new JsonResponse(['token' => $newToken->getToken()], Response::HTTP_ACCEPTED);
        }
        else 
        {
            return new JsonResponse(['message' => 'Wrong password'], Response::HTTP_BAD_REQUEST);

        }
        

        // dd($decoded);


    }

   
}
