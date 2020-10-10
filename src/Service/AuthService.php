<?php

namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthService
{
    private const TOKEN_EXPIRATION = 3600;

    /** @var UserService */
    private $userService;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var JWTEncoderInterface */
    private $jwtEncoder;

    public function __construct(UserService $userService, UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder)
    {
        $this->userService = $userService;
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function isCredentialsValid(string $login, string $password): bool
    {
        $user = $this->userService->findUserByLogin($login);
        if ($user === null) {
            return false;
        }

        return $this->passwordEncoder->isPasswordValid($user, $password);
    }

    public function getToken(string $login): ?string
    {
        $tokenData = ['username' => $login, 'exp' => time() + self::TOKEN_EXPIRATION];

        return $this->jwtEncoder->encode($tokenData);
    }
}