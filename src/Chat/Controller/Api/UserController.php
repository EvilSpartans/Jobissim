<?php

declare(strict_types=1);

namespace App\Chat\Controller\Api;

use App\Chat\Services\UsersManager;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @FOSRest\Route("/api-user/")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     *
     * @OA\Tag(name="User")
     * @OA\Response(
     *     response="200",
     *     description="retrieve list of friends successfully",
     *     @OA\Schema(@OA\Items(ref=@Model(type="App\Entity\User")))
     * )
     * @OA\Response(response="404", description="entity not found")
     * @OA\Response(response="403", description="Unauthorized user to make this action")
     * @OA\Response(response="500", description="server error")
     *
     * @FOSRest\Get("users-followed-list", name="users_followed_list_by_user", methods={"GET"})
     * @FOSRest\View(serializerGroups={"chat_list"}, statusCode=Response::HTTP_OK)
     *
     * @param UserRepository $userRepository
     * @param UsersManager $usersManager
     * @return View
     *
     */
    public function __invoke(UserRepository $userRepository, UsersManager $usersManager): View
    {
        return $this->view($usersManager->getFollowedList($this->getUser()), Response::HTTP_OK);
    }
}