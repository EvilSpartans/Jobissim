<?php

declare(strict_types=1);

namespace App\Chat\Controller\Api;

use App\Entity\User;
use App\Chat\Services\Builder;
use App\Repository\MessagingRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @FOSRest\Route("/api-messagings/")
 */
final class MessagingsController extends AbstractFOSRestController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     *
     * @OA\Tag(name="Messagings")
     * @OA\Response(
     *     response="200",
     *     description="retrieve list of chat for current user successfully",
     *     @OA\Schema(@OA\Items(ref=@Model(type="App\Entity\Messaging")))
     * )
     * @OA\Response(response="404", description="entity not found")
     * @OA\Response(response="403", description="Unauthorized user to make this action")
     * @OA\Response(response="500", description="server error")
     *
     * @FOSRest\Get("chat-list-by-user/{id}", name="chat_list_by_user", methods={"GET"})
     * @FOSRest\View(serializerGroups={"chat_list"}, statusCode=Response::HTTP_OK)
     *
     * @param User $user
     * @param MessagingRepository $messagingRepository
     * @param Builder $builder
     *
     * @return View
     *
     * @throws \Exception
     */
    public function __invoke(User $user, MessagingRepository $messagingRepository, Builder $builder): View
    {
        if ($user !== $this->getUser()) {
            throw new \LogicException('Unauthorized user to make this action');
        }
        try {
            return $this->view($builder->getMessagings($messagingRepository->findByAuthorOrParticipants($user)), Response::HTTP_OK);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
