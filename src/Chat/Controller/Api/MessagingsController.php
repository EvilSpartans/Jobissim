<?php

declare(strict_types=1);

namespace App\Chat\Controller\Api;

use App\Entity\User;
use App\Chat\Services\Builder;
use App\Repository\MessagingRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints;

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
    public function list(User $user, MessagingRepository $messagingRepository, Builder $builder): View
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

    /**
     * @Security("is_granted('ROLE_USER')")
     *
     * @OA\Tag(name="Messagings")
     * @OA\Response(
     *     response="200",
     *     description="retrieve messaging by contributors and current user successfully",
     *     @OA\Schema(@OA\Items(ref=@Model(type="App\Entity\Messaging")))
     * )
     * @OA\Response(response="404", description="entity not found")
     * @OA\Response(response="403", description="Unauthorized user to make this action")
     * @OA\Response(response="500", description="server error")
     *
     * @FOSRest\QueryParam(name="contributors", description="array of contributors id", requirements={@Constraints\Type(type="array")} )
     * @FOSRest\Get("get-messaging-by-contributors", name="get_messaging_by_contributor", methods={"GET"})
     * @FOSRest\View(serializerGroups={"chat_list"}, statusCode=Response::HTTP_OK)
     *
     * @param MessagingRepository $messagingRepository
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return View
     *
     * @throws \Exception
     */
    public function getMessagingsByContributors(
        MessagingRepository $messagingRepository,
        ParamFetcherInterface $paramFetcher
    ): View
    {
        if (!$this->getUser()) {
            throw new \LogicException('Unauthorized user to make this action');
        }
        $output = [];
        try {
            if (is_array($paramFetcher->get('contributors'))) {
                foreach($messagingRepository->getMessagingByContributors($paramFetcher->get('contributors'), $this->getUser()) as $messagings) {
                    foreach ($messagings as $messaging) {
                        $output[] = $messaging;
                    }
                }
            }
            return $this->view($output,Response::HTTP_OK);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
