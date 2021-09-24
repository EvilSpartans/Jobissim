<?php

declare(strict_types=1);

namespace App\Chat\Services;

use App\Entity\Message;
use App\Entity\Messaging;
use Pusher\Pusher;
use Pusher\PusherException;

final class PusherManager
{
    /**
     * @throws PusherException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function trigger(Message $message, Messaging $messaging, Pusher $pusher): void
    {
        $data = [
            'content' => $message->getContent(),
            'image' => $message->getAuthor()->getAvatar(),
            'user' => $message->getAuthor()->getUsername(),
            'date' => $message->getCreatedAt()->format('d/m/y')
        ];

        $pusher->trigger(sprintf('my-channel-%s', $messaging->getId()), 'my_event', $data);
    }
}
