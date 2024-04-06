<?php

namespace App\Controller;

use App\Entity\Interest;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\InterestRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[AsController]
class InterestController extends AbstractController
{
    private $entityManager;
    private $interestRepository;
    private ?User $user;

    public function __construct(
        InterestRepository $interestRepo, 
        EntityManagerInterface $em
        )
    {
        $this->entityManager = $em;
        $this->interestRepository = $interestRepo;
    }


    #[Route('/api/interests', name: 'post-interest', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function post(Request $request): JsonResponse
    {
        $interest = new Interest();
        $data = json_decode($request->getContent(), true);

        foreach ($data as $key => $value)
        {
            if (property_exists(Interest::class, $key) && $value !== null)
            {
                $setterMethod = 'set' . ucfirst($key);
                $interest->$setterMethod($value);
            }
        }

        $this->user = $this->getUser();
        $interest->setProfile($this->user->getProfile());

        $this->entityManager->persist($interest);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Added successfully'], Response::HTTP_ACCEPTED);
        // dd($interest);
    }


    #[Route(
        name: 'delete-interest',
        path: 'api/interests/{id}',
        methods: ['DELETE']
    )]
    #[IsGranted('INTEREST_OWNER', subject: 'interest')]
    public function delete (Interest $interest, $id)
    {
        // dd($interest);
        $this->entityManager->remove($interest);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Deleted successfully'], Response::HTTP_ACCEPTED);
    }


    #[Route(
        name: 'edit-interest',
        path: 'api/interests/{id}',
        methods: ['PUT']
    )]
    #[IsGranted('INTEREST_OWNER', subject: 'interest')]
    public function edit (Request $request, Interest $interest)
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $key => $value)
        {
            if (property_exists(Interest::class, $key) && $value !== null)
            {
                $setterMethod = 'set' . ucfirst($key);
                $interest->$setterMethod($value);
            }
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'UPD successfully'], Response::HTTP_ACCEPTED);


    }



    
}
