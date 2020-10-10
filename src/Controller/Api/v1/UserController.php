<?php


namespace App\Controller\Api\v1;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api/v1/user") */
class UserController
{
    /** @var UserService */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("", methods={"POST"})
     */
    public function saveUserAction(Request $request): Response
    {
        $userDto = new UserDTO([
            'login' => $request->request->get('login'),
            'password' => $request->request->get('password'),
            'roles' => $request->request->get('roles')
        ]);
        $userId = $this->userService->saveUser(new User(), $userDto);
        [$data, $code] = $userId === null ?
            [['success' => false], 400] :
            [['success' => true, 'userId' => $userId], 200];

        return new JsonResponse($data, $code);
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function getUsersAction(Request $request): Response
    {
        $perPage = $request->request->get('perPage');
        $page = $request->request->get('page');
        $users = $this->userService->getUsers($page ?? 0, $perPage ?? 20);
        $code = empty($users) ? 204 : 200;

        return new JsonResponse(['users' => $users], $code);
    }

    /**
     * @Route("", methods={"DELETE"})
     */
    public function deleteUserAction(Request $request): Response
    {
        $userId = $request->query->get('userId');
        $result = $this->userService->deleteUserById($userId);

        return new JsonResponse(['success' => $result], $result ? 200 : 404);
    }

    /**
     * @Route("", methods={"PATCH"})
     */
    public function updateUserAction(Request $request): Response
    {
        $userId = $request->request->get('userId');
        $userDto = new UserDTO([
            'login' => $request->request->get('login'),
            'password' => $request->request->get('password'),
            'roles' => json_decode($request->request->get('roles'), true)
        ]);
        $result = $this->userService->updateUser($userId, $userDto);

        return new JsonResponse(['success' => $result], $result ? 200 : 404);
    }
}