<?php

namespace App\Controller;

// use ApiPlatform\Api\UrlGeneratorInterface;
use App\Service\CustomTokenGenerator;
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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Firebase\JWT\JWT;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\Mailer\Mailer;

use Symfony\Component\Security\Core\Security;


use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\Regex;

//schemes:['https'],

class RegistrationController extends AbstractController
{

    private $userPasswordHasher;
    private $userRepo;
    private $entityManager;
    private User $user;
    private CustomTokenGenerator $tokenGenerator;


    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher, 
        UserRepository $uR, 
        EntityManagerInterface $eM, 
        CustomTokenGenerator $tokenGenerator
        )
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepo = $uR;
        $this->entityManager = $eM;   
        $this->tokenGenerator = $tokenGenerator;
     
    }

    #[Route('/register', name: 'registration',  methods:['POST', "GET"] )]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = $request->getContent();
        $decoded =  json_decode($data, true);


        if ($decoded['password'] == ''  || $decoded['email'] == '')
        {
            // dd("HERE");
            return new JsonResponse(['error' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
        }

        // $controllEmail = $this->userRepo->findBy(['email' => $decoded['']])
    

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
        $user->setResetToken(0);
        $entityManager->persist($user);


        try {
            $entityManager->flush();
            return new JsonResponse(['message' => 'Data successfully saved'], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Conflict',
                'message' => 'Duplicate entry for email',
                'details' => $e->getMessage()
            ], 409);
        }

        // $entityManager->flush();

        // return new JsonResponse(['message' => 'User was added'], Response::HTTP_CREATED);
    }

    #[Route('/reset-password', name: 'reset-password',  methods:['POST', "GET"] )]
    public function forgot (Request $request, MailerInterface $mailer)
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];

        if (empty($email))
            return new JsonResponse(['error' => 'Email are required.']);

        $user = $this->userRepo->findOneBy(['email' => $email]);

        if (!$user)
        {
            return new JsonResponse (['error' => 'Wrong email åß']);
        }

        $token = $this->tokenGenerator->generateToken();

        try
        {
            $user->setResetToken($token);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

        }
        catch (\Exception $e)
        {
            return new JsonResponse (['error' => 'Invalid åß']);

        }

        $url = $this->generateUrl('app-reset-password', ['reset_token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        // $newUrl = str_replace('127.0.0.1:8001', 'symfony-blog.fromargo.com/symfony-blog-api/blog-api/public', $url );

        $message = (new Email())
            ->from('formydjangoblog@gmail.com')
            ->to($user->getEmail())
            // ->html("<p>Hello,</p><p>Click <a href=\"$newUrl\">here</a> to reset your password.</p>");
            ->html("<p>Hello,</p><p>Click <a href=\"$url\">here</a> to reset your password.</p>");


        $mailer->send($message);



        return new JsonResponse(['message' => 'Sent']);


    }


    #[Route('/resetpassword', name: 'app-reset-password',  methods:['POST', "GET"] )]

    public function resetpassword (Request $request)
    {
        $token = trim($request->query->get("reset_token"), '"' );
        $user = $this->userRepo->findOneBy(['reset_token' => $token]);

        if ($request->isMethod('POST'))
        {
            $data = $request->getContent();
            parse_str($request->getContent(), $data);
            $password = $data['password'];

            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );

            $this->entityManager->flush();
            
            return $this->render('done.html.twig');
        }


        return $this->render("reset.html.twig", [
            'email' => $user->getEmail(),
        ]);


        // dd($user);

        // return $this->redirectToRoute('login');
    }


    // 
    #[Route('/login', name: 'login',  methods:['POST', "GET"], schemes:['http'] )]

    public function login (Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];
        if (empty($email) || empty($password))
            // throw new BadCredentialsException ('Email and password are required.');
            return new JsonResponse(['error' => 'Email and password are required.']);

        // dd($email);

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
    public function logout (Request $request)
    {
        $apiToken = $request->headers->get('x-api-token');
     
        // dd($apiToken);


        return new JsonResponse(['message' => 'Logout Successfully']);
    }
   
}
