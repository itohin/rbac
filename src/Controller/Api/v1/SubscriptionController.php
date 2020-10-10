<?php

namespace App\Controller\Api\v1;


use App\DTO\AddFollowerDTO;
use App\Service\AsyncService;
use App\Service\SubscriptionService;
use App\Service\UserService;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Mikhail Kamorin aka raptor_MVK
 *
 * @copyright 2020, raptor_MVK
 *
 * @Annotations\Route("/api/v1/subscription")
 */
final class SubscriptionController
{
    /** @var SubscriptionService */
    private $subscriptionService;
    /** @var UserService */
    private $userService;
    /** @var AsyncService */
    private $asyncService;

    public function __construct(SubscriptionService $subscriptionService, UserService $userService, AsyncService $asyncService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->userService = $userService;
        $this->asyncService = $asyncService;
    }

    /**
     * @Annotations\Get("/list-by-author")
     *
     * @QueryParam(name="authorId", requirements="\d+")
     */
    public function listSubscriptionByAuthorAction(int $authorId): View
    {
        $followers = $this->subscriptionService->getFollowers($authorId);
        [$code, $data] = empty($followers) ? [204, ''] : [200, ['followers' => $followers]];

        return View::create($data, $code);
    }

    /**
     * @Annotations\Get("/list-by-follower")
     *
     * @QueryParam(name="followerId", requirements="\d+")
     */
    public function listSubscriptionByFollowerAction(int $followerId): View
    {
        $authors = $this->subscriptionService->getAuthors($followerId);
        [$code, $data] = empty($authors) ? [204, ''] : [200, ['authors' => $authors]];

        return View::create($data, $code);
    }

    /**
     * @Annotations\Post("")
     *
     * @RequestParam(name="authorId", requirements="\d+")
     * @RequestParam(name="followerId", requirements="\d+")
     */
    public function subscribeAction(int $authorId, int $followerId): View
    {
        $success = $this->subscriptionService->subscribe($authorId, $followerId);
        $code = $success ? 200 : 400;

        return View::create(['success' => $success], $code);
    }


    /**
     * @Route("/followers", methods={"POST"})
     */
    public function addFollowersAction(Request $request): View
    {
        $userId = $request->request->get('userId');
        $followerLogin = $request->request->get('followerLogin');
        $count = $request->request->get('count');
        $async = $request->request->get('async');
        $user = $this->userService->findUserById($userId);
        if ($user === null) {
            return View::create(['message' => "Author $userId was not found"], 400);
        }
        $createdFollowers = $async === '0' ?
            $this->subscriptionService->addFollowers($user, $followerLogin, $count) :
            $this->asyncService->publishToExchange(
                AsyncService::ADD_FOLLOWER,
                (new AddFollowerDTO($userId, $followerLogin, $count))->toAMQPMessage()
            );

        return View::create(['created' => $createdFollowers], 200);
    }
}