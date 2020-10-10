<?php

namespace App\Controller\Api\SaveUser\v2;


use App\Service\UserService;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\View\ViewHandlerInterface;
use Psr\Log\LoggerInterface;

class Controller
{
    use ControllerTrait;

    /** @var UserService */
    private $userService;
    /** @var LoggerInterface */
    private $logger;


    public function __construct(UserService $userService, LoggerInterface $logger, ViewHandlerInterface $viewHandler)
    {
        $this->userService = $userService;
        $this->logger = $logger;
        $this->viewhandler = $viewHandler;
    }

    public function saveUserAction()
    {

    }

}