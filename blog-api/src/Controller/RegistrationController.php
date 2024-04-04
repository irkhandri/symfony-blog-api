<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profile;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;



class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
   
        $data = $request->getContent();
        $decoded =  json_decode($data, true);

        // dd("Hello |" . $decoded['hash']) ;
        if ($decoded['email'] && $decoded['hash']) {

            // dd("Hello s|" . $decoded['hash']) ;

            $user->setPassword($decoded['hash']);
            $user->setEmail($decoded['email']);

            $profile = new Profile ();
            $profile->setEmail($decoded['email']);

            $user->setProfile($profile);



            $entityManager->persist($user);
            $entityManager->flush();


            return new JsonResponse(['message' => 'User was added'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['message' => 'WRONG INPUT'], Response::HTTP_CREATED);
    }

   
}
