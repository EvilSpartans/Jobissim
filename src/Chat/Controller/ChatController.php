<?php

namespace App\Chat\Controller;

use Pusher\Pusher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChatController extends AbstractController
{
    /**
     * @Route("/chat", name="chat", methods={"GET"})
     */
    public function __invoke(): Response
    {
        if (!$this->getUser()) {
            throw new \LogicException('You don\'t have access to this page');
        }

        return $this->render('chat/messagings.html.twig');
    }
}
