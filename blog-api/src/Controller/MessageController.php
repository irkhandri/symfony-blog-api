<?php

namespace App\Controller;

use App\Utils;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;


#[AsController]
class MessageController extends AbstractController
{
    private $entityMager;
    private $profileRepository;
    private $messageRepository;
    private $userRepository;
    private User $user;

    public function __construct (
        EntityManagerInterface $em, 
        ProfileRepository $profileRepo,
        MessageRepository $messagerepo,
        UserRepository $userRepository
    )
    {
        $this->entityMager = $em;
        $this->profileRepository = $profileRepo;
        $this->messageRepository = $messagerepo;
        $this->userRepository = $userRepository;
    }

    #[Route(
        name: 'count-unread',
        path: 'api/messages/unread',
        methods: ['GET']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    public function unread (Request $request) 
    {
        $token = $request->headers->get('x-api-token');
        $user = $this->userRepository->find(Utils::tokenToUserId($token));
        $unreadMessages = $this->messageRepository->count(['is_read' => false, 'recipient' => $user->getProfile()]);
        // dd($unreadMessages);
        return new JsonResponse( $unreadMessages, Response::HTTP_ACCEPTED);
    }

    

    // #[Route('api/messages', name: 'post_message', methods: ["POST"])]
    #[Route(
        name: 'post-message',
        path: 'api/messages/{id}',
        methods: ['POST']
    )]
    public function post(Request $request, $id): JsonResponse
    {

        $recipient = $this->profileRepository->find($id);
        if ($recipient) 
        {
            $this->user = $this->getUser();

            $data = json_decode($request->getContent(), true);
            $newMessage = new Message();

            $newMessage->setSubject($data['subject']);
            $newMessage->setText($data['text']);
            $newMessage->setRecipient($recipient);
            $newMessage->setSender($this->user->getProfile());

            $createdDateTime = new \DateTime('now');

            $newMessage->setCreated($createdDateTime);

            $this->entityMager->persist($newMessage);
            $this->entityMager->flush();
        }

        return new JsonResponse(['message' => 'Message created'], Response::HTTP_ACCEPTED);

        // dd($recipient);

    }

    #[Route(
        name: 'inbox-messages',
        path: 'api/inbox-messages',
        methods: ['GET']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    public function inbox(): JsonResponse
    {
        $this->user = $this->getUser();

        $messages = $this->messageRepository->findBy(['recipient' => $this->user->getProfile()], ['is_read' => 'ASC', 'created' => 'DESC']);

        // dd($messages[0]->getSender());

        $responseData = [];
        foreach ($messages as $message) {
            // dd($message->getSender());
            $responseData[] = [
                'id' => $message->getId(),
                'subject' => $message->getSubject(),
                'text' => $message->getText(),
                'recipient' => [
                    'id' => $message->getRecipient()->getId(),
                    'name' => $message->getRecipient()->getName(),
                    // Další potřebné atributy příjemce
                ],
                'sender' => [
                    'id' => $message->getSender()->getId(),
                    'name' => $message->getSender()->getName()
                ],
                'is_read' => $message->isIsRead(),
                'created' => $message->getCreated()
            ];
        }
        // dd($responseData);
        return new JsonResponse($responseData);
    }



    #[Route(
        name: 'outbox-messages',
        path: 'api/outbox-messages',
        methods: ['GET']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]

    public function outbox(): JsonResponse
    {
        $this->user = $this->getUser();

        $messages = $this->messageRepository->findBy(['sender' => $this->user->getProfile()], ['created' => 'DESC']);

        // dd($messages[0]->getSender());

        $responseData = [];
        foreach ($messages as $message) {
            // dd($message->getSender());
            $responseData[] = [
                'id' => $message->getId(),
                'subject' => $message->getSubject(),
                'text' => $message->getText(),
                'recipient' => [
                    'id' => $message->getRecipient()->getId(),
                    'name' => $message->getRecipient()->getName(),
                ],
                'sender' => [
                    'id' => $message->getSender()->getId(),
                    'name' => $message->getSender()->getName()
                ],
                'is_read' => $message->isIsRead(),
                'created' => $message->getCreated()
            ];
        }
        // dd($responseData);
        return new JsonResponse($responseData);
    }


    #[Route(
        name: 'current-message',
        path: 'api/messages/{id}',
        methods: ['GET']
    )]
    #[IsGranted('MESSAGE_OWNER', subject: 'message')]
    public function message ($id, Message $message) : JsonResponse
    {
        $message->setIsRead(true);
        $responseData = [
            'id' => $message->getId(),
            'subject' => $message->getSubject(),
            'text' => $message->getText(),
            'recipient' => [
                'id' => $message->getRecipient()->getId(),
                'name' => $message->getRecipient()->getName(),
            ],
            'sender' => [
                'id' => $message->getSender()->getId(),
                'name' => $message->getSender()->getName()
            ],
            'is_read' => $message->isIsRead(),
            'created' => $message->getCreated()
        ];

        $this->entityMager->flush();

        return new JsonResponse($responseData);
    }


}
