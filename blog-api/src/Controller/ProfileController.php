<?php
namespace App\Controller;

use App\Entity\Profile;
use App\Repository\BlogRepository;
use App\Repository\InterestRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
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
    private $blogRepository;
    private $interestRepository;

    public function __construct(
        ProfileRepository $profileRepo, 
        BlogRepository $blogRepo ,
        EntityManagerInterface $em,
        InterestRepository $interestRepository
        )
    {
        $this->blogRepository = $blogRepo;
        $this->profileRepo = $profileRepo;
        $this->interestRepository = $interestRepository;
        $this->entityManager = $em;
    }

    #[Route (
        name: 'edit-profile',
        path: 'api/profiles/{id}',
        methods: ["Patch"]
    )]
    public function edit ($id, Request $request)
    {
        $profile = $this->profileRepo->find($id);
        // dd($profile->getName());
        $data = json_decode($request->getContent(), true);

        foreach ($data as $key => $value) {
            // Pokud atribut existuje v entitě a není null, provede se aktualizace
            if (property_exists(Profile::class, $key) && $value !== null) {
                $setterMethod = 'set' . ucfirst($key);
                $profile->$setterMethod($value);
            }
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Edit successfully'], Response::HTTP_CREATED);

        dd('HERE');
    }


}
