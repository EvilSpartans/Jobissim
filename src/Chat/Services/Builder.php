<?php

declare(strict_types=1);

namespace App\Chat\Services;

use App\Entity\Messaging;

final class Builder
{
    public function getMessagings(array $messagings): array
    {
        $output = [];
        /** @var Messaging $messaging */
        foreach($messagings as $messaging) {
            $output['messaging'][] = [
                'id' => $messaging->getId(),
                'createdAt' => $messaging->getCreatedAt()->format('d/m/y'),
                'author' => $messaging->getAuthor(),
                'contributors' => $messaging->getParticipants(),
                'messages' => $messaging->getMessages(),
            ];
        }

        return $output;
    }
}
