<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\UpdatedAtTrait;
use Doctrine\ORM\Mapping;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 * @Mapping\Table(name="`user`")
 * @Mapping\Entity(repositoryClass="App\Repository\UserRepository")
 * @Mapping\HasLifecycleCallbacks()
 *
 */
class User implements \JsonSerializable, UserInterface
{
    use CreatedAtTrait, UpdatedAtTrait;

    /**
     * @Mapping\Column(name="id", type="bigint", unique=true)
     * @Mapping\Id
     * @Mapping\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Mapping\Column(type="string", length=32, nullable=false, unique=true)
     */
    private $login;

    /**
     * @var string
     *
     * @Mapping\Column(type="string", length=120, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @Mapping\Column(type="json")
     */
    private $roles;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'roles' => $this->roles
        ];
    }
}