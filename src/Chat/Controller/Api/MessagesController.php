<?php

declare(strict_types=1);

namespace App\Chat\Controller\Api;

use App\Entity\Message;
use App\Entity\Messaging;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use http\Exception\RuntimeException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @FOSRest\Route("/api-message/")
 */
final class MessagesController extends AbstractFOSRestController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     *
     * @OA\Tag(name="Messages")
     * @OA\Response(
     *     response="200",
     *     description="retrieve list of chat for current user successfully",
     *     @OA\Schema(@OA\Items(ref=@Model(type="App\Entity\Message")))
     * )
     * @OA\Response(response="404", description="entity not found")
     * @OA\Response(response="403", description="Unauthorized user to make this action")
     * @OA\Response(response="500", description="server error")
     *
     * @FOSRest\Get("get-messages-by-messaging/{id}", name="get_messages", methods={"GET"})
     * @FOSRest\View(statusCode=Response::HTTP_OK)
     *
     * @param Messaging $messaging
     * @param MessageRepository $messageRepository
     *
     * @return string
     */
    public function getMessages(Messaging $messaging, MessageRepository $messageRepository): string
    {
        if (!$this->getUser()) {
            throw new \LogicException('Unauthorized user to make this action');
        }

        $output = [];
        foreach ($messageRepository->getMessages($messaging->getId()) as $message) {
            $output[$message['id']] = [
                'author' => $message['author_username'],
                'contributors' => $message['contributors_username'],
                'avatar' => $message['avatar'],
                'content' => $message['content'],
                'createdAt' => $message['createdAt']->format('d-m-Y')
            ];
        }

        return $this->renderView('chat/messages.html.twig', [
            'messages' => $output
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     *
     * @OA\Tag(name="Messages")
     * @OA\Response(
     *     response="201",
     *     description="post new message successfully",
     *     @OA\Schema(@OA\Items(ref=@Model(type="App\Entity\Message")))
     * )
     * @OA\Response(response="404", description="entity not found")
     * @OA\Response(response="403", description="Unauthorized user to make this action")
     * @OA\Response(response="500", description="server error")
     *
     * @FOSRest\RequestParam(name="content", description="message")
     * @FOSRest\Post("new-message-in-messaging/{id}", name="new_message", methods={"POST"})
     * @FOSRest\View(statusCode=Response::HTTP_OK)
     *
     * @param Messaging $messaging
     * @param EntityManagerInterface $entityManager
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return View
     */
    public function new(
        Messaging $messaging,
        EntityManagerInterface $entityManager,
        ParamFetcherInterface $paramFetcher
    ): View
    {
        if (!$this->getUser()) {
            throw new \LogicException('Unauthorized user to make this action');
        }

        try {
            $message = new Message();
            $message->setAuthor($this->getUser());
            $message->setContent($paramFetcher->get('content'));
            $message->setCreatedAt(new \DateTime());
            $message->setMessaging($messaging);
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->view('success', Response::HTTP_CREATED);

        } catch (\RuntimeException $e) {
            throw new RuntimeException('error : '. $e);
        }
    }
}