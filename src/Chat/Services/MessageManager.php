<?php

declare(strict_types=1);

namespace App\Chat\Services;

use App\Entity\Message;
use App\Entity\Messaging;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class MessageManager
{
    public function newMessage(
        User $user,
        EntityManagerInterface
        $entityManager,
        string $content,
        Messaging $messaging
    ): Message {
        $message = new Message();
        $message->setAuthor($user);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTime());
        $message->setMessaging($messaging);
        $entityManager->persist($message);
        $entityManager->flush();

        return $message;
    }
}
