<?php

namespace App\DTO;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;


class UserDTO
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=32)
     */
    public $login;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=32)
     */
    public $password;

    public $roles;

    public function __construct(array $data)
    {
        $this->login = $data['login'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->roles = json_decode($data['roles'], true) ?? [];
    }

    public function fromEntity(User $user): self
    {
        return new self([
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
            'roles' => $user->getRoles(),
        ]);
    }
}