<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Entity\Profile;
use App\Form\RegistrationFormType;
use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\RequiresHttps;

use Firebase\JWT\JWT;
use Symfony\Component\Security\Core\Security;


use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
//schemes:['https'],

class RegistrationController extends AbstractController
{

    private $userPasswordHasher;
    private $userRepo;
    private $entityManager;
    private User $user;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher, 
        UserRepository $uR, 
        EntityManagerInterface $eM, 
        )
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepo = $uR;
        $this->entityManager = $eM;        
    }

    #[Route('/register', name: 'registration',  methods:['POST', "GET"] )]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = $request->getContent();
        $decoded =  json_decode($data, true);

        try 
        {
            !isset($decoded['password']) ?  throw new \Exception('Missing  password') : null ;
            !isset($decoded['email']) ?  throw new \Exception('Missing  email') : null ;

        } catch (\Exception $e) 
        {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $decoded['password']
            )
        );

        $user->setEmail($decoded['email']);

        $profile = new Profile ();
        $profile->setEmail($decoded['email']);

        $user->setProfile($profile);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User was added'], Response::HTTP_CREATED);
    }


    // 
    #[Route('/login', name: 'login',  methods:['POST', "GET"] )]

    public function login (Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];
        if (empty($email) || empty($password))
            throw new BadCredentialsException ('Email and password are required.');
            // return new JsonResponse(['message' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        
        $user = $this->userRepo->findOneBy(['email' => $email]);

        if(!$user  || !$this->userPasswordHasher->isPasswordValid($user, $password))
            return new JsonResponse (['error' => 'Invalid email or password.']);
      
        $key = 'secret_key';

        $payload = [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
        ];

        $issuedAt = time();
        $expirationTime = $issuedAt + 3600 * 12;                    // TIME 

        $token = JWT::encode([
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $payload
        ],$key , 'HS256');

        return new JsonResponse(['token' => $token]);





        

    }
    // public function login (Request $request): Response
    // {
    //     $token = bin2hex(random_bytes(60));

    //     $data = $request->getContent();
    //     $decoded =  json_decode($data, true);

    //     $user = $this->userRepo->findOneBy(['email' => $decoded['email'] ]);

    //     if (!$user) 
    //     {
    //         return new JsonResponse(['message' => 'Wrong email or password'], Response::HTTP_BAD_REQUEST);
    //     }
    //     if ($this->userPasswordHasher->isPasswordValid($user, $decoded['password']))
    //     {
    //         $newToken = new ApiToken();

    //         $newToken->setToken($token);
    //         $newToken->setUser($user);

    //         $this->entityManager->persist($newToken);
    //         $this->entityManager->flush();

    //         return new JsonResponse(['token' => $newToken->getToken()], Response::HTTP_ACCEPTED);
    //     }
    //     else 
    //     {
    //         return new JsonResponse(['message' => 'Wrong email or password'], Response::HTTP_BAD_REQUEST);

    //     }
        

    //     // dd($decoded);


    // }


    #[Route('/logout', name: 'logout',  methods:['POST', "GET"] )]
    public function logout (Request $request, ApiTokenRepository $tokenRepo)
    {
        $apiToken = $request->headers->get('x-api-token');
     
        // dd($apiToken);
        $token = $tokenRepo->findOneBy(['token' => $apiToken]);

        $this->entityManager->remove($token);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Logout Successfully']);
    }
   
}
