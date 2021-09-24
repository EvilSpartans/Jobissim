<?php

declare(strict_types=1);

namespace App\Chat\Services;

use App\Entity\User;
use App\Repository\UserRepository;

class UsersManager
{
    /*** @var UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {

        $this->userRepository = $userRepository;
    }

    public function getFollowedList(User $user): array
    {
        $users = $this->userRepository->getFollowedList($user);
        $output = [];

        /** @var User $user */
        foreach ($users as $user) {
            $output[] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];
        }

        return $output;
    }
}